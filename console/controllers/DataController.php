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
        $reader->setLoadSheetsOnly('Сводный прайс');

        $root = Category::find()->where(['name' => 'root'])->one();
        if (!$root) {
            $root = new Category();
            $root->name = 'root';
            $root->makeRoot();
            $slug = $root->getSlug();
            $slug->slug = 'root';
            $slug->save();
        }

//        $tmpCat = new Category();
//        $tmpCat->name = 'временная';
//        $tmpCat->appendTo($root);
//        $slug = $tmpCat->getSlug();
//        $slug->slug = '1234567';
//        $slug->save();


        for ($startRow = 3; $startRow <= 5723; $startRow += $chunkSize) {
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
                if (empty($row['G'])) {
                    continue;
                }

                $brand = Brand::find()->where(['name' => $row['O']])->one();
                if (!$brand) {
                    $this->stdout("Adding brand '{$row['O']}'" . PHP_EOL);
                    $brand = new Brand();
                    $brand->name = trim($row['O']);
                    $brand->save();
                    $slug = $brand->getSlug();
                    $slug->slug = $this->transliterate($brand->name);
                    $slug->save();
                }

                $topCategory = null;
                $categories = explode('_', trim($row['M']));
                foreach ($categories as $category) {
                    $cat = Category::find()->where(['name' => $category])->one();
                    if (!$cat) {
                        $this->stdout("Adding category '{$category}'" . PHP_EOL);
                        $cat = new Category();
                        $cat->name = $category;
                        if (!$topCategory) {
                            $cat->appendTo($root);
                        } else {
                            $cat->appendTo($topCategory);
                        }
                        $slug = $cat->getSlug();
                        $slug->slug = $this->transliterate($category);
                        $slug->save();
                    }
                    $topCategory = $cat;
                }

                $row['G'] = trim($row['G']);
                $row['H'] = trim($row['H']);
                $row['J'] = trim($row['J']);
                $this->stdout("Adding item '{$row['G']}'" . PHP_EOL);

                $item = Item::find()->where(['article' => $row['G']])->one();
                if ($item) {
                    $row['G'] .= rand(10, 99);
                }

                $item = new Item();
                $item->category_id = $topCategory->id;
                $item->brand_id = $brand->id;
                $item->article = $row['G'];
                $item->name = $row['H'];
                $item->description = HtmlPurifier::process($row['J']);
                $item->price = (int)$row['K'];
                $item->save();
                $slug = $item->getSlug();
                $slug->slug = $this->transliterate($item->name);
                $slug->save();
            }
        }

        $this->stdout('OK' . PHP_EOL);
        return ExitCode::OK;
    }

    private function transliterate($text)
    {
        $text = strtolower($text);
        $text = str_replace(' ', '_', $text);
        $text = trim($text, '_');
        $text = preg_replace("/[^A-Za-z0-9_-]/", '', $text);
        return iconv('UTF-8', 'ASCII//TRANSLIT', $text);

    }
}