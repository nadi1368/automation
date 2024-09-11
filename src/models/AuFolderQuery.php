<?php

namespace hesabro\automation\models;

use Yii;

/**
 * This is the ActiveQuery class for [[AFolder]].
 *
 * @see AuFolder
 */
class AuFolderQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuFolder[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuFolder|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return AuFolderQuery
     */
    public function active() : AuFolderQuery
    {
        return $this->onCondition(['<>', AuFolder::tableName() . '.status', AuFolder::STATUS_DELETED]);
    }


    /**
     * @return AuFolderQuery
     */
    public function justActive() : AuFolderQuery
    {
        return $this->andWhere([AuFolder::tableName() . '.status' => AuFolder::STATUS_ACTIVE]);
    }
    /**
     * @param $type
     * @return AuFolderQuery
     */
    public function byType($type) : AuFolderQuery
    {
        return $this->andWhere(['type' => $type]);
    }
}
