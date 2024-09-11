<?php

namespace hesabro\automation\models;
use hesabro\automation\Module;
use Yii;

/**
 * Class AuLetterClientWithoutSlave
 * @package hesabro\automation\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class AuLetterClientWithoutSlave extends AuLetterClient
{

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get(Module::getInstance()->clientDb);
    }
}