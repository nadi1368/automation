<?php

namespace hesabro\automation\models;

use hesabro\automation\Module;
use yii\base\Model;

/**
 * Class FormLetterReference
 * @package hesabro\automation\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class FormLetterReference extends Model
{
    public $user_id;
    public AuLetter $letter;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            ['user_id', 'validateUnique'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'user_id' => Module::t('module', 'User ID'),
        ];
    }

    public function validateUnique($attribute, $params)
    {
        if (!$this->hasErrors() && AuLetterUser::find()->byLetter($this->letter->id)->byUser($this->user_id)->exists()) {
            $this->addError($attribute, 'به کاربر مورد نظر قبلا ارجاع داده شده است');
        }
    }

    /**
     * @return bool
     */
    public function save()
    {
        $auLetterUser = new AuLetterUser();
        $auLetterUser->title = 'ارجاع نامه';
        $auLetterUser->user_id = $this->user_id;
        $auLetterUser->letter_id = $this->letter->id;
        $auLetterUser->type = AuLetterUser::TYPE_REFERENCE;
        $flag = $auLetterUser->save();
        $auLetterActivity = new AuLetterActivity();
        $auLetterActivity->letter_id = $this->letter->id;
        $auLetterActivity->type = AuLetterActivity::TYPE_REFERENCE;
        $auLetterActivity->referenceUserId = $this->user_id;
        return $flag && $auLetterActivity->save();
    }
}
