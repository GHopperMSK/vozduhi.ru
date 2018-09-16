<?php

namespace common\models;

use Yii;
use yii\web\Session;
use \yii\helpers\Url;
use yii\base\UserException;
use yii\helpers\FileHelper;
use common\models\Item;
use yii\base\Model;

/**
 * This is the model class for Shopping Cart
 *
 * @property array $items
 */
class SessionCart extends Model
{
    const CART_SECTION = 'cart';

    public $items = [];
    public $session;

    public function setSession(Session $session)
    {
        $this->session = $session;
        $this->items = $this->session->get(self::CART_SECTION, []);
    }

    /**
     * Add Item to shopping cart
     * @param \common\models\Item $item
     */
    public function addItem(Item $item)
    {
        $image = null;
        $images = $item->images;
        if (count($images) > 0) {
            $image = array_shift($images)->getUrl(['width' => 70, 'height' => 70]);
        } else {
            $image = PlaceholderImage::getUrl(['width' => 70, 'height' => 70]);
        }
        if (isset($this->items[$item->id])) {
            $this->items[$item->id]['count']++;
        } else {
            $this->items[$item->id] = [
                'image' => $image,
                'name' => $item->name,
                'price' => $item->discount->price ? $item->discount->price : $item->price,
                'count' => 1,
            ];
        }

        $this->save();
    }

    /**
     * Removes Item with ItemId from the shopping cart
     * @param $itemId
     */
    public function removeItem($itemId)
    {
        if (isset($this->items[$itemId])) {
            unset($this->items[$itemId]);
            $this->save();
        }
    }

    public function purge()
    {
        $this->session->remove(self::CART_SECTION);
    }

    public function save()
    {
        $this->session->set(self::CART_SECTION, $this->items);
    }

    public function getTotalCount()
    {
        return count($this->items);
    }

    public function getTotalSum()
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += (float)$item['price'] * (int)$item['count'];
        }
        return $sum;
    }

}