<?php

use yii\db\Migration;

/**
 * Class m240703_132603_automation_folder
 */
class m240703_132603_automation_folder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%automation_folder}}',
            [
                'id' => $this->integer()->notNull(),
                'title' => $this->string(64),
                'start_number' => $this->integer()->unsigned()->notNull(),
                'end_number' => $this->integer()->unsigned()->null(),
                'last_number' => $this->bigInteger()->unsigned()->null(),
                'description' => $this->string(128),
                'type' => $this->tinyInteger()->notNull(),
                'status' => $this->tinyInteger()->notNull()->defaultValue('1'),
                'created_at' => $this->integer()->unsigned()->notNull(),
                'created_by' => $this->integer()->unsigned(),
                'updated_at' => $this->integer()->unsigned()->notNull(),
                'updated_by' => $this->integer()->unsigned(),
                'deleted_at' => $this->integer()->unsigned()->notNull()->defaultValue('0'),
                'slave_id' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            ],
            $tableOptions
        );
        $this->addPrimaryKey('PRIMARYKEY', '{{%automation_folder}}', ['id', 'slave_id']);
        $this->alterColumn("{{%automation_folder}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id', '{{%automation_folder}}', ['slave_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%automation_folder}}');
    }
}
