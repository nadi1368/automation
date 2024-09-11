<?php

use yii\db\Migration;

/**
 * Class m240706_042531_automation_letter_activity
 */
class m240706_042531_automation_letter_activity extends Migration
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
            '{{%automation_letter_activity}}',
            [
                'id' => $this->integer()->notNull(),
                'letter_id' => $this->integer()->notNull(),
                'type' => $this->tinyInteger()->notNull(),
                'additional_data' => $this->json(),
                'status' => $this->tinyInteger()->notNull()->defaultValue('1'),
                'created_at' => $this->integer()->unsigned()->notNull(),
                'created_by' => $this->integer()->unsigned(),
                'slave_id' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            ],
            $tableOptions
        );
        $this->addPrimaryKey('PRIMARYKEY', '{{%automation_letter_activity}}', ['id', 'slave_id']);
        $this->alterColumn("{{%automation_letter_activity}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id', '{{%automation_letter_activity}}', ['slave_id']);


        $this->addForeignKey(
            'fk_automation_letter_activity_id',
            "{{%automation_letter_activity}}",
            'letter_id',
            "{{%automation_letter}}",
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%automation_letter_activity}}');
    }

}
