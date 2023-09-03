<?php

namespace app\core\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yiier\helpers\DateHelper;

/**
 * This is the model class for table "{{%top_transaction_outbound}}".
 *
 * @property int $id
 * @property int $user_id
 * @property float $amount
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class TopTransactionOutbound extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%top_transaction_outbound}}';
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
            [['user_id', 'amount'], 'default', 'value' => 0],
            [['user_id'], 'filter', 'filter' => 'intval', 'skipOnArray' => true],
            [['amount'], 'filter', 'filter' => 'floatval', 'skipOnArray' => true],
            [['user_id'], 'integer', 'min' => 0],
            [['amount'], 'double', 'min' => 0],
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
