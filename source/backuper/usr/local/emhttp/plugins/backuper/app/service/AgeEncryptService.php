<?php

namespace App\Service;

use App\App;
use Exception;

class AgeEncryptService implements EncryptInterface
{
    private string $keyPath;

    public function __construct()
    {
        $pluginPath = App::get()->getConfig()['plugin_path_flash'];

        $this->keyPath = "{$pluginPath}/age_key.txt";
    }

    /**
     * Encrypt a plain directory to an encrypted destination file.
     *
     * @param string $from source directory to encrypt.
     * @param string $to encrypted file destination path.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function encrypt(string $from, string $to): bool
    {
        $this->throwUnlessKeyFile();

        $publicKey = $this->getPublicKey();

        exec("tar -cz {$from} | age -r $publicKey > '{$to}.age'");

        return file_exists("{$to}.age");
    }

    /**
     * Decrypt a directory encrypted with an age key.
     *
     * @param string $from encrypted directory.
     * @param string $to decrypted directory destination.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function decrypt(string $from, string $to): bool
    {
        $this->throwUnlessKeyFile();

        exec("age --decrypt -i {$this->keyPath} -o {$to} {$from}");

        return file_exists($to);
    }

    /**
     * Generate an Age key file stored inside plugin directory.
     *
     * @return self
     *
     * @throws Exception
     *
     *@see self::getPublicKey()
     *
     */
    public function generateKeyFile(): self
    {
        exec("age-keygen -o {$this->keyPath}");

        $this->throwUnlessKeyFile();

        return $this;
    }


    /**
     * Extract public key from Age key file.
     *
     * @see /boot/config/plugins/backuper/age_key.txt
     *
     * @return string
     *
     * @throws Exception
     *
     */
    public function getPublicKey(): string
    {
        $this->throwUnlessKeyFile();

        return exec("age-keygen -y {$this->keyPath}");
    }

    /**
     * Get entire key file content.
     *
     * @see /boot/config/plugins/backuper/age_key.txt
     *
     * @throws Exception
     */
    public function getEntireKey(): string
    {
        $this->throwUnlessKeyFile();

        return file_get_contents($this->keyPath);
    }

    public function hasEncryptionFile(): bool
    {
        return file_exists($this->keyPath);
    }

    /**
     * Allow to import existing age key file to the plugin.
     *
     * @param array $keyFile age keyfile.
     *
     * @return void
     */
    public function importKeyFile(array $keyFile)
    {
        // TODO
    }

    private function throwUnlessKeyFile(): void
    {
        if ($this->hasEncryptionFile()) {
            return;
        }

        throw new Exception("Unable to found age key file {$this->keyPath}");
    }
}
