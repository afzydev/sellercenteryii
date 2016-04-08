<?php

namespace mdm\admin\controllers;
use common\controllers\AppController;

/**
 * DefaultController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class DefaultController extends AppController

    /**
     * Action index
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
