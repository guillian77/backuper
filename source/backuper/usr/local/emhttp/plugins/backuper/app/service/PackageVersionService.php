<?php

namespace app\service;

use app\App;

class PackageVersionService
{
    const VERSIONS = [
        'local' => 'localVersionPath',
        'remote' => 'remoteVersionPath',
    ];

    private string $localVersionPath;
    private string $remoteVersionPath;

    public function __construct()
    {
        $conf = App::get()->getConfig();

        $this->localVersionPath = $conf['local_plg'];
        $this->remoteVersionPath = $conf['remote_plg'];
    }

    public function hasNew(): bool
    {
        $local = $this->getVersion("remote");
        $remote = $this->getVersion("local");

        return $remote > $local;
    }

    public function getVersion(string $type)
    {
        $type = self::VERSIONS[$type];

        // TODO: Handle remote timeout/non exist.
        $fileContent = file_get_contents($this->$type, false, $this->customCtx());

        preg_match("/[0-9]{12}/", $fileContent, $match);

        return $match;
    }

    private function customCtx()
    {
        return stream_context_create(['http'=> ['timeout' => 180]]);
    }
}
