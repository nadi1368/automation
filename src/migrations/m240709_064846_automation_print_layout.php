<?php

use yii\db\Migration;

/**
 * Class m240709_064846_automation_print_layout
 */
class m240709_064846_automation_print_layout extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%automation_print_layout}}',
            [
                'id' => $this->integer()->notNull(),
                'title' => $this->string(64),
                'logo' => $this->string(128),
                'additional_data' => $this->json(),
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
        $this->addPrimaryKey('PRIMARYKEY', '{{%automation_print_layout}}', ['id', 'slave_id']);
        $this->alterColumn("{{%automation_print_layout}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id', '{{%automation_print_layout}}', ['slave_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%automation_print_layout}}');
    }
}
