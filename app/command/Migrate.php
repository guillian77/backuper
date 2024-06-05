<?php

namespace App\Command;

use App\App;

class Migrate extends BaseCommand
{
    public string $commandName = "db:migrate";

    public string $commandDescription = "Allow to migrate database from migrations.";
    public function execute(): void
    {
        $pluginPath = App::get()->getConfig()['plugin_path'];
        $migrationPath = $pluginPath.DIRECTORY_SEPARATOR."migrations";

        $migrationFiles = array_filter(scandir($migrationPath), function ($path) {
            return ($path !== "." && $path !== "..");
        });

        foreach ($migrationFiles as $migrationFile) {
            $migrationContent = file_get_contents($migrationPath.DIRECTORY_SEPARATOR.$migrationFile);

            App::get()->getDb()->conn->query($migrationContent);
        }

        $this->output->success("Migrations successfully executed.");
    }
}
