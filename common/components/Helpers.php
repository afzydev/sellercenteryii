<?php

namespace common\components;

use Yii;
use yii\base\Component;
use kartik\mpdf\Pdf;
use common\components\Configuration;
use common\components\Message;
use common\models\MyActiveRecordShop;
use backend\models\AssociateSeller;

class Helpers extends Component {

   /**
     * Function name    : encrypt
     * Description      : This function used to encrypt password entered by user.
     * @param           : 
     * @return          : @encrypted string
     * Created By       : Mohd Afzal
     * Created Date     : 22-12-2015
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public static function encrypt($passwd) {
        return md5(yii::$app->params['cookie_key'] . $passwd);
    }

   /**
     * Function name    : getSessionId
     * Description      : This function used to get loggedin session id.
     * @param           : 
     * @return          : @encrypted string
     * Created By       : Mohd Afzal
     * Created Date     : 10-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */


    public static function getSessionId(){
        if(!Yii::$app->user->isGuest)
            return Yii::$app->user->identity->id_employee;
    }

   /**
     * Function name    : getSessionFullName
     * Description      : This function used to get loggedin session name.
     * @param           : 
     * @return          : @string
     * Created By       : Mohd Afzal
     * Created Date     : 10-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */


    public static function getSessionFullName(){
        return ucwords(Yii::$app->user->identity->firstname.' '.Yii::$app->user->identity->lastname);
    }

    public static function getSellerInfo($idSeller=null){
            $connection = MyActiveRecordShop::getDb();
            if(is_null($idSeller))
                $idSeller=self::getSessionId();
            else
                $idSeller=$idSeller;

            $Query = "Select sellerinfo.company AS company_name,sellerinfo.city as city,sellerinfo.postcode as pincode,sellerinfo.address1 as address,sellerinfo.seller_rating as seller_rating
            from ".EMPLOYEE." emp 
            INNER JOIN ".SELLERINFO." sellerinfo ON sellerinfo.id_seller = emp.id_employee
            WHERE emp.id_employee=".$idSeller."
            ";
            return $connection->createCommand($Query)->queryOne();
    }

    public static function getFormattedDate($date) {
        if ($date) {
            return date('d/m/Y', strtotime($date));
        } else {
            return '';
        }
    }

    public static function getFormattedNumber($number, $place = 0) {
        if ($number) {
            return number_format(ceil($number), $place);
        } else {
            return '';
        }
    }

    public static function formatNumberByType($num,$type){

        switch($type)
        {
            case 'ceil' :
            $number = ceil($num);
            break;
            case 'floor' :
            $number = floor($num);
            break;
        }
        return $number;

    }

    public static function formatDecimalNumber($number, $place = 0) {
        if ($number) {
            return number_format($number, $place);
        } else {
            return '';
        }
    }

  /**
     * Function name    : isSeller
     * Description      : This function used to check loggedin user is seller or not.
     * @param           : 
     * @return          : true or false
     * Created By       : Mohd Afzal
     * Created Date     : 10-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

    public static function isSeller() {
        if (!isset(yii::$app->params['profile_id'][Yii::$app->user->identity->id_profile]))
            return true;

        return false;
    }

  /**
     * Function name    : isSeller
     * Description      : This function used to check loggedin user is superadmin or not.
     * @param           : 
     * @return          : true or false
     * Created By       : Mohd Afzal
     * Created Date     : 10-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

    public static function isSuperAdmin() {
        if (isset(yii::$app->params['profile_id'][Yii::$app->user->identity->id_profile]))
            return true;

        return false;
    }


  /**
     * Function name    : isAdmin
     * Description      : This function used to check logged in user is admin or not.
     * @param           : 
     * @return          : true or false
     * Created By       : Preet Saxena
     * Created Date     : 02-03-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */

    public static function isAdmin() {
        if (!isset(yii::$app->params['profile_id'][Yii::$app->user->identity->id_profile])){

            $sellerInfo = AssociateSeller::getAssociatedSellerList();
            if (!empty($sellerInfo) && count($sellerInfo)>0){ 
              return true;
            }
        }

        return false;
    }


    /**
     * Function name    : getImagePath
     * Description      : This function used to get the image from the Image id.
     * @param           : @int, @string, @string, @string
     * @return          : @string
     * Created By       : Ravi kumar
     * Created Date     : 18-01-2016
     * Modified By      : 
     * Modified Date    : 00-00-0000
     */
    public static function getImagePath($imageId, $imgSize = 'small', $type = 'jpg', $default = '') {
        $serverPath = Configuration::get('PRODUCT_IMAGE_PATH');
        //$defaultImagePath = $serverPath . 'img/bp/en-default-cart_default.jpg';

        if ($imageId !== '') {
            $defaultPath = $serverPath . 'img/p/';
            $imageName = '';
            $isDefault = $default ? '_' . $default : '';
            switch ($imgSize) {
                case 'cart':
                    $imageName = $imageId . '-' . $imgSize . $isDefault;
                    break;
                case 'small':
                    $imageName = $imageId . '-' . $imgSize . $isDefault;
                    break;
                case 'medium':
                    $imageName = $imageId . '-' . $imgSize . $isDefault;
                    break;
                case 'large':
                    $imageName = $imageId . '-' . $imgSize . $isDefault;
                    break;
                case 'home':
                    $imageName = $imageId . '-' . $imgSize . $isDefault;
                    break;
                case 'thickbox':
                    $imageName = $imageId . '-' . $imgSize . $isDefault;
                    break;
                default:
                    $imageName = $imageId;
                    break;
            }
            $recursiveFolderPath = implode('/', str_split($imageId));
            $setImage = $defaultPath . $recursiveFolderPath . '/' . $imageName . '.' . $type;
            return $setImage;
        } else {
            return $serverPath . 'img/bp/en-default-cart_default.jpg';
        }
    }

    //set model properties with value
    public static function setModelByParams(&$model, $params = array()) {
        foreach ($params as $key => $value) {
            if (property_exists($model, $key))
                $model->$key = $value;
        }
        return $model;
    }

    //unset model properties 
    public static function unsetModelByParams(&$model, $params = array()) {
        foreach ($params as $key => $value) {
            if (property_exists($model, $key))
                $model->$key = '';
        }
        return $model;
    }

    //format json
    public static function formatJson($error=null,$success=null,$message=null,$data=null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $responseData=['error'=>$error,'success'=>$success,'message'=>$message,'responseData'=>$data];
        return $responseData;
    }

    //download Pdf
    public static function downloadPdf($content, $filename, $title, $params = array()) 
    {
        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_DOWNLOAD,
            // set mPDF properties on the fly
            'filename' => $filename.'.pdf',
            // your html content input
            'content' => $content,
            // format content from your own css file if needed or use the
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => $title],
            // call mPDF methods on the fly
            'methods' => [
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        return $pdf;
    }

    public static function urlSerialize($data) {
        $uri = urldecode($data);
        parse_str($uri, $output);
        return serialize($output);
    }

    public static function unserializeHtmlBuildQuery($query_string) {
        $unserialize = unserialize($query_string);
        return http_build_query($unserialize);
    }

    public static function getSellerPayoutDetails($service_tax,$mrp,$sellingPrice,$shop_margin,$pg_fee,$shipping_charge,$quantity){

        /*for shop margin*/
        $a = ($sellingPrice * $shop_margin) / 100 ;
        $A = number_format(($a + ( $a * $service_tax / 100 )),2);
        /*for payment gateway fee*/        
        $b = ($sellingPrice * $pg_fee) / 100 ;
        $B = number_format(($b + ( $b * $service_tax / 100 )),2);
        /*for shipping cost*/
        $c = $shipping_charge * $quantity;
        $C = number_format(($c + ( $c * $service_tax / 100 )),2);
        /*shop total margin*/
        $totalMargin = $A + $B + $C;
        /*total vendor payout*/
        $vendorPayout = $sellingPrice - $totalMargin;
        $vendorPayout=number_format($vendorPayout,2);
        $discountInRupee = ($mrp - $sellingPrice);
        $discount=(($mrp - $sellingPrice) / $mrp)*100;
        $discount=number_format($discount,2);
        $total_deductions=number_format($totalMargin,2);

        return [
        'shop_margin'=> $A,
        'pg_fee'=> $B,
        'shipping_charge'=> $C,
        'totalMargin'=> $totalMargin,
        'vendorPayout'=> $vendorPayout,
        'discountInRupee'=>$discountInRupee,
        'discount'=>$discount,
        'total_deductions'=>$total_deductions
        ];
    }

}

?>