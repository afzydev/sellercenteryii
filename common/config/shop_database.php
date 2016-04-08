<?php 
  return [
            'class' => 'yii\db\Connection',
            'dsn' => "mysql:host=".$DSN[0]['w']['host'].";port=".$DSN[0]['w']['port'].";dbname=".$DSN[0]['w']['db']."",
            'username' => "".$DSN[0]['w']['user']."",
            'password' => "".$DSN[0]['w']['password']."",
            'charset' => 'utf8',
        ];
   