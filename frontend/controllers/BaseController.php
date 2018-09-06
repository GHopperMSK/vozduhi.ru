<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Category;
use \yii\helpers\Url;
use common\models\SessionCart;
use common\models\CategorySlug;

/**
 * Main controller
 */
class BaseController extends Controller
{
    public $categories = null;
    public $menu = null;

    public function init() {
        $categoriesFlat = Category::find()
            ->select([
                '*',
                'slug' => CategorySlug::find()
                    ->select(['slug'])
                    ->where('category_id = cat.id')
                    ->orderBy(['created_at' => SORT_DESC])
                    ->limit(1),
                ])
            ->alias('cat')
            ->tree()
            ->asArray()
            ->all();
        $this->categories = $this->flatToHierarchy($categoriesFlat);

        $sessionCart = new SessionCart();
        $sessionCart->setSession(Yii::$app->session);
        $this->view->params['sessionCart'] = $sessionCart;
    }

    public function beforeAction($action)
    {
        if (!$this->menu) {
            $menu = $this->constructMenu($this->categories);
            $menu[] = [
                'label' => 'Бренды',
                'url' => Url::to([
                    'brand/index',
                ]),
                'active' => (Yii::$app->controller->id === 'brand') && (Yii::$app->controller->action->id === 'index'),
            ];
            $menu[] = [
                'label' => 'Акции',
                'url' => Url::to([
                    'sale/index',
                ]),
                'active' => (Yii::$app->controller->id === 'sale') && (Yii::$app->controller->action->id === 'index'),
            ];
            $this->view->params['categories'] = $menu;
        }

        return parent::beforeAction($action);
    }

    /**
     * @link https://stackoverflow.com/questions/841014/nested-sets-php-array-and-transformation/886931#886931
     * @param array $categories
     * @return array
     */
    private function flatToHierarchy($categories) {
        $tree = [];
        if (is_array($categories) && (count($categories) > 0)) {
            // Node Stack. Used to help building the hierarchy
            $stack = [];

            foreach ($categories as $node) {
                $item = $node;
                $item['children'] = [];

                // Number of stack items
                $l = count($stack);

                // Check if we're dealing with different levels
                while ($l > 0 && $stack[$l - 1][Category::NS_DEPTH_ATTRIBUTE] >= $item[Category::NS_DEPTH_ATTRIBUTE]) {
                    array_pop($stack);
                    $l--;
                }

                // Stack is empty (we are inspecting the root)
                if ($l == 0) {
                    // Assigning the root node
                    $i = count($tree);
                    $tree[$i] = $item;
                    $stack[] = &$tree[$i];
                } else {
                    // Add node to parent
                    $i = count($stack[$l - 1]['children']);
                    $stack[$l - 1]['children'][$i] = $item;
                    $stack[] = &$stack[$l - 1]['children'][$i];
                }
            }
        }

        return $tree;
    }

    private function constructMenu($categories) {
        $menu = [];

        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;
        $slug = Yii::$app->request->get('slug');

        if (is_array($categories) && (count($categories) > 0)) {
            foreach($categories as $key => $category) {
                $isActive = ($controller == 'category' && $action == 'view' && $slug == $category['slug']);
                $menu[$key] = [
                    'label' => $category['name'],
                    'url' => Url::to([
                        'category/view',
                        'slug' => $category['slug'],
                    ]),
                    'active' => $isActive,
                ];
                if (is_array($category['children']) && (count($category['children']) > 0)) {
                    $menu[$key]['items'] = $this->constructMenu($category['children']);
                }
            }
        }

        return $menu;
    }
}
