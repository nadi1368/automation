<?php

namespace hesabro\automation\models;

/**
 * This is the ActiveQuery class for [[AuLetterActivity]].
 *
 * @see AuLetterActivity
 */
class AuLetterActivityQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuLetterActivity[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuLetterActivity|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>',AuLetterActivity::tableName().'.status', AuLetterActivity::STATUS_DELETED]);
    }
}
