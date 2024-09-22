<?php

namespace hesabro\automation\migrations;

use hesabro\automation\models\AuSignature;
use yii\db\Migration;

/**
 * Class m240907_044307_alter_table_au_signature
 */
class m240907_044307_alter_table_au_signature extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(AuSignature::tableName(), 'user_id', $this->integer()->null()->after('title'));
        $this->addColumn(AuSignature::tableName(), 'additional_data', $this->json()->null());
        $this->createIndex('au_signature_user', AuSignature::tableName(), ['user_id', 'slave_id'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(AuSignature::tableName(), 'user_id');
        $this->dropColumn(AuSignature::tableName(), 'additional_data');
        $this->dropIndex('au_signature_user', AuSignature::tableName());

    }
}
