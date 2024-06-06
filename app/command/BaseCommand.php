<?php

namespace App\Command;

use App\App;
use App\Service\OutputService;
use SQLite3;

abstract class BaseCommand
{
    protected OutputService $output;
    protected SQLite3 $conn;

    public string $commandName;
    public string $commandDescription;
    private array $argv;

    public function __construct(array $argv)
    {
        $this->output = new OutputService();
        
        $this->argv = $argv;

        $this->conn = App::get()->getDb()->conn;
    }

    protected function getParameter(int $number, mixed $default = null)
    {
        if (!isset($this->argv[$number])) {
            return $default;
        }


        return $this->argv[$number];
    }

    abstract public function execute(): void;
}
