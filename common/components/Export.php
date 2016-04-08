<?php
namespace common\components;
use Yii;
use yii\base\Component;
use yii\helpers\Url;

class Export extends Component {

    /**
    * Export csv
    */
   public static function exportCsv($data=array(), $filename){
		   header('Content-type: application/csv');
       header('Content-Disposition: attachment; filename='.$filename);
		   $fp = fopen('php://output', 'w');
		   foreach ($data as $fields) {
				$result=fputcsv($fp, $fields);
			}
            fclose($fp);
	 }

}
?>