<?php

namespace App\Command;

use App\App;

class DbTableTruncate extends BaseCommand
{
    const DB_TABLES = [
        'backup_history',
        'configuration',
        'directory',
    ];

    public string $commandName = "db:table:truncate";

    public string $commandDescription = "Allow to truncate db specific table.";
    public function execute(): void
    {
        $tableName = $this->getPositionalParameter(2);

        if (!$tableName) {
            $this->output->error("A table should be specified.");

            return;
        }

        if (!in_array($tableName, self::DB_TABLES)) {
            $this->output->error("$tableName table does not exist.");

            return;
        }

        $res = $this->conn->query("DELETE FROM $tableName;");

        if (!$res) { return; }

        $this->output->success("Migrations successfully executed.");
    }
}
