<?php

use yii\db\Migration;

/**
 * Class m240704_064343_automation_letter_customer
 */
class m240704_064343_automation_letter_customer extends Migration
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
            '{{%automation_letter_user}}',
            [
                'id' => $this->integer()->notNull(),
                'letter_id' => $this->integer()->notNull(),
                'user_id' => $this->integer()->notNull(),
                'type' => $this->tinyInteger()->notNull(),
                'title' => $this->string(64)->null(),
                'status' => $this->tinyInteger()->notNull()->defaultValue('1'),
                'slave_id' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            ],
            $tableOptions
        );
        $this->addPrimaryKey('PRIMARYKEY', '{{%automation_letter_user}}', ['id', 'slave_id']);
        $this->alterColumn("{{%automation_letter_user}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id', '{{%automation_letter_user}}', ['slave_id']);


        $this->addForeignKey(
            'fk_automation_letter_id',
            "{{%automation_letter_user}}",
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
        $this->dropTable('{{%automation_letter_user}}');
    }
}
