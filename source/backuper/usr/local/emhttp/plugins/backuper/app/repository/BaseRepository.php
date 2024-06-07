<?php

namespace App\Repository;

use App\App;
use App\Entity\EntityManager;
use ReflectionProperty;
use Src\Database;

class BaseRepository
{
    public Database $db;
    private EntityManager $entityManager;

    public function __construct() {
        $this->db = App::get()->getDb();

        $this->entityManager = new EntityManager();
    }

    public function select(string $query): array
    {
        $results = $this->db->conn->query($query);

        $formattedData = [];

        $entityName = $this->entityManager->entityFromRepo($this);

        while($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $formattedData[] = $this->entityManager->arrayToEntity($row, $entityName);
        }

        return $formattedData;
    }

    public function findAll(): object | array
    {
        $tableName = $this->entityManager->repoToTable($this);

        return $this->select("SELECT * FROM $tableName;");
    }

    public function upsert(object $entity): object
    {
        if ($this->isInitialized($entity)) {
            $this->update($entity);

            return $entity;
        }

        $this->save($entity);

        return $entity;
    }

    public function update(object $entity): void
    {
        $tableName = $this->getEntityTableName($entity);
        $entityState = $this->currentEntityValues($entity);
        $columnNames = array_keys($entityState);

        $stmt = $this->db->conn->prepare("UPDATE $tableName SET {$this->getPreparedUpdate($columnNames)} WHERE id = {$entity->getId()}");

        foreach ($columnNames as $columnName) {
            $valueToSave = $entityState[$columnName];

            $this->convertType($valueToSave);

            $stmt->bindParam(":{$columnName}", $valueToSave, $this->associateType($valueToSave));

            unset($valueToSave);
        }

        $stmt->execute();
    }

    public function save(object $entity): void
    {
        $tableName = $this->getEntityTableName($entity);

        $entityState = $this->currentEntityValues($entity);

        $columnNames = array_filter( // Let DB handle ID on INSERT.
            array_keys($entityState),
            function($c) { return $c !== "id"; }
        );

        $query = "INSERT INTO $tableName {$this->getPreparedInsert($columnNames)}";

        $stmt = $this->db->conn->prepare($query);

        foreach ($columnNames as $columnName) {
            $valueToSave = $entityState[$columnName];

            $this->convertType($valueToSave);

            $stmt->bindParam(":{$columnName}", $valueToSave, $this->associateType($valueToSave));

            unset($valueToSave);
        }

        $stmt->execute();

        $entity->setId($this->db->conn->lastInsertRowID());
    }

    private function isInitialized(object $entity): bool
    {
        return (new ReflectionProperty($entity, "id"))->isInitialized($entity);
    }

    private function associateType(mixed $value): int
    {
        switch (gettype($value)) {
            case "integer":
            case "boolean":
                return SQLITE3_INTEGER;
            default:
                return SQLITE3_TEXT;
        }
    }

    private function convertType(mixed &$value): mixed
    {
        if (is_a($value, 'DateTime')) {
            $value = $value->format('Y-m-d H:i:s');
        }

        return $value;
    }

    private function getPreparedInsert(array $columnNames): string
    {
        $fieldString = "";
        $valueString = "";

        foreach ($columnNames as $columnName) {
            $fieldString .= "{$columnName}, ";
            $valueString .= ":{$columnName}, ";
        }

        $fieldString = trim($fieldString, ", ");
        $fieldString = "({$fieldString})";

        $valueString = trim($valueString, ", ");
        $valueString = "({$valueString})";

        return "{$fieldString} VALUES {$valueString}";
    }

    private function getPreparedUpdate(array $columnNames): string
    {
        $updateQueryParams = "";

        foreach ($columnNames as $columnName) {
            $updateQueryParams .= "$columnName = :{$columnName}, ";
        }

        return trim($updateQueryParams, ", ");
    }

    private function getEntityTableName(object $entity): string
    {
        $explode = explode("\\", get_class($entity));

        $tableName = end($explode);
        $tableName = implode('_', preg_split('/(?=[A-Z])/', $tableName));

        return  strtolower(trim($tableName, "_"));
    }

    private function currentEntityValues($entity): array
    {
        $objectVars = get_mangled_object_vars($entity);

        $cleanedProperties = [];

        foreach ($objectVars as $propertyName => $propertyValue) {
            $columnName = trim(str_replace(get_class($entity), "", $propertyName));
            $columnName = implode('_', preg_split('/(?=[A-Z])/', $columnName));
            $columnName = strtolower($columnName);

            $cleanedProperties[$columnName] = $propertyValue;
        }

        return $cleanedProperties;
    }
}
