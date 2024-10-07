<?php

namespace hesabro\automation\models;


/**
 * Class AuWorkFlowQuery
 * @package hesabro\automation\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class AuWorkFlowQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuWorkFlow[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuWorkFlow|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return mixed
     */
    public function active()
    {
        return $this->onCondition(['<>', AuWorkFlow::tableName() . '.status', AuWorkFlow::STATUS_DELETED]);
    }

    /**
     * @param $type
     * @return mixed
     */
    public function byLetterType($type)
    {
        return $this->andWhere([AuWorkFlow::tableName() . '.letter_type'=>$type]);
    }
}
