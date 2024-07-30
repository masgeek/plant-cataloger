<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DiseaseReport */

$this->title = 'Create Disease Report';
$this->params['breadcrumbs'][] = ['label' => 'Disease Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disease-report-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
