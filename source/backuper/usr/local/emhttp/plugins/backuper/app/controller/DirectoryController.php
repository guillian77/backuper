<?php

namespace App\Controller;

use App\Entity\Directory;
use App\Repository\BackupHistoryRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\DirectoryRepository;
use App\Serializer\ConfigurationSerializer;
use App\Serializer\DirectorySerializer;
use App\Service\CronHandler;
use App\Service\RequestService;

class DirectoryController extends BaseController
{
    private DirectoryRepository $directoryRepo;
    private DirectorySerializer $directorySerializer;
    private ConfigurationRepository $configurationRepo;
    private BackupHistoryRepository $historyRepo;
    private ConfigurationSerializer $configurationSerializer;
    private RequestService $request;
    private CronHandler $cronHandler;

    public function __construct()
    {
        parent::__construct();

        $this->directoryRepo = new DirectoryRepository();
        $this->configurationRepo = new ConfigurationRepository();
        $this->historyRepo = new BackupHistoryRepository();
        $this->directorySerializer = new DirectorySerializer();
        $this->configurationSerializer = new ConfigurationSerializer();
        $this->request = new RequestService();
        $this->cronHandler = new CronHandler();
    }

    public function index(): string
    {
        ($_POST) && $this->handleSubmit();

        return $this->render('backuper.html', [
            "dirs" => $this->directoryRepo->findAll(),
            "conf" => $this->configurationRepo->findAll()[0],
            "history" => $this->historyRepo->findAll(),
        ]);
    }

    private function handleSubmit(): void
    {
        $this->directoryRepo->deleteByIds(
            json_decode($this->request->post('deleted_dirs'))
        );

        $this->saveDirectory($this->request->post("target_dir", []), Directory::TYPE_TARGET);
        $this->saveDirectory($this->request->post("backup_dir", []), Directory::TYPE_BACKUP);

        $this->configurationRepo->upsert(
            $this->configurationSerializer->deserialize($this->request->post('conf'))
        );

        $this->cronHandler->generate();
    }

    private function saveDirectory(?array $dirs, string $type): void
    {
        foreach ($dirs as $dirId => $dirPath) {
            $toUpdateDir = $this->directorySerializer->deserialize([
                'id' => $dirId == "new" ? null : $dirId,
                'path' => $dirPath,
                'type' => $type
            ]);

            $this->directoryRepo->upsert($toUpdateDir);
        }
    }
}
