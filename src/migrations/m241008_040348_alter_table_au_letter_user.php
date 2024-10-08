<?php

use yii\db\Migration;

/**
 * Class m241008_040348_alter_table_au_letter_user
 */
class m241008_040348_alter_table_au_letter_user extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%automation_letter_user}}', 'step', $this->integer()->notNull()->defaultValue(0)->after('status'));
        $this->addColumn('{{%automation_letter_user}}', 'additional_data', $this->json()->notNull()->after('step'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%automation_letter_user}}', 'step');
        $this->dropColumn('{{%automation_letter_user}}', 'additional_data');
    }
}
