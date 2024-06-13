<?php

namespace app\controller;

use app\service\FileBrowserService;

class BrowserController extends BaseController
{
    private FileBrowserService $fileBrowser;

    public function __construct()
    {
        $this->fileBrowser = new FileBrowserService();
    }

    public function browseAction(?string $target = null): string
    {
        return $this->jsonResponse($this->fileBrowser->scan($target));
    }
}
