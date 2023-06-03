<?php

namespace app\controllers;

use app\models\Categories;
use app\models\Goods;
use Yii;
use app\models\User;
use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class GoodsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => [
                'add-delete',
                'add-delete-category',
                'list-categories',
                'list',
                'by-category',
                'edit',
                'edit-category',
                'test'
            ],
            'rules' => [
                [
                    'actions' => ['list-categories', 'list', 'by-category', 'test'],
                    'allow' => true,
                    'roles' => ['?', '@'] //all users
                ],
                [
                    'actions' => ['add-delete', 'edit', 'edit-category', 'add-delete-category'],
                    'allow' => true,
                    'roles' => ['@'] //only log in users
                ]
            ]
        ];
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'add-delete' => ['post', 'delete'],
                'add-delete-category' => ['post', 'delete'],
                'edit' => ['put'],
                'edit-category' => ['put'],
                'list-categories' => ['get'],
                'list' => ['get'],
                'by-category' => ['get'],
                'test' => ['get']
            ]
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ]
        ];

        return $behaviors;
    }

    public function actionAddDelete()
    {
        try {
            if (Yii::$app->getRequest()->isPost) {
                if ((new User)->validateUserByAccessToken(getallheaders()['Access-Token'])) {
                        $model = new Goods();

                        $params = Yii::$app->getRequest()->getBodyParams();

                        $name_product = (!!$params['name_product']) ? $params['name_product'] : null;
                        $category_id = (!!$params['category_id']) ? (int)$params['category_id'] : null;

                        if (
                            (!is_null($name_product) || !is_null($category_id))
                            && $model->load(['name' => $name_product, 'category_id' => $category_id], '')
                        ) {
                            $product = $model->addProduct();
                            if (!$model->hasErrors() && !empty($product)) {
                                return array(
                                    'data' => $product,
                                    'message' => "Product successfully added",
                                    'code' => 200
                                );
                            }
                            return array(
                                'data' => [],
                                'message' => ($model->hasErrors()) ? $model->getErrors() : 'Params is not defined',
                                'code' => 400
                            );
                        }
                }
                return array(
                    "data" => [],
                    "message" => "Input Access-token property in headers IS NOT equal Access-token property of user",
                    "code" => 400
                );
            } elseif (
                Yii::$app->getRequest()->isDelete
                && (new User)->validateUserByAccessToken(getallheaders()['Access-Token'])
            ) {
                $model = new Goods();
                $params = Yii::$app->getRequest()->getQueryParams();

                $name_product = (!!$params['name_product']) ? $params['name_product'] : null;
                $category_id = (!!$params['category_id']) ? (int)$params['category_id'] : null;

                if (
                    (!is_null($name_product) || !is_null($category_id))
                    && $model->load(['name' => $name_product, 'category_id' => $category_id], '')
                    && $model->isDelete()
                ) {
                    return array(
                        'data' => ['entry params' => $model],
                        'message' => "Product is successfully deleted",
                        'code' => 200
                    );
                }
                return array(
                    'data' => ['entry params' => $model],
                    'message' => "Product with entry params is not deleted!",
                    'code' => 200
                );
            }
        }catch (\Exception $exception) {

            return array(
                "data" => [$exception],
                "message" => "Undefined error. You entered 'Access-token'?",
                "code" => 500
            );
        }
        return array();
    }

    public function actionAddDeleteCategory(): array
    {
        try {
            $result = [];
            $model = new Categories();

            if (
                Yii::$app->getRequest()->isPost
                && (new User)->validateUserByAccessToken(getallheaders()['Access-Token'])
            ) {
                $params = Yii::$app->getRequest()->getBodyParams();

                $name_category = (!!$params['name_category']) ? $params['name_category'] : null;
                $characteristic = (!!$params['characteristic']) ? $params['characteristic'] : null;

                if (
                    $model->load(['name_category' => $name_category, 'characteristic' => $characteristic], '')
                ) {
                    $result = $model->addCategory();
                }
            } elseif (
                Yii::$app->getRequest()->isDelete
                && (new User)->validateUserByAccessToken(getallheaders()['Access-Token'])
            ) {
                $params = Yii::$app->getRequest()->getQueryParams();

                $name_category = (!!$params['name_category']) ? $params['name_category'] : null;

                if (
                    (!is_null($name_category) )
                    && $model->load(['name_category' => $name_category], '')
                    && $model->isDelete()
                ) {
                    $result = $model;
                }
            }

            if (!$result) {
                return array(
                    "data" => $result,
                    "message" => ($model->hasErrors()) ? $model->getErrors() : 'Bad request',
                    "code" => 400
                );
            }

            return array(
                "data" => $result,
                "message" => 'success',
                "code" => 200
            );
        } catch (\Exception $exception) {
            return array(
                "data" => [],
                "message" => "Undefined error. You entered 'Access-token'?",
                "code" => 400
            );
        }
    }

    public function actionListCategories(): array
    {
        $model = new Categories();
        $result = $model->getAllCategories();
        if (empty($result)) {
            return array(
                "data" => [],
                "message" => $model->getErrors(),
                "code" => 400
            );
        }

        return array(
            "data" => $result,
            "message" => 'success',
            "code" => 200
        );
    }

    public function actionList(): array
    {
        $model = new Goods();
        $result = $model->getAllGoodsWithCategories();
        if (empty($result)) {
            return array(
                "data" => [],
                "message" => $model->getErrors(),
                "code" => 400
            );
        }

        return array(
            "data" => $result,
            "message" => 'success',
            "code" => 200
        );
    }

    public function actionByCategory(): array
    {
        try {
            $result = [];
            $model = new Goods();
            if (Yii::$app->getRequest()->isGet) {
                $params = Yii::$app->getRequest()->getQueryParams();

                $category_id = (!!$params['category_id']) ? (int)$params['category_id'] : null;

                if ($model->load(['category_id' => $category_id], '')) {
                    $result = $model->getGoodsByCategory();
                }
            }
            if (!$result) {
                $model->addError('', 'This is GET-request. By this category not find`s goods');
            }
            return array(
                "data" => $result,
                "message" => ($model->hasErrors()) ? $model->getErrors() : 'success',
                "code" => 200
            );
        } catch (\Exception $exception) {
            return array(
                "data" => [$exception],
                "message" => 'Undefined error.',
                "code" => 500
            );
        }
    }

    public function actionEdit(): array
    {
        try {
            $params = Yii::$app->getRequest()->getQueryParams();
            if (
                Yii::$app->getRequest()->isPut
                && (new User)->validateUserByAccessToken(getallheaders()['Access-Token'])
            ) {
                $model = new Goods();

                $name_product = (!!$params['name_product']) ? $params['name_product'] : null;
                Goods::$_changeNameOn = (!!$params['change_name_on']) ? $params['change_name_on'] : '';
                $category_id = (!!$params['category_id']) ? (int)$params['category_id'] : null;

                if ($model->load(['name' => $name_product, 'category_id' => $category_id], '')
                    && !empty($model->edit())
                ){
                    return array(
                        'data' => $model->edit(),
                        "message" => 'Success is edited product',
                        "code" => 200
                    );
                }
            }
            return array(
                "data" => ['inputParams' => $params],
                "message" => 'Is not correctly entry params',
                "code" => 400
            );
        } catch (\Exception $exception) {
            return array(
                "data" => [$exception],
                "message" => "This is PUT-request. You entered 'Access-token'?",
                "code" => 500
            );
        }
    }

    public function actionEditCategory(): array
    {
        try {
            $params = Yii::$app->getRequest()->getQueryParams();
            $result = [];
            $model = new Categories();

            if (
                Yii::$app->getRequest()->isPut
                && (new User)->validateUserByAccessToken(getallheaders()['Access-Token'])
            ) {
                $name_category = (!!$params['name_category']) ? $params['name_category'] : null;
                Categories::$_changeNameOn = (!!$params['change_name_on']) ? $params['change_name_on'] : '';
                Categories::$_changeCharacteristicOn = (!!$params['change_characteristic_on']) ?
                    $params['change_characteristic_on'] : '';

                if ($model->load(['name_category' => $name_category], '')
                    && !empty($model->edit())
                ){
                    $result = $model->edit();
                }
            }
            return array(
                "data" => $result,
                "message" => "Category is success edited",
                "code" => 200
            );
        } catch (\Exception $exception) {
            return array(
                "data" => [$exception],
                "message" => "Undefined error. You entered 'access-token' in headers? 
                    All required params input: name_category, change_name_on, change_characteristic_on?",
                "code" => 500
            );
        }
    }

    public function actionTest()
    {
        return phpinfo();
    }
}