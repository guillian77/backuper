<?php

namespace app\repository;

class MigrationRepository extends BaseRepository
{
    public function findByName(string $name): false|\SQLite3Result
    {
        return $this->db->conn->query("SELECT * FROM migration WHERE name = '$name'");
    }
}
