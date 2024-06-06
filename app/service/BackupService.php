<?php

namespace App\Service;

use App\Entity\BackupHistory;
use App\Entity\Configuration;
use App\Entity\Directory;
use App\Repository\ConfigurationRepository;
use App\Repository\DirectoryRepository;
use DateTime;

class BackupService
{
    private ConfigurationRepository $confRepo;
    private DirectoryRepository $direRepo;
    private OutputService $output;
    private Configuration $conf;
    private BackupHistory $history;
    private array $backupDirs;
    private array $targetDirs;

    public function __construct()
    {
        $this->confRepo = new ConfigurationRepository();
        $this->direRepo = new DirectoryRepository();
        $this->output   = new OutputService();
        $this->history  = new BackupHistory();

        $this->conf = $this->confRepo->findAll(true)[0];

        $this->backupDirs = $this->direRepo->findByType(Directory::TYPE_BACKUP);
        $this->targetDirs = $this->direRepo->findByType(Directory::TYPE_TARGET);

        $this->handleBackup();
    }

    private function handleBackup()
    {
        $this->output->title("Backup start");

        $this->history
            ->setStartedAt(new DateTime("now"))
            ->setStatus(BackupHistory::STATUS_START)
            ->setRunType()
            ->upsert()
            ;

        $this->output->spaces(1);

        foreach ($this->targetDirs as $targetDir) {
            $this->history
                ->setStatus(BackupHistory::STATUS_RUNNING)
                ->upsert();

            $targetDirPath = $targetDir->getPath();

            $this->output->info("Current target: $targetDirPath");

            $this->output->spaces(1);

            // TODO: Remove this double foreach :O
            foreach ($this->backupDirs as $backupDir) {
                $backupDirPath = $backupDir->getPath();

                $this->output->section("OPERATE ON: $backupDirPath");
                $this->history
                    ->setStatus(BackupHistory::STATUS_BACKUP)
                    ->upsert();

                if (!file_exists($backupDirPath)) {
                    $this->output->warning("Skip not found $backupDirPath");

                    continue;
                }

                if (!$this->conf->getBackupEnabled()) {
                    $this->output->info("Backup currently disabled.");

                    continue;
                }

                $archiveSuccess = $this->archiveDir($backupDirPath, $targetDirPath, $this->conf->getEncryptEnabled());

                (!$archiveSuccess)
                    && $this->output->error("Failed to archive $backupDirPath")
                    || $this->output->success("Successfully backup.");

                $this->output->spaces(1);
            }

            if (!$this->conf->getPurgeEnabled()) {
                $this->output->info("Purge currently disabled.");

                continue;
            }

            $this->output->info("Purge $targetDirPath");
            $this->purgeDir($targetDirPath);
        }

        $this->history
            ->setBackupNumber(count($this->backupDirs))
            ->setTargetNumber(count($this->targetDirs))
            ->setStatus(BackupHistory::STATUS_SUCCESS)
            ->upsert();

        $this->output->spaces(1);
        $this->output->success("Backup end.");
    }

    private function archiveDir(string $from, string $to, bool $encrypt = false): bool
    {
        if (!file_exists($to)) { return false;  }

        $fileBaseName = basename($from);
        $fileDirName  = dirname($from);
        $archiveFileName = "TEST_{$fileBaseName}.tar.gz";

        /**
         * Classic backup.
         */
        if (!$encrypt) {
            exec("cd {$fileDirName} && tar czf {$archiveFileName} $fileBaseName");
            exec("cp {$fileDirName}/{$archiveFileName} $to");

            return file_exists("$to/$archiveFileName");
        }

        if (!$this->conf->getEncryptionKey()) {
            $this->output->error("An encryption key should be configured.");

            return false;
        }

        /**
         * Encrypted backup.
         *
         * TODO: Use Interface instead of calling the service directly.
         */
        $encryptService = new AgeEncryptService($this->conf->getEncryptionKey());

        $encryptService->encrypt($from, "$to/$archiveFileName");

        return file_exists("$to/$archiveFileName");
    }

    private function purgeDir(string $directory): void
    {
        $this->history
            ->setStatus(BackupHistory::STATUS_PURGE)
            ->upsert();

        $dirService = new DirectoryService();

        $dirs = $dirService->scan($directory);

        $olds = $dirService->oldThan($dirs, $this->conf->getRetentionDays(), 'd');

        foreach ($olds as $old) {
            $this->output->info("Remove {$old['path']}");

            unlink($old['path']);
        }

        $this->history
            ->setPurgedNumber(count($olds))
            ->setFinishedAt(new DateTime())
            ->upsert();

        $this->output->success(count($olds). " backups has been removed.");
    }
}
