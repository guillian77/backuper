<?php

namespace app\serializer;

use App\Entity\Migration;
use DateTime;

class MigrationSerializer
{
    public function serialize(Migration $migration): array
    {
        return [
            'id' => $migration->getId(),
            'name' => $migration->getName(),
            'execution' => $migration->getExecution(),
        ];
    }

    public function deserialize(array $migration): Migration
    {
        return (new Migration())
            ->setId($migration['id'])
            ->setName($migration['name'])
            ->setExecution(new DateTime($migration['execution']));
    }
}
