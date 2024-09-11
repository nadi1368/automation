<?php

namespace hesabro\automation\models;

/**
 * This is the ActiveQuery class for [[AuLetterClient]].
 *
 * @see AuLetterClient
 */
class AuLetterClientQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return AuLetterClient[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return AuLetterClient|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->onCondition(['<>',AuLetterClient::tableName().'.status', AuLetterClient::STATUS_DELETED]);
    }
    /**
     * @param $userId
     * @return AuLetterUserQuery
     */
    public function byClient($clientId): AuLetterClientQuery
    {
        return $this->andWhere(['client_id' => $clientId]);
    }

    /**
     * @param $userId
     * @return AuLetterUserQuery
     */
    public function byLetter($letterId): AuLetterClientQuery
    {
        return $this->andWhere(['letter_id' => $letterId]);
    }

    /**
     * @param $userId
     * @return AuLetterUserQuery
     */
    public function byStatus($status): AuLetterClientQuery
    {
        return $this->andWhere(['status' => $status]);
    }
}
