<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\DiseaseReportSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="form-disease-report-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>

    <?= $form->field($model, 'reported_by')->textInput(['maxlength' => true, 'placeholder' => 'Reported By']) ?>

    <?= $form->field($model, 'country_code')->textInput(['maxlength' => true, 'placeholder' => 'Country Code']) ?>

    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true, 'placeholder' => 'Phone Number']) ?>

    <?= $form->field($model, 'disease_type')->textInput(['maxlength' => true, 'placeholder' => 'Disease Type']) ?>

    <?php /* echo $form->field($model, 'solution')->textarea(['rows' => 6]) */ ?>

    <?php /* echo $form->field($model, 'date_reported')->textInput(['placeholder' => 'Date Reported']) */ ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
