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

    public function generate()
    {
        $confRepo = new ConfigurationRepository();

        $conf = $confRepo->findAll(true)[0];

        $relatedCron = self::CRONS[$conf->getScheduleType()];

        $pluginPath = App::get()->getConfig()['plugin_path_boot'];

        $cronPath = "{$pluginPath}/backuper.cron";
        $cronCommand = "{$pluginPath}/bin/console backup";

        file_put_contents(
            $cronPath,
            "{$relatedCron} {$cronCommand}\n"
        );

        exec("/usr/local/sbin/update_cron");
    }
}
