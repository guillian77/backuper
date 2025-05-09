<?php

namespace Src;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Exception;

class Database
{
    private const DB_NAME = "backuper.sqlite3";

    public Connection $conn;

    public EntityManager $entityManager;
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

        $dsnParser = new DsnParser();
        $connectionParams = $dsnParser->parse('sqlite3:///plugins/backuper/backuper.sqlite3');
        $paths = [$conf['plugin_path_http'] . "/app/entity"];
        $config = ORMSetup::createAttributeMetadataConfiguration($paths, true);

        $config->setNamingStrategy((new UnderscoreNamingStrategy()));

        $this->conn = DriverManager::getConnection($connectionParams);
        $this->entityManager = new EntityManager($this->conn, $config);
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
