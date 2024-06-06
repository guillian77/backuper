<?php

namespace App\Service;

class AgeEncryptService implements EncryptInterface
{
    private string $encryptionKey;

    public function __construct(string $encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;
    }

    public function encrypt(string $from, string $to): bool
    {
        return exec("tar -cz {$from} | age -r {$this->encryptionKey} > '{$to}.age'");
    }

    public function decrypt(): bool
    {
        return exec();
    }
}
