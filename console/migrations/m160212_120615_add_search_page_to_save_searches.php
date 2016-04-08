<?php

use yii\db\Schema;
use yii\db\Migration;

class m160212_120615_add_search_page_to_save_searches extends Migration
{
    public function up()
    {
        $this->addColumn('save_searches', 'search_page', $this->string(200)->notNull());
    }


    public function down()
    {
        echo "m160212_120615_add_search_page_to_save_searches cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
