<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\HtmlPurifier;
use common\models\Item;
use common\models\Order;
use common\models\Attribute;
use common\models\Brand;
use common\models\Category;
use console\models\ChunkReadFilter;

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

    public function actionParseFile($inputFileName)
    {
        setlocale(LC_ALL, 'ru_RU');

        if (!file_exists($inputFileName)) {
            $this->stdout("File '{$inputFileName}' not found" . PHP_EOL);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $chunkSize = 20;

        /** Create a new Reader  **/
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Ods();
        $reader->setLoadSheetsOnly('Export Products Sheet');

        $root = new Category();
        $root->name = 'root';
        $root->makeRoot();
        $slug = $root->getSlug();
        $slug->slug = 'root';
        $slug->save();

        $tmpCat = new Category();
        $tmpCat->name = 'временная';
        $tmpCat->appendTo($root);
        $slug = $tmpCat->getSlug();
        $slug->slug = '1234567';
        $slug->save();


        for ($startRow = 2; $startRow <= 5723; $startRow += $chunkSize) {
            // Create a new Instance of our Read Filter, passing in the limits on which rows we want to read
            $chunkFilter = new ChunkReadFilter($startRow, $chunkSize);
            // Tell the Reader that we want to use the new Read Filter that we've just Instantiated
            $reader->setReadFilter($chunkFilter);
            // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
            $spreadsheet = $reader->load($inputFileName);

            // Do some processing here

            $sheetData = $spreadsheet->getActiveSheet()
                ->toArray(null, true, true, true);
            foreach($sheetData as $row) {
                if (!$row['Y']) {
                    continue;
                }

                $row['A'] = trim($row['A']);
                $row['B'] = trim($row['B']);
                $row['Y'] = trim($row['Y']);

                $brand = Brand::find()->where(['name' => $row['Y']])->one();
                if (!$brand) {
                    $this->stdout("Adding brand '{$row['Y']}'" . PHP_EOL);
                    $brand = new Brand();
                    $brand->name = trim($row['Y']);
                    $brand->save();
                    $slug = $brand->getSlug();
                    $slug->slug = $this->transliterate($brand->name);
                    $slug->save();
                }

                $this->stdout("Adding item '{$row['B']}'" . PHP_EOL);
                if (!$row['A']) {
                    $row['A'] = rand(10000, 99999);
                }

                $item = Item::find()->where(['article' => $row['A']])->one();
                if ($item) {
                    continue;
                }

                $item = new Item();
                $item->category_id = $tmpCat->id;
                $item->brand_id = $brand->id;
                $item->article = $row['A'];
                $item->name = $row['B'];
                $item->description = HtmlPurifier::process($row['D']);
                $item->price = (int)$row['F'];
                $item->save();
                $slug = $item->getSlug();
                $slug->slug = $this->transliterate($item->name);
                $slug->save();
            }
        }

        $this->stdout('OK' . PHP_EOL);
        return ExitCode::OK;
    }

    private function transliterate($text) {
        $text = strtolower($text);
        $text = str_replace(' ', '_', $text);
        $text = trim($text, '_');
        $text = preg_replace("/[^A-Za-z0-9_-]/", '', $text);
        return iconv('UTF-8', 'ASCII//TRANSLIT', $text);

    }

}