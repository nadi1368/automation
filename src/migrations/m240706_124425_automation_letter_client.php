<?php

use yii\db\Migration;

/**
 * Class m240706_124425_automation_letter_client
 */
class m240706_124425_automation_letter_client extends Migration
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
            '{{%automation_letter_client}}',
            [
                'id' => $this->primaryKey(),
                'letter_id' => $this->integer()->notNull(),
                'client_id' => $this->integer()->notNull(),
                'type' => $this->tinyInteger()->notNull(),
                'title' => $this->string(64)->null(),
                'additional_date' => $this->json(),
                'status' => $this->tinyInteger()->notNull()->defaultValue('1'),
                'slave_id' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            ],
            $tableOptions
        );

        $this->addForeignKey(
            'fk_automation_letter_client_id',
            "{{%automation_letter_client}}",
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
        $this->dropTable('{{%automation_letter_client}}');
    }
}
