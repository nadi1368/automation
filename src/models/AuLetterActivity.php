<?php

namespace hesabro\automation\models;

use backend\modules\storage\behaviors\StorageUploadBehavior;
use backend\modules\storage\models\StorageFiles;
use common\behaviors\CdnUploadFileBehavior;
use yii\helpers\ArrayHelper;

/**
 * @inheritdocs
 *
 * @mixin  StorageUploadBehavior
 */
class AuLetterActivity extends AuLetterActivityBase
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => CdnUploadFileBehavior::class,
                'model_class' => 'letters',
                'allowed_mime_types' => 'application,image',
            ],
            [
                'class' => StorageUploadBehavior::class,
                'modelType' => StorageFiles::MODEL_TYPE_AUTOMATION_LETTER,
                'attributes' => ['file'],
                'accessFile' => StorageFiles::ACCESS_PRIVATE,
                'scenarios' => [self::SCENARIO_ATTACH],
                'sharedWith' => function (self $model) {
                    $auLetter = AuLetterWithoutSlave::findOne($model->letter_id);
                    return
                        $auLetter !== null &&
                        $auLetter->type == AuLetter::TYPE_OUTPUT &&
                        $auLetter->input_type == AuLetter::INPUT_OUTPUT_SYSTEM ?
                            array_keys(ArrayHelper::map($auLetter->recipientClientWithoutSlave, 'client_id', 'client_id')) :
                            [];
                }
            ],
        ]);
    }

    public function getStorageFileContent(string $attribute) {
        return $this->getStorageFile($attribute)->one()?->getFileContent();
    }

    public function getStorageFileName(string $attribute) {
        return $this->getFileStorageName($attribute);
    }

    public function getStorageFileUrl(string $attribute) {
        return $this->getFileUrl($attribute);
    }
}
