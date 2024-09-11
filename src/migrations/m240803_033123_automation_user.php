<?php

use yii\db\Migration;

/**
 * Class m240803_033123_automation_user
 */
class m240803_033123_automation_user extends Migration
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
            '{{%automation_user}}',
            [
                'id' => $this->integer()->notNull(),
                'firstname' => $this->string(64),
                'lastname' => $this->string(64),
                'phone' => $this->string(64),
                'mobile' => $this->string(11),
                'email' => $this->string(128),
                'address' => $this->string(256),
                'description' => $this->text(),
                'user_id' => $this->integer()->defaultValue(0),
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
        $this->addPrimaryKey('PRIMARYKEY', '{{%automation_user}}', ['id', 'slave_id']);
        $this->alterColumn("{{%automation_user}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id', '{{%automation_user}}', ['slave_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%automation_user}}');
    }
}
