 $(".select-on-check-all").click(function () {
     $('input:checkbox').not(this).prop('checked', this.checked);
 });
function changeStatus(){
	var checkedOrderIds = $('#w4-container input:checkbox:checked').map(function() {
		return this.value;
	}).get();
	checkedOrderIds.shift();
	if(($("#w4-container input:checkbox:checked").length==0))
	{
		alert('Please select orders to process!');
		return false;
	}
    var formData="orderIds="+checkedOrderIds;
    //var formData=$('#w0').serialize();
    loader.position='middle';
    loader.init();
    $.ajax({
            url:base_url+'index.php?r=orders/filterstatus',
            type:'POST',
            data:formData,
            success:function(result){
                loader.close();
                
                if(result.success)
                {
                     $('#showUpdateButton').show();
                     $('#showStatus').show();
                }
                else
                {

                    $('#messageBox').show();
                    $('#messageBox').removeClass('alert alert-success');
                    $('#messageBox').addClass('alert alert-danger');
                    $('#messageBox').html(result.message);
                }
            }
    });
	
}

function filterStatus(value)
{
	window.location.href=base_url+"index.php?r=orders/index&status="+value;
}

function selectReason(val)
{
	var value="id_order_state="+val;
 	loader.position='middle';
 	loader.init();
	$.ajax({
            url:base_url+'index.php?r=orders/getorderreason',
            type:'POST',
            data:value,
            success:function(result){
            	loader.close();
                $('#showUpdateButton').show();
                if(result.success)
                {
                	$('#showReasonDropdwon').show();
                	$('#reasonDropDown').empty();
					$("#reasonDropDown").append("<option value>Select Reason</option>");

					$.each(result.getAllStateReason, function (key, value) {
					$("#reasonDropDown").append($("<option></option>").val(value.id_order_state_reason).html(value.reason));
					});
                }
                else
                {
                	$('#showReasonDropdwon').hide();
                	$('#reasonDropDown').empty();
                }
            }
	});
}

function updateOrderStatus(){
 	var data=$('#w0').serialize();
    var checkedOrderIds = $('#w4-container input:checkbox:checked').map(function() {
        return this.value;
    }).get();
    checkedOrderIds.shift();
    formData=data+'&orderIds='+checkedOrderIds;
 	loader.position='middle';
 	loader.init();
 	$.ajax({
            url:base_url+'index.php?r=orders/changestatus',
            type:'POST',
            data:formData,
            success:function(result){
            	loader.close();
                if(result.error)
                {
                	$('#messageBox').show();
                	$('#messageBox').removeClass('alert alert-success');
                	$('#messageBox').addClass('alert alert-danger');
                	$('#messageBox').html(result.message);
                }
                else
                {
                	$('#messageBox').show();
                	$('#messageBox').removeClass('alert alert-danger');
                	$('#messageBox').addClass('alert alert-success');
                	$('#messageBox').html(result.message);
                }
            }
	    });
 }
 function changeOrderStatus(){
    $('#showStatus').show();
}