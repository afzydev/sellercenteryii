<?php

use yii\db\Schema;
use yii\db\Migration;

class m160122_053040_visits extends Migration
{
    public function up()
    {
         $this->createTable('{{%visits}}', [
		    'id'=>$this->primaryKey(),
            'request_date' => $this->datetime()->notNull(),
            'remote_ip' => $this->string(30)->notNull(),
			'server_ip' => $this->string(30)->notNull(),
            'request_url' => $this->string(255)->notNull(),
            'seller_id' => $this->integer(),
            'browser' => $this->string(100),
			'session_id'=>$this->string(100),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable('{{%visits}}');
    }

}
