<?php

namespace hesabro\automation\models;

use hesabro\automation\Module;
use Yii;
use yii\base\Model;
use yii\helpers\HtmlPurifier;

/**
 * Class FormLetterConfirmStep
 * @package hesabro\automation\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class FormLetterConfirmStep extends Model
{
    public $answer;
    public $signature = false;
    public AuLetter $letter;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['answer', 'trim'],
            ['answer', 'string'],
            ['signature', 'boolean'],
            ['signature', 'validateSignature'],
        ];
    }



    public function validateSignature($attribute, $params)
    {
        if (!$this->hasErrors() && $this->signature && !AuSignature::find()->byUser(Yii::$app->user->id)->exists()) {
                $this->addError($attribute, "امضا شما در سیستم ثبت نشده است.");
        }
    }

    public function attributeLabels()
    {
        return [
            'answer' => Module::t('module', 'Description'),
            'signature' => Module::t('module', 'Signature'),
        ];
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $flag = true;
        if (($auLetterUser = AuLetterUser::find()->byLetter($this->letter->id)->byStep($this->letter->current_step)->byUser(Yii::$app->user->id)->byStatus([AuLetterUser::STATUS_WAIT_VIEW, AuLetterUser::STATUS_VIEWED])->limit(1)->one()) !== null) {
            $auLetterUser->status = AuLetterUser::STATUS_ANSWERED;
            $flag = $auLetterUser->save(false);
        }
        $auLetterActivity = new AuLetterActivity();
        $auLetterActivity->letter_id = $this->letter->id;
        $auLetterActivity->type = AuLetterActivity::TYPE_ANSWER;
        $auLetterActivity->answer = $this->answer ? HtmlPurifier::process($this->answer) : 'تایید شده';
        $flag = $flag && $auLetterActivity->save();

        if ($this->signature && ($signature = AuSignature::find()->byUser(Yii::$app->user->id)->limit(1)->one()) !== null) {
            $flag = $this->letter->signature($signature);
        }
        return $flag && $this->letter->afterConfirmUSerInCurrentStep();
    }
}
