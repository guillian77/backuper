<?php

namespace App;

use App\Controller\DirectoryController;
use Src\Database;

class App
{
    private static $appInstance = null;

    private ?string $pluginPath = null;

    private Database $db;

    private ?array $config;

    public static function get(): App
    {
        if (self::$appInstance == null) {
            self::$appInstance = new App();
        }

        return self::$appInstance;
    }

    public function boot(string $pluginRoot): self
    {
        $this->pluginPath = $pluginRoot;

        $this->config = include "{$pluginRoot}/config.php";

        if ($this->config['dev_mode']) {
            $this->enablePhpErrors();
        }

        $this->loadRequiredFiles();

        return $this;
    }

    public function getDb(): Database
    {
        return $this->db = new Database("{$this->config['plugin_path_boot']}/backuper.sqlite3");
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    private function enablePhpErrors(): void
    {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    }

    private function loadRequiredFiles()
    {
        // Manually load required files because compose is not available under Unraid OS (Phar ext.).
        require "{$this->pluginPath}/src/functions.php";
        require "{$this->pluginPath}/src/Database.php";

        set_include_path($this->pluginPath . PATH_SEPARATOR . "app");
        spl_autoload_extensions(".php");
        spl_autoload_register(function ($class) {
            $this->autoload($class);
        });
    }

    private function autoload($class)
    {
        $explode = explode("\\", $class);

        $className = end($explode);

        unset($explode[count($explode) - 1]);

        require strtolower(implode(DIRECTORY_SEPARATOR, $explode)) . DIRECTORY_SEPARATOR . "$className.php";
    }
}
