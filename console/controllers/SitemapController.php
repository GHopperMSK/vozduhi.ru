<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\Item;
use common\models\Category;
use common\models\Brand;
use SitemapPHP\Sitemap;

class SitemapController extends Controller
{

    public function actionIndex()
    {
        $sitemap = new Sitemap(Yii::$app->params['domainFrontend']);

        $sitemap->setPath(Yii::getAlias('@frontend') . '/web/');

        $sitemap->addItem('', '1.0', 'daily', 'Today');

        foreach(Category::find()->orderBy([Category::NS_LEFT_ATTRIBUTE => SORT_DESC])->batch(50) as $categories) {
            foreach($categories as $category){
                $sitemap->addItem($category->getUrl(), '8.0', 'daily', 'Today');
            }
        }

        foreach(Item::find()->orderBy(['modified_at' => SORT_DESC])->batch(50) as $items) {
            foreach($items as $item){
                $sitemap->addItem($item->getUrl(), '8.0', 'daily', 'Today');
            }
        }

        foreach(Brand::find()->orderBy(['name' => SORT_DESC])->batch(50) as $brands) {
            foreach($brands as $brand){
                $sitemap->addItem($brand->getUrl(), '8.0', 'daily', 'Today');
            }
        }

        $sitemap->createSitemapIndex(Yii::$app->params['domainFrontend'] . '/', 'Today');

    }
}
