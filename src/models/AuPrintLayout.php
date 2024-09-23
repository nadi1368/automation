<?php

namespace hesabro\automation\models;

use backend\modules\storage\behaviors\StorageUploadBehavior;
use backend\modules\storage\models\StorageFiles;
use common\behaviors\CdnUploadFileBehavior;

class AuPrintLayout extends AuPrintLayoutBase
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => CdnUploadFileBehavior::class,
                'model_class' => 'automation_print',
                'allowed_mime_types' => 'application,image',
            ],
            [
                'class' => StorageUploadBehavior::class,
                'modelType' => StorageFiles::MODEL_TYPE_AUTOMATION_PRINT,
                'attributes' => ['logo'],
                'accessFile' => StorageFiles::ACCESS_PRIVATE,
                'scenarios' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE],
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
