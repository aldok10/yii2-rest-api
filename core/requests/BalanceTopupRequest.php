<?php

namespace app\core\requests;

class BalanceTopupRequest extends \yii\base\Model
{
    public $amount;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['amount', 'required'],
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
        ];
    }
}
