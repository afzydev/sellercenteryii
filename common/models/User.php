<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\components\Helpers;


/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $active
 * @property integer $created_at
 * @property integer $updated_at
 * 
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    
    public $passwd;
    public $auth_key;
    /**
     * @inheritdoc
     */
    public static function getDb() {
        return Yii::$app->dbshop;
    }

    public static function tableName()
    {
        return EMPLOYEE;
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => function($event) {
                    $format = "Y-m-d h:i:s"; // any format you wish
                    return date($format); 
                }
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['active', 'required'],
            ['active', 'default', 'value' => self::STATUS_ACTIVE],
            ['active', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'message' => 'This username has already been taken.'],
            ['username', 'match', 'pattern' => '/^(?![0-9]*$)[a-zA-Z0-9_\.\-]+$/', 'message' => 'Your username can only contain alphanumeric characters, underscores, dot and dashes.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            [['firstname','lastname'], 'required'],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'message' => 'This email address has already been taken.'],

            ['passwd', 'required','on' => 'create'],
            ['passwd', 'string', 'min' => 6]
        ];
    }
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['firstname','lastname', 'email','passwd','active'],
            'update' => ['firstname','lastname', 'email','passwd', 'active'],
            'default' => ['username', 'email', 'active'],
        ];
    }
    
     /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'active' => 'Status',
        ];
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id_employee' => $id, 'active' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    // public static function findByUsername($username)
    // {
    //     return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    // }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'active' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates passwd
     *
     * @param string $passwd passwd to validate
     * @return boolean if passwd provided is valid for current user
     */
    public function validatePassword($passwd)
    {
        $encryptedPass = Yii::$app->helpers->encrypt($passwd);
        if(static::find()->where(['passwd' => $encryptedPass, 'active' =>self::STATUS_ACTIVE])->exists())
            return true;
    }

    /**
     * Generates passwd hash from passwd and sets it to the model
     *
     * @param string $passwd
     */
    public function setPassword($password)
    {
        return $this->passwd = Yii::$app->helpers->encrypt($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new passwd reset token
     */
    public function generatePasswordResetToken()
    {
        $this->passwd_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes passwd reset token
     */
    public function removePasswordResetToken()
    {
        $this->passwd_reset_token = null;
    }
    
    /**
     * create user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function createUser()
    {
        if ($this->validate()) {
            if(!empty($this->passwd)){
                
                $this->setpasswd($this->passwd);
                $this->generateAuthKey();
            } 
            if ($this->save()) {
                 return \Yii::$app->mailer->compose(['html' => 'createuser-html.php', 'text' => 'passwordResetToken-text'], ['user' => $this])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setTo($this->email)
                    ->setSubject('Welcome ' . \Yii::$app->name)
                    ->send();
                return $this;
            }
        }

        return null;
    }
    
        /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email) {
        return static::findOne(['email' =>$email,'active'=>self::STATUS_ACTIVE]);
    }
    
    
    /**
     * Checks if the logged in user is super admin
     *
     * @access public
     * @return boolean
     */
    public static function isRoot() {
        return Yii::$app->user->id == 1;
    }

    public function beforeDelete() {
        if($this->id == 1){
            return false;
        }    

        return parent::beforeDelete();
    }
    
    public function beforeSave($insert) {
        if($this->id == 1){
            $this->active = self::STATUS_ACTIVE;
        }
       
        return parent::beforeSave($insert);
    }
    
    /**
     * This method call in beforeAction it contain functionlity to check before action
     * @return boolean whether the action should be executed.
     */
    public function getUserLocation() {
        // functionlity to check before every action
        $location = "";
        $id = Yii::$app->user->id;
        $user = User::findOne($id);
        if (empty($location)) {
            throw new HttpException(405, "You didn't assigned with Location! Contact with Administrator.");
            return false;
        }
        return $location;
    }
    
    /**
     *  This function is used to get all the assigned roles of logged-in user.
     */
    public function getUserRolesAndPermission($user_id = ''){
        $id = (empty($user_id) ? Yii::$app->user->id : $user_id);
        $roles_permission = \Yii::$app->authManager->getRolesByUser($id);
        return $roles_permission;
    }
}
