<?php 
use miloschuman\highcharts\Highcharts;
                   
 
                    echo Highcharts::widget([
                       'options' => [
                          'title' => ['text' => 'Orders'],
                          'xAxis' => [
                             'categories' => $data['order']['ordersDate'],
                          ],
                          'yAxis' => [
                             'title' => ['text' => 'No. of Orders']
                          ],
                          'series' => [
                            ['name'=> 'Orders', 'data'=>  $data['order']['orderTotal']],
                            
                         ]  
                    ]]); ?>