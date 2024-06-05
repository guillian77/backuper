<?php

namespace App\Service;

use App\App;
use App\Repository\ConfigurationRepository;

class CronHandler
{
    const CRONS = [
        'hourly' =>  "0 */1 * * *",
        'daily' =>   "0 0 */1 * *",
        'monthly' => "0 0 * */1 *",
        'yearly' =>  "0 0 1 1 *",
    ];

    const DS = DIRECTORY_SEPARATOR;

    public function generate()
    {
        $confRepo = new ConfigurationRepository();

        $conf = $confRepo->findAll(true);

        $relatedCron = self::CRONS[$conf->getScheduleType()];

        $pluginPath = App::get()->getConfig()['plugin_path'];

        $cronPath = $pluginPath .self::DS . "backuper.cron";
        $cronBinaryPath = $pluginPath . self::DS . "bin" . self::DS . "startSchedule.php";

        file_put_contents($cronPath, "{$relatedCron} {$cronBinaryPath}\n");
    }
}
