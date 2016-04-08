<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

?>
<?php $form = ActiveForm::begin(['id'=>'updatePriceForm']); ?>
<div class="container">
<div class="row">
	<div class="col-md-6">
 		<div class="" id="messagePricePopupBox" style="display:none;"></div>
    </div>
</div>
<div class="row">

	<div class="col-md-2">
        <div class="form-group">
        <label>M.R.P.</label> 
        </div>  
		<div class="form-group">
        <input type="text"  name="mrp_price" id="id_price" class="form-control" value="<?php echo $price; ?>" >
		</div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
        <label> Current Selling Price</label> 
        </div>  
    		<div class="form-group">
            <input type="text" readonly name="current_selling_price" id="current_selling_price" class="form-control" value="<?php echo $sell_price; ?>" >
    		</div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
        <label>New Selling Price</label> 
        </div>  
        <div class="form-group">
            <input type="text"  name="selling_price" id="id_selling_price" class="form-control" value="<?php echo $sell_price; ?>" >
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-6">
      <input type="hidden" name="id_product" value="<?php echo $_GET['id'];?>">
      <input type="hidden" name="id_employee" value="<?php echo $id_employee;?>">
      <div class="form-group" style="text-align: center;">
      <button type="button" class="btn btn-primary" onclick="updatePrice()">Update Price</button>
      </div>
    </div>
</div>
</div>
<?php ActiveForm::end(); ?>
