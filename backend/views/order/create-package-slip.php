<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

?>
<script type="text/javascript">
	function closebtn(){
		$('#modal').addClass('fade modal out');
		$('.modal-backdrop fade in').css('z-index',-1);
		$('#modal').hide();

	}
</script>
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true" 0="">×</button>
<b>Download Package Slip</b>
</div>
<div class="modal-body">

<div class="row">
	<div class="col-md-12" >
		<div class="" id="packageSlipMessagePopupBox" style="display:none;"></div>
	</div>
</div>

<div class="row">
<?php $form = ActiveForm::begin(['action' => 'index.php?r=order/invoices&type=popup_download','id'=>'packageSlipForm']); ?>
	<div class="col-md-12">
		<b>Number of Package Slip downloadable : </b><?php if(!empty($packageslipcreatable)) echo $packageslipcreatable;else echo 0; ?><br>
	</div>
	<div class="col-md-12" style="text-align: center;">
		<div class="form-group">
		<?php
		if(!empty($packageslipcreatable)){
		?>
			<input type="hidden" name="invoiceMultipleOrderIds" id="invoiceMultipleOrderIds" value="<?php if(!empty($idOrders)) echo $idOrders;?>" />
			<a href="javascript:void(0)" onclick="downloadPackageSlip(null)" class = "btn btn-primary btn-md">Download Packing Slip</a>
		<?php 
		} ?>
		</div>
    </div>
<?php ActiveForm::end(); ?>

</div>
<div class="row">
	<div class="col-md-12">
		<div class="form-group">
	    <b>
		<p>Note : <br>
		</b>
		Package Slip will not download for those orders whose Waywill Number or Package Slip Number is not generated<br>
		Download limit is <?php echo !empty($invoiceLimit)?$invoiceLimit:0; ?>
		</p>
		
		</div>
	</div>
</div>


</div>

<div class="modal-footer">

</div>