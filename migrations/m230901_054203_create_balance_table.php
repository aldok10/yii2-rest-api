<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%balance}}`.
 */
class m230901_054203_create_balance_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%balance}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unique(),
            'amount' => $this->decimal(65, 2)->defaultValue(0),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%balance}}');
    }
}
