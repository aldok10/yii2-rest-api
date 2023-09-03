<?php

namespace app\core\exceptions;

use app\core\traits\ExceptionTrait;
use yii\web\HttpException;

class InternalException extends HttpException
{
    use ExceptionTrait;

    /**
     * Constructor.
     * @param string $message error message
     * @param int $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct(
        $message = null,
        $code = ErrorCodes::INTERNAL_ERROR,
        \Exception $previous = null
    ) {
        $message = $message ?: t('app/error', ErrorCodes::INTERNAL_ERROR);
        parent::__construct(self::getHttpCode((int)$code), $message, (int)$code, $previous);
    }
}
