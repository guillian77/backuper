<?php

namespace App\Service;

use App\Entity\Configuration;
use App\Entity\Directory;
use App\Repository\ConfigurationRepository;
use App\Repository\DirectoryRepository;

class BackupService
{
    private ConfigurationRepository $confRepo;
    private DirectoryRepository $direRepo;
    private OutputService $output;
    private Configuration $conf;
    private array $backupDirs;
    private array $targetDirs;

    public function __construct()
    {
        $this->confRepo = new ConfigurationRepository();
        $this->direRepo = new DirectoryRepository();
        $this->output   = new OutputService();

        $this->conf = $this->confRepo->findAll(true);

        $this->backupDirs = $this->direRepo->findByType(Directory::TYPE_BACKUP);
        $this->targetDirs = $this->direRepo->findByType(Directory::TYPE_TARGET);

        $this->handleBackup();
    }

    private function handleBackup()
    {
        $this->output->title("Backup start");

        $this->output->spaces(1);

        foreach ($this->targetDirs as $targetDir) {
            $targetDirPath = $targetDir->getPath();

            $this->output->info("Current target: $targetDirPath");

            $this->output->spaces(1);

            // TODO: Remove this double foreach :O
            foreach ($this->backupDirs as $backupDir) {
                $backupDirPath = $backupDir->getPath();

                $this->output->section("OPERATE ON: $backupDirPath");

                if (!file_exists($backupDirPath)) {
                    $this->output->warning("Skip not found $backupDirPath");

                    continue;
                }

                if ($this->conf->getBackupEnabled()) {
                    $this->backupDir($backupDirPath, $targetDirPath);
                } else {
                    $this->output->info("Backup currently disabled.");
                }

                if ($this->conf->getPurgeEnabled()) {
                    $this->purgeDir($backupDirPath, $targetDirPath);
                }

                $this->output->spaces(1);
            }
        }

        $this->output->spaces(1);
        $this->output->success("Backup end.");
    }

    private function backupDir(string $from, string $to)
    {
        if (!file_exists($to)) { $this->output->error("Unable to find $to"); }

        $fileBaseName = basename($from);
        $fileDirName  = dirname($from);
        $archiveFileName = "{$fileBaseName}.tar.gz";

        exec("cd {$fileDirName} && tar czf {$archiveFileName} $fileBaseName");
        exec("cp {$fileDirName}/{$archiveFileName} $to");

        $this->output->success("Successfully backup.");
    }

    private function purgeDir(string $from, string $to)
    {
        if (!file_exists($to)) { $this->output->error("Unable to find $to"); }

        // TODO: Do something.
//        exec("find ${target} -name ${pattern} -mtime +${DAYS_RETENTION} -exec rm -rf {} +")

        $this->output->success("Successfully purged.");
    }
}
