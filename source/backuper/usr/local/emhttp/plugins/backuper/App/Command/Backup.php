<?php

namespace App\Command;

use App\Entity\History;
use App\Repository\DirectoryRepository;
use App\Service\BackupService;
use DateTime;

class Backup extends BaseCommand
{
    public string $commandName = "backup";

    public string $commandDescription = "Manually launch backups.";
    private ?History $history = null;

    public function commandUsage(): string
    {
        return "    -e | --encrypt - Enable backup with age encryption.";
    }

    public function execute(): void
    {
        $this->output->title("Start backup");

        $encrypt = ($this->getOption("encrypt", false) || $this->hasArgument("e"));

        $this->output->info("Encryption: " . (($encrypt) ? "enabled" : "disabled (default"));

        $targets = (new DirectoryRepository())->findTargetsDirs();
        $backupService = new BackupService();

        $runType = ($encrypt) ? History::RUN_TYPE_BACKUP_ENCRYPTED : History::RUN_TYPE_BACKUP;

        $history = $this->getHistory();
        $history
            ->setStartedAt(new \DateTime())
            ->setStatus(History::STATUS_START)
            ->setRunType($runType)
            ->upsert();

        foreach ($targets as $target) {
            $backupService->run($target->getPath(), $encrypt, $history);

            $history
                ->incrementTargetNumber()
                ->upsert();
        }

        $history
            ->setFinishedAt(new DateTime())
            ->setStatus(History::STATUS_END)
            ->upsert();

        $this->output->success("Backup {$history->getBackupNumber()} directories on {$history->getTargetNumber()} target(s).");
    }

    public function setHistory(History $history): self
    {
        $this->history = $history;

        return $this;
    }

    public function getHistory(): History
    {
        if (!$this->history) {
            return new History();
        }

        return $this->history;
    }
}
