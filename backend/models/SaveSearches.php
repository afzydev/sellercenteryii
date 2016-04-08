<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use common\components\Helpers as Helper;

/**
* 
*/
class SaveSearches extends \common\models\MyActiveRecord
{
	public $query_string;
    public $name;
    public $id_employee;
    public $page;
    public static function tableName() {
        return '{{%save_searches}}';
    }

    public function saveData(){
        try{
            $connection = $this->getDb();

            if(static::find()->where([ 'name' => $this->name,'id_employee'=>Helper::getSessionId() ])->orWhere(['query_string'=>$this->query_string,'id_employee'=>Helper::getSessionId()])->exists())
            {
                return false;
            }

    		return $connection->createCommand()
        	->insert(SAVE_SEARCHES, [
                'id_employee'   => Helper::getSessionId(),
                'name'          => $this->name,  
                'query_string'  => $this->query_string,
                'search_page'  => $this->page,
            ])
        	->execute();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    public function getSearchData($page){
        try{
            $connection = $this->getDb();
            $query = "SELECT * FROM " . SAVE_SEARCHES . " where id_employee = " . Helper::getSessionId()." AND search_page= '".$page."'";
            return $connection->createCommand($query)->queryAll();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

    public function deleteSearchData($idDelete){

        try{
            $connection = $this->getDb();
            $query = "DELETE  FROM " . SAVE_SEARCHES . " where id=".$idDelete." ";
            return $connection->createCommand($query)->execute();
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }

}