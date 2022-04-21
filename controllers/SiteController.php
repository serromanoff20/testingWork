<?php

namespace app\controllers;

use app\models\Geocoder;
use app\models\MassiveData;
use app\models\User;
use app\models\notifications\Push;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
//use app\models\ContactForm;
use app\models\Account;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'check', 'geocoder'],
                'rules' => [
                    [
                        'actions' => ['logout', 'geocoder'],
                        'allow' => true,
                        'roles' => ['@'], // logged users
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'geocoder' => ['get']
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
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
     * Displays about page.
     *
     * @return string
    */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Personal account user
     *
    */
    public function actionMyindex(){
        return $this->render('myindex');
    }

    /**
     * Performed after checked on button "Зашифровать пароль"
     * @return string
    */
    public function actionPasswordEncryption(){
        return MassiveData::encryption();
    }

    /**
     * Performed after checked on button "Обновить данные пользователя"
     */
    public function actionUpdateDataUser(){
//Доделать клиентскую часть по обновлению данных пользователя

//        Yii::$app->response->format = Response::FORMAT_JSON;
        $idUser = Yii::$app->user->id;
        $modelPush = new Push();

        if ($modelPush->updateDataUserById($idUser)){
            Yii::$app->response->format = Response::FORMAT_JSON;



            return 'истина';
        }
        return 'ложь';

//        return $this->render('myindex');
    }

    /**
     * Login action.
     *
     * @return Response|string
    */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $modelForm = new LoginForm();
        if ($modelForm->load(Yii::$app->request->post()) && $modelForm->login()) {
            $user = new User();
            if ($user->populateFieldsInDB()){
                return $this->goHome();
            } else {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $AS = false;
                return $AS;
            }


        } else {
            $modelForm->password = '';
            return $this->render('login', [
                'model' => $modelForm,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return Response
    */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Used by Rest API
     * @return array|\yii\db\ActiveRecord[]
     */

    public function actionUserBase()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return MassiveData::resultTableUsers();
    }

    public function actionTest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user = new User();

        $user->populateFieldsInDB();

        return $user;
    }

    public function actionGeocoder()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $GC = new Geocoder();

        return $GC->initFile();
    }

    // Запомнить конструкцию:
    //  return (new User())->yourIP();

}
