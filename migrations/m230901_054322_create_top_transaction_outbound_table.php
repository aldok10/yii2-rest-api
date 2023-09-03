<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%top_transaction_outbound}}`.
 */
class m230901_054322_create_top_transaction_outbound_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%top_transaction_outbound}}', [
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
        $this->dropTable('{{%top_transaction_outbound}}');
    }
}
