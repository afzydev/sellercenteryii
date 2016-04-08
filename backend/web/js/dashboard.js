/*$(function() {
   $('.get-updated-details').on('click', function(e) {
    alert('clicked');
     var panelId  = $(this).closest('table').attr('id');
     alert(panelId);

     formData = 'panelId=' + panelId;
        ajaxloader.loadURL(base_url + "index.php?r=site/update", formData, function(result) {
            if (typeof result !== 'undefined') {
                if (result) {
                    $('#'+panelId).empty();
                    $('#'+panelId).html(result.responseData);
                } else {
                    //$('#createInvoice').submit();
                }
            }
        });
   });
});*/

function updateDashboardDetails(updateType){
     var panelId  = updateType;
     formData = 'panelId=' + panelId;
        ajaxloader.loadURL(base_url + "index.php?r=site/update", formData, function(result) {
            if (typeof result !== 'undefined') {
                if (result) {
                    $('#'+panelId).empty();
                    $('#'+panelId).html(result.responseData);
                } else {
                    //$('#createInvoice').submit();
                }
            }
        });
}

 function getFormattedPartTime(partTime){
        if (partTime<10)
           return "0"+partTime;
        return partTime;
    }
  function Back_date(day)
  {    
    var back_GTM = new Date(); back_GTM.setDate(back_GTM.getDate() - day);
    var b_dd = back_GTM.getDate();
    var b_mm = back_GTM.getMonth()+1;
    var b_yyyy = back_GTM.getFullYear();
    if (b_dd < 10) {
        b_dd = '0' + b_dd
    }
    if (b_mm < 10) {
        b_mm = '0' +b_mm
    }
    
    var back_date=  b_dd + '-' + b_mm + '-' + b_yyyy;
    return back_date;
    
}
function filterbybtn(type){
         var dateob = new Date();
         var currentYear = dateob.getFullYear();
         var currentMonth = dateob.getMonth() + 1;
         var currentDay = dateob.getDate();
        
         if(type=='month'){
             var prevthirty=Back_date(30);
             var startDate = prevthirty ;
            // var startDate = "01" + "-" +  currentMonth + "-" + currentYear; 
             
         }
         else if(type=='day'){
             var prevfifteen=Back_date(15);
             var startDate = prevfifteen ;
             // var prevfifteen=dateob.getDate()-15;
             // var startDate = prevfifteen + "-" +  currentMonth + "-" + currentYear;
            // var startDate=startDate.setDate(startDate-5);
             //alert(startDate);
         }else{
             var startDate = "01" + "-" +  "01" + "-" + currentYear;
         }
         var endDate = currentDay + "-" +  currentMonth + "-" + currentYear;
         
         $('#order-from_date_add').val(startDate);
         $('#order-to_date_add').val(endDate);
         filterSalesOrders(type);
         $('#'+type+"_filter").addClass('btn-success');
}
function filterSalesOrders(type) {
    if(type=='day')
    {
      $("#month_filter").removeClass('btn-success');
      $("#day_filter").addClass('btn-success');
      $("#month_filter").addClass('btn-primary');
    }
    else
    {
      $("#day_filter").removeClass('btn-success');
      $("#month_filter").addClass('btn-success');
      $("#day_filter").addClass('btn-primary');
    }

    var formData=$('#filtersalesorders').serialize();
    //alert(formData);
    //loader.position='middle';
    //loader.init();

    ajaxloader.loadURL(base_url+"index.php?r=site/filtersalesorders",formData,function (result){
           if(typeof result != 'undefined'){
           	if(result.success)
           	{
           		var finalres=JSON.stringify(result.responseData);
           		jsonparseobj = $.parseJSON(finalres);
           		//var jsonparseobj = $.parseJSON(result.responseData); 
           		var orderdates=jsonparseobj.orderdate;
           		var ordertotal=jsonparseobj.ordertotal;
           		var salesdate=jsonparseobj.salesdate;
           		var saleTotal=jsonparseobj.saleTotal;
           		var countOrdersAndMap=jsonparseobj.pageData.countOrdersAndMap;
              var bestSellingProduct=jsonparseobj.pageData.bestSellingProduct;
        			var chart = $('#order_chart').highcharts();
        			chart.series[0].setData(ordertotal);
        			chart.xAxis[0].setCategories(orderdates);

        			var sales_chart=$('#sales_chart').highcharts();
        			sales_chart.series[0].setData(saleTotal);
        			sales_chart.xAxis[0].setCategories(salesdate);

             	$('#salesOrdersView').empty();
              $('#salesOrdersView').html(countOrdersAndMap);
              
              $('#_bestSellingProduct').empty();
              $('#_bestSellingProduct').html(bestSellingProduct);
            }
        }
    }); 
}

