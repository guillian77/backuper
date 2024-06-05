<?php

namespace App\Repository;

use App\App;
use Src\Database;

class BaseRepository
{
    public Database $db;

    public function __construct()
    {
        $this->db = App::get()->getDb();
    }

    public function upsert(object $entity): void
    {
        $existingRow = $this->db->conn
            ->query("SELECT id FROM {$this->getEntityTableName($entity)} WHERE id = {$entity->getId()}")
            ->fetchArray(SQLITE3_ASSOC);

        if ($existingRow) {
            $this->update($entity);

            return;
        }

        $this->save($entity);
    }

    public function update(object $entity): void
    {
        $tableName = $this->getEntityTableName($entity);
        $entityState = $this->currentEntityValues($entity);
        $columnNames = array_keys($entityState);

        $stmt = $this->db->conn->prepare("UPDATE $tableName SET {$this->getPreparedUpdate($columnNames)} WHERE id = {$entity->getId()}");

        foreach ($columnNames as $columnName) {
            $valueToSave = $entityState[$columnName];

            $stmt->bindParam(":{$columnName}", $valueToSave);

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

            $stmt->bindParam(":{$columnName}", $valueToSave);

            unset($valueToSave);
        }

        $stmt->execute();
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

        return strtolower(end($explode));
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
