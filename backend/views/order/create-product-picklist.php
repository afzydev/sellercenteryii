<?php
use yii\helpers\Html;
use common\components\Helpers as Helper;
use yii\widgets\ActiveForm;
?>


<div class="row">
	<div class="col-md-12">
  		<div class="" id="createProductPicklistMessagePopupBox" style="display:none;"></div>    
 	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<?php $form = ActiveForm::begin(['action' => 'index.php?r=order/generate-product-picklist&type=export','id'=>'createProductPicklistForm']); ?>
		<div class="col-md-4">
			<div class="form-group">
			<?php
			if(!empty($sellers) && count($sellers)>0){
			?>
				<select class="form-control" name="id_seller_product_picklist" id="id_seller_product_picklist">
					<option value="">Select Seller</option>
					<?php				
	                    foreach ($sellers as $sellerDetail) {
	                        ?>
	                        <option value="<?php echo $sellerDetail['id_seller']; ?>" <?php if(isset($getValues['sellerFilter']) && in_array($sellerDetail['id_seller'], $getValues['sellerFilter'])){echo 'selected';} ?>><?php echo $sellerDetail['company']; ?></option>
	                       <?php
	                            }
	                        ?>
				</select>
			<?php
		} else {
			?>
				<input type="hidden" name="id_seller_product_picklist" id="id_seller_product_picklist" value="<?php if(Helper::getSessionId()) { echo Helper::getSessionId(); } ?>"></input>
		<?php } ?>
			</div>
	    </div>	
		<div class="col-md-6">
			<div class="form-group">
				<button type="button" class="btn btn-primary btn-lg" onclick="createProductPicklist()">Generate</button>
			</div>
	    </div>
		<?php ActiveForm::end(); ?>
	</div>
</div>

