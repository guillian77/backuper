<?php

namespace App\Repository;

use App\App;
use Src\Database;

class BaseRepository
{
    protected Database $db;

    public function __construct()
    {
        $this->db = App::get()->getDb();
    }

    public function save($entity): void
    {
        $tableName = $this->getEntityTableName($entity);

        $entityState = $this->currentEntityValues($entity);

        $columnNames = array_keys($entityState);

        $query = "UPDATE $tableName SET {$this->getPreparedParams($columnNames)} WHERE id = {$entity->getId()}";

        $stmt = $this->db->conn->prepare($query);

        foreach ($columnNames as $columnName) {
            $valueToSave = $entityState[$columnName];

            $stmt->bindParam(":{$columnName}", $valueToSave);

            unset($valueToSave);
        }

        $stmt->execute();
    }

    private function getPreparedParams(array $columnNames): string
    {
        $preparedString = "";

        foreach ($columnNames as $columnName) {
            $preparedString .= "$columnName = :{$columnName}, ";
        }

        return trim($preparedString, ", ");
    }

    private function getEntityTableName(object $entity): string
    {
        $explode = explode("\\", get_class($entity));

        return strtolower(end($explode));
    }

    private function currentEntityValues($entity, bool $skipId = true): array
    {
        $objectVars = get_mangled_object_vars($entity);

        $cleanedProperties = [];

        $entityColumns = [];
        $entityColumnsValues = [];

        foreach ($objectVars as $propertyName => $propertyValue) {
            $columnName = trim(str_replace(get_class($entity), "", $propertyName));

            if ($columnName == "id" && $skipId) {
                continue;
            }

            $cleanedProperties[$columnName] = $propertyValue;
        }

        return $cleanedProperties;
    }
}
