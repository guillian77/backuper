<?php

namespace app\command;

class DbTableTruncate extends BaseCommand
{
    const DB_TABLES = [
        'backup_history',
        'configuration',
        'directory',
        'migration',
    ];

    public string $commandName = "db:table:truncate";

    public string $commandDescription = "Allow to truncate db specific table.";

    protected function commandUsage(): string
    {
        $usage  = "    <table name>\n";
        $usage .= "    -a | --all  - Drop or delete data from ALL tables.\n";
        $usage .= "    -d | --drop - Specify the operation type to DROP instead of DELETE.\n";

        return $usage;
    }

    public function execute(): void
    {
        $selectedTables = readline("Table Name (all) :");

        if (!$selectedTables) { $this->output->error("Table(s) should be specified."); return; }

        $tables = self::DB_TABLES;

        if ($selectedTables !== "all") { $tables = [$selectedTables]; }

        array_map([$this, "cleanTable"], $tables);

        $this->output->success("Table cleaned successfully.");
    }

    private function cleanTable(string $table): void
    {
        if (!in_array($table, self::DB_TABLES)) {
            $this->output->error("$table table does not exist.");

            return;
        }


        $queryType = "DELETE FROM";
        if ($this->getOption("drop") || $this->hasArgument("d")) { $queryType = "DROP TABLE"; }

        $query = "$queryType $table;";

        $this->output->info($query);

        $this->conn->query($query);
    }
}
