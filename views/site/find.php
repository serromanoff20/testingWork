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
]);
print_r ($model->id);
?>


<div>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $form->field($model,'id')?>

    <?= Html::submitButton('Find')?>

    <br>
    <br>

    <li>
<!--        username: --><?php //$model->check();?>
    </li>

</div>
<?php ActiveForm::end()?>
