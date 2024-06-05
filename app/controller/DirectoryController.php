<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Repository\ConfigurationRepository;
use App\Repository\DirectoryRepository;
use App\Serializer\ConfigurationSerializer;
use App\Serializer\DirectorySerializer;
use App\Service\CronHandler;
use App\Service\RequestService;

class DirectoryController extends BaseControler
{
    private DirectoryRepository $directoryRepo;
    private DirectorySerializer $directorySerializer;
    private ConfigurationRepository $configurationRepo;
    private ConfigurationSerializer $configurationSerializer;
    private RequestService $request;
    private CronHandler $cronHandler;

    public function __construct()
    {
        parent::__construct();

        $this->directoryRepo = new DirectoryRepository();
        $this->configurationRepo = new ConfigurationRepository();
        $this->directorySerializer = new DirectorySerializer();
        $this->configurationSerializer = new ConfigurationSerializer();
        $this->request = new RequestService();
        $this->cronHandler = new CronHandler();
    }

    public function index(): string
    {
        ($_POST) && $this->handleSubmit();

        $dirs = $this->directoryRepo->findAll();
        $conf = $this->configurationRepo->findAll();

        return $this->render('backuper.html', [
            "dirs" => $dirs,
            "conf" => $conf,
        ]);
    }

    private function handleSubmit(): void
    {
        $this->directoryRepo->deleteByIds(
            json_decode($this->request->post('deleted_dirs'))
        );

        $this->saveDirectory($this->request->post("target_dir"), Directory::TYPE_TARGET);
        $this->saveDirectory($this->request->post("backup_dir"), Directory::TYPE_BACKUP);

        $this->configurationRepo->upsert(
            $this->configurationSerializer->deserialize($this->request->post('conf'))
        );

        $this->cronHandler->generate();
    }

    private function saveDirectory(?array $dirs, string $type): void
    {
        foreach ($dirs as $dirId => $dirPath) {
            $toUpdateDir = $this->directorySerializer->deserialize([
                'id' => $dirId,
                'path' => $dirPath,
                'type' => $type
            ]);

            $this->directoryRepo->upsert($toUpdateDir);
        }
    }
}
