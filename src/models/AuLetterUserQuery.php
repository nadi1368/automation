<?php

namespace hesabro\automation\models;

/**
 * This is the ActiveQuery class for [[AuLetterCustomer]].
 *
 * @see AuLetterUser
 */
class AuLetterUserQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuLetterUser[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuLetterUser|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>', AuLetterUser::tableName() . '.status', AuLetterUser::STATUS_DELETED]);
    }

    /**
     * @param $userId
     * @return AuLetterUserQuery
     */
    public function byUser($userId): AuLetterUserQuery
    {
        return $this->andWhere(['user_id' => $userId]);
    }

    /**
     * @param $userId
     * @return AuLetterUserQuery
     */
    public function byLetter($letterId): AuLetterUserQuery
    {
        return $this->andWhere(['letter_id' => $letterId]);
    }

    /**
     * @param $userId
     * @return AuLetterUserQuery
     */
    public function byType($type): AuLetterUserQuery
    {
        return $this->andWhere(['type' => $type]);
    }

    /**
     * @param $userId
     * @return AuLetterUserQuery
     */
    public function byStatus($status): AuLetterUserQuery
    {
        return $this->andWhere(['status' => $status]);
    }
    /**
     * @param $userId
     * @return AuLetterUserQuery
     */
    public function byStep($step): AuLetterUserQuery
    {
        return $this->andWhere(['step' => $step]);
    }
}
