<?php

namespace App\Command;

use App\Service\BackupService;

class Backup extends BaseCommand
{
    public string $commandName = "backup";

    public string $commandDescription = "Manually launch backups.";
    public function execute(): void
    {
        new BackupService();
    }
}
