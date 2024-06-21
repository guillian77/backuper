<?php

namespace app\service;

use App\Entity\History;
use App\Entity\Configuration;
use App\Entity\Directory;
use App\Repository\ConfigurationRepository;
use App\Repository\DirectoryRepository;

class PurgeService
{
    private History $history;
    private Configuration $conf;

    public function __construct()
    {
        $this->history = new History();
        $this->conf = (new ConfigurationRepository())->findAll()[0];
    }

    public function run(History $history = null)
    {
        if ($history) { $this->history = $history; }

        $this->history
            ->setStatus(History::STATUS_PURGE)
            ->upsert();

        $targetDirs = (new DirectoryRepository())->findByType(Directory::TYPE_TARGET);

        foreach ($targetDirs as $targetDir) {
            $this->purgeDir($targetDir->getPath());

            $this->history
                ->incrementTargetNumber()
                ->upsert();
        }
    }

    public function purgeDir(string $directory): void
    {
        $this->history
            ->setStatus(History::STATUS_PURGE)
            ->upsert();

        $dirService = new DirectoryService();

        $dirs = $dirService->scan($directory);

        $olds = $dirService->oldThan($dirs, $this->conf->getRetentionDays(), 'd');

        foreach ($olds as $old) {
            if (is_dir($old['path'])) { continue; }

            unlink($old['path']);

            $this->history
                ->incrementPurgedNumber()
                ->upsert();
        }
    }
}
