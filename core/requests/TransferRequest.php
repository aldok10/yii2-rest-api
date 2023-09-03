<?php

namespace app\core\requests;

use app\core\exceptions\InvalidArgumentException;
use app\core\models\User;
use app\core\validators\ExistValidator;

class TransferRequest extends \yii\base\Model
{
    public $amount;
    public $to_username;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amount', 'to_username'], 'required'],

            [
                'to_username',
                ExistValidator::class,
                'targetClass' => User::class,
                'targetAttribute' => 'username',
                'code' => 404,
                'message' => 'Destination user not found',
                'exception' => InvalidArgumentException::class,
            ],

            ['amount', 'default', 'value' => 0],
            ['amount', 'number', 'min' => 0.01, 'max' => 10000000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'amount' => t('app', 'Amount'),
            'to_username' => t('app', 'To Username'),
        ];
    }
}
