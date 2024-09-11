<?php

use hesabro\automation\models\AuFolder;
use yii\db\Migration;

/**
 * Class m240704_061412_automation_letter
 */
class m240704_061412_automation_letter extends Migration
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
            '{{%automation_letter}}',
            [
                'id' => $this->integer()->notNull(),
                'parent_id' => $this->integer()->null(),
                'sender_id' => $this->integer()->null()->comment('فرستنده'),
                'type' => $this->tinyInteger()->notNull(),
                'title' => $this->string(64),
                'folder_id' => $this->integer()->null(),
                'body' => $this->text(),
                'number' => $this->bigInteger(),
                'input_number' => $this->string(32)->comment('شماره نامه وارده'),
                'input_type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('نحوه دریافت نامه وارده'),
                'date' => $this->string(10),
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
        $this->addPrimaryKey('PRIMARYKEY', '{{%automation_letter}}', ['id', 'slave_id']);
        $this->alterColumn("{{%automation_letter}}", 'id', $this->integer()->notNull()->append('AUTO_INCREMENT'));
        $this->createIndex('slave_id', '{{%automation_letter}}', ['slave_id']);


        $this->addForeignKey(
            'fk_automation_folder_id',
            "{{%automation_letter}}",
            'folder_id',
            AuFolder::tableName(),
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%automation_letter}}');
    }
}
