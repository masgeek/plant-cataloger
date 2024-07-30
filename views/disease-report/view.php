<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\DiseaseReport */
/* @var $providerDiseaseReportImages yii\data\ActiveDataProvider */

$this->title = $model->disease_type;
$this->params['breadcrumbs'][] = ['label' => 'Disease Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disease-report-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Disease Report:' . ' ' . Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">

            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>

    <div class="row">
        <?php
        $gridColumn = [
            ['attribute' => 'id', 'visible' => false],
            'reported_by',
            'country_code',
            'phone_number',
            'disease_type',
            'solution:ntext',
            'date_reported',
        ];
        echo DetailView::widget([
            'model' => $model,
            'attributes' => $gridColumn
        ]);
        ?>
    </div>

    <div class="row">
        <?php
        if ($providerDiseaseReportImages->totalCount) {
            $gridColumnDiseaseReportImages = [
                ['class' => 'yii\grid\SerialColumn'],
                ['attribute' => 'id', 'visible' => false],
                'image_path',
            ];
            echo Gridview::widget([
                'dataProvider' => $providerDiseaseReportImages,
                'pjax' => false,
                'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-disease-report-images']],
                'panel' => [
                    'type' => GridView::TYPE_PRIMARY,
                    'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode('Disease Report Images'),
                ],
                'columns' => $gridColumnDiseaseReportImages,
                'export' => false,
                'toggleData' => false,
            ]);
        }
        ?>
    </div>
</div>
