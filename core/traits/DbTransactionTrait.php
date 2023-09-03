<?php

namespace app\core\traits;

use app\core\exceptions\InternalException;
use Yii;
use yii\db\Connection;

/**
 * Trait DbTransactionTrait
 */
trait DbTransactionTrait
{
    /**
     * @param callable $callback
     * @param Connection $db optional
     * @return void
     * @throws \Exception
     */
    public static function dbTransaction(callable $callback, Connection $db = null)
    {
        if ($db === null) {
            /** @var Connection $db */
            $db = Yii::$app->getDb();
        }

        $transaction = $db->beginTransaction();

        try {
            call_user_func($callback);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new InternalException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
