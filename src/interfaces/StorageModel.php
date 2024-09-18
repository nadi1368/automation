<?php

namespace hesabro\automation\interfaces;

interface StorageModel
{
    public function getStorageFileContent(string $attribute);

    public function getStorageFileName(string $attribute);

    public function getStorageFileUrl(string $attribute);
}
