<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use common\models\Item;
use common\models\Order;
use common\models\Attribute;
use common\models\Brand;
use common\models\Category;

class DataController extends Controller
{

    public function actionPurge()
    {
        $statusMessage = '';
        if($this->confirm('Do you want to purge the Database completely', false)) {
            $this->stdout('Purging...' . PHP_EOL);

            $transaction = Yii::$app->db->beginTransaction();
            try {

                Item::deleteAll();
                Order::deleteAll();
                Attribute::deleteAll();
                Brand::deleteAll();
                Category::deleteAll();

                $transaction->commit();
                $statusMessage = 'OK' . PHP_EOL;
            } catch (\Exception $exc) {
                $transaction->rollBack();
                $this->stdout('Something went wrong' . PHP_EOL);
                return ExitCode::UNSPECIFIED_ERROR;
            }

        } else {
            $statusMessage = 'Cancelled' . PHP_EOL;
        }

        $this->stdout($statusMessage);
        return ExitCode::OK;
    }

}
