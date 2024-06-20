<?php

namespace app\repository;

use app\entity\Migration;
use app\serializer\MigrationSerializer;

class MigrationRepository extends BaseRepository
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
