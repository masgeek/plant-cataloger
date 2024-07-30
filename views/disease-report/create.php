<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DiseaseReport */

$this->params['breadcrumbs'][] = ['label' => 'Disease Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disease-report-create row">
    <div class="col-md-8 mx-auto">
        <?= $this->render('_form', ['model' => $model]) ?>
    </div>
</div>
