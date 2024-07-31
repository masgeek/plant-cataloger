<?php
return [
    'traceLevel' => YII_DEBUG ? 3 : 0,
    'targets' => [
        [
            'class' => 'yii\log\FileTarget',
            'levels' => ['info', 'error', 'warning'],
//            'except' => ['yii\db\*', 'yii\httpclients\*'],
            'logVars' => [],
        ],
        [
            'class' => 'codemix\streamlog\Target',
            'url' => 'php://stdout',
            'levels' => ['info', 'error', 'warning'],
            'except' => ['yii\db\*', 'yii\httpclients\*'],
            'logVars' => [],
        ],
    ],
];