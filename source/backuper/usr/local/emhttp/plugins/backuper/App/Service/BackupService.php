<?php

namespace App\Service;

use App\Entity\History;
use App\Entity\Configuration;
use App\Entity\Directory;
use App\Repository\ConfigurationRepository;
use App\Repository\DirectoryRepository;

class BackupService
{
    private DirectoryRepository $direRepo;
    private Configuration $conf;
    private History $history;
    private array $backupDirs;

    public function __construct()
    {
        $this->direRepo = new DirectoryRepository();
        $this->history  = new History();
        $this->conf = (new ConfigurationRepository())->findAll()[0];
        $this->backupDirs = $this->direRepo->findByType(Directory::TYPE_BACKUP);
    }

    public function run(
        string $target,
        bool $withEncryption = false,
        History $history = null
    ): void
    {
        if ($history) { $this->history = $history; }

        $this->history
            ->setStatus(History::STATUS_BACKUP)
            ->upsert();

        $dirs = (new DirectoryRepository())->findByType(Directory::TYPE_BACKUP);

        foreach ($dirs as $dir) {
            if ($dir->getPaused()) { continue; }

            $success = $this->archiveDir($dir->getPath(), $target, $withEncryption);

            if ($success) { $this->history->incrementBackupNumber(); continue; }

            $this->history
                ->setStatus(History::STATUS_ERROR)
                ->setFinishedAt(new \DateTime())
                ->upsert();

            die(1);
        }
    }

    private function archiveDir(string $from, string $to, bool $encrypt = false): bool
    {
        if (!file_exists($to)) { return false;  }

        $fileBaseName = basename($from);
        $fileDirName  = dirname($from);
        $date = (new \DateTime())->format("Y-m-d_H-i-s");
        $archiveFileName = "BACKUPER_{$date}_{$fileBaseName}.tar.gz";

        /**
         * Classic backup.
         */
        if (!$encrypt) {
            exec("cd {$fileDirName} && tar czf {$archiveFileName} $fileBaseName");
            exec("mv {$fileDirName}/{$archiveFileName} $to");

            return file_exists("$to/$archiveFileName");
        }

        /**
         * Encrypted backup.
         */
        $encryptService = new AgeEncryptService();

        return $encryptService->encrypt($from, "$to/$archiveFileName");
    }
}
