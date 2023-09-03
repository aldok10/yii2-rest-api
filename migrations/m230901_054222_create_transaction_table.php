<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%transaction}}`.
 */
class m230901_054222_create_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%transaction}}', [
            'id' => $this->primaryKey(),
            'from_user_id' => $this->integer()->notNull(),
            'to_user_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(65, 2)->defaultValue(0),
            'transaction_type' => $this->tinyInteger()->defaultValue(0),
            'created_at' => $this->timestamp()->defaultValue(null),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%transaction}}');
    }
}
