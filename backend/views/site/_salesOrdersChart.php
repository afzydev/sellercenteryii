<?php
use miloschuman\highcharts\Highcharts;
 
$ordersDate=[];
$orderTotal=[];
$salesData=[];
$saleTotal=[];

?>
<div class="col-md-6">
<div id="orders" style="margin-bottom: 10px;">
<?php
if(isset($data['order']['ordersDate'])) 
{ 
    $ordersDate = $data['order']['ordersDate']; 
    $orderTotal=$data['order']['orderTotal'];
    //print_r($orderTotal);exit;
}
echo Highcharts::widget([
   'id' => 'order_chart',
   'options' => [
      'title' => ['text' => 'Orders'],
      'xAxis' => [
         'categories' => $ordersDate,
      ],
      'yAxis' => [
         'title' => ['text' => 'No. of Orders']
      ],
      'series' => [
        ['name'=> 'No. of orders', 'data'=>  $orderTotal],
        
     ]  
]]); 
?>
</div>
</div>
<div class="col-md-6">
<div id="sales" style="margin-bottom: 10px;">

<?php

if(isset($data['sale']['salesDate'])) 
{ 
    $salesData = $data['sale']['salesDate']; 
    $saleTotal=$data['sale']['saleTotal'];
}

echo Highcharts::widget([
   'id' => 'sales_chart',
   'options' => [
      'title' => ['text' => 'Sales'],
      'xAxis' => [
         'categories' => $salesData,
      ],
      'yAxis' => [
         'title' => ['text' => 'Sales In Rs.']
      ],
      'series' => [
        ['name'=> 'Sales Revenue', 'data'=>  $saleTotal,],

     ]  
]]);


?>
</div>
</div>