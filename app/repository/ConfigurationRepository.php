<?php

namespace App\Repository;

use App\Entity\Configuration;

class ConfigurationRepository extends BaseRepository
{
    public function findAll(bool $asEntity = false): Configuration | array
    {
        return $this->db->select(
            "SELECT * FROM configuration;",
            ($asEntity) ? Configuration::class : null
        )[0];
    }

    public function findScheduleConf(): Configuration
    {
        return $this->db
            ->select("SELECT * FROM configuration WHERE key = 'schedule';", Configuration::class)[0];
    }

    public function findScheduleAndBackup(bool $asEntity = false)
    {
        return $this->db->select(
            "SELECT * FROM configuration WHERE key IN ('backup_enabled','purge_enabled');",
            ($asEntity) ? Configuration::class : null,
            indexBy: "key"
        );
    }
}