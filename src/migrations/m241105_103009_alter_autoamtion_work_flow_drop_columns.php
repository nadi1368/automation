<?php

use hesabro\automation\models\AuWorkFlow;
use yii\db\Migration;

/**
 * Class m241105_103009_alter_autoamtion_work_flow_drop_columns
 */
class m241105_103009_alter_autoamtion_work_flow_drop_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn(AuWorkFlow::tableName(), 'order_by');
        $this->dropColumn(AuWorkFlow::tableName(), 'operation_type');
        $this->dropIndex('au_work_flow_letter_type', AuWorkFlow::tableName());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn(AuWorkFlow::tableName(), 'order_by', $this->integer());
        $this->addColumn(AuWorkFlow::tableName(), 'operation_type', $this->integer()->comment('AND OR'));
        $this->createIndex('au_work_flow_letter_type', AuWorkFlow::tableName(), ['letter_type', 'slave_id'], true);
    }
}
