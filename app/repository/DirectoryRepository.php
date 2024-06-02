<?php

namespace App\Repository;

use App\Entity\Directory;

class DirectoryRepository extends BaseRepository
{
    /**
     * @param string $type
     *
     * @return Directory[]
     */
    public function findAllByType(string $type): array
    {
        return $this->db->select("SELECT * FROM directory WHERE type = '{$type}';", Directory::class);
    }
}
