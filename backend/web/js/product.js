/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
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

    $('#multipleProductIds').val(checkedOrderIds);


});

function saveProductSearch(){
   var formData=$('#saveSearchForm').serialize();
   loader.position='middle';
   loader.init();

   ajaxloader.loadURL(base_url+"index.php?r=product/save-search",formData,function (result){
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
  /**
     * Function name    : Update Stock Available
     * Description      : This function used to Stock Available.
     * @param           : 
     * @return          : Success Popup box
     * Created By       : Liyakat Ali
     * Created Date     : 16-02-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
function UpdateOutOfStock(){
    if ($('#id_stock').is('[readonly]') ) 
    {
     $('#id_stock').val('');
     $('#id_stock').attr('readonly', false); 
     $('#id_stock').css('background-color' , '#fff');  
    }
    else
    {
     $('#id_stock').val('');
     $('#id_stock').attr('readonly', 'true'); 
     $('#id_stock').css('background-color' , '#DEDEDE');  
    }
  }
 
function updateStockProduct(){
   
    var formStockUpdateData = $('#updateStockForm').serialize();
    //loader.position='middle';
    var checkStockValue = $('#id_stock').val();
    if(isNaN(checkStockValue))
    {
      $('#messagestockkPopupBox').show();
      $('#messagestockkPopupBox').removeClass('alert alert-success');
      $('#messagestockkPopupBox').addClass('alert alert-danger');
      $('#messagestockkPopupBox').html('Please Enter Quantity In Digits');  
      $('#id_stock').val('');
    }else if(checkStockValue < 0 )
    {
      $('#messagestockkPopupBox').show();
      $('#messagestockkPopupBox').removeClass('alert alert-success');
      $('#messagestockkPopupBox').addClass('alert alert-danger');
      $('#messagestockkPopupBox').html('Quantity must be a positive value');  
      $('#id_stock').val('');
    }
    else
    {
      loader.init();
      ajaxloader.loadURL(base_url+"index.php?r=product/update-stock",formStockUpdateData,function (result){
          if(typeof result != 'undefined'){
              if(result.success)
               {
                   $('#messagestockkPopupBox').show();
                   $('#messagestockkPopupBox').removeClass('alert alert-danger');
                   $('#messagestockkPopupBox').addClass('alert alert-success');
                   $('#messagestockkPopupBox').html(result.message);
                   window.setTimeout(function(){location.reload()},1000);
               }
               else
               {
                   $('#messagestockkPopupBox').show();
                   $('#messagestockkPopupBox').removeClass('alert alert-success');
                   $('#messagestockkPopupBox').addClass('alert alert-danger');
                   $('#messagestockkPopupBox').html(result.message);
               }
          }
          
}); 
  }
}
  /**
     * Function name    : Update Price
     * Description      : This function used to update price.
     * @param           : 
     * @return          : Success Popup box
     * Created By       : Liyakat Ali
     * Created Date     : 16-02-2016
     * Modified By      : Mohd Afzal
     * Modified Date    : 20-02-2016
     */
function updatePrice(){
   
    var formPriceUpdateData = $('#updatePriceForm').serialize();
     loader.position='middle';
    //var checkPriceValue = $('#id_price').val();
    //var checkSellPriceValue = $('#id_selling_price').val();

     loader.init();
     ajaxloader.loadURL(base_url+"index.php?r=product/update-price",formPriceUpdateData,function (result){
          if(typeof result != 'undefined'){
              if(result.success)
               {
                   $('#messagePricePopupBox').show();
                   $('#messagePricePopupBox').removeClass('alert alert-danger');
                   $('#messagePricePopupBox').addClass('alert alert-success');
                   $('#messagePricePopupBox').html(result.message);
                   window.setTimeout(function(){location.reload()},1000);
               }
               else
               {   
                   $('#messagePricePopupBox').show();
                   $('#messagePricePopupBox').removeClass('alert alert-success');
                   $('#messagePricePopupBox').addClass('alert alert-danger');
                   $('#messagePricePopupBox').html(result.message);
               }
          }
   }); 

}

function changeProductStatus(type){

  if(confirm('Are you sure want to change the status of the product'))
  {
       var id_employee=$('#employee').val();
       var id_product=$('#id_product').val();

       var formData='status='+type+'&id_employee='+id_employee+'&id_product='+id_product;
       loader.init();
       ajaxloader.loadURL(base_url+"index.php?r=product/update-product-status",formData,function (result){
            if(typeof result != 'undefined'){
                if(result.success)
                 {
                    var url = window.location.href; 
                    window.location.href=url;
                 }
                 else
                 {   
                    alert(result.message);
                 }
            }
     }); 
  }
}

$('#advancedSearchBtn').click(function(){
    $('#advancedSearchForm').slideToggle( "slow" );
});

function searchProductData(value){
    window.location.href=value;
}

function filterSellers(value)
{
        window.location.href=base_url+"index.php?r=product/index&sellers="+value;
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
function selectcatData(value)
{
    if(GetURLParameter('sellers'))
    {
        window.location.href=base_url+"index.php?r=product/index&search=true&sellers="+GetURLParameter('sellers')+"&cat="+value;
    }
    else
        window.location.href=base_url+"index.php?r=product/index&search=true&cat="+value;

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
$('input[name="from_date_add"]').prop("readonly", true);
$('input[name="to_date_add"]').prop("readonly", true);

$('input[name="from_date_add"]').css("background", "#fff");
$('input[name="to_date_add"]').css("background", '#fff');

$('input[name="from_date_upd"]').prop("readonly", true);
$('input[name="to_date_upd"]').prop("readonly", true);

$('input[name="from_date_upd"]').css("background", "#fff");
$('input[name="to_date_upd"]').css("background", '#fff');


$(function(){
    $(".wrapper1").scroll(function(){
        $(".wrapper2")
            .scrollLeft($(".wrapper1").scrollLeft());
    });
    $(".wrapper2").scroll(function(){
        $(".wrapper1")
            .scrollLeft($(".wrapper2").scrollLeft());
    });
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