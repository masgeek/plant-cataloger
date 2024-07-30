<?php
/** @noinspection UndetectableTableInspection */

namespace app\common\models;

use app\common\components\DateHelper;
use app\models\ItemSale;
use app\models\SaleItem;
use app\models\ShopIssuanceItem;
use app\models\ShopIssuanceStatus;
use app\models\StockOrderItem;
use Exception;
use mootensai\relation\RelationTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class BaseModel
 * @property int $stock_category_id
 * @package app\common\models
 */
class BaseModel extends ActiveRecord
{
    use RelationTrait;

    public $selected_item_name;
    public $stock_category_id;
    public $item_shop_id;
    public $item_count = 50;

    public $pdfFileName = "report.pdf";
    public $excelFileName = "report.xlsx";

    public $itemRange = [
        -1 => 'All items',
        5 => '5 items',
        10 => '10 items',
        20 => '20 items',
        50 => '50 items',
        100 => '100 items',
        150 => '150 items',
        200 => '200 items',
        500 => '500 items',
        1000 => '1000 items',
        1500 => '1500 items',
    ];

    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
            ]
        ];
    }

    /**
     * @return int
     * @throws Exception
     * @Deprecated Functionality moved to the DateHelper class
     */
    public function generateTimeStamp(): int
    {
        $dateHelper = new DateHelper();
        return $dateHelper->generateTimeStamp();
    }

    /**
     * @param $itemId
     * @return int|mixed
     */
    public function checkTotalItemStock($itemId)
    {
        $itemsStock = StockOrderItem::find()
            ->where(['item_id' => $itemId])
            ->sum('quantity_received');
        return $itemsStock == null ? 0 : $itemsStock;
    }

    /**
     * @param $itemId
     * @return int|mixed
     *
     * @deprecated This function will be removed in future releases
     */
    public function checkSoldStock($itemId)
    {
        $itemsStock = ItemSale::find()
            ->where(['item_id' => $itemId])
            ->sum('sale_quantity');

        return $itemsStock == null ? 0 : $itemsStock;
    }

    /**
     * @param $itemId
     * @param $shopId
     * @return float
     */
    public function checkSingleShopItemStock($itemId, $shopId): float
    {
        $issuanceStatus = [
            ShopIssuanceStatus::getReceivedStatus(),
            ShopIssuanceStatus::getPartialStatus()
        ];

        $stockLeft = 0;
        $itemsStock = ShopIssuanceItem::find()
            ->joinWith('request')
            ->where(['item_id' => $itemId])
            ->andWhere(['shop_issuance_master.shop_id' => $shopId])
            ->andWhere(['IN', 'shop_issuance_master.issuance_status_id', $issuanceStatus])
            ->sum('quantity_received');

        $soldStock = $this->checkShopSoldItemStock($itemId, $shopId);
        if ($itemsStock != null) {
            $stockLeft = $itemsStock - $soldStock;
        }
        return (float)$stockLeft;
    }

    /**
     * @param $itemId
     * @param $shopId
     * @return int|mixed
     */
    public function checkShopSoldItemStock($itemId, $shopId)
    {
        $soldQty = $this->checkShopSoldQty($itemId, $shopId);
        $returnedQty = $this->checkShopReturnedQuantity($itemId, $shopId);
        return ($soldQty - $returnedQty);
    }

    public function checkShopSoldQty($itemId, $shopId)
    {
        $soldShopQuantity = SaleItem::find()
            ->joinWith('saleNo')
            ->where(['item_id' => $itemId])
            ->andWhere(['is_return' => 0])
            ->andWhere(['sales_master.shop_id' => $shopId])
            ->andWhere(['sales_master.sale_closed' => 1])
            ->sum('sale_quantity');
        return $soldShopQuantity == null ? 0 : $soldShopQuantity;
    }


    public function checkShopReturnedQuantity($itemId, $shopId)
    {
        $returnedShopQuantity = SaleItem::find()
            ->joinWith('saleNo')
            ->where(['item_id' => $itemId])
            ->andWhere(['is_return' => 1])
            ->andWhere(['sales_master.shop_id' => $shopId])
            ->andWhere(['sales_master.sale_closed' => 1])
            ->sum('return_qty');

        return $returnedShopQuantity == null ? 0 : $returnedShopQuantity;
    }

    /**
     * @param $itemId
     * @param array|int[] $issuanceStatus
     * @return bool|int|mixed|string
     */
    public function checkAllShopItemStock($itemId, array $issuanceStatus = [3, 4])
    {

        $itemsStock = ShopIssuanceItem::find()
            ->innerJoinWith('request')
            ->where(['item_id' => $itemId])
            ->andWhere(['IN', 'shop_issuance_master.issuance_status_id', $issuanceStatus])
            ->sum('quantity_received');
        return $itemsStock == null ? 0 : $itemsStock;
    }

    public function computeAvailableItemStock($itemId)
    {
        $itemsStock = $this->checkTotalItemStock($itemId);
        $itemSold = $this->checkAllShopItemStock($itemId);
        return ($itemsStock - $itemSold);
    }

    /**
     * @param int $qtyRequested
     * @param int $itemId
     * @return bool
     */
    public
    function isItemStockSufficient($qtyRequested, $itemId)
    {
        $available = $this->computeAvailableItemStock($itemId);
        return $qtyRequested <= 0 ? false : ($available > $qtyRequested);
    }

    /**
     * @param array $models
     * @param string $fieldName
     * @param bool $asCurrency
     * @return string
     * @throws InvalidConfigException
     */
    public
    static function getTotal(array $models, $fieldName, $asCurrency = true)
    {
        $total = 0;
        foreach ($models as $item) {
            $total += $item[$fieldName];
        }
        return $asCurrency ? Yii::$app->formatter->asCurrency($total) : $total;
    }

    /**
     * @param $reportType
     * @throws InvalidConfigException
     */
    public
    function setFileName($reportType)
    {
        $fileStart = Yii::$app->formatter->asDatetime('now', 'php:Y_m_d_H_i');
        $fileEnd = Yii::$app->formatter->asDatetime('now', 'php:Y_m_d_H_i');
        $filename = "{$reportType}_{$fileStart}_to_{$fileEnd}";
        $this->pdfFileName = "{$filename}.pdf";
        $this->excelFileName = "{$filename}.xlsx";
    }

    /**
     * @return array
     */
    protected
    function setMonthsArray()
    {
        $monthsData = [];
        for ($i = 0; $i <= 11; $i++) {
            $monthsData[$i] = null;
        }

        return $monthsData;
    }

    /**
     * @param $year
     * @return array
     */
    public
    function setShortMonthName($year)
    {
        $monthsData = $this->setMonthsArray();
        $labels = [];
        foreach ($monthsData as $key => $data) {
            $monthNumber = $key + 1; //increment by one to allow number to name conversion
            $jd = GregorianToJD($monthNumber, 1, $year);
            $labels[] = JDMonthName($jd, 0);
        }
        return $labels;
    }
}
