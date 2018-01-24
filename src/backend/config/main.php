<?php

$params  = require(__DIR__ . '/params.php');
$db      = require(__DIR__ . '/db.php');
$chacad  = require(__DIR__ . '/chacad.php');
$iceberg = require(__DIR__ . '/iceberg.php');
$sinu    = require(__DIR__ . '/sinu.php');
$modules = require(__DIR__ . '/modules.php');
$aliases = require(__DIR__ . '/aliases.php');

$config = [
    'id'         => 'padlock',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'modules'    => $modules,
    'aliases'    => $aliases,
    'components' => [
        'view'         => [
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@vendor/insite/yii2-theme-espire'
                ],
            ],
        ],
        'assetManager' => [
            'linkAssets' => true,
        ],
        'request'      => [
// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey'  => 'nolberto',
            'enableCsrfValidation' => false,
        ],
        'cache'        => [
            'class' => 'yii\caching\FileCache',
        ],
        'user'         => [
            'identityClass'   => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer'       => [
            'class'            => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
// 'useFileTransport' to false and configure a transport
// for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'           => $db,
        'chacad'       => $chacad,
        'iceberg'      => $iceberg,
        'sinu'         => $sinu,
        'google'       => [
            'class'            => 'idk\yii2\google\apiclient\components\GoogleApiClient',
            'credentialsPath'  => '@runtime/google-apiclient/admin_71f528e0-5eb1-4a4d-8a8e-c5938b579ff5.json',
            'clientSecretPath' => '@runtime/google-apiclient/secret.json',
        ],
        'ldap'         => [
            'class'   => 'nolbertovilchez\yii2\Ldap',
            'options' => [
                'server'         => 'UPCH130',
                'domain'         => 'upch.edu.pe',
                'dc'             => 'dc=upch,dc=edu,dc=pe',
                'version'        => 3,
                'port'           => 389,
                'admin_username' => 'testdrllo01',
                'admin_password' => 'Xtestdrllo01',
            ]
        ],
        'urlManager'   => [
            'enablePrettyUrl' => true,
            'showScriptName'  => false,
            'rules'           => [
            ],
        ],
    ],
    'params'     => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][]      = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
            // uncomment the following to add your IP if you are not connecting from localhost.
            //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][]    = 'gii';
    $config['modules']['gii'] = [
        'class'      => 'yii\gii\Module',
        'generators' => [
            'modules' => 'insite\gii\module\Generator'
        ],
    ];
}

return $config;
