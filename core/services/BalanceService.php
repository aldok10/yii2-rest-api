<?php

namespace app\core\services;

use app\core\exceptions\InvalidArgumentException;
use app\core\models\Balance;
use app\core\models\TopTransactionOutbound;
use app\core\models\Transaction;
use app\core\requests\BalanceTopupRequest;
use app\core\requests\TransferRequest;
use app\core\services\UserService;
use app\core\traits\DbTransactionTrait;
use app\core\types\TransactionType;
use yii\base\Model;
use yii\db\ActiveRecord;
use yiier\helpers\Setup;

class BalanceService
{
    use DbTransactionTrait;

    public const FROM_SYSTEM_ID = 0;

    public const CACHE_KEY_TOP_TRX_USER = 'topTransactingUserByValue';

    /**
     * @param int $user_id
     * @param array $columns
     * @return Balance|ActiveRecord|null
     * @throws InvalidArgumentException
     */
    public static function getBalanceByUserId(int $user_id, array $columns = ["*"])
    {
        return Balance::find()
        ->select($columns)
        ->where(['user_id' => $user_id])
        ->limit(1)
        ->one() ?? throw new InvalidArgumentException('Unauthorized', 401);
    }

    /**
     * @param int $user_id
     * @return TopTransactionOutbound|ActiveRecord|null
     * @throws InvalidArgumentException
     */
    public static function getTopTransactionOutboundByUserId(int $user_id, $columns = ["*"])
    {
        return TopTransactionOutbound::find()
        ->select($columns)
        ->where(['user_id' => $user_id])
        ->limit(1)
        ->one() ?? throw new InvalidArgumentException('Unauthorized', 401);
    }

    /**
     * @param int $limit
     * @return TopTransactionOutbound|ActiveRecord|null
     * @throws InvalidArgumentException
     */
    public static function getTopTransactingUserByValue(int $limit = 10)
    {
        return cache()->getOrSet(
            key: self::CACHE_KEY_TOP_TRX_USER,
            callable: fn() => TopTransactionOutbound::find()
                ->alias('t')
                ->select(['u.username', 't.amount as transacted_value'])
                ->innerJoin('user u', 'u.id=t.user_id')
                ->where(['>', 't.amount', 0])
                ->orderBy(['t.amount' => SORT_DESC])
                ->limit($limit)
                ->asArray()
                ->all(),
            duration: 1800,
        );
    }

    /**
     * @param int $user_id
     * @param int $limit
     * @return array|null
     * @throws InvalidArgumentException
     */
    public static function getTopTransactionsPerUser(int $user_id, int $limit = 10)
    {
        $transactions = Transaction::find()
            ->select(['amount', 'to_user_id'])
            ->where(['or', ['from_user_id' => $user_id], ['to_user_id' => $user_id]])
            ->orderBy(['amount' => SORT_DESC])
            ->limit($limit)
            ->asArray()
            ->all();

        $user = user()->getIdentity();

        foreach ($transactions as &$transaction) {
            $multiply = ($transaction['to_user_id'] !== $user_id) ? -1 : 1;
            $transaction['username'] = $user?->username;
            $transaction['amount'] = floatval($transaction['amount'] * $multiply);
            unset($transaction['to_user_id']);
        }

        return $transactions;
    }

    /**
     * @param BalanceTopupRequest $request
     * @param int $user_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function storeBalanceTopup(BalanceTopupRequest $request, int $user_id): array
    {
        self::dbTransaction(function () use ($request, $user_id) {
            $transaction = new Transaction();
            $transaction->amount = $request->amount;
            $transaction->to_user_id = $user_id;
            $transaction->from_user_id = self::FROM_SYSTEM_ID;
            $transaction->transaction_type = TransactionType::TOPUP;
            self::save($transaction);

            $balance = self::getBalanceByUserId($user_id);
            $balance->amount += $request->amount;
            self::save($balance);
        });

        return [
            'message' => 'Topup successful',
        ];
    }

    /**
     * @param TransferRequest $request
     * @param int $from_user_id
     * @return array
     * @throws \yii\db\Exception
     */
    public static function transfer(TransferRequest $request, int $from_user_id): array
    {
        $user = UserService::getUserByUsername($request->to_username);
        $to_user_id = intval($user?->id);

        if ($from_user_id === $to_user_id) {
            throw new InvalidArgumentException('You cant transfer balance to your account', 400);
        }

        self::dbTransaction(function () use ($request, $from_user_id, $to_user_id) {

            $myBalance = self::getBalanceByUserId($from_user_id);
            $myBalance->amount -= $request->amount;
            self::save($myBalance);

            $targetBalance = self::getBalanceByUserId($to_user_id);
            $targetBalance->amount += $request->amount;
            self::save($targetBalance);

            $transaction = new Transaction();
            $transaction->amount = $request->amount;
            $transaction->to_user_id = $to_user_id;
            $transaction->from_user_id = $from_user_id;
            $transaction->transaction_type = TransactionType::TRANSFER;
            self::save($transaction);

            $topTransactionOutbound = self::getTopTransactionOutboundByUserId($from_user_id);
            $topTransactionOutbound->amount += $request->amount;
            self::save($topTransactionOutbound);

            cache()->delete(self::CACHE_KEY_TOP_TRX_USER);
        });

        return [
            'message' => 'Transfer success',
        ];
    }

    private static function save(Model &$model)
    {
        if (!$model->save()) {
            throw new \yii\db\Exception(Setup::errorMessage($model->firstErrors), $model->getErrors());
        }
    }
}
