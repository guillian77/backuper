<?php

namespace App\Entity;

use DateTime;
use ReflectionClass;

class EntityManager
{

    /**
     * Convert "EntityNameRepository" to "EntityName"
     *
     * @param object $repoName
     *
     * @return string
     */
    public function entityFromRepo(object $repoName): string
    {
        $entityName  = "App\\Entity\\";
        $entityName .= $this->classBaseName($repoName);

        return str_replace("Repository", "", $entityName);
    }

    /**
     * Convert "EntityNameRepository" to "entity_name".
     *
     * @param object $repoName
     *
     * @return string
     */
    public function repoToTable(object $repoName): string
    {
        $snakeName = $this->pascalToSnake(get_class($repoName));

        return str_replace("_repository", "", $snakeName);
    }

    /**
     * Convert EntityName::class to entity_name.
     *
     * @param object $entity
     *
     * @return string
     */
    public function pascalToSnake(string $string): string
    {
        $explode = explode("\\", $string);

        $tableName = end($explode);
        $tableName = implode('_', preg_split('/(?=[A-Z])/', $tableName));

        return  strtolower(trim($tableName, "_"));
    }

    public function classBaseName(object $class, bool $toLower = false): string
    {
        $explode = explode("\\", get_class($class));

        $baseName = end($explode);

        if (!$toLower) {
            return $baseName;
        }

        return strtolower($baseName);
    }

    public function getEntitySetters(object $entity)
    {
        $reflexion = new ReflectionClass($entity);
        $methods = $reflexion->getMethods();

        $setters = array_filter($methods, function ($method) {
            return strpos($method, 'set');
        });

        return array_map(function($setter) {
            return $setter->name;
        }, $setters);
    }

    public function columnNameFromMethod(string $methodName)
    {
        return str_replace("set_", "", $this->pascalToSnake($methodName));
    }

    /**
     * Convert data Array to Entity.
     *
     * @param array $row
     * @param string|null $className
     *
     * @return Object
     */
    public function arrayToEntity(array $row, ?string $className): Object
    {
        $classInstance = new $className;

        $setters = $this->getEntitySetters($classInstance);

        foreach ($setters as $setter) {
            $columnName = $this->columnNameFromMethod($setter);

            $value = $this->convertForParam($row[$columnName]);

            call_user_func([$classInstance, $setter], $value);
        }

        return $classInstance;
    }

    private function convertForParam(mixed $value): mixed
    {
        if (!$value) { return $value; }

        $respondToDateTime = is_a(DateTime::createFromFormat('Y-m-d H:i:s', $value) , 'DateTime');

        if ($respondToDateTime) {
            return DateTime::createFromFormat('Y-m-d H:i:s', $value);
        }

        return $value;
    }
}
