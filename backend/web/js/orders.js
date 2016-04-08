 $(".select-on-check-all").click(function () {
     $('#gridViewId input:checkbox:checked').not(this).prop('checked', this.checked);
     setTimeout(highlightRows, 1);
 });

$("input:checkbox").change(function() {
    var someObj = {};
    checkedOrderIds = [];
    $("input:checkbox").each(function() {
        if ($(this).is("#gridViewId input:checkbox:checked")) {
            checkedOrderIds.push($(this).val());
        } 
    });
    if($.inArray('1',checkedOrderIds)==0)
       checkedOrderIds.shift();

    $('#multipleOrderIds').val(checkedOrderIds);
    $('#invoiceMultipleOrderIds').val(checkedOrderIds);
    $('#manifestMultipleOrderIds').val(checkedOrderIds);
    $('#picklistMultipleOrderIds').val(checkedOrderIds);


    $('#showStatus').hide();
    $('#showUpdateButton').hide();
    $('#showReasonDropdwon').hide();
    $('#reasonDropDown').empty();
    $('#statusDropdown').empty();

});


function changeStatus(type){

    if(type=='index')
    {
    	var checkedOrderIds = $('#gridViewId input:checkbox:checked').map(function() {
    		return this.value;
    	}).get();
    	if(($("#gridViewId input:checkbox:checked").length==0))
    	{
    		alert('Please select orders to process!');
    		return false;
    	}

        if($.inArray('1',checkedOrderIds)==0)
           checkedOrderIds.shift();

        if(checkedOrderIds.length==0)
        {
            alert('There are no orders to process!');
            return false;
        }
        var formData="orderIds="+checkedOrderIds;
    }
    else
    {
        var formData="orderIds="+$('#orderIds').val();;

    }
    $('#messageBox').empty();
    $('#messageBox').removeClass('alert alert-success');
    $('#messageBox').removeClass('alert alert-danger');    
    loader.position='middle';
    loader.init();

    ajaxloader.loadURL(base_url+"index.php?r=order/filterstatus",formData,function (result){
           if(typeof result != 'undefined'){

                if(result.success)
                {
                    var arr = Object.keys(result.responseData).map(function(k) { return result.responseData[k] });
                    
                    if(arr.length==1)
                    {
                        var droptext='No Possible Status to update';
                    }
                    else
                    {
                        var droptext='Select Status';
                    }
                    
                    $('#showUpdateButton').show();
                    $('#showStatus').show();
                    $('#statusDropdown').empty();
                    $("#statusDropdown").append("<option value>"+droptext+"</option>");
                    
                    $.each(result.responseData, function (key, value) {
                        if(key!='current_state')
                        {
                            $("#statusDropdown").append($("<option></option>").val(value.id_order_state).html(value.name));
                        }
                    });
                    var stateId=result.responseData['current_state'];
                    $('#statusDropdown option[value='+stateId+']').prop('disabled',true);
                }
                else
                {
                    $('#showReasonDropdwon').hide();
                    $('#reasonDropDown').empty();
                    $('#showStatus').hide();
                    $('#showUpdateButton').hide();
                    $('#statusDropdown').empty();
                    $('#messageBox').show();
                    $('#messageBox').removeClass('alert alert-success');
                    $('#messageBox').addClass('alert alert-danger');
                    $('#messageBox').html(result.message);
                }
           }
    });	
}

function GetURLParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
            break;
        }
    }
    return false;
}

function filterStatus(value)
{
    if(GetURLParameter('sellers'))
    {
        window.location.href=base_url+"index.php?r=order/index&search=true&sellers="+GetURLParameter('sellers')+"&status="+value;
    }
    else
        window.location.href=base_url+"index.php?r=order/index&search=true&status="+value;

}

function selectReason(page,val)
{
	var formData="id_order_state="+val;
 	loader.position='middle';
    ajaxloader.loadURL(base_url+"index.php?r=order/getorderreason",formData,function (result){
           if(typeof result != 'undefined'){
            if(result.success)
            {
                if(page=="popup")
                {
                     $('#showPopupUpdateButton').show();
                     $('#showPopupReasonDropdwon').show();
                     $('#reasonPopupDropDown').empty();
                     $("#reasonPopupDropDown").append("<option value>Select Reason</option>");
                     $.each(result.responseData, function (key, value) {
                     $("#reasonPopupDropDown").append($("<option></option>").val(value.id_order_state_reason).html(value.reason));
                     });
                }
                else
                {
                     $('#showUpdateButton').show();
                     $('#showReasonDropdwon').show();
                     $('#reasonDropDown').empty();
                     $("#reasonDropDown").append("<option value>Select Reason</option>");
                     $.each(result.responseData, function (key, value) {
                     $("#reasonDropDown").append($("<option></option>").val(value.id_order_state_reason).html(value.reason));
                     });
                }
            }
            else
            {
                if(page=="popup")
                {
                 $('#showPopupUpdateButton').show();
                 $('#showPopupReasonDropdwon').hide();
                 $('#reasonPopupDropDown').empty();                    
                }
                else
                {
                 $('#showUpdateButton').show();
                 $('#showReasonDropdwon').hide();
                 $('#reasonDropDown').empty();
                }
            }
             
           }
    });
}

function updateOrderStatus(page,type){
    if(page=='popup')
    {
      var data=$('#popupUpdateForm').serialize();
    }
    else if(page=='viewpage')
    {
      var data=$('#updateStateViewForm').serialize();
    }
    else
    {
 	  var data=$('#updateStatusForm').serialize();
    }
    if(type=="bulkUpdate")
    {
        var checkedOrderIds = $('#gridViewId input:checkbox:checked').map(function() {
            return this.value;
        }).get();

        if($.inArray('1',checkedOrderIds)==0)
           checkedOrderIds.shift();

        if(checkedOrderIds.length==0)
        {
            alert('There are no orders to process!');
            return false;
        }

        formData=data+'&orderIds='+checkedOrderIds;
 	}
    else
        formData=data;

    $('#messageBox').empty();
    $('#messageBox').removeClass('alert alert-success');
    $('#messageBox').removeClass('alert alert-danger');    
    loader.position='middle';
    ajaxloader.loadURL(base_url+"index.php?r=order/changestatus",formData,function (result){
           if(typeof result != 'undefined'){
                if(result.error)
                {
                    if(page=='popup')
                    {
                        $('#messagePopupBox').show();
                        $('#messagePopupBox').removeClass('alert alert-success');
                        $('#messagePopupBox').addClass('alert alert-danger');
                        $('#messagePopupBox').html(result.message);
                    }
                    else
                    {
                        $('#messageBox').show();
                        $('#messageBox').removeClass('alert alert-success');
                        $('#messageBox').addClass('alert alert-danger');
                        $('#messageBox').html(result.message);
                    }
                }
                else
                {
                    if(page=='popup')
                    {
                        $('#messagePopupBox').show();
                        $('#messagePopupBox').removeClass('alert alert-danger');
                        $('#messagePopupBox').addClass('alert alert-success');
                        $('#messagePopupBox').html(result.message);
                        window.setTimeout(function(){location.reload()},1000);
                    }              
                    else
                    {

                        $('#messageBox').show();
                        $('#messageBox').removeClass('alert alert-danger');
                        $('#messageBox').addClass('alert alert-success');
                        $('#messageBox').html(result.message);
                        window.setTimeout(function(){location.reload()},1000);
                    }
                }
             
           }
    });
    
 }

//  function changeOrderStatus(){
//     $('#showStatus').show();
// }

$('#advancedSearchBtn').click(function(){
    $('#advancedSearchForm').slideToggle( "slow" );
});

 function changeOrderStatus(){
    $('#showStatus').show();
}

function exportSelectedRows(){
    var checkedOrderIds = $('#gridViewId input:checkbox:checked').map(function() {
        return this.value;
    }).get();
    if(($("#gridViewId input:checkbox:checked").length==0))
    {
        alert('Please select orders to process!');
        return false;
    }
    if($.inArray('1',checkedOrderIds)==0)
       checkedOrderIds.shift();

    if(checkedOrderIds.length==0)
    {
        alert('There are no orders to process!');
        return false;
    }

    formData='orderIds='+checkedOrderIds;
    loader.position='middle';

    ajaxloader.loadURL(base_url+"index.php?r=order/export-selected-rows",formData,function (result){
           if(typeof result != 'undefined'){
                if(result.error)
                {
                    $('#messageBox').show();
                    $('#messageBox').removeClass('alert alert-success');
                    $('#messageBox').addClass('alert alert-danger');
                    $('#messageBox').html(result.message);
                }
                else if(result.csv_exported)
                {
                    var url=base_url+'csvfile/'+result.donwloadExportedFile;
                    window.open(url, '_blank');
                }             
           }
    });

 }
 
function filterSellers(value)
{
        window.location.href=base_url+"index.php?r=order/index&sellers="+value;
}

function saveSearch(){
    var formData=$('#saveSearchForm').serialize();
    loader.position='middle';
    loader.init();

    ajaxloader.loadURL(base_url+"index.php?r=order/save-search",formData,function (result){
           if(typeof result != 'undefined'){
                if(result.success)
                {
                    $('#messageSearchPopupBox').show();
                    $('#messageSearchPopupBox').removeClass('alert alert-danger');
                    $('#messageSearchPopupBox').addClass('alert alert-success');
                    $('#messageSearchPopupBox').html(result.message);
                    window.setTimeout(function(){location.reload()},1000);
                }
                else
                {

                    $('#messageSearchPopupBox').show();
                    $('#messageSearchPopupBox').removeClass('alert alert-success');
                    $('#messageSearchPopupBox').addClass('alert alert-danger');
                    $('#messageSearchPopupBox').html(result.message);
                }
           }
    }); 
}

function searchData(value,type){
    if(type=='search')
        window.location.href=value;
    else
    {
        if (confirm('Are you sure want to delete')) {
            window.location.href=value;
        }
    }
}


//16-01-2016 created by Ravi kumar
$('#chck-dwnld-stats-btn').on('click', function(e) {
    var orderId = $('#invoiceMultipleOrderIds').val();
    formData = 'orderIds=' + orderId;
    e.preventDefault();
    ajaxloader.loadURL(base_url + "index.php?r=order/check-download-status-ajax", formData, function(result) {
        if (typeof result !== 'undefined') {
            if (result.status) {
                if (confirm('Are you sure you want to download duplicate Package Slip?')) {
                    $('#createInvoice').submit();
                }
            } else {
                $('#createInvoice').submit();
            }
        }
    });
});

$('.check-select-highlight').click(function() {
    if ($(this).is(':checked')) {
        $(this).closest('tr').addClass('tr-selected');
    }else{
        $(this).closest('tr').removeClass('tr-selected'); 
    }
});
if ($('.check-select-highlight').is(':checked')) {
    $(this).closest('tr').addClass('tr-selected');
}else{
    $(this).closest('tr').removeClass('tr-selected'); 
}
    
function highlightRows(){
    $(".check-select-highlight").each(function() { 
        if ($(this).is(':checked')) {
            $(this).closest('tr').addClass('tr-selected');
            //$('.kartik-sheet-style').removeClass('tr-selected');
        }else{
            $(this).closest('tr').removeClass('tr-selected'); 
        }    
   });
}

$("#from_date_add").prop("readonly", true);
$("#to_date_add").prop("readonly", true);
$("#from_date_add").css("background", '#fff');
$("#to_date_add").css("background", '#fff');

$('#openSearchBtn').click(function(){
   $('.open-search-sec').slideToggle(300);
});


$(function() {
   $('.upate-status-btn').click(function(e) {
     e.preventDefault();
     $('#showStatus').hide();
     $('#showReasonDropdwon').hide();
     $('#showUpdateButton').hide();
     $('#modal').modal('show').find('.modal-content')
     .load($(this).attr('href'));
   });
});
// scroll full page
// 



function createManifest(){
    var formData=$('#createManifestForm').serialize();
    loader.position='middle';
    var seller=false;
    var carrier=false;
    loader.init();
    $('#createManifestMessagePopupBox').hide();

    if($('#id_carrier_pop').val()=='')
    {
        var carrier=true;
        var message='Please select Carrier ';
        $('#createManifestMessagePopupBox').show();
        $('#createManifestMessagePopupBox').removeClass('alert alert-success');
        $('#createManifestMessagePopupBox').addClass('alert alert-danger');
        $('#createManifestMessagePopupBox').html(message);
        loader.close();
        return false;
    }
    if($('#id_seller_pop').val()=='')
    {
        var carrier=true;
        var message='Please select Seller  ';
        $('#createManifestMessagePopupBox').show();
        $('#createManifestMessagePopupBox').removeClass('alert alert-success');
        $('#createManifestMessagePopupBox').addClass('alert alert-danger');
        $('#createManifestMessagePopupBox').html(message);
        loader.close();
        return false;
    }

    ajaxloader.loadURL(base_url+"index.php?r=order/create-manifest",formData,function (result){
           if(typeof result != 'undefined'){
                if(result.success)
                {
                    //$('#createManifestForm').submit();
                    document.getElementById("createManifestForm").submit();
                }
                else
                {

                    $('#createManifestMessagePopupBox').show();
                    $('#createManifestMessagePopupBox').removeClass('alert alert-success');
                    $('#createManifestMessagePopupBox').addClass('alert alert-danger');
                    $('#createManifestMessagePopupBox').html(result.message);
                }
           }
    }); 
}


function createPicklist(){
    var formData=$('#createPicklistForm').serialize();
    loader.position='middle';
    var seller=false;
    var carrier=false;
    loader.init();
    $('#createPicklistMessagePopupBox').hide();

    if($('#id_carrier_picklist').val()=='')
    {
        var carrier=true;
        var message='Please select Carrier ';
        $('#createPicklistMessagePopupBox').show();
        $('#createPicklistMessagePopupBox').removeClass('alert alert-success');
        $('#createPicklistMessagePopupBox').addClass('alert alert-danger');
        $('#createPicklistMessagePopupBox').html(message);
        loader.close();
        return false;
    }
    if($('#id_seller_picklist').val()=='')
    {
        var carrier=true;
        var message='Please select Seller  ';
        $('#createPicklistMessagePopupBox').show();
        $('#createPicklistMessagePopupBox').removeClass('alert alert-success');
        $('#createPicklistMessagePopupBox').addClass('alert alert-danger');
        $('#createPicklistMessagePopupBox').html(message);
        loader.close();
        return false;
    }

    ajaxloader.loadURL(base_url+"index.php?r=order/create-picklist",formData,function (result){
           if(typeof result != 'undefined'){
                if(result.success)
                {
                    document.getElementById("createPicklistForm").submit();
                }
                else
                {

                    $('#createPicklistMessagePopupBox').show();
                    $('#createPicklistMessagePopupBox').removeClass('alert alert-success');
                    $('#createPicklistMessagePopupBox').addClass('alert alert-danger');
                    $('#createPicklistMessagePopupBox').html(result.message);
                }
           }
    }); 
}

function createProductPicklist(){
    var formData=$('#createProductPicklistForm').serialize();
    loader.position='middle';
    var seller=false;
    var carrier=false;
    loader.init();
    $('#createProductPicklistMessagePopupBox').hide();

    if($('#id_seller_product_picklist').val()=='')
    {
        var carrier=true;
        var message='Please select Seller  ';
        $('#createProductPicklistMessagePopupBox').show();
        $('#createProductPicklistMessagePopupBox').removeClass('alert alert-success');
        $('#createProductPicklistMessagePopupBox').addClass('alert alert-danger');
        $('#createProductPicklistMessagePopupBox').html(message);
        loader.close();
        return false;
    }

    ajaxloader.loadURL(base_url+"index.php?r=order/generate-product-picklist",formData,function (result){
           if(typeof result != 'undefined'){
                if(result.success)
                {
                    document.getElementById("createProductPicklistForm").submit();
                }
                else
                {

                    $('#createProductPicklistMessagePopupBox').show();
                    $('#createProductPicklistMessagePopupBox').removeClass('alert alert-success');
                    $('#createProductPicklistMessagePopupBox').addClass('alert alert-danger');
                    $('#createProductPicklistMessagePopupBox').html(result.message);
                }
           }
    }); 
}


$(document).ready(function(){
    $('.packing_slip_ajax').click(function(){
        //alert('dddd');
        //document.pdf_gen.submit();
        //var url = $(this).attr('url');alert(url);
        window.open(base_url+"index.php?r=order/single-invoice&id_order="+$(this).attr('id_order'));
       /* $.ajax({
                type : "GET",
                url: base_url+"index.php?r=order/single-invoice&id_order="+id_order, 
                success: function(result){
                                     
                }
            });*/
        });
});

function openPopupUrl(type){
    if(type=="downloadpackageslip")
        $('#modal').modal('show').find('.modal-content').load(base_url+"index.php?r=order/donwload-package-slip");

}

function downloadPackageSlip(idOrder){
    if(!idOrder)
    {
        var formData=$('#packageSlipForm').serialize();
    }
    else
    {
        var formData='invoiceMultipleOrderIds='+idOrder;
        $('#invoiceMultipleOrderIds').val(idOrder);
    }
    loader.position='middle';
    loader.init();
    $('#packageSlipMessagePopupBox').hide();

    ajaxloader.loadURL(base_url+"index.php?r=order/invoices",formData,function (result){
           if(typeof result != 'undefined'){
                if(result.success)
                {
                    if(!idOrder)
                    {
                        document.getElementById("packageSlipForm").submit();
                    }
                    else
                    {
                        document.getElementById("createInvoice").submit();
                    }
                }
                else
                {
                    $('#packageSlipMessagePopupBox').show();
                    $('#packageSlipMessagePopupBox').removeClass('alert alert-success');
                    $('#packageSlipMessagePopupBox').addClass('alert alert-danger');
                    $('#packageSlipMessagePopupBox').html(result.message);
                }
           }
    }); 
}
