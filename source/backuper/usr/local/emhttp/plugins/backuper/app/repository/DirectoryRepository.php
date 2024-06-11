<?php

namespace App\Repository;

use App\Entity\Directory;

class DirectoryRepository extends BaseRepository
{
    public function findTargetsDirs()
    {
        return $this->findByType(Directory::TYPE_TARGET);
    }

    public function findBackupsDirs()
    {
        return $this->findByType(Directory::TYPE_BACKUP);
    }

    /**
     * @param string $type
     *
     * @return Directory[]
     */
    public function findByType(string $type): array
    {
        return $this->select("SELECT * FROM directory WHERE type = '{$type}';");
    }

    public function deleteByIds(array $ids)
    {
        if (!$ids) { return null; }

        $stmt = $this->db->conn->prepare("DELETE FROM directory WHERE id IN (:id)");

        $ids = implode(", ", $ids);

        $stmt->bindParam(":id", $ids);

        $this->db->conn->query(str_replace("'", "", $stmt->getSQL(true)));

        $stmt->clear();
    }

    public function updatePaused(bool $pause, int $id): void
    {
        $stmt = $this->db->conn->prepare("UPDATE directory SET paused = :pause WHERE id = :id;");

        $stmt->bindParam(":pause", $pause, SQLITE3_INTEGER);
        $stmt->bindParam(":id", $id);

        dump($stmt->getSQL(true));

        $stmt->execute();
    }
}
