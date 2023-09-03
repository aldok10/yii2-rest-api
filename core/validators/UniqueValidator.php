<?php

namespace app\core\validators;

use app\core\exceptions\InvalidArgumentException;

class UniqueValidator extends \yii\validators\UniqueValidator
{
    /** @var int */
    public $code = 409;

    /** @var InvalidArgumentException */
    public $exception = InvalidArgumentException::class;

    /** {@inheritDoc} */
    public function validateAttribute($model, $attribute)
    {
        parent::validateAttribute($model, $attribute);
        if ($model->hasErrors($attribute)) {
            throw new $this->exception(
                $this->formatMessage(
                    $this->message,
                    [
                        'attribute' => $model->getAttributeLabel($attribute),
                        'value' => $model->$attribute,
                    ]
                ),
                $this->code
            );
        }
    }
}
