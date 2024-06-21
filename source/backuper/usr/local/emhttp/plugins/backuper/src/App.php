<?php

namespace Src;

class App
{
    private static $appInstance = null;

    private ?string $pluginPath = null;

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
            register_shutdown_function([$this, 'shutdown']);
        }

        return $this;
    }

    public function getDb(): Database
    {
        return Database::getInstance($this->config);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    private function shutdown(): void
    {
        $error = error_get_last();

        if ($error['type'] === E_ERROR) {
            dd($error);
        }

        if (!$error) { return; }

        dump($error);
    }
}
