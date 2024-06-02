<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Repository\DirectoryRepository;

class DirectoryController extends BaseControler
{
    public function index(): string
    {
        ($_POST) && $this->handleSubmit();

        $repo = new DirectoryRepository();

        $backupDirs = $repo->findAllByType("backup");
        $targetDirs = $repo->findAllByType("target");

        return $this->render('configuration.html', [
            "backup_dirs" => array_map([$this, "serialize"], $backupDirs),
            "target_dirs" => array_map([$this, "serialize"], $targetDirs),
        ]);
    }

    private function handleSubmit(): void
    {
        $this->saveDirectory($_POST['target_dir'], Directory::TYPE_TARGET);
        $this->saveDirectory($_POST['backup_dir'], Directory::TYPE_BACKUP);
    }

    private function saveDirectory(array $dirs, string $type): void
    {
        $repo = new DirectoryRepository();

        foreach ($dirs as $dirId => $dirPath) {
            $toUpdateDir = $this->deserialize([
                'id' => $dirId,
                'path' => $dirPath,
                'type' => $type
            ]);

            $repo->save($toUpdateDir);
        }
    }

    /**
     * Convert entity to serialized data.
     *
     * TODO: Create a default serializer.
     *
     * @param Directory $directory Directory entity to serialize.
     * @return array
     */
    private function serialize(Directory $directory): array
    {
        return [
            "id" => $directory->getId(),
            "path" => $directory->getPath(),
            "type" => $directory->getType(),
        ];
    }

    /**
     * Convert standard data to entity.
     *
     * TODO: Create a default deserializer.
     *
     * @param array $directory Directory to deserialize.
     *
     * @return Directory
     */
    private function deserialize(array $directory): Directory
    {
        return (new Directory())
            ->setId($directory['id'])
            ->setPath($directory['path'])
            ->setType($directory['type'])
        ;
    }
}
