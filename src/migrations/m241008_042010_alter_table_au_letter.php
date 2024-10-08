
<?php

use yii\db\Migration;

/**
 * Class m241008_042010_alter_table_au_letter
 */
class m241008_042010_alter_table_au_letter extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%automation_letter}}', 'current_step', $this->integer()->notNull()->defaultValue(0)->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%automation_letter}}', 'current_step');
    }
}
