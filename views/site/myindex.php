<?php

/* @var $this yii\web\View */
///* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use app\models\User;
use app\models\MassiveData;

$this->title = Yii::$app->user->identity->username;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode(Yii::$app->user->identity->username) ?></h1>

    <div class="show_all_data" onclick="show('data')">
        Показать подробную информацию о пользователе
    </div>

    <label>Ваш ID: </label> <?=Yii::$app->user->id;?>
    <br>
    <div class="all_data" id=data>
        <label>Ваш IP: </label> <?=Yii::$app->request->userIP;?>
        <br>
        <label>Ваш токен: </label> <?=User::findByUsername(Yii::$app->user->identity->username)->accessToken;?>
        <br>
        <label>Подписка на push-уведомления: </label> <?=User::findByUsername(Yii::$app->user->identity->username)->push;?>

    </div>
    <label>Последние время входа: </label> <?=User::getLoginDate()?>
    <br>

    <div style="color:#999;">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
        fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
    </div>
    <br>
<!--    Доделать клиентскую часть по обновлению данных пользователя -->
<!--    <?//= $form = ActiveForm::begin([
//            'fieldConfig' => [
//                'template' => '<div class="col-md-1">{label}</div><div class="col-md-5">{input}</div><div class="col-md-6">{error}</div>',
//            ]
//        ]);
//    ?>-->
    <input type="checkbox"  value="false"> Подписаться на push-уведомления

<!--        <?//=User::findIdentity(Yii::$app->user->id)->push;?>-->
    <br><br>
    <a class="btn btn-lg btn-success" href="http://localhost:8081/site/update-data-user">Обновить данные пользователя</a>
<!--    <?php //ActiveForm::end();?>-->
    <a class="btn btn-lg btn-success" href="http://localhost:8081/site/password-encryption">Зашифровать пароль</a>
</div>

<script type="application/javascript">
    function show(all_data){
        var datas = document.getElementById(all_data);

        if(datas.style.display === 'none'){
            datas.style.display = 'block';
        } else {
            datas.style.display = 'none';
        }
    }
</script>
