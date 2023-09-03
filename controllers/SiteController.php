<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\HttpException;

class SiteController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return [
            'message' => 'Welcome!'
        ];
    }

    /**
     * @return string
     */
    public function actionHealthCheck()
    {
        return 'OK';
    }

    /**
     * @return array
     */
    public function actionError(): array
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            $errors = [
                'exception' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'previous' => $exception->getPrevious(),
                'trace' => explode(PHP_EOL, $exception->getTraceAsString()),
            ];
            Yii::error($errors + ['request_id' => Yii::$app->requestId->id], 'response_data_error');

            if (
                YII_DEBUG
                && !($exception instanceof HttpException)
            ) {
                return $errors;
            }

            return [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ];
        }
        return [];
    }
}
