<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DiseaseReport */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="disease-report-form card">
    <div class="card-body">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->errorSummary($model); ?>
        <?= $form->field($model, 'id', ['template' => '{input}'])->textInput(['style' => 'display:none']); ?>


        <div class="row">
            <div class="col-md">
                <?= $form->field($model, 'reported_by')->textInput(['maxlength' => true, 'placeholder' => 'Reported By']) ?>
            </div>
            <div class="col-md">
                <?= $form->field($model, 'country_code')->widget(\kartik\widgets\Select2::classname(), [
                    'data' => \yii\helpers\ArrayHelper::map(\app\models\Country::find()->orderBy('country_code')->asArray()->all(), 'country_code', 'country_code'),
                    'options' => ['placeholder' => 'Choose Countries'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </div>
        </div>

        <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true, 'placeholder' => 'Phone Number']) ?>

        <?= $form->field($model, 'disease_type')->textInput(['maxlength' => true, 'placeholder' => 'Disease Type']) ?>

        <?= $form->field($model, 'date_reported')->textInput(['placeholder' => 'Date Reported']) ?>

        <?= $form->field($model, 'solution')->textarea(['rows' => 6]) ?>


        <div class="form-group">
            <?php
            $commonClasses = 'btn btn-lg btn-block';
            $buttonClass = $model->isNewRecord ? 'btn btn-outline-success' : 'btn btn-primary';
            $buttonLabel = $model->isNewRecord ? 'Add' : 'Update';
            ?>
            <?= Html::submitButton(
                Html::tag('span', '', ['class' => 'spinner-grow spinner-grow-sm']) . ' ' . $buttonLabel,
                [
                    'class' => "$commonClasses $buttonClass"
                ]
            ) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
