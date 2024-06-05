<?php

namespace App\Command;

use App\Service\OutputService;

abstract class BaseCommand
{
    protected OutputService $output;

    public string $commandName;

    public string $commandDescription;

    public function __construct()
    {
        $this->output = new OutputService();
    }

    abstract public function execute(): void;
}
