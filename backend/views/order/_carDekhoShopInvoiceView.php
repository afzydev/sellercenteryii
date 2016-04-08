<?php

use yii\helpers\Html;
use common\components\Helpers as Helper;

if (class_exists('backend\assets\AppAsset')) {
    backend\assets\AppAsset::register($this);
} else {
    app\assets\AppAsset::register($this);
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">

    </head>
    <body>
        <?php $this->beginBody() ?>
        <?php //if (isset($duplicate) && !empty($duplicate)) { ?>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 100%" align="center">
                        <h4><?php //echo $duplicate; ?>Packing slip</h4>                        
                    </td>    
                </tr>
            </table>
        <?php //} ?>

        <table style="width: 100%; border-bottom:#c8c8c8 solid 1.5px"  cellpadding="5">
            <tr>
                <td style="width: 20%">
                    <img src="images/cardekho-pdf-logo.png" style="width:100px; height:100px;"/>
                </td>
                <td style="width:60%">
                    <table style="width:100%">
                        <tr>
                            <td style="font-size:8pt; color:#333;"></td>
                        </tr>
                        <tr>
                            <td style="font-size:10pt; color:#333;"><?php echo $seller_info['company']; ?><br/><span style='font-size:8pt !important;'><?php echo trim($seller_info['address1'] . ',' . $seller_info['address2'], ',') . ', ' . $seller_info['city'] . ', ' . $seller_info['postcode'] . ', ' . $seller_info['seller_state'] . ', India'; ?></span></td>
                        </tr>
                    </table>
                </td>

                <td style="width: 20%; text-align: center;">
                    <table style="width:100%">
                        <tr>
                            <td style="font-size:8pt; color:#333;;">
                                <p style="float:left"><?php echo $title; ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size:8pt; color:#333;">
                                <?php if ($orders['waybill']) { ?>
                            <barcode code="<?php echo $orders['waybill']; ?>" type="C128B"/>
                            <p style="letter-spacing: 4px;"><?php echo $orders['waybill']; ?></p>
                        <?php } ?>
                            
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
<!--Address Table-->
<table style="width:100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width:33%" >
            <table style="width:100%" border="0" cellpadding="3">
                <tr>
                    <td style="font-size:8pt; color:#000; font-weight:bolder;"><strong>Order Number:</strong></td><td style="font-size:8pt; color:#000; font-weight:normal;">
                        <?php
                        if (isset($order_details[0]['sub_order_position']) && $order_details[0]['sub_order_position'] > 1) {
                            echo $orders['order_number'] . '#' . $order_details[0]['sub_order_position'];
                        } else {
                            echo $orders['order_number'];
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-size:8pt; color:#333; font-weight:bold;">Order Date:</td><td style="font-size:8pt; color:#333; font-weight:normal;"><?php echo Helper::getFormattedDate($orders['date_add']); ?></td>
                </tr>
                <tr>
                    <td style="font-size:8pt; color:#333; font-weight:bold;"><strong>Payment Method:</strong></td>
                    <td style="font-size:8pt; color:#333; font-weight:normal;">
                        <?php if ($orders['payment']) { ?>
                            <span style="display:block; width:100%;font-size:12pt; color:#333; margin:0;font-weight: bold;"><?php echo $orders['payment']; ?></span>
                            <span style="display:block; width:100%;font-size:8pt; color:#333; margin:0;"><?php //echo $orders['amount'];           ?></span>
                        <?php } else { ?>
                            <span style="display:block;font-size:8pt; color:#333; margin:0;">No payment</span>
                        <?php } ?>

                    </td>
                </tr>
                <!-- <tr>
                    <td style="font-size:8pt; color:#333;"><strong>Invoice Date:</strong></td><td style="font-size:8pt; color:#333; font-weight:normal;"><?php echo Helper::getFormattedDate($orders['date_add']); ?></td>
                </tr> -->
                <?php if (isset($orders['vat_number']) && $orders['vat_number']) { ?>
                    <tr>
                        <td style="font-size:8pt; color:#333;"><strong>VAT/TIN:</strong></td><td style="font-size:8pt; color:#333; font-weight:normal;"><?php echo $orders['vat_number']; ?></td>
                    </tr>
                <?php } ?>
                <tr><td style="font-size:8pt; color:#333; font-weight:bold;">Carrier</td><td style="font-size:8pt; color:#333; font-weight:normal;"><?php echo $orders['carrier_name']; ?></td></tr>
            </table>
        </td>

        <td style="width:33%;" valign="top">
            <?php if ($orders['shipping_adress']) { ?>
                <table style="width: 100%">
                    <tr>
                        <td style="width: 100%" valign="top">
                            <span style="font-weight: bold; font-size: 8pt; color: #000">Delivery Address</span><br />
                            <span style="font-weight: normal; font-size: 8pt; color: #333"><?php
                                if ($orders['shipping_adress']) {
                                    echo $orders['delivery_name'].'<br>';
                                    echo $orders['shipping_adress'].'<br>';
                                    echo $orders['shipping_city'].'<br>';
                                    echo $orders['shipping_country'].'-'.$orders['shipping_postcode'].'<br/>Ph-'.$orders['delivery_phone'];

                                } else {
                                    echo $orders['billing_name'].'<br>';
                                    echo $orders['billing_adress'].'<br>';
                                    echo $orders['billing_city'].'<br>';
                                    echo $orders['billing_country'].'-'.$orders['billing_postcode'].'<br/>Ph-'.Ph-$orders['billing_phone'];
                                }
                                ?></span>
                        </td>
                        <td style="width: 100%" valign="top">
                            <span style="font-weight: bold; font-size: 8pt; color: #000">Billing Address</span><br />
                            <span style="font-weight: normal; font-size: 8pt; color: #333"><?php echo $orders['billing_name']. '<br>'. $orders['billing_adress'].',<br/>'.$orders['billing_city'].',<br/>'.$orders['billing_country'].'-'.$orders['billing_postcode'].'<br/>Ph-'.$orders['billing_phone']; ?></span>
                        </td>
                    </tr>
                </table>
            <?php } else { ?>
                <table style="width: 100%">
                    <tr>

                        <td style="width: 100%" valign="top">
                            <span style="font-weight: bold; font-size: 8pt; color: #000">Billing & Delivery Address</span><br />
                            <span style="font-weight: bold; font-size: 8pt; color: #333"><?php echo $orders['billing_adress'].',<br/>'.$orders['billing_city'].',<br/>'.$orders['billing_country'].'-'.$orders['billing_postcode'].'<br/>'.$orders['billing_phone']; ?></span>
                        </td>
                        <td style="width: 100%" valign="top">

                        </td>
                    </tr>
                </table>
            <?php } ?>
        </td>
    </tr>
</table>

<!--Product details table-->
<div style="line-height:0.4pt">&nbsp;</div>
<table style="width: 100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width: 100%; text-align: right">
            <table style="width: 100%; border-top:#c8c8c8 solid 1.5px" cellpadding="3" cellspacing="0">
                <tr style="line-height:4px;">
                    <td style="text-align: left; border-bottom:#c8c8c8 solid 1.5px; color: #333; padding-left: 10px; font-weight: bold; width: 30%;font-size: 8pt;">Product / Reference</td>
                    <td style="text-align: right; border-bottom:#c8c8c8 solid 1.5px; color: #333; padding-left: 10px; font-weight: bold; width: 10%;font-size: 8pt;">MRP</td>

                    <!-- <?php if (isset($tax_excluded_display) && !$tax_excluded_display) { ?>
                        <td style=" color: #333; border-bottom:#c8c8c8 solid 1.5px; text-align: right; font-weight: bold; width: 15%">
                            <table width="100%">
                                <tr>
                                    <td style="font-size:8pt; color:#333;font-weight: bold;">Unit Price </td>
                                </tr>
                                <tr>
                                    <td style="font-size:6pt; color:#333;">(Tax Excl.)</td>
                                </tr>
                            </table>
                        </td>
                    <?php } ?> -->

                    <td style="color: #333;border-bottom:#c8c8c8 solid 1.5px; text-align: right; font-weight: bold; width: 10%">
                        <table width="100%">
                            <tr>
                                <td style="font-size:8pt; color:#333;font-weight: bold;">Price</td>
                            </tr>
                            <!-- <tr>
                                <td style="font-size:6pt; color:#333;">
                                    <?php
                                    if (isset($tax_excluded_display) && $tax_excluded_display) {
                                        echo '(Tax Excl.)';
                                    } else {
                                        echo '(Tax Incl.)';
                                    }
                                    ?>                       			 
                                </td>
                            </tr> -->
                        </table>
                    </td>

                    <!-- <td style=" color: #333; border-bottom:#c8c8c8 solid 1.5px;text-align: right; font-weight: bold; width: 10%; white-space: nowrap;font-size:8pt;">Discount</td> -->
                    <td style=" color: #333;border-bottom:#c8c8c8 solid 1.5px; text-align: center; font-weight: bold; width: 10%;font-size:8pt;">Qty</td>

                    <td style=" color: #333;border-bottom:#c8c8c8 solid 1.5px; text-align: right; font-weight: bold; width: 15%;font-size:8pt;">

                        <table width="100%">
                            <tr>
                                <td style="font-size:8pt; color:#333;font-weight: bold;">Total</td>
                            </tr>
                            <!-- <tr>
                                <td style="font-size:6pt; color:#333;">
                                    <?php
                                    if (isset($tax_excluded_display) && $tax_excluded_display) {
                                        echo '(Tax Excl.)';
                                    } else {
                                        echo '(Tax Incl.)';
                                    }
                                    ?>                      			 
                                </td>
                            </tr> -->
                        </table>
                    </td>
                </tr>
                <!-- PRODUCTS -->

                <?php
                if (is_array($order_details) && count($order_details)) {
                    foreach ($order_details as $order_detail) {
                        ?>				
                        <tr style="line-height:10px;">
                            <td style="text-align: left; width: 30%;font-size: 10pt;">
                                <?php
                                echo $order_detail['product_name'] ? $order_detail['product_name'] . PHP_EOL : '';
                                echo $order_detail['product_supplier_reference'] ? "Product SkU: " . $order_detail['product_supplier_reference'] . PHP_EOL : '';
                                //echo $orders['voucher_name'] ? 'Voucher Applied: ' . $orders['voucher_name'] : '';
                                ?>
                            </td>

                            <td style="text-align: right; width: 10%; white-space: nowrap;font-size: 10pt;">
                                <?php
                                if (isset($order_detail['product_price']) && !empty($order_detail['product_price'])) {
                                    echo Helper::getFormattedNumber($order_detail['product_price']);
                                } else {
                                    echo '';
                                }
                                ?>
                            </td> 
                            <!-- <?php if (!$tax_excluded_display) { ?>
                                <td style="text-align: right; width: 10%; white-space: nowrap;font-size: 8pt;">
                                    <?php echo Helper::getFormattedNumber($order_detail['unit_price_tax_excl']); ?>
                                </td>
                            <?php } ?> -->
                            <td style="text-align: right; width: 10%; white-space: nowrap;font-size: 10pt;">
                                <!-- <?php
                                if (isset($tax_excluded_display) && $tax_excluded_display) {
                                    echo Helper::getFormattedNumber($order_detail['unit_price_tax_excl']);
                                } else {
                                    echo Helper::getFormattedNumber($order_detail['unit_price_tax_incl']);
                                }
                                ?> -->
                                <?php
                                    echo Helper::getFormattedNumber($order_detail['unit_price_tax_incl']);
                                ?>
                            </td>


                            <!-- <td style="text-align: right; width: 10%;font-size: 8pt;">
                                <?php
                                if (isset($order_detail['reduction_amount']) && ($order_detail['reduction_amount'] > 0)) {
                                    echo Helper::getFormattedNumber($order_detail['reduction_amount']);
                                } elseif ((isset($order_detail['reduction_percent'])) && ($order_detail['reduction_percent'] > 0)) {
                                    echo $order_detail['reduction_percent'] . '%';
                                } else {
                                    echo '--';
                                }
                                ?>					
                            </td> -->

                            <td style="text-align: center; width: 10%;font-size: 10pt;">
                                <?php echo $order_detail['product_quantity']; ?>
                            </td>

                            <td style="text-align: right;  width: 15%; white-space: nowrap;font-size: 8pt;">
                                <?php
                                // if (isset($tax_excluded_display) && $tax_excluded_display) {
                                //     echo Helper::getFormattedNumber($order_detail['total_price_tax_excl']);
                                // } else {
                                //     echo Helper::getFormattedNumber($order_detail['total_price_tax_incl']);
                                // }
                                echo Helper::getFormattedNumber($order_detail['total_price_tax_incl']);
                                ?>				
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <!-- END PRODUCTS -->
            </table>

            <!-- TOTAL DETAILS -->
            <table style="width: 100%; border-top:#c8c8c8 solid 1.5px; border-bottom:#c8c8c8 solid 1.5px;" cellpadding="3">
                <!-- <?php if (($order_invoice['total_paid_tax_incl'] - $order_invoice['total_paid_tax_excl']) > 0) { ?>
                    <tr style="line-height:5px;">
                        <td style="width: 83%; text-align: right; font-weight: bold; font-size:10pt">Product Total (Tax Excl.)</td>
                        <td style="width: 17%; text-align: right; font-size:10pt"><?php echo Helper::getFormattedNumber($order_invoice['total_products']); ?></td>
                    </tr>

                    <tr style="line-height:5px;">
                        <td style="width: 83%; text-align: right; font-weight: bold; font-size:10pt">Product Total (Tax Incl.)</td>
                        <td style="width: 17%; text-align: right; font-size:10pt"><?php echo Helper::getFormattedNumber($order_invoice['total_products_wt']); ?></td>
                    </tr>
                <?php } else { ?>
                    <tr style="line-height:5px;">
                        <td style="width: 83%; text-align: right; font-weight: bold; font-size:10pt">Product Total</td>
                        <td style="width: 17%; text-align: right; font-size:10pt"><?php echo Helper::getFormattedNumber($order_invoice['total_products']); ?></td>
                    </tr>
                <?php } ?> -->
                <tr style="line-height:5px;">
                        <td style="width: 83%; text-align: right; font-weight: bold; font-size:10pt">Product Total</td>
                        <td style="width: 17%; text-align: right; font-size:10pt"><?php echo Helper::getFormattedNumber($order_invoice['total_products']); ?></td>
                    </tr>


                <?php if ($order_invoice['total_discount_tax_incl'] > 0) { ?>
                    <tr style="line-height:5px;">
                        <td style="text-align: right; font-weight: bold; font-size:10pt">Coupon Discount</td>
                        <td style="width: 17%; text-align: right; font-size:10pt">-<?php echo Helper::getFormattedNumber($order_invoice['total_discount_tax_incl']); ?></td>
                    </tr>
                <?php } ?>

                <!-- <?php if ($order_invoice['total_wrapping_tax_incl'] > 0) { ?>
                    <tr style="line-height:5px;">
                        <td style="text-align: right; font-weight: bold;  font-size:10pt">Wrapping Cost</td>
                        <td style="width: 17%; text-align: right; font-size:10pt">
                            <?php
                            if (isset($tax_excluded_display) && !($tax_excluded_display)) {
                                echo Helper::getFormattedNumber($order_invoice['total_wrapping_tax_excl']);
                            } else {
                                echo Helper::getFormattedNumber($order_invoice['total_wrapping_tax_incl']);
                            }
                            ?>

                        </td>
                    </tr>
                <?php } ?> -->

                

                    <tr style="line-height:5px;">
                        <td style="text-align: right; font-weight: bold; font-size:10pt">Shipping Charge</td>
                        <td style="width: 17%; text-align: right; font-size:10pt">
                            <?php
                            echo Helper::getFormattedNumber($orders['total_shipping']);
                            ?>
                        </td>
                    </tr>

                
                <?php if ($orders['cod_charge'] > 0) { ?>
                    <tr style="line-height:5px;">
                        <td style="text-align: right; font-weight: bold; font-size:10pt">Cod Charge</td>
                        <td style="width: 17%; text-align: right; font-size:10pt">
                            <?php
                            echo Helper::getFormattedNumber($orders['cod_charge']);
                            ?>
                        </td>
                    </tr>
                <?php } ?>

                <!-- <?php if ((Helper::getFormattedNumber($order_invoice['total_paid_tax_incl']) - Helper::getFormattedNumber($order_invoice['total_paid_tax_excl'])) > 0) { ?>
                    <tr style="line-height:5px; border-top:#c8c8c8 solid 1.5px;  border-bottom:#c8c8c8 solid 1.5px">
                        <td style="text-align: right; font-weight: bold; font-size:10pt">Total Tax</td>
                        <td style="width: 17%; text-align: right; font-size:10pt"><?php echo Helper::getFormattedNumber($order_invoice['total_paid_tax_incl']) - Helper::getFormattedNumber($order_invoice['total_paid_tax_excl']); ?></td>
                    </tr>
                <?php } ?> -->

                <tr style="line-height:5px; border-top:#c8c8c8 solid 1.5px;  border-bottom:#c8c8c8 solid 1.5px">
                    <td style="text-align: right; font-weight: bold; font-size:12pt;border-top: #c8c8c8 solid 1.5px;">
                    <?php if($orders['payment']=="Cash on delivery (COD)") { ?>
                    Total to be collected
                    <?php } else {?>
                    Total
                    <?php } ?>
                    </td>
                    <td style="width: 17%; text-align: right; font-size:12pt;border-top: #c8c8c8 solid 1.5px;">&#8377;<?php echo Helper::getFormattedNumber($order_invoice['total_paid_tax_incl']); ?></td>
                </tr>

            </table>
            <table style="width: 100%;" cellpadding="3">
                <tr style="line-height:25px;">
                    <td style="width: 100%; text-align: center;font-size: 10pt;">
                        Please note this is NOT a VAT invoice.
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
<?php $this->endBody() ?>
</body>
<?php echo $breakPage ? '<pagebreak />' : ''; ?>
</html>
<?php $this->endPage() ?>

