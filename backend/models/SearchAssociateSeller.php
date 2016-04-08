<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use backend\models\AssociateSeller;
use common\components\Configuration;

/**
 * SearchAssociateSeller represents the model behind the search form about `backend\models\AssociateSeller`.
 */
class SearchAssociateSeller extends AssociateSeller
{
	public $arr_id_seller, $employee_details = array();
	public $criteria, $id_employee, $company, $city;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_employee', 'id_profile', 'id_lang', 'stats_compare_option', 'default_tab', 'bo_width', 'bo_menu', 'active', 'optin', 'id_last_order', 'id_last_customer_message', 'id_last_customer'], 'integer'],
            [['lastname', 'firstname', 'email', 'passwd', 'last_passwd_gen', 'stats_date_from', 'stats_date_to', 'stats_compare_from', 'stats_compare_to', 'preselect_date_range', 'bo_color', 'bo_theme', 'bo_css'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
      try{
    		 $this->setIdSeller();
         $where='1=1';
    		 $this->setEmpDetail();
         $array = $this->arr_id_seller;
          $stringSeller ="("; 
         if(!empty($array)){        
         foreach ($array as $value) {
          $stringSeller .= $value.',';
         }
        $stringSeller = rtrim($stringSeller, ',');
        
      }else{
        $stringSeller .='-1';
      }
      $stringSeller .=")";
            $connection = AssociateSeller::getDb();
    		$this->criteria = " WHERE e.id_profile=".Yii::$app->params['ps_seller_profile_id']." ";
    	    if(!empty($this->id_employee))
    	      $this->criteria .= " and e.id_employee like '".$this->id_employee."%'";
    	   if(!empty($this->email))
    	      $this->criteria .= " and e.email like '".$this->email."%'";
    	   if(!empty($this->firstname))
    	      $this->criteria .= " and e.firstname like '".$this->firstname."%'";
    	   if(!empty($this->lastname))
    	      $this->criteria .= " and e.lastname like '".$this->lastname."%'";
    	   if(!empty($this->company))
    	      $this->criteria .= " and info.company like '".$this->company."%'";
    	   if(!empty($this->city))
    	     $this->criteria .= " and info.city like '".$this->city."%'";
               if($this->active!='')
    	     $this->criteria .= " and e.active = '".$this->active."'";
            $count = $connection->createCommand("SELECT count(e.id_employee) from ".EMPLOYEE." e INNER JOIN ".PROFILE_LANG." p ON e.id_profile=p.id_profile LEFT JOIN ".SELLER_INFO." info ON info.id_seller=e.id_employee ".$this->criteria)->queryScalar();
           
           $Query = "SELECT info.company ,e.active,  info.city,e.id_employee, If(e.id_employee in ".$stringSeller.",1,0) as assign, e.email, e.firstname, e.lastname, p.id_profile, p.name as profile, e.active, 'assign' as 'assign' 
           from ".EMPLOYEE." e 
           INNER JOIN ".PROFILE_LANG." p ON e.id_profile=p.id_profile 
           LEFT JOIN ".SELLER_INFO." info ON info.id_seller=e.id_employee ".$this->criteria;

             $dataProvider = new SqlDataProvider([
                      'db'  => $connection,   
                      'sql' => $Query,
                      'totalCount' => $count,
                      'pagination' => [
                              'pageSize' => Configuration::get('PAGE_SIZE'),
                      ],
                      'sort' => [
                              'attributes' => [
                                      'id_employee',
    								  'company',
    								  'firstname',
    								  'lastname',
    								  'email',
    								  'city',
                      'assign'
                              ],
                      ],
            ]);
          return $dataProvider;
      }
      catch(Exception $e){
        CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
      }
    }
	
	//set seller id
	public function setIdSeller(){
    try{
		 $rows = Yii::$app->get('db')->createCommand('SELECT id_seller from '.USER_SELLER_MAPPING." WHERE id_user=".Yii::$app->getRequest()->getQueryParam('employee_id'))->queryAll();
		 foreach($rows as $row){
			 $this->arr_id_seller[] = $row['id_seller'];
		 }
    }
    catch(Exception $e){
      CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
    }
	}
	
	//set employee detail
	public function setEmpDetail(){
    try{
  		 $dread = Yii::$app->get('dbshop')->createCommand('SELECT *from '.EMPLOYEE." WHERE id_employee=".Yii::$app->getRequest()->getQueryParam('employee_id'))->query();
  		 $this->employee_details = current($dread->readAll());
	   }
    catch(Exception $e){
      CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
    }
  }
	
}
