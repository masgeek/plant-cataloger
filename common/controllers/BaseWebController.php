<?php


namespace app\common\controllers;


use app\common\components\MyAccessControl;
use app\models\City;
use app\models\Employee;
use app\models\Sale;
use app\models\SaleItem;
use app\models\ShopIssuanceItem;
use app\models\StockOrder;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 *
 * @property-read mixed $cacheShop
 */
class BaseWebController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => MyAccessControl::class,
                'allowActions' => ['request-password-reset', 'login',]

            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                    'validate-sale' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

}
