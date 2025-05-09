<?php

namespace app\repository;

use App\Entity\Migration;
use app\serializer\MigrationSerializer;
use Doctrine\ORM\EntityRepository;

class MigrationRepository extends EntityRepository
{
    public function findByName(string $name): null|Migration
    {
        $migration = $this->db->conn
            ->query("SELECT * FROM migration WHERE name = '$name' LIMIT 1")
            ->fetchArray(SQLITE3_ASSOC);

        if (!$migration) { return null; }

        return (new MigrationSerializer())->deserialize($migration);
    }
}
