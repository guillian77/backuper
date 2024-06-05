<?php

namespace App\Service;

class AgeEncryptService implements EncryptInterface
{
    public function encrypt(string $key): bool
    {
        return true;
//        return exec('tar -cz ${dir_path} | age -r $(age-keygen -y ${SECRET_KEY_PATH}) > "${target_dir}/${archiveName}.age"');
    }

    public function decrypt(): bool
    {
        return exec();
    }
}
