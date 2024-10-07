<?php

use yii\db\Migration;

/**
 * Class m241005_142318_automation_work_flow
 */
class m241005_142318_automation_work_flow extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(
            '{{%automation_work_flow}}',
            [
                'id' => $this->integer()->notNull(),
                'title' => $this->string(64),
                'letter_type' => $this->integer()->comment('نوع نامه'),
                'order_by' => $this->integer(),
                'operation_type' => $this->integer()->comment('AND OR'),
                'additional_data' => $this->json(),
                'status' => $this->tinyInteger()->notNull()->defaultValue('1'),
                'created_at' => $this->integer()->unsigned()->notNull(),
                'created_by' => $this->integer()->unsigned(),
                'updated_at' => $this->integer()->unsigned()->notNull(),
                'updated_by' => $this->integer()->unsigned(),
                'deleted_at' => $this->integer()->unsigned()->notNull()->defaultValue('0'),
                'slave_id' => $this->integer()->unsigned()->notNull(),
            ],
            $tableOptions
        );
        $this->addPrimaryKey('PRIMARYKEY', '{{%automation_work_flow}}', ['id', 'slave_id']);
        $this->alterColumn("{{%automation_work_flow}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id', '{{%automation_work_flow}}', ['slave_id']);
        $this->createIndex('au_work_flow_letter_type', \hesabro\automation\models\AuWorkFlow::tableName(), ['letter_type', 'slave_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%automation_work_flow}}');
    }
}
