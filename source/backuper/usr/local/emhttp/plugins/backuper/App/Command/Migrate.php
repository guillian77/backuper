<?php

namespace App\Command;

use App\App;
use App\Entity\Migration;
use App\Repository\MigrationRepository;
use DateTime;

class Migrate extends BaseCommand
{
    public string $commandName = "db:migrate";
    public string $commandDescription = "Allow to migrate database from migrations.";

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
            $exist = $this->migrationRepo->findByName($migrationFile);

            if ($exist) {
                $this->output->info("$migrationFile already executed.");

                continue;
            }

            $this->executeQuery($migrationFile);

            (new Migration())
                ->setName($migrationFile)
                ->setExecution(new DateTime("now"))
                ->upsert();

            $this->output->info("$migrationFile has been executed.");
        }

        $this->output->success("Migrations successfully executed.");
    }

    private function createMigrationTableIfNotExist(): void
    {
        $this->conn->query("CREATE TABLE IF NOT EXISTS migration(id INTEGER PRIMARY KEY, name TEXT NOT NULL, execution DATETIME NOT NULL);");
    }

    private function executeQuery(string $migrationFile): void
    {
        $migrationContent = file_get_contents( "{$this->migrationPath}/{$migrationFile}");

        $executed = $this->conn->query($migrationContent);

        if ($executed) { return; }

        $this->output->error("Failed to apply migration $migrationFile");

        die(1);
    }
}
