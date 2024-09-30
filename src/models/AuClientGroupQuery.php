<?php

namespace hesabro\automation\models;

/**
 * This is the ActiveQuery class for [[AuClientGroup]].
 *
 * @see AuClientGroup
 */
class AuClientGroupQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuClientGroup[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuClientGroup|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>',AuClientGroup::tableName().'.status', AuClientGroup::STATUS_DELETED]);
    }
}
