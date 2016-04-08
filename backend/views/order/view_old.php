<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\AssociateSeller */

$this->title = $model->id_employee;
$this->params['breadcrumbs'][] = ['label' => 'Associate Sellers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-view">


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
            'attribute' =>  'id_order',
            'label'     =>  'Order Id',
            ],
            [
            'attribute' => 'invoice_ref_key',
            'label' => 'Order Number',
            ],
            [
            'attribute' => 'reference',
            'label' => 'Sub-Order Number',
            ],
            [
            'attribute' => 'product_name',
            'label' => 'Product Name',
            ],
            [
            'attribute' => 'customer',
            'label' => 'Customer Name',
            ],
            [
            'attribute' => 'cust_email',
            'label' => 'Email',
            ],
            [
            'attribute' => 'response_waywill',
            'label' => 'Waybill Number',
            ],
            [
                'attribute' =>  'unit_price_tax_incl',
                'label' => 'Product Price',
            ],
            [
                'attribute' =>  'cod_charge',
                'label'     =>  'COD',
            ],
            [
                'attribute' =>  'total_shipping',
                'label'     =>  'Shipment',
            ],
            [
                'attribute' =>  'total_paid_tax_incl',
                'label'     =>  'Total Price',
            ],
            [
                'attribute' =>  'payment',
                'label'     =>  'Mode of Payment',
            ],
            [
                'attribute' => 'osname',
                'label' => 'Order Status',
            ],
            [
                'attribute' =>  'confirmed_by',
                'label'     =>  'Confirmed By',
            ],
            [
                'attribute' =>  'delivery_days',
                'label'     =>  'Delivery Days',
            ],
            [
                'attribute' =>  'shipped_date_add',
                'label'     =>  'Shipped Date',
            ],
            [
                'attribute' =>  'delivered_date_add',
                'label'     =>  'Delivery Date',
            ],          
            'invoice_number',
            [
            'attribute' => 'total_paid_tax_incl',
            'label' => 'Total Paid',
            ],
            'mobile_number',
            [
                'attribute' =>  'address1',
                'label'     =>  'Customer Address 1',
            ],
            [
                'attribute' =>  'address2',
                'label'     =>  'Customer Address 2',
            ],
            [
                'attribute' =>  'state_name',
                'label'     =>  'State',
            ],
            [
                'attribute' =>  'city',
                'label'     =>  'City',
            ],
            [
                'attribute' =>  'postcode',
                'label'     =>  'Postcode',
            ],                                                                  
            [
            'attribute' => 'product_quantity',
            'label' => 'Quantity',
            ],
           'date_add',
        ],
    ]) ?>

</div>
