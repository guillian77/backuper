<?php

namespace app\command;

use App\App;
use app\entity\Migration;
use app\repository\MigrationRepository;
use DateTime;

class MigrationStatus extends BaseCommand
{
    public string $commandName = "db:migration:status";
    public string $commandDescription = "Allow to monitor migrations status.";

    private MigrationRepository $migrationRepo;
    private mixed $pluginPath;
    private string $migrationPath;

    public function __construct(array $argv)
    {
        parent::__construct($argv);

        $this->pluginPath = App::get()->getConfig()['plugin_path_http'];
        $this->migrationPath = $this->pluginPath.DIRECTORY_SEPARATOR."migrations";
        $this->migrationRepo = new MigrationRepository();
    }

    public function execute(): void
    {
        $this->createMigrationTableIfNotExist();

        $migrationFiles = array_filter(scandir($this->migrationPath), function ($path) {
            return ($path !== "." && $path !== "..");
        });

        foreach ($migrationFiles as $migrationFile) {
            $migration = $this->migrationRepo->findByName($migrationFile);

            if (!$migration) {
                echo "|   | $migrationFile |     /      | /  | \n";
                continue;
            }

            $tableString  = "| {$migration->getId()} ";
            $tableString .= "| {$migration->getName()} ";
            $tableString .= "| {$migration->getExecution()->format("Y-m-d")} ";
            $tableString .= "| UP |\n";

            echo $tableString;
        }
    }

    private function createMigrationTableIfNotExist(): void
    {
        $this->conn->query("CREATE TABLE IF NOT EXISTS migration(id INTEGER PRIMARY KEY, name TEXT NOT NULL, execution DATETIME NOT NULL);");
    }
}
