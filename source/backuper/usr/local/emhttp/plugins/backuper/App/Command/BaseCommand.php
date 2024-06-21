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

    private array $options = [];

    private array $arguments = [];

    public function __construct(array $argv)
    {
        $this->output = new OutputService();
        
        $this->argv = $argv;

        $this->extractOptionsAndArguments();

        $this->conn = App::get()->getDb()->conn;

        if ($this->getOption("help") || $this->hasArgument("h")) {
            echo $this->commandName . "\n";

            echo $this->commandUsage() . "\n";

            echo "    -h | --help - Display help for this command.\n";

            die();
        }
    }

    protected function commandUsage(): string { return ""; }

    protected function getPositionalParameter(int $number, mixed $default = null)
    {
        if (!isset($this->argv[$number])) {
            return $default;
        }


        return $this->argv[$number];
    }

    /**
     * Return option value.
     *
     * Sample: --with-encryption
     * Sample: --backup-dir=/mnt/appdata/an_app
     *
     * @param string $key
     * @param string|null $default
     *
     * @return string|null
     */
    protected function getOption(string $key, string $default = null): ?string
    {
        if (!isset($this->options[$key])) {
            return $default;
        }

        return $this->options[$key];
    }

    /**
     *
     * @param string $key
     * @return bool
     */
    protected function hasArgument(string $key): bool
    {
        return in_array($key, $this->arguments);
    }

    private function extractOptionsAndArguments()
    {
        foreach ($this->argv as $value) {
            // OPTIONS
            if (str_starts_with($value, "--")) {
                $explode = explode("=", $value);

                $key = str_replace("--", "", $explode[0]);

                if (!isset($explode[1])) {
                    $this->options[$key] = true;

                    continue;
                }

                $this->options[$key] = $explode[1];

                continue;
            }

            // ARGUMENTS
            if (str_starts_with($value,"-")) {
                $arguments = str_split($value);
                unset($arguments[0]);

                $this->arguments = array_merge($this->arguments, $arguments);
            }
        }
    }

    abstract public function execute(): void;
}
