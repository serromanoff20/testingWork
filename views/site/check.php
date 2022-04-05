<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\FindForm;

$this->title = 'Find users';
$this->params['breadcrumbs'][] = $this->title;
$model = $this->params['idFindForm'];

$form = ActiveForm::begin([
    'id' => 'find-form',
//    'options' => ['class' => 'form-horizontal'],
])
?>


<div>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $form->field($model,'id')?>
    <!--    <input placeholder="Enter id user" type="text">-->

    <?= HTML::submitButton('Find')?>
    <?php ActiveForm::end()?>
    <br>
    <br>

    <li>
        username: <?php $model->findUsernameById();?>
    </li>

</div>
