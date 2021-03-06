<?php

namespace frontend\controllers;

use Yii;

class LandingController extends SecureController
{
    public $layout = 'landing';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access']['rules'][] = [
            'actions' => ['index'],
            'allow' => true,
            'roles' => ['?', '@']
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest === false) {
            return $this->redirect(['task/index']);
        }

        return $this->render('index');
    }
}
