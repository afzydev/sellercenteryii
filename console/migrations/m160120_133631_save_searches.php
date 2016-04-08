<?php

use yii\db\Schema;
use yii\db\Migration;

class m160120_133631_save_searches extends Migration
{
    public function up()
    {
         $this->createTable('{{%save_searches}}', [
            'id' => $this->primaryKey(),
            'id_employee' => $this->integer()->notNull(),
            'name' => $this->string(200)->notNull(),
            'query_string' => $this->text()->notNull(),
            'created_at' => $this->datetime()->notNull()->defaultValue(date('Y-m-d H:i:s'))
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable('{{%save_searches}}');
    }

   
}
