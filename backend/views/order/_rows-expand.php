<?php
use common\components\Helpers as Helper;
?>
<table class="table table-striped">
    <tr>
        <th>Customer Name</th>
        <th>Customer Address 1</th>
        <th>City</th>
        <th>State</th>
        <th>Postcode</th>
        <th>Customer Phone</th>
    </tr>
    <tr>
    
        <td><?php echo $model['customer']; ?></td>
        <td><?php echo $model['address1']; ?></td>
        <td><?php echo $model['city']; ?></td>
        <td><?php echo $model['state_name']; ?></td>
        <td><?php echo $model['postcode']; ?></td>
        <td><?php echo $model['mobile_number']; ?></td>
    </tr>
</table>