<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use common\models\SignupForm;
use backend\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\controllers\AppController;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends AppController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        if(isset($_GET['UserSearch']['email']) && !empty($_GET['UserSearch']['email'])){
                $searchModel->email = $_GET['UserSearch']['email'];
        }
        if(isset($_GET['UserSearch']['id_employee']) && !empty($_GET['UserSearch']['id_employee'])){
                $searchModel->id_employee = $_GET['UserSearch']['id_employee'];
        }
        if(isset($_GET['UserSearch']['firstname']) && !empty($_GET['UserSearch']['firstname'])){
                $searchModel->firstname = $_GET['UserSearch']['firstname'];
        }
        if(isset($_GET['UserSearch']['lastname']) && !empty($_GET['UserSearch']['lastname'])){
                $searchModel->lastname = $_GET['UserSearch']['lastname'];
        }
        if(isset($_GET['UserSearch']['profile']) && !empty($_GET['UserSearch']['profile'])){
                $searchModel->profile = $_GET['UserSearch']['profile'];
        }
        if(isset($_GET['UserSearch']['active'])){
                $searchModel->active = $_GET['UserSearch']['active'];
        }
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User(['scenario' => 'create']);
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->createUser()) {
               return $this->redirect(['view', 'id' => $model->id]);
            }            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('update'); 
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->createUser()) {
               return $this->redirect(['view', 'id' => $model->id]);
            } 
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
