<?php

namespace common\models;

use Yii;
use creocoder\nestedsets\NestedSetsQueryBehavior;

class MenuQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }

    /**
     * Gets the whole tree.
     * @return \yii\db\ActiveQuery
     */
    public function tree()
    {
        $model = new $this->owner->modelClass();

        $columns = [$model->leftAttribute => SORT_ASC];

        if ($model->treeAttribute !== false) {
            $columns = [$model->treeAttribute => SORT_ASC] + $columns;
        }

        $this->owner
            ->andWhere(['>', $model->depthAttribute, 0])
            ->addOrderBy($columns);

        return $this->owner;
    }

}

