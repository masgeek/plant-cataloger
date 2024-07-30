<?php


return [
    'class' => 'yii\db\Connection',
//    'dsn' => 'mysql:host=127.0.0.1;dbname=plant_disease_catalog',
    'dsn' => getenv('DB_DSN'),
    'username' => getenv('DB_USERNAME') ?? 'root',
    'password' => getenv('DB_PASSWORD'),
    'charset' => getenv('DB_CHARSET') ?? 'utf8',

    // Schema cache options (for production environment)
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];