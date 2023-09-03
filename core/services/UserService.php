<?php

namespace app\core\services;

use app\core\exceptions\InternalException;
use app\core\models\Balance;
use app\core\models\TopTransactionOutbound;
use app\core\models\User;
use app\core\requests\CreateUserRequest;
use app\core\traits\DbTransactionTrait;
use app\core\types\UserStatus;
use DateTimeImmutable;
use sizeg\jwt\Jwt;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yiier\helpers\Setup;

class UserService
{
    use DbTransactionTrait;

    /**
     * @param CreateUserRequest $request
     * @return array
     * @throws \yii\db\Exception
     */
    public static function createUser(CreateUserRequest $request): array
    {

        $user = new User();
        $balance = new Balance();
        $topTransactionOutbound = new TopTransactionOutbound();

        self::dbTransaction(function () use ($request, &$user, &$balance, &$topTransactionOutbound) {
            $user->username = $request->username;
            $user->setPassword($request->password);
            $user->generateAuthKey();
            self::save($user);

            $balance->user_id = $user->id;
            $balance->amount = 0;
            self::save($balance);

            $topTransactionOutbound->user_id = $user->id;
            $topTransactionOutbound->amount = 0;
            self::save($topTransactionOutbound);

            user()->setIdentity($user);
        });

        return [
            'user' => $user,
            'balance' => $balance,
            'token' => self::getToken(),
        ];
    }


    /**
     * @return string
     * @throws \Throwable
     */
    public static function getToken(): string
    {
        /** @var Jwt $jwt */
        $jwt = Yii::$app->jwt;
        if (!$jwt->signerKeyContents) {
            throw new InternalException(t('app', 'The JWT secret must be configured first.'));
        }

        $signer = $jwt->getSigner();
        $key = $jwt->getSignerKey();
        $issuedAt = new DateTimeImmutable();
        $expiresAt = $issuedAt->modify('+72 hours');

        return $jwt->getBuilder()
            ->issuedBy(params('appUrl'))
            ->identifiedBy(Yii::$app->name)
            ->issuedAt($issuedAt)
            ->expiresAt($expiresAt)
            ->withClaim('username', \user('username'))
            ->withClaim('id', \user('id'))
            ->getToken($signer, $key)
            ->toString();
    }


    /**
     * @param string $value
     * @return User|ActiveRecord|null
     */
    public static function getUserByUsername(string $value)
    {
        return User::find()->where([
                'status'   => UserStatus::ACTIVE,
                'username' => $value
            ])
            ->limit(1)
            ->one();
    }

    private static function save(Model &$model)
    {
        if (!$model->save()) {
            throw new \yii\db\Exception(Setup::errorMessage($model->firstErrors), $model->getErrors());
        }
    }
}
