<?php

namespace hesabro\automation\models;

/**
 * This is the ActiveQuery class for [[AuUser]].
 *
 * @see AuUser
 */
class AuUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>',AuUser::tableName().'.status', AuUser::STATUS_DELETED]);
    }
}
