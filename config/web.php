<?php

use app\core\models\User;

$common = require(__DIR__ . '/common.php');
$params = require __DIR__ . '/params.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'v1' => [
            'class' => app\modules\v1\Module::class,
        ],
    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => yii\web\JsonParser::class,
            ],
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => env('COOKIE_VALIDATION_KEY')
        ],
        'response' => [
            'class' => yii\web\Response::class,
            'on beforeSend' => function ($event) {
                yii::createObject([
                    'class' => yiier\helpers\ResponseHandler::class,
                    'event' => $event,
                ])->formatResponse();
            },
        ],
        'jwt' => [
            'class' => sizeg\jwt\Jwt::class,
            'signer' => \sizeg\jwt\JwtSigner::HS256,
            'signerKey' => \sizeg\jwt\JwtKey::PLAIN_TEXT,
            'signerKeyContents' => env('JWT_SECRET'),
            'signerKeyPassphrase' => env('JWT_KEY'),
            'constraints' => [
                function () {
                    // Verifies the claims iat, nbf, and exp, when present (supports leeway configuration)
                    return new \Lcobucci\JWT\Validation\Constraint\LooseValidAt(
                        \Lcobucci\Clock\SystemClock::fromSystemTimezone()
                    );
                },
                function () {
                    // Verifies if the token was signed with the expected signer and key
                    return new \Lcobucci\JWT\Validation\Constraint\SignedWith(
                        Yii::$app->jwt->getSigner(),
                        Yii::$app->jwt->getSignerKey()
                    );
                },
            ],
            ],
        'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CNY',
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::class,
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'POST create_user' => 'v1/user/create',
                'GET balance_read' => 'v1/user/balance-read',
                'POST balance_topup' => 'v1/user/balance-topup',
                'POST transfer' => 'v1/user/transfer',
                'GET top_users' => 'v1/user/top',
                'GET top_transactions_per_user' => 'v1/user/top-transactions-per-user',

                "GET health-check" => 'site/health-check',
                '<module>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
            ],
            'cache' => 'cacheFS',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return \yii\helpers\ArrayHelper::merge($common, $config);
