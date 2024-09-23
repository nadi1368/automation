<?php

namespace hesabro\automation\models;

use backend\modules\master\models\Client;
use backend\modules\storage\behaviors\StorageUploadBehavior;
use backend\modules\storage\models\StorageFiles;
use common\behaviors\CdnUploadFileBehavior;
use hesabro\automation\interfaces\StorageModel;

class AuSignature extends AuSignatureBase implements StorageModel
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => CdnUploadFileBehavior::class,
                'model_class' => 'signature',
                'allowed_mime_types' => 'application,image',
            ],
            [
                'class' => StorageUploadBehavior::class,
                'modelType' => StorageFiles::MODEL_TYPE_AUTOMATION_SIGNATURE,
                'attributes' => ['signature'],
                'accessFile' => StorageFiles::ACCESS_PRIVATE,
                'scenarios' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE],
                'sharedWith' => function (self $model) {
                    return array_keys(Client::itemAlias('ParentBranches'));
                }
            ],
        ]);
    }


    public function getStorageFileContent(string $attribute)
    {
        return $this->getStorageFile($attribute)->one()?->getFileContent();
    }

    public function getStorageFileName(string $attribute)
    {
        return $this->getFileStorageName($attribute);
    }

    public function getStorageFileUrl(string $attribute)
    {
        return $this->getFileUrl($attribute);
    }
}
