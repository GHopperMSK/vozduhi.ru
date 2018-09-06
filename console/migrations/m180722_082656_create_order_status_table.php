<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order_status`.
 */
class m180722_082656_create_order_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('order_status', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);

        $this->insert('order_status', [
            'id' => 1,
            'name' => 'Новый',
        ]);
        $this->insert('order_status', [
            'id' => 2,
            'name' => 'В обработке',
        ]);
        $this->insert('order_status', [
            'id' => 3,
            'name' => 'Отправлен',
        ]);
        $this->insert('order_status', [
            'id' => 4,
            'name' => 'Получен',
        ]);
        $this->insert('order_status', [
            'id' => 5,
            'name' => 'Отменен',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('order_status');
    }
}
