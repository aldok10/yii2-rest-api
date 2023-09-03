<?php

namespace app\core\traits;

use app\core\services\UserService;
use app\core\services\BalanceService;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Trait ServiceTrait
 * @property-read UserService $userService
 * @property-read BalanceService $balanceService
 */
trait ServiceTrait
{
    /**
     * @return UserService|object
     */
    public function getUserService()
    {
        try {
            return Yii::createObject(UserService::class);
        } catch (InvalidConfigException $e) {
            return new UserService();
        }
    }

    /**
     * @return BalanceService|object
     */
    public function getBalanceService()
    {
        try {
            return Yii::createObject(BalanceService::class);
        } catch (InvalidConfigException $e) {
            return new BalanceService();
        }
    }
}
