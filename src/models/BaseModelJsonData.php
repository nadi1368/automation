<?php

namespace hesabro\automation\models;

use yii\base\Model;

/**
 * Class BaseModelJsonData
 * @package hesabro\automation\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class BaseModelJsonData
{
    public function __construct($config = [])
    {
        if (is_array($config)) {
            foreach ($config as $name => $value) {
                if(property_exists($this,$name))
                {
                    $this->$name = $value;
                }
            }
        }
    }
}