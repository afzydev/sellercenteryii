<?php

namespace backend\controllers;

use Yii;
use backend\models\Employee;
use backend\models\SeachEmployee;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\controllers\AppController;

/**
 * EmployeeController implements the CRUD actions for Employee model.
 */
class EmployeeController extends AppController
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
     * Lists all Employee models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SeachEmployee();
        if(isset($_GET['SeachEmployee']['email']) && !empty($_GET['SeachEmployee']['email'])){
			$searchModel->email = $_GET['SeachEmployee']['email'];
        }
        if(isset($_GET['SeachEmployee']['id_employee']) && !empty($_GET['SeachEmployee']['id_employee'])){
                $searchModel->id_employee = $_GET['SeachEmployee']['id_employee'];
        }
        if(isset($_GET['SeachEmployee']['firstname']) && !empty($_GET['SeachEmployee']['firstname'])){
                $searchModel->firstname = $_GET['SeachEmployee']['firstname'];
        }
        if(isset($_GET['SeachEmployee']['lastname']) && !empty($_GET['SeachEmployee']['lastname'])){
                $searchModel->lastname = $_GET['SeachEmployee']['lastname'];
        }
        if(isset($_GET['SeachEmployee']['profile']) && !empty($_GET['SeachEmployee']['profile'])){
                $searchModel->profile = $_GET['SeachEmployee']['profile'];
        }
        if(isset($_GET['SeachEmployee']['active'])){
                $searchModel->active = $_GET['SeachEmployee']['active'];
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Employee model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Employee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Employee();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_employee]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Employee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_employee]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Employee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Employee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Employee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Employee::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
