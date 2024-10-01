<?php

namespace hesabro\automation\models;

use Yii;
use common\behaviors\SendAutoCommentsBehavior;
use common\models\CommentsType;
use yii\helpers\Html;


/**
 * Class AuLetter
 * @package hesabro\automation\models
 * @author Nader <nader.bahadorii@gmail.com>
 */
class AuLetter extends AuLetterBase
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::LETTER_INTERNAL,
                'title' => 'نامه های داخلی',
                'scenarioValid' => [self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT, self::SCENARIO_CONFIRM_AND_SEND_INTERNAL],
                'callAfterUpdate' => true
            ],
            [
                'class' => SendAutoCommentsBehavior::class,
                'type' => CommentsType::LETTER_OUTPUT,
                'title' => 'نامه های وارده بین سیستمی',
                'scenarioValid' => [self::SCENARIO_RECEIVE_INPUT],
                'callAfterUpdate' => false
            ],
        ]);
    }

    /**
     * @return array
     */
    public function getUserMail(): array
    {
        $users = [];
        if (in_array($this->getScenario(), [self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT, self::SCENARIO_CONFIRM_AND_SEND_INTERNAL])) {
            foreach ($this->recipientUser as $auLetterUser) {
                $users[] = $auLetterUser->user_id;
            }
        }
        return $users;
    }

    /**
     * @return string
     */
    public function getLinkMail(): string
    {
        return Yii::$app->urlManager->createAbsoluteUrl(['/automation/' . AuLetterBase::itemAlias('TypeControllers', $this->type) . '/view', 'id' => $this->id]);
    }

    /**
     * @return string
     */
    public function getContentMail(): string
    {
        $content = '';
        if (in_array($this->getScenario(), [self::SCENARIO_CONFIRM_AND_RECEIVE_INPUT, self::SCENARIO_CONFIRM_AND_SEND_INTERNAL])) {
            $content = Html::tag('p', "یک نامه در سیستم ثبت شده است.");
            if ($this->update !== null) {
                $content .= Html::tag('p', 'این نامه توسط "' . $this->update?->fullName . '" در سیستم ثبت شد.');
            }
            $content .= Html::tag('p', 'عنوان نامه : "' . $this->title . '"');
        }
        if (in_array($this->getScenario(), [self::SCENARIO_RECEIVE_INPUT])) {
            $content = Html::tag('p', "یک نامه در سیستم ثبت شده است.");
            $content .= Html::tag('p', 'این نامه توسط "' . $this->showSender() . '" در سیستم ثبت شد.');
            $content .= Html::tag('p', 'عنوان نامه : "' . $this->title . '"');
        }
        return $content;
    }

    /**
     * @return bool
     */
    public function autoCommentCondition(): bool
    {
        return true;
    }
}
