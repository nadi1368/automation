<?php

namespace hesabro\automation\models;

/**
 * This is the ActiveQuery class for [[AuLetter]].
 *
 * @see AuLetterActivity
 */
class AuLetterQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuLetter[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuLetter|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>', AuLetter::tableName() . '.status', AuLetter::STATUS_DELETED]);
    }

    /**
     * @param $myId
     * @return AuLetterQuery
     */
    public function byUser($myId)
    {
        return $this->joinWith([
            'user' => function (\yii\db\ActiveQuery $query) use ($myId) {
                $query->andWhere([AuLetterUser::tableName() . '.user_id' => $myId]);
            }
        ]);
    }

    /**
     * @param $myId
     * @return AuLetterQuery
     */
    public function byUserDontView($myId)
    {
        return $this->joinWith([
            'user' => function (\yii\db\ActiveQuery $query) use ($myId) {
                $query->andWhere([AuLetterUser::tableName() . '.user_id' => $myId, AuLetterUser::tableName() . '.status' => AuLetterUser::STATUS_WAIT_VIEW]);
            }
        ]);
    }

    /**
     * @param $status
     * @return AuLetterQuery
     */
    public function byStatus($status): AuLetterQuery
    {
        return $this->andWhere([AuLetter::tableName() . '.status' => $status]);
    }

    /**
     * @param $status
     * @return AuLetterQuery
     */
    public function byType($type): AuLetterQuery
    {
        return $this->andWhere([AuLetter::tableName() . '.type' => $type]);
    }

    /**
     * @param $status
     * @return AuLetterQuery
     */
    public function byInputType($type): AuLetterQuery
    {
        return $this->andWhere([AuLetter::tableName() . '.input_type' => $type]);
    }

    /**
     * @param $status
     * @return AuLetterQuery
     */
    public function waitInput(): AuLetterQuery
    {
        return $this->byType(AuLetter::TYPE_INPUT)->byInputType(AuLetter::INPUT_OUTPUT_SYSTEM)->byStatus(AuLetter::STATUS_WAIT_CONFIRM);
    }
}
