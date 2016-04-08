<?php

use yii\db\Schema;
use yii\db\Migration;

class m160118_090552_insert_into_site_config extends Migration
{
    public function up()
    {
         $this->batchInsert('site_config', ['id_shop_group','id_shop', 'name', 'value'],[
		        [NULL, 1, 'PDF_TEMPLATE', '_carDekhoShopInvoiceView'],
			    [NULL, 2, 'PDF_TEMPLATE', '_cardekhoInvoiceView'],
				[NULL, 3, 'PDF_TEMPLATE', '_gaadiShopInvoiceView'],
				[NULL, NULL, 'PRODUCT_IMAGE_PATH', 'https://shopimg1.cardekho.com/'],
				[NULL, NULL, 'PAGE_SIZE', 50],
		   ]);
    }

    public function down()
    {
		
		$this->delete(
			'site_config',"name = 'SHOP_DATABASE'"
			);
		$this->delete(
			'site_config',"name = 'SHOP_OMS_DATABASE'"
			);
        $this->delete(
			'site_config',"name = 'PDF_TEMPLATE'"
			);
	    $this->delete(
			'site_config',"name = 'PRODUCT_IMAGE_PATH'"
			);
    }
}
