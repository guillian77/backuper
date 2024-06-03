<?php

namespace Src;

use Exception;
use ReflectionClass;
use ReflectionException;
use SQLite3;

class Database
{
    public SQLite3 $conn;

    public function __construct(string $db_file)
    {
        if (!file_exists($db_file)) {
            throw new Exception("Missing database file.");
        }

        $this->conn = new SQLite3($db_file);
    }

    public function select(string $query, string $entity = null): array
    {
        $results = $this->conn->query($query);

        $formattedData = [];
        while($row = $results->fetchArray(SQLITE3_ASSOC)){
            $formattedData[] = $this->buildResult($row, $entity);
        }

        return $formattedData;
    }

    /**
     * @throws ReflectionException
     */
    private function buildResult(array $row, ?string $entity): Object|array
    {
        if (!$entity) {
            return $row;
        }

        $classInstance = new $entity;

        $reflectedProperties = (new ReflectionClass($entity))->getProperties();

        foreach ($reflectedProperties as $reflectedProperty) {
            $propertyName = $reflectedProperty->name;
            $setterMethod = "set" . ucfirst($propertyName);
            call_user_func([$classInstance, $setterMethod], $row[$propertyName]);
        }

        return $classInstance;
    }
}
