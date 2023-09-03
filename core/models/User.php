<?php

namespace app\core\models;

use app\core\types\UserStatus;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yiier\helpers\DateHelper;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $username
 * @property string|null $avatar
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property int|null $status Status: 1 normal 0 frozen
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property-write string $password
 * @property-read string $authKey
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
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
            ['status', 'default', 'value' => UserStatus::ACTIVE],
            ['status', 'in', 'range' => [UserStatus::ACTIVE, UserStatus::UNACTIVATED]],
            [['username'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->where(['id' => $id, 'status' => UserStatus::ACTIVE])
            ->limit(1)
            ->one();
    }


    /**
     * @param \Lcobucci\JWT\UnencryptedToken $token
     * @param null $type
     * @return void|IdentityInterface
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $userId = (string)$token->claims()->get('id');
        return cache()->getOrSet($token->payload(), function () use ($userId) {
            return self::findIdentity($userId);
        }, 1800);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     * @throws \yii\base\Exception
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @return array
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token']);

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
