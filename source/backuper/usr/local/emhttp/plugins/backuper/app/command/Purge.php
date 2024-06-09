<?php

namespace App\Command;

use App\Entity\BackupHistory;
use App\Repository\DirectoryRepository;
use App\Service\BackupService;
use app\service\PurgeService;
use DateTime;

class Purge extends BaseCommand
{
    public string $commandName = "purge";

    public string $commandDescription = "Manually launch purge.";
    private ?BackupHistory $history = null;
    private bool $comeFromOutside = false;

    public function commandUsage(): string
    {
        return "    --days=<DAY_NUMBER (default: 7)> - Allow to specify retention day.";
    }

    public function execute(): void
    {
        $this->output->title("Start purge");

        $retention = $this->getOption("days", 7);

        $this->output->info("Retention: $retention days.");

        $history = $this->getHistory();
        $history
            ->setStartedAt(new DateTime())
            ->setStatus(BackupHistory::STATUS_START)
            ->setRunType(BackupHistory::RUN_TYPE_PURGE)
            ->upsert();

        (new PurgeService())->run($history);

        $history
            ->setStatus(BackupHistory::STATUS_END)
            ->setFinishedAt(new DateTime())
            ->upsert();

        $targetCount = $history->getTargetNumber();
        if ($this->comeFromOutside) {
            $targetCount = $targetCount/2;
        }

        $this->output->success("Purge {$history->getPurgedNumber()} directories on {$targetCount} target(s).");
    }

    public function setHistory(BackupHistory $history): self
    {
        $this->history = $history;
        $this->comeFromOutside = true;

        return $this;
    }

    public function getHistory(): BackupHistory
    {
        if (!$this->history) {
            return new BackupHistory();
        }

        return $this->history;
    }
}
