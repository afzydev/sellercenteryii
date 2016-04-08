<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use backend\models\Employee;
use common\components\Configuration;

/**
 * SeachEmployee represents the model behind the search form about `backend\models\Employee`.
 */
class SeachEmployee extends Employee
{
    public $criteria,$email,$id_employee,$firstname,$lastname,$active,$profile;
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
            $connection = Employee::getDb();
             $this->criteria = " WHERE 1=1 ";
		    if(!empty($this->email))
		        $this->criteria .= " and e.email like '".$this->email."%'";
                    if(!empty($this->id_employee))
		        $this->criteria .= " and e.id_employee like '".$this->id_employee."'";
                    if(!empty($this->firstname))
		        $this->criteria .= " and e.firstname like '".$this->firstname."%'";
                    if(!empty($this->lastname))
		        $this->criteria .= " and e.lastname like '".$this->lastname."%'";
                    if(!empty($this->profile))
		        $this->criteria .= " and p.name like '".$this->profile."%'";
                    if($this->active!='')
		        $this->criteria .= " and e.active = '".$this->active."'";
       
                    $count = $connection->createCommand("SELECT count(e.id_employee) from ".EMPLOYEE." e INNER JOIN ".PROFILE_LANG." p ON e.id_profile=p.id_profile".$this->criteria)->queryScalar();               
		    $Query = "SELECT e.id_employee, e.email, e.firstname, e.lastname, p.id_profile, p.name as profile, e.active, 'associate' as 'associate' from ".EMPLOYEE." e INNER JOIN ".PROFILE_LANG." p ON e.id_profile=p.id_profile".$this->criteria;
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
                                                'email',
                                                'firstname',
                                                'lastname',
                                                'active',
                                                'profile',
                                                'associate'
                                        ],	
				],
		  ]);
          return $dataProvider;
        }
        catch(Exception $e){
          CustomException::errorLog($e->getMessage(), __FUNCTION__, __FILE__, __LINE__);
        }

    }
}
