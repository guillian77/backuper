<?php

namespace App\Controller;

use App\App;
use App\Entity\Configuration;
use App\Entity\Directory;
use App\Repository\BackupHistoryRepository;
use App\Repository\ConfigurationRepository;
use App\Repository\DirectoryRepository;
use App\Serializer\ConfigurationSerializer;
use App\Serializer\DirectorySerializer;
use App\Service\AgeEncryptService;
use App\Service\CronHandler;
use app\service\FlashBagService;
use app\service\PackageVersionService;
use App\Service\RequestService;
use Exception;

class DirectoryController extends BaseController
{
    private DirectoryRepository $directoryRepo;
    private DirectorySerializer $directorySerializer;
    private ConfigurationRepository $configurationRepo;
    private BackupHistoryRepository $historyRepo;
    private ConfigurationSerializer $configurationSerializer;
    private RequestService $request;
    private CronHandler $cronHandler;
    private AgeEncryptService $encryptService;
    private FlashBagService $flashBag;
    private PackageVersionService $version;

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
        $this->encryptService = new AgeEncryptService();
        $this->flashBag = new FlashBagService();
        $this->version = new PackageVersionService();
    }

    /**
     * @throws Exception
     */
    public function index(): string
    {
        $this->displayVersionFlash();

        ($_POST) && $this->handleSubmit();

        $conf = $this->configurationRepo->findAll()[0];

        $this->createKeyIfNotExist($conf);

        return $this->render('backuper.html', [
            "dirs" => $this->directoryRepo->findAll(),
            "conf" => $conf,
            "history" => $this->historyRepo->findAll(limit: 10, orderBy: "id DESC"),
            "age_file" => "data:text/plain;base64," . base64_encode($this->encryptService->getEntireKey()),
            "flashes" => $this->flashBag->read(),
            "dev" => App::get()->getConfig()['dev_mode']
        ]);
    }

    /**
     * Allow to pause/unpause a directory from backup list.
     *
     * @param string $pause pause/unpause.
     * @param int $id
     *
     * @return string
     */
    public function pauseAction(string $pause, int $id): string
    {
        $pause = ($pause === "true");

        $this->directoryRepo->updatePaused($pause, $id);

        return $this->jsonResponse("success");
    }

    private function displayVersionFlash(): void
    {
        if (!$this->version->hasNew()) {
            return;
        }

        $message  = "A new version of Backuper is available.";
        $message .= " <a href='/Plugins'>Update Backuper</a>";

        $this->flashBag->add("info", $message);
    }

    /**
     * Generate an age key file if not exist yet.
     *
     * @param Configuration $conf
     *
     * @return void
     *
     * @throws Exception
     */
    private function createKeyIfNotExist(Configuration $conf): void
    {
        if ($this->encryptService->hasEncryptionFile() && $conf->getEncryptionKey()) {
            return;
        }

        $this->encryptService->generateKeyFile();

        $this->flashBag->add(FlashBagService::TYPE_SUCCESS, "Age encryption key has been generated.");

        $key = $this->encryptService->getPublicKey();

        $conf
            ->setEncryptionKey($key)
            ->upsert();
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

        $this->flashBag->add("success", "Configuration has been saved.");

        $this->request->redirect("/Backuper");
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
