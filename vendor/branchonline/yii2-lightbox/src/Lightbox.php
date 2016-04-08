<?php

namespace branchonline\lightbox;

use yii\base\Widget;
use yii\helpers\Html;

class Lightbox extends Widget {

    /**
     * @var array containing the attributes for the images
     */
    public $files = [];

    public function init() {
        LightboxAsset::register($this->getView());
    }

    public function run() {
        $html = '';
        $i=0;
        foreach ($this->files as $file) {
            
            if (!isset($file['thumb']) || !isset($file['original'])) {
                continue;
            }

            $attributes = [
                'data-title' => isset($file['title']) ? $file['title'] : '',
            ];

            if (isset($file['group'])) {
                $attributes['data-lightbox'] = $file['group'];
            } else {
                $attributes['data-lightbox'] = 'image-' . uniqid();
            }
            $display='block';
            $width='80px';
            if(isset($file['pageType']))
            {
                $width='40px';
                if($i>0){
                    $display='none';
                }
            }
            $img = Html::img($file['thumb'],['width'=>$width,'style'=>'display:'.$display]);
            $a = Html::a($img, $file['original'], $attributes);
            $div=Html::tag('div', $a, ['style'=>'float:left;width: 25%;']);

            $html .= $div;
           
            $i++;
        }
        return $html;
    }

}