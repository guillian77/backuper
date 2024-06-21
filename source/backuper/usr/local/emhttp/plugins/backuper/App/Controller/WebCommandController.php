<?php

namespace app\controller;

use App\App;
use App\Service\FlashBagService;
use App\Service\RequestService;

class WebCommandController
{
    const COMMANDS = [
        'all' => 'backup_and_purge',
        'backup' => 'backup',
        'purge' => 'purge',
    ];

    const MESSAGES = [
        'all' => "Backup and purge",
        'backup' => "Backup",
        'purge' => 'Purge',
    ];

    private RequestService $request;
    private FlashBagService $flashBag;

    public function __construct()
    {
        $this->request = new RequestService();
        $this->flashBag = new FlashBagService();
    }

    public function dispatch()
    {
        $action = $this->request->get('action');

        $pid = $this->callCommand($action);

        if (!$pid) {
            $this->flashBag->add(FlashBagService::TYPE_ERROR, "Unable to execute $action");
        }

        $message = self::MESSAGES[$action] . " has been launched successfully. <a href='#healthcheck'>Monitor it</a>.";

        $this->flashBag->add(FlashBagService::TYPE_SUCCESS, $message);

        // TODO: Create a ".lock" file to prevent multiple processes.
        // TODO: Maybe save PID inside to give opportunity to kill it more easily.

        $this->request->redirect('/Backuper#healthcheck');
    }

    /**
     * Allow to call console command.
     *
     * @param $action
     *
     * @return int|null Process ID
     */
    private function callCommand($action): ?int
    {
        $bin = App::get()->getConfig()['plugin_path_http'] . "/bin";

        if (!isset(self::COMMANDS[$action])) {
            return false;
        }

        $command = self::COMMANDS[$action];

        $processId =  "php $bin/console $command > /dev/null 2>&1 & echo $!; ";

        return exec($processId);
    }
}
