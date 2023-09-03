<?php

return [
    'timeZone' => env('APP_TIME_ZONE'),
    'language' => env('APP_LANGUAGE'),
    'name' => env('APP_NAME'),
    'bootstrap' => ['log', 'ideHelper', app\core\EventBootstrap::class],
    'components' => [
        'ideHelper' => [
            'class' => Mis\IdeHelper\IdeHelper::class,
            'configFiles' => [
                'config/web.php',
                'config/common.php',
                'config/console.php',
            ],
        ],
        'requestId' => [
            'class' => yiier\helpers\RequestId::class,
        ],
        'cacheFS' => [
            'class' => yii\caching\FileCache::class,
        ],
        'cache' => [
            'class' => yii\redis\Cache::class,
        ],
        'redis' => [
            'class' => yii\redis\Connection::class,
            'hostname' => env('REDIS_DSN'),
            'port' => env('REDIS_PORT'),
            'database' => env('REDIS_DB'),
        ],
        'db' => [
            'class' => yii\db\Connection::class,
            'dsn' => env('DB_DSN'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'tablePrefix' => env('DB_TABLE_PREFIX'),
            'charset' => 'utf8mb4',
            'enableSchemaCache' => YII_ENV_PROD,
            'schemaCacheDuration' => 604800, // 1 week
            'schemaCache' => 'cacheFS',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@app/core/messages',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'exception.php',
                    ],
                ],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yiier\helpers\FileTarget::class,
                    'levels' => ['error'],
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
                    'logFile' => '@app/runtime/logs/error/app.log',
                    'enableDatePrefix' => true,
                ],
                [
                    'class' => yiier\helpers\FileTarget::class,
                    'levels' => ['warning'],
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
                    'logFile' => '@app/runtime/logs/warning/app.log',
                    'enableDatePrefix' => true,
                ],
                [
                    'class' => yiier\helpers\FileTarget::class,
                    'levels' => ['info'],
                    'categories' => ['request'],
                    'logVars' => [],
                    'maxFileSize' => 1024,
                    'logFile' => '@app/runtime/logs/request/app.log',
                    'enableDatePrefix' => true
                ],
                [
                    'class' => yiier\helpers\FileTarget::class,
                    'levels' => ['warning'],
                    'categories' => ['debug'],
                    'logVars' => [],
                    'maxFileSize' => 1024,
                    'logFile' => '@app/runtime/logs/debug/app.log',
                    'enableDatePrefix' => true
                ],
            ],
        ],
    ],
];
