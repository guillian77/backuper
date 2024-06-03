<?php

namespace App\Repository;

class ConfigurationRepository extends BaseRepository
{
    public function findAll(): array
    {
        return $this->db->select("SELECT * FROM configuration;");
    }
}
