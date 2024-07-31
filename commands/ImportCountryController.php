<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Country;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Exception;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ImportCountryController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @return int Exit code
     * @throws Exception
     */
    public function actionIndex(): int
    {
        $url = 'https://raw.githubusercontent.com/lukes/ISO-3166-Countries-with-Regional-Codes/master/all/all.csv';
        $data = file_get_contents($url);
        $rows = array_map('str_getcsv', explode("\n", $data));

        if (count($rows) < 2) {
            echo "No data found in the CSV file.\n";
            return ExitCode::NOINPUT;
        }

        $header = array_shift($rows);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($rows as $row) {
                if (count($row) !== count($header)) {
                    continue; // Skip incomplete or invalid rows
                }

                $rowData = array_combine($header, $row);

                // Create and save the country model
                $country = Country::findOne($rowData['alpha-2']) ?? new Country();
                $country->country_code = $rowData['alpha-2'];
                $country->country_name = $rowData['name'];
                if (!$country->validate()) {
                    Yii::error("Failed to validate country: " . print_r($country->errors, true));
                    $transaction->rollBack();
                    return ExitCode::DATAERR;
                }
                if (!$country->save()) {
                    Yii::error("Failed to save country: " . print_r($country->errors, true));
                    $transaction->rollBack();
                    return ExitCode::DATAERR;
                }
            }

            $transaction->commit();
            echo "Country data imported successfully.\n";
            return ExitCode::OK;
        } catch (\Exception $e) {
            Yii::error("Transaction failed: " . $e->getMessage());
            $transaction->rollBack();
            return ExitCode::UNSPECIFIED_ERROR;
        } catch (\Throwable $e) {
            Yii::error("Transaction failed: " . $e->getMessage());
            $transaction->rollBack();
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

}
