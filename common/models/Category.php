<?php

namespace common\models;

use Yii;

use creocoder\nestedsets\NestedSetsBehavior;
use \yii\helpers\Url;

/**
 * This is the model class for table "menu".
 *
 * @property int $id
 * @property int $lft
 * @property int $rgt
 * @property int $depth
 * @property string $name
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @var Category[]
     */
    public $parents;

    /**
     * The parent category id, obtained from the form
     * @var integer
     */
    public $formParent;

    const NS_TREE_ATTRIBUTE = false;
    const NS_LEFT_ATTRIBUTE = 'lft';
    const NS_RIGHT_ATTRIBUTE = 'rgt';
    const NS_DEPTH_ATTRIBUTE = 'depth';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'categories';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['formParent'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'formParent' => 'Parent',
            'description' => 'Description',
        ];
    }

    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => self::NS_TREE_ATTRIBUTE,
                'leftAttribute' => self::NS_LEFT_ATTRIBUTE,
                'rightAttribute' => self::NS_RIGHT_ATTRIBUTE,
                'depthAttribute' => self::NS_DEPTH_ATTRIBUTE,
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new MenuQuery(get_called_class());
    }

    /**
     * @return common\models\CategorySlug
     */
    public function getSlug()
    {
        if ($this->id) {
            $slug = CategorySlug::find()
                ->where(['category_id' => $this->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            if (!$slug) {
                $slug = new CategorySlug();
                $slug->category_id = $this->id;
            }
        } else {
            $slug = new CategorySlug();
        }

        return $slug;
    }

    public function getRoute()
    {
        return ['category/view', 'slug' => $this->slug->slug];
    }

    public function getUrl()
    {
        return Url::to($this->getRoute());
    }

    public function afterFind()
    {
        parent::afterFind();

        $this->parents = $this->parents()->orderBy([self::NS_LEFT_ATTRIBUTE => SORT_ASC])->all();
        if ($this->parents) {
            $this->formParent = end($this->parents)->id;
        } else {
            $this->parents = [];
        }
    }

    /**
     *
     * @return string
     */
    public function getTitle()
    {
        $str = '';
        $parents = $this->parents;
        foreach ($parents as $parent) {
            $str .= $parent->name . ' > ';
        }
        return $str . $this->name;
    }

    public function getItems()
    {
        return $this->hasMany(Item::className(), ['category_id' => 'id']);
    }

    /**
     * Return the category and its parents attribute list
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryTreeAttributes()
    {
        /**
         * get parent categories due to their attributes spread to the ancestors
         * @param array[integer] $parents
         */
        $parents = [];
        foreach ($this->parents as $parent) {
            $parents[] = $parent->id;
        }

        /**
         * Get children categories due to their items will output in search list
         * and we have to be able to filter them
         * @param array[integer] $children
         */
        $children = self::getChildren($this->id)->select(['id'])->column();

        $categoriesTotal = array_merge($parents, $children);

        return Attribute::find()
            ->select('{{%attributes}}.*')
            ->leftJoin('{{%category_attributes}}', '{{%category_attributes}}.attribute_id = {{%attributes}}.id')
            ->leftJoin('{{%categories}}', '{{%category_attributes}}.category_id = {{%categories}}.id')
            ->orderBy('{{%category_attributes}}.pos')
            ->where(['IN', '{{%categories}}.id', $categoriesTotal]);
    }

    /**
     * Return the category attribute list
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryAttributes()
    {
        // TODO: add order by 'pos' column
        return $this->hasMany(Attribute::className(), ['id' => 'attribute_id'])
            ->viaTable('{{%category_attributes}}', ['category_id' => 'id']);
    }

    /**
     * Return all ancestors of given category
     * @param $categoryId
     * @return MenuQuery|\yii\db\ActiveQuery
     */
    public static function getChildren($categoryId)
    {
        $query = self::find()
            ->where([
                '>=',
                Category::NS_LEFT_ATTRIBUTE,
                Category::find()
                    ->where(['id' => $categoryId])
                    ->select(Category::NS_LEFT_ATTRIBUTE)
            ])
            ->andWhere([
                '<=',
                Category::NS_RIGHT_ATTRIBUTE,
                Category::find()
                    ->where(['id' => $categoryId])
                    ->select(Category::NS_RIGHT_ATTRIBUTE)
            ]);

        return $query;

    }

    public function afterDelete()
    {
        parent::afterDelete();

        CategorySlug::purgeItem($this->id);
    }
}
