<?php

namespace App\Service;

interface EncryptInterface
{
    public function encrypt(string $key): bool;

    public function decrypt(): bool;
}
