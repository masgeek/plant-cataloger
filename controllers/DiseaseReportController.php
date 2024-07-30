<?php

namespace app\controllers;

use Yii;
use app\models\DiseaseReport;
use app\models\search\DiseaseReportSearch;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii\web\Response;

/**
 * DiseaseReportController implements the CRUD actions for DiseaseReport model.
 */
class DiseaseReportController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all DiseaseReport models.
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new DiseaseReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DiseaseReport model.
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $model = $this->findModel($id);
        $providerDiseaseReportImages = new \yii\data\ArrayDataProvider([
            'allModels' => $model->diseaseReportImages,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerDiseaseReportImages' => $providerDiseaseReportImages,
        ]);
    }

    /**
     * Creates a new DiseaseReport model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|yii\web\Response
     * @throws Exception
     */
    public function actionCreate(): \yii\web\Response|string
    {
        $model = new DiseaseReport();

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing DiseaseReport model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return string|Response
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): string|\yii\web\Response
    {
        $model = $this->findModel($id);

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing DiseaseReport model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return Response
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionDelete(int $id): \yii\web\Response
    {
        $this->findModel($id)->deleteWithRelated();

        return $this->redirect(['index']);
    }


    /**
     * Finds the DiseaseReport model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DiseaseReport the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DiseaseReport::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Action to load a tabular form grid
     * for DiseaseReportImages
     * @return string
     * @throws NotFoundHttpException
     * @author Yohanes Candrajaya <moo.tensai@gmail.com>
     * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
     *
     */
    public function actionAddDiseaseReportImages(): string
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('DiseaseReportImages');
            if (
                (Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load'
                    && empty($row)) || Yii::$app->request->post('_action') == 'add') {
                $row[] = [];
                return $this->renderAjax('_formDiseaseReportImages', ['row' => $row]);
            }
        }
        throw new NotFoundHttpException('The requested page does not exist.');

    }
}
