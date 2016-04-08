<?php

namespace backend\controllers;

use Yii;
use backend\models\AssociateSeller;
use backend\models\SearchAssociateSeller;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\controllers\AppController;

/**
 * AssociateSellerController implements the CRUD actions for AssociateSeller model.
 */
class AssociateSellerController extends AppController
{
    public $enableCsrfValidation = false;
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
     * Lists all AssociateSeller models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchAssociateSeller();
		if(isset($_REQUEST['SearchAssociateSeller']['id_employee']) && !empty($_REQUEST['SearchAssociateSeller']['id_employee'])){
			$searchModel->id_employee = $_REQUEST['SearchAssociateSeller']['id_employee'];
		}
		if(isset($_REQUEST['SearchAssociateSeller']['email']) && !empty($_REQUEST['SearchAssociateSeller']['email'])){
			$searchModel->email = $_REQUEST['SearchAssociateSeller']['email'];
		}
		if(isset($_REQUEST['SearchAssociateSeller']['firstname']) && !empty($_REQUEST['SearchAssociateSeller']['firstname'])){
			$searchModel->firstname = $_REQUEST['SearchAssociateSeller']['firstname'];
		}
		if(isset($_REQUEST['SearchAssociateSeller']['lastname']) && !empty($_REQUEST['SearchAssociateSeller']['lastname'])){
			$searchModel->lastname = $_REQUEST['SearchAssociateSeller']['lastname'];
		}
		if(isset($_REQUEST['SearchAssociateSeller']['company']) && !empty($_REQUEST['SearchAssociateSeller']['company'])){
			$searchModel->company = $_REQUEST['SearchAssociateSeller']['company'];
		}
		if(isset($_REQUEST['SearchAssociateSeller']['city']) && !empty($_REQUEST['SearchAssociateSeller']['city'])){
			$searchModel->city = $_REQUEST['SearchAssociateSeller']['city'];
		}
                if(isset($_REQUEST['SearchAssociateSeller']['active'])){
                $searchModel->active = $_REQUEST['SearchAssociateSeller']['active'];
                }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AssociateSeller model.
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
     * Creates a new AssociateSeller model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AssociateSeller();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_employee]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AssociateSeller model.
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
     * Deletes an existing AssociateSeller model.
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
     * Finds the AssociateSeller model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AssociateSeller the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AssociateSeller::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionAssignment()
    {
        $response = array();
        $result = true;
        $getParam = $_REQUEST;
        
        if($getParam['id']=='')
        {
           $response['description']['desc']='User id should not be blank for mapping'; 
           $result = false;
        }

        if($getParam['sellerId']=='')
        {
            $response['description']['desc']='Seller id should not be blank for mapping';
            $result = false;
        }
        if(count($getParam)>0 && $result)
        {
            AssociateSeller::addRemoveAssociateSeller($getParam['id'],$getParam['sellerId']);
            $response['result'] =  $result;
        }
        else
        {
           $result = false;
           $response['result'] =  $result;
        }
        return json_encode($response);
    }
}
