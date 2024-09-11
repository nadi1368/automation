<?php

namespace hesabro\automation\models;

/**
 * This is the ActiveQuery class for [[AuSignature]].
 *
 * @see AuSignature
 */
class AuSignatureQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuSignature[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuSignature|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>',AuSignature::tableName().'.status', AuSignature::STATUS_DELETED]);
    }


    /**
     * @return AuFolderQuery
     */
    public function justActive() : AuSignatureQuery
    {
        return $this->andWhere([AuSignature::tableName() . '.status' => AuSignature::STATUS_ACTIVE]);
    }
}
