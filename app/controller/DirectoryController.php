<?php

namespace App\Controller;

use App\Repository\DirectoryRepository;

class DirectoryController extends BaseControler
{
    public function index(): string
    {
        $repo = new DirectoryRepository();

        $backupDirs = $repo->findAllByType("backup");

        return json_encode([
            'backups' => array_map([$this, "serialize"], $backupDirs),
            'target' => [],
        ]);
    }

    public function edit(): void
    {
        $request = $_POST;
    }

    private function update()
    {

    }

    public function serialize($directory): array
    {
        return [
            "id" => $directory->getId(),
            "path" => $directory->getPath(),
            "type" => $directory->getType(),
        ];
    }
}
