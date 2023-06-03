<?php

namespace app\controllers;


use yii\filters\AccessControl;
use yii\web\Controller;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => ['index', 'myindex'],
            'rules' => [
                [
                    'actions' => ['index'],
                    'allow' => true,
                    'roles' => ['?', '@'],//all users
                ]
                ,[
                    'actions' => ['myindex'],
                    'allow' => true,
                    'roles' => ['@'],//only logged users
                ]
            ],
        ];

        return $behaviors;
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Personal account user
     *
    */
    public function actionMyindex(){
        return $this->render('myindex');
    }
}
