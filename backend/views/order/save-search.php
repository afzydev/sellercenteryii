<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

?>
<div class="container">
<div class="row">
	<div class="col-md-6">
 		<div class="" id="messageSearchPopupBox" style="display:none;"></div>
    </div>
</div>
<div class="row">
<?php $form = ActiveForm::begin(['id'=>'saveSearchForm']); ?>
	<div class="col-md-2" id="showStatus">
		<div class="form-group">
			<input type="text" name="name" class="form-control" >
		</div>
    </div>

    <div class="col-md-2">
		<div class="form-group">
		<input type="hidden" name="query_string" value="<?php echo $_SERVER['QUERY_STRING'];?>">
			<button type="button" class="btn btn-primary" onclick="saveSearch()">Save</button>
		</div>
    </div>

<?php ActiveForm::end(); ?>
</div>
</div>