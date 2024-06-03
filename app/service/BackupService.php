<?php

namespace App\Service;

use App\Repository\ConfigurationRepository;

class BackupService
{
    private ConfigurationRepository $confRepo;
    private array $conf;

    public function __construct()
    {
        $this->confRepo = new ConfigurationRepository();

        $this->conf = $this->confRepo->findScheduleAndBackup(true);

        $this->conf['backup_enabled']->getValue() && $this->handleBackup();
        $this->conf['purge_enabled']->getValue() && $this->handlePurge();
    }

    private function handleBackup()
    {
        echo "handle backup start\n";
    }

    private function handlePurge()
    {
        echo "handle purge start\n";
    }
}
