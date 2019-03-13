<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%versioning}}`.
 */
class m190313_085151_create_versioning_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%version_logger}}', [
            'id' => $this->bigInteger(),
            'action' => $this->string(),
            'created_at' => $this->dateTime(),
            'object_id' => $this->bigInteger(),
            'object_class' => $this->string(),
            'data' => $this->text(),
            'created_by' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%versioning}}');
    }
}
