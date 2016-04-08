<?php
return [
            'class' => 'yii\db\Connection',
            'dsn' => "mysql:host=".$DSN[1]['w']['host'].";port=".$DSN[1]['w']['port'].";dbname=".$DSN[1]['w']['db']."",
            'username' => "".$DSN[1]['w']['user']."",
            'password' => "".$DSN[1]['w']['password']."",
            'charset' => 'utf8',
        ];
