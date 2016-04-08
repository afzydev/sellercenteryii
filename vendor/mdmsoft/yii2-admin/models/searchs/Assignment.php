<?php

namespace mdm\admin\models\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

/**
 * AssignmentSearch represents the model behind the search form about Assignment.
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Assignment extends Model
{ public $criteria,$email,$id_employee,$firstname,$lastname,$active,$profile;
  //  public $id;
 //   public $username;
  //  public $firstname;
  //  public $lastname;
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
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rbac-admin', 'ID'),
            'username' => Yii::t('rbac-admin', 'Username'),
            'name' => Yii::t('rbac-admin', 'Name'),
        ];
    }

    /**
     * Create data provider for Assignment model.
     * @param  array                        $params
     * @param  \yii\db\ActiveRecord         $class
     * @param  string                       $usernameField
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params, $class)
    {
    /*    $query = $class::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);*/
        $connection = $class::getDb();
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
		    $query = "SELECT e.id_employee, e.email, e.firstname, e.lastname, p.id_profile, p.name as profile, e.active from ".EMPLOYEE." e INNER JOIN ".PROFILE_LANG." p ON e.id_profile=p.id_profile".$this->criteria;
		    
                    $dataProvider = new SqlDataProvider([
				'db'  => $connection,   
				'sql' => $query,
				'totalCount' => $count,
			//	'pagination' => [
			//		'pageSize' => Yii::$app->params['pagesize'],
			//	],
				'sort' => [
       				    'attributes' => [
                                                'id_employee',
                                                'email',
                                                'firstname',
                                                'lastname',
                                                'active',
                                                'profile',
                                    ],	
				],
		  ]);
        /* if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
       /*
        $query->andFilterWhere(['like', $usernameField, $this->username]);
        $query->andFilterWhere(['like', $firstname, $this->firstname]);
        $query->andFilterWhere(['like', $lastname, $this->lastname]);
         */
        return $dataProvider;
    }
}
