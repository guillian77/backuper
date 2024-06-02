<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Repository\DirectoryRepository;
use App\Serializer\DirectorySerializer;

class DirectoryController extends BaseControler
{
    private DirectoryRepository $directoryRepo;

    private DirectorySerializer $directorySerializer;

    public function index(): string
    {
        $this->directoryRepo = new DirectoryRepository();
        $this->directorySerializer = new DirectorySerializer();

        ($_POST) && $this->handleSubmit();

        $dirs = $this->directoryRepo->findAll();

        return $this->render('configuration.html', [
            "dirs" => $dirs,
            "dir_ids" => array_column($dirs, 'id'),
        ]);
    }

    private function handleSubmit(): void
    {
        $this->saveDirectory($_POST['target_dir'], Directory::TYPE_TARGET);
        $this->saveDirectory($_POST['backup_dir'], Directory::TYPE_BACKUP);

        $this->directoryRepo->deleteByIds(json_decode($_POST['deleted_dirs']));
    }

    private function saveDirectory(array $dirs, string $type): void
    {
        foreach ($dirs as $dirId => $dirPath) {
            $toUpdateDir = $this->directorySerializer->deserialize([
                'id' => $dirId,
                'path' => $dirPath,
                'type' => $type
            ]);

            $this->directoryRepo->save($toUpdateDir);
        }
    }
}
