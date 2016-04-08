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
<b>Please select status update</b>
</div>
<div class="modal-body">

<div class="container">

<div class="row">
	<div class="col-md-6" >
		<div class="" id="messagePopupBox" style="display:none;"></div>
	</div>
</div>

<div class="row">
<?php $form = ActiveForm::begin(['id'=>'popupUpdateForm']); ?>
	<div class="col-md-2" id="showStatus">
		<div class="form-group">
		<?php if($getOrderStatuses) {?>
			<select class="form-control" name="id_order_state" onchange="selectReason('popup',this.value)">
				<option value="">Select Status</option>
				<?php foreach($getOrderStatuses as $row) {?>
					<option value="<?php echo $row['id_order_state'] ?>"  ><?php echo $row['name'] ?></option>
				<?php } ?>
			</select>
		<?php } else { ?>
			<select class="form-control" name="id_order_state">
				<option value="">No Data Found.Select different filter status</option>
			</select>
		<?php } ?>
		</div>
    </div>

    <div class="col-md-2" id="showPopupReasonDropdwon" style="display:none;">
		<div class="form-group">
			<select id="reasonPopupDropDown" class="form-control" name="id_order_state_reason">
				
			</select>
		</div>
    </div>

    <div class="col-md-2" id="showPopupUpdateButton" style="display:none;">
		<div class="form-group">
		<input type="hidden" name="orderIds" value="<?php echo $id_order;?>">
		<button type="button" class="btn btn-primary" onclick="updateOrderStatus('popup','singleUpdate')">Update</button>
		</div>
    </div>

<?php ActiveForm::end(); ?>

</div>

</div>
</div>

<div class="modal-footer">

</div>