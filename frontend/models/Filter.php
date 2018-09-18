<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Item;
use common\models\Category;
use common\models\Brand;

/**
 * Filter is the model behind the search filters.
 */
class Filter extends Model
{
    const DEFAULT_PRICE_MIN = 0;
    const DEFAULT_PRICE_MAX = 1000000;

    /**
     * @var array
     */
    public $filters;

    /**
     * @var array
     */
    public $brandsFilter;

    /**
     * Data, obtained from web form
     * @var array integer
     */
    public $brands;


    /**
     * Price values, obtained from web form. Two digits,
     * separated by comma.
     *
     * @var string
     */
    public $price;

    /**
     * Start price for filtering
     * @var integer
     */
    public $priceStart;

    /**
     * End price for filtering
     * @var integer
     */
    public $priceEnd;

    /**
     * Minimal possible price value
     * @var integer
     */
    public $priceMin;

    /**
     * Maximum possible price value
     * @var integer
     */
    public $priceMax;

    /**
     * @var integer
     */
    public $categoryId;

    public $brandId;

    /**
     * @var array
     */
    public $attr = [];

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attr', 'brands', 'price'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    /**
     * Get minimal and maximum item prices from given category
     * @param $categoryId integer
     * @return array[min,max]
     */
    private function getMinMaxPrice($categoryId)
    {
        $priceResult = (new \yii\db\Query())
            ->select([
                'min' => new \yii\db\Expression('MIN(price)'),
                'max' => new \yii\db\Expression('MAX(price)')
            ])
            ->from('{{%items_value}}')
            ->where([
                'in',
                'category_id',
                Category::getChildren($categoryId)->select(['id'])
            ])
            ->one();

        if (empty($priceResult['min'])) {
            $priceResult['min'] = self::DEFAULT_PRICE_MIN;
        }

        if (empty($priceResult['max'])) {
            $priceResult['max'] = self::DEFAULT_PRICE_MAX;
        }

        return [$priceResult['min'], $priceResult['max']];
    }

    /**
     * Get minimal and maximum item prices from given brand
     * @param $brandId integer
     * @return array[min,max]
     */
    private function getMinMaxPriceByBrand($brandId)
    {
        $priceResult = (new \yii\db\Query())
            ->select([
                'min' => new \yii\db\Expression('MIN(price)'),
                'max' => new \yii\db\Expression('MAX(price)')
            ])
            ->from('{{%items_value}}')
            ->where([
                '=',
                'brand_id',
                $brandId
            ])
            ->one();

        if (empty($priceResult['min'])) {
            $priceResult['min'] = self::DEFAULT_PRICE_MIN;
        }

        if (empty($priceResult['max'])) {
            $priceResult['max'] = self::DEFAULT_PRICE_MAX;
        }

        return [$priceResult['min'], $priceResult['max']];
    }

    /**
     * Get all existed brands from given category
     * @param $categoryId integer
     * @return array
     */
    private function getBrandsFilter($categoryId)
    {
        $brandsFilter = [];

        $brands = Brand::find()
            ->where([
                'in',
                'id',
                Item::find()
                    ->distinct()
                    ->select('brand_id')
                    ->where(['in', 'category_id', Category::getChildren($categoryId)->select(['id'])])
            ])
            ->all();

        foreach ($brands as $brand) {
            $brandsFilter[$brand->id] = $brand->name;
        }

        return $brandsFilter;
    }

    public function getFilters(Category $category) {
        $filters = [];
        $attributes = $category->getCategoryTreeAttributes()->all();
        foreach ($attributes as $attribute) {
            $values = [];
            foreach ($attribute->getFilterValues() as $value) {
                $values["{$attribute->id}_{$value['id']}"] = $value['value'];
            }
            $filters[] = [
                'id' => $attribute->id,
                'code' => $attribute->code,
                'name' => $attribute->name,
                'values' => $values,
            ];
        }
        $this->filters = $filters;

        list($this->priceMin, $this->priceMax) = $this->getMinMaxPrice($category->id);
        if (strpos($this->price, ',')) {
            list($this->priceStart, $this->priceEnd) = explode(',', $this->price);
        }

        $this->brandsFilter = $this->getBrandsFilter($category->id);

        $this->categoryId = $category->id;
    }

    public function getBrandFilters(Brand $brand) {
        $filters = [];

        $categoryIds = $brand->getCategoryIds();
        foreach($categoryIds as $categoryId) {
            $category = Category::findOne(['id' => $categoryId]);
            $attributes = $category->getCategoryTreeAttributes()->all();
            foreach ($attributes as $attribute) {

                $isNew = true;
                foreach ($filters as $filter) {
                    if ($filter['id'] === $attribute->id) {
                        $isNew = false;
                        break;
                    }
                }

                if (!$isNew) {
                    continue;
                }

                $values = [];
                foreach ($attribute->getFilterValues() as $value) {
                    $values["{$attribute->id}_{$value['id']}"] = $value['value'];
                }
                $filters[] = [
                    'id' => $attribute->id,
                    'code' => $attribute->code,
                    'name' => $attribute->name,
                    'values' => $values,
                ];
            }
        }

        $this->filters = $filters;

        list($this->priceMin, $this->priceMax) = $this->getMinMaxPriceByBrand($brand->id);
        if (strpos($this->price, ',')) {
            list($this->priceStart, $this->priceEnd) = explode(',', $this->price);
        }

        $this->brandsFilter = [];

        $this->brandId = $brand->id;
    }

    /**
     * The amount of checked filters
     * @return int
     */
    public function getCount()
    {
        $i = 0;

        if (($this->priceStart && $this->priceMin !== (int)$this->priceStart)
            || ($this->priceEnd && $this->priceMax !== (int)$this->priceEnd))
        {
            $i++;
        }

        if (is_array($this->brands)) {
            $i += count($this->brands);
        }

        foreach ($this->attr as $a) {
            if (is_array($a)) {
                $i += count($a);
            }
        }

        return $i;
    }
}
