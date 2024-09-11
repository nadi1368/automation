<?php

namespace hesabro\automation\models;

/**
 * This is the ActiveQuery class for [[AuPrintLayout]].
 *
 * @see AuPrintLayout
 */
class AuPrintLayoutQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuPrintLayout[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuPrintLayout|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>',AuPrintLayout::tableName().'.status', AuPrintLayout::STATUS_DELETED]);
    }

    /**
     * @return AuFolderQuery
     */
    public function justActive() : AuPrintLayoutQuery
    {
        return $this->andWhere([AuPrintLayout::tableName() . '.status' => AuPrintLayout::STATUS_ACTIVE]);
    }
}
