<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\SqlDataProvider;
use common\models\User;
use common\components\Configuration;


/**
 * UserSearch represents the model behind the search form about `backend\models\User`.
 */
class UserSearch extends User
{
    public $criteria,$email,$id_employee,$firstname,$lastname,$active,$profile;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'active'], 'integer'],
            [['firstname','lastname','email','profile'], 'safe'],
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
            $connection = User::getDb();
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
                $this->criteria .= " and p.profile like '".$this->profile."%'";
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
