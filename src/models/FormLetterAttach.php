<?php

namespace hesabro\automation\models;

use hesabro\automation\Module;
use Yii;
use yii\base\Model;
use yii\helpers\HtmlPurifier;

/**
 * Class FormLetterAttach
 * @package hesabro\automation\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class FormLetterAttach extends Model
{
    public $file;
    public AuLetter $letter;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['answer', 'trim'],
            ['answer', 'required'],
            ['answer', 'string'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'answer' => Module::t('module', 'Description'),
        ];
    }

    /**
     * @return bool
     */
    public function save() : bool
    {
        $flag = true;
        if (($auLetterUser = AuLetterUser::find()->byLetter($this->letter->id)->byUser(Yii::$app->user->id)->byStatus([AuLetterUser::STATUS_WAIT_VIEW, AuLetterUser::STATUS_VIEWED])->limit(1)->one()) !== null) {
            $auLetterUser->status = AuLetterUser::STATUS_ANSWERED;
            $flag = $auLetterUser->save(false);
        }
        $auLetterActivity = new AuLetterActivity();
        $auLetterActivity->letter_id = $this->letter->id;
        $auLetterActivity->type = AuLetterActivity::TYPE_ANSWER;
        $auLetterActivity->answer = HtmlPurifier::process($this->answer);
        return $flag && $auLetterActivity->save();
    }
}
