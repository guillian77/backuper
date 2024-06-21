<?php

namespace App\Service;

use DateTime;

class DirectoryService
{
    public function scan(string $path)
    {
        if (!file_exists($path)) {
            throw new \Exception("$path not found.");
        }

        $dirs = scandir($path);

        $dirsInfo = [];
        foreach ($dirs as $key => $dir) {
            $fullPath = $path .DIRECTORY_SEPARATOR . $dir;
            $timeStamp = filemtime($fullPath);
            $dateTime = (new DateTime())->setTimestamp($timeStamp);

            $dirsInfo[$key]['name'] = $dir;
            $dirsInfo[$key]['path'] = $fullPath;
            $dirsInfo[$key]['mtime'] = $timeStamp;
            $dirsInfo[$key]['date'] = date("Ymd", $timeStamp);
            $dirsInfo[$key]['datetime'] = $dateTime;
            $dirsInfo[$key]['date_diff'] = (new DateTime())->diff($dateTime);
        }

        return $dirsInfo;
    }

    public function oldThan(array $dirs, int $number, string $periodSelector)
    {
        return array_filter($dirs, function ($dir) use ($periodSelector, $number) {
            return $dir['date_diff']->$periodSelector > $number;
        });
    }
}
