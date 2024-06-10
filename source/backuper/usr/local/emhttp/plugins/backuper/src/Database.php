<?php

namespace src;

use Exception;
use SQLite3;

class Database
{
    private const DB_NAME = "backuper.sqlite3";

    public SQLite3 $conn;
    private static Database|null $_instance = null;

    /**
     * @var string $runningDB Represent database used when application running.
     */
    private string $runningDB;

    /**
     * @var string $persistedDB Database used to persist data into Unraid.
     */
    private string $persistedDB;

    /**
     * Get Database singleton.
     *
     * @param $conf
     *
     * @return Database
     */
    public static function getInstance($conf): Database
    {
        if (!self::$_instance) {
            self::$_instance = new Database($conf);
        }
        return self::$_instance;
    }

    public function __construct(array $conf)
    {
        $this->runningDB = $conf['plugin_path_http'] . "/" . self::DB_NAME;
        $this->persistedDB = $conf['plugin_path_flash'] . "/" . self::DB_NAME;

        if (!file_exists($this->runningDB)) $this->pullPersistedDB();

        $this->conn = new SQLite3($this->runningDB);
    }

    public function __destruct() { $this->persistRunningDB(); }

    private function pullPersistedDB(): void
    {
        if (!file_exists($this->persistedDB)) file_put_contents($this->persistedDB, "");

        $copied = copy($this->persistedDB,  $this->runningDB);

        if ($copied) return;

        throw new Exception("Unable to pull persisted database.");
    }

    private function persistRunningDB(): void
    {
        $copied = copy($this->runningDB, $this->persistedDB);

        if ($copied) return;

        throw new Exception("Unable to persist running database into flash.");
    }
}
