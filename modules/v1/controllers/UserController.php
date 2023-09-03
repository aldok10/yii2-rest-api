<?php

namespace app\modules\v1\controllers;

use app\core\exceptions\InternalException;
use app\core\exceptions\InvalidArgumentException;
use app\core\models\User;
use app\core\requests\BalanceTopupRequest;
use app\core\requests\CreateUserRequest;
use app\core\requests\TransferRequest;
use app\core\traits\ServiceTrait;

/**
 * User controller for the `v1` module
 */
class UserController extends ActiveController
{
    use ServiceTrait;

    public $modelClass = User::class;
    public $noAuthActions = ['create'];

    /**
     * {@inheritDoc}
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['index'], $actions['delete'], $actions['create']);
        return $actions;
    }

    /**
     * POST: /create_user
     * Register a new user by username.
     * Username is to be unique for every user.
     *
     * @return array
     * @throws InternalException
     * @throws InvalidArgumentException
     */
    public function actionCreate()
    {
        // return reponse http code 201 if succesfully create user
        response()?->setStatusCode(201);

        return $this?->userService?->createUser(
            request: $this?->validate(CreateUserRequest::class)
        ) ?? [];
    }

    /**
     * GET: /balance_read
     * Fetch the current balance.
     *
     * @return array
     * @throws InternalException
     * @throws InvalidArgumentException
     */
    public function actionBalanceRead()
    {
        return $this?->balanceService?->getBalanceByUserId(
            user_id: user()?->getId(),
            columns: ['amount'],
        ) ?? [];
    }

    /**
     * POST: /transfer
     * Transfer money from one user to another.
     * Wallet balance to be always >= 0. The money
     * should be deducted from the user whose account
     * the transfer is being initiated and added to
     * the user identified by "to_username" in
     * the request body. Behaviourally, this overall
     * operation is expected to be always atomic i.e.
     * it should succeed or fail as a whole.
     *
     * @return array
     * @throws InternalException
     * @throws InvalidArgumentException
     */
    public function actionTransfer()
    {
        // return reponse http code 204 if succesfully topup
        response()?->setStatusCode(204);

        return $this?->balanceService?->transfer(
            request: $this?->validate(TransferRequest::class),
            from_user_id: user()?->getId(),
        ) ?? [];
    }

    /**
     * GET: /top_users
     * Return the list of top 10 users by value of transfers.
     * The transfer value should consider only outbound
     * transfers done by a particular user (debits).
     * The transfer value per user should be the total
     * aggregated value. This API should be considered
     * to be "read-heavy" and should be optimized as such.
     *
     * @return array
     * @throws InternalException
     * @throws InvalidArgumentException
     */
    public function actionTop()
    {
        return $this?->balanceService?->getTopTransactingUserByValue() ?? [];
    }

    /**
     * POST: /balance_topup
     * Return the list of top 10 users by value of transfers.
     * The transfer value should consider only outbound
     * transfers done by a particular user (debits).
     * The transfer value per user should be the total
     * aggregated value. This API should be considered
     * to be "read-heavy" and should be optimized as such.
     *
     * @return array
     * @throws InternalException
     * @throws InvalidArgumentException
     */
    public function actionBalanceTopup()
    {
        // return reponse http code 204 if succesfully topup
        response()?->setStatusCode(204);

        return $this?->balanceService?->storeBalanceTopup(
            request: $this?->validate(BalanceTopupRequest::class),
            user_id: user()?->getId(),
        ) ?? throw new InvalidArgumentException('Invalid Token', 401);
    }

    /**
     * GET: /top_transactions_per_user
     * Return the top 10 transactions by value for the user.
     * The response should include both credits (transfers to
     * the user) and debits (transfers from the user). Transaction
     * value should be considered irrespective of the direction
     * of transfer (credit and debit). Response to be sorted on
     * the absolute value of the transactions in descending order.
     * Debit transactions to be returned as negative values.
     * In case of user with no transactions, a successful response
     * with an empty list of transactions is to be returned.
     *
     * @return array
     * @throws InternalException
     * @throws InvalidArgumentException
     */
    public function actionTopTransactionsPerUser()
    {
        return $this?->balanceService?->getTopTransactionsPerUser(
            user_id: user()?->getId(),
        ) ?? throw new InvalidArgumentException('Invalid Token', 401);
    }
}
