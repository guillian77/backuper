<?php

namespace App\Command;

use App\Entity\History;
use DateTime;

class BackupAndPurge extends BaseCommand
{
    public string $commandName = "backup_and_purge";

    public string $commandDescription = "Manually launch backup and purge.";
    private Backup $backupCommand;
    private Purge $purgeCommand;

    public function __construct(array $argv)
    {
        parent::__construct($argv);

        $this->backupCommand = new Backup($argv);
        $this->purgeCommand = new Purge($argv);
    }

    public function commandUsage(): string
    {
        $usage  = "    -e | --encrypt - Enable backup with age encryption.\n";
        $usage .= "    --days=<DAY_NUMBER (default: 7)> - Allow to specify retention day.";

        return $usage;
    }

    public function execute(): void
    {
        $history = new History();

        $this->output->title("Backup and Purge");

        $this->backupCommand
            ->setHistory($history)
            ->execute();

        $this->purgeCommand
            ->setHistory($history)
            ->execute();

        $encrypt = ($this->getOption("encrypt", false) || $this->hasArgument("e"));
        $runType = ($encrypt) ? History::RUN_TYPE_ALL_ENCRYPTED : History::RUN_TYPE_ALL;

        $history
            ->setRunType($runType)
            ->setTargetNumber($history->getTargetNumber()/2)
            ->setFinishedAt(new DateTime())
            ->upsert();
    }
}
