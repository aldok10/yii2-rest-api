<?php

namespace app\core\requests;

use app\core\exceptions\InvalidArgumentException;
use app\core\models\User;
use app\core\validators\UniqueValidator;

class CreateUserRequest extends \yii\base\Model
{
    public $username;
    public $password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'trim'],
            [['username'], 'required'],

            [
                'username',
                'match',
                'pattern' => '/^[a-z]\w*$/i',
                'message' => t('app', '{attribute} can only be numbers and letters.')
            ],
            [
                'username',
                UniqueValidator::class,
                'targetClass' => User::class,
                'code' => 409,
                'message' => 'Username already exists',
                'exception' => InvalidArgumentException::class,
            ],
            ['username', 'string', 'min' => 4, 'max' => 60],


            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => t('app', 'Username'),
            'password' => t('app', 'Password'),
        ];
    }
}
