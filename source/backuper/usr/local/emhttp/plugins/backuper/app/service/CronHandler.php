<?php

namespace App\Service;

use App\App;
use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;

class CronHandler
{
    const CRONS = [
        'minute' =>  "*/1 * * * *",
        'hourly' =>  "0 */1 * * *",
        'daily' =>   "0 0 */1 * *",
        'monthly' => "0 0 * */1 *",
        'yearly' =>  "0 0 1 1 *",
    ];

    public function generate()
    {
        $confRepo = new ConfigurationRepository();
        $conf = $confRepo->findAll(true)[0];
        $bootPlugin = App::get()->getConfig()['plugin_path_boot'];
        $emHttpPlugin = App::get()->getConfig()['plugin_path'];
        $cronPath = "{$bootPlugin}/backuper.cron";

        $command = $this->getCommandFromConf($conf);

        $relatedCron = self::CRONS[$conf->getScheduleType()];
        file_put_contents(
            $cronPath,
            "{$relatedCron} {$emHttpPlugin}/bin/console {$command}\n"
        );

        exec("/usr/local/sbin/update_cron");
    }

    private function getCommandFromConf(Configuration $conf): ?string
    {
        $command = "";

        $retention = $conf->getRetentionDays();
        $encrypt = ($conf->getEncryptEnabled()) ? "-e" : "";

        if ($conf->getBackupEnabled()) {
            $command = "backup $encrypt";
        }
        else if ($conf->getPurgeEnabled()) {
            $command = "purge --days=$retention";
        }

        if ($conf->getPurgeEnabled() && $conf->getBackupEnabled()) {
            $command = "backup_and_purge $encrypt --days=$retention";
        }

        return trim($command, " ");
    }
}
