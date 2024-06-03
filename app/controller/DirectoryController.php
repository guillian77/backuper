<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Repository\ConfigurationRepository;
use App\Repository\DirectoryRepository;
use App\Serializer\ConfigurationSerializer;
use App\Serializer\DirectorySerializer;

class DirectoryController extends BaseControler
{
    private DirectoryRepository $directoryRepo;

    private DirectorySerializer $directorySerializer;

    private ConfigurationRepository $configurationRepo;

    private ConfigurationSerializer $configurationSerializer;

    public function __construct()
    {
        parent::__construct();

        $this->directoryRepo = new DirectoryRepository();
        $this->configurationRepo = new ConfigurationRepository();
        $this->directorySerializer = new DirectorySerializer();
        $this->configurationSerializer = new ConfigurationSerializer();
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

        $this->saveConfiguration($_POST['conf']);
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

    private function saveConfiguration(array $conf): void
    {
        $this->configurationRepo->save($this->configurationSerializer->deserialize(
            'backup_enabled',
            isset($conf['backup_enabled']) ? 1 : 0
        ));

        $this->configurationRepo->save($this->configurationSerializer->deserialize(
            'purge_enabled',
            isset($conf['purge_enabled']) ? 1 : 0
        ));

        $this->configurationRepo->save($this->configurationSerializer->deserialize(
            'retention_days',
            $conf['retention_days']
        ));

        $this->configurationRepo->save($this->configurationSerializer->deserialize(
            'schedule',
            $conf['schedule']
        ));
    }
}
