<?php 
use miloschuman\highcharts\Highcharts;
 
                    echo Highcharts::widget([
                       'options' => [
                          'title' => ['text' => 'Sales'],
                          'xAxis' => [
                             'categories' => $data['sale']['salesDate'],
                          ],
                          'yAxis' => [
                             'title' => ['text' => 'Sales In Rs.']
                          ],
                          'series' => [
                            ['name'=> 'Sales', 'data'=>  $data['sale']['saleTotal'],],
            
                         ]  
                    ]]); ?>