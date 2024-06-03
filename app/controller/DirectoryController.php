<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Repository\ConfigurationRepository;
use App\Repository\DirectoryRepository;
use App\Serializer\DirectorySerializer;

class DirectoryController extends BaseControler
{
    private DirectoryRepository $directoryRepo;

    private DirectorySerializer $directorySerializer;

    private ConfigurationRepository $configurationRepo;

    public function __construct()
    {
        parent::__construct();

        $this->directoryRepo = new DirectoryRepository();
        $this->configurationRepo = new ConfigurationRepository();
        $this->directorySerializer = new DirectorySerializer();
    }

    public function index(): string
    {
        ($_POST) && $this->handleSubmit();

        $dirs = $this->directoryRepo->findAll();
        $conf = $this->configurationRepo->findAll();

        return $this->render('backuper.html', [
            "dirs" => $dirs,
            "conf" => $conf,
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
