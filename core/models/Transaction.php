<?php

namespace app\core\models;

use app\core\types\TransactionType;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yiier\helpers\DateHelper;

/**
 * This is the model class for table "{{%transaction}}".
 *
 * @property int $id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property float $amount
 * @property int $transaction_type
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Transaction extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%transaction}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_user_id', 'to_user_id', 'amount'], 'default', 'value' => 0],
            [['from_user_id', 'to_user_id'], 'filter', 'filter' => 'intval', 'skipOnArray' => true],
            [['amount'], 'filter', 'filter' => 'floatval', 'skipOnArray' => true],
            [['from_user_id', 'to_user_id'], 'integer', 'min' => 0],
            [['amount'], 'double', 'min' => 0],
            [['transaction_type'], 'in', 'range' => [TransactionType::TOPUP, TransactionType::TRANSFER]],
        ];
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();

        $dateFields = ['created_at', 'updated_at'];
        foreach ($dateFields as $field) {
            if (isset($fields[$field])) {
                $fields[$field] = function (self $model) use ($field) {
                    return DateHelper::datetimeToIso8601($model?->{$field} ?? '');
                };
            }
        }

        return $fields;
    }
}
