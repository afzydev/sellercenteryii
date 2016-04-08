<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use backend\assets\ProductAsset;
/* @var $this yii\web\View */
ProductAsset::register($this);

?>
<div class="container">
<div class="row">
	<div class="col-md-6">
 		<div class="" id="messagestockkPopupBox" style="display:none;"></div>
    </div>
</div>
<div class="row">
<?php $form = ActiveForm::begin(['id'=>'updateStockForm']); ?>
    <div class="col-md-2" id="showStatus">
		<div class="form-group">
                    <input type="text" id="id_stock" name="quantity" class="form-control"value="<?php echo $stock;?>" >
		</div>
    </div>
    <div class="col-md-1" style="width: 30px;">
    <div class="form-group">
        <input type="checkbox" class="btn btn-primary" name="outofstock" value="0" onclick="UpdateOutOfStock()" id="OutOfStock"/>
    </div>    
    </div>
     <div class="col-md-2">
    <div class="form-group">
        <label style="padding-top: 5px;">&nbsp;Make Out Of Stock</label> 
    </div>    
    </div>
    <div class="col-md-2">
        <input type="hidden" name="id_product" value="<?php echo $_GET['id'];?>">
        <div class="form-group">
			<button type="button" class="btn btn-primary" onclick="updateStockProduct()">Update Stock</button>
		</div>
                
    </div>
    
    
<?php ActiveForm::end(); ?>
</div>
</div>
