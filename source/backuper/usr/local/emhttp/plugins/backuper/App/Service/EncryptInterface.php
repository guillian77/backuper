<?php

namespace App\Service;

interface EncryptInterface
{
    public function encrypt(string $from, string $to): bool;

    public function decrypt(string $from, string $to): bool;
}
