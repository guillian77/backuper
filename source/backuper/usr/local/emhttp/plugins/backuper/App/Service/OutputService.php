<?php

namespace App\Service;

use DateTime;

class OutputService
{
    public function title(string $message): void
    {
        $title  = $this->formatDate() . " ----------------------------------------------------\n";
        $title .= $this->formatDate() . " {$message}\n";
        $title .= $this->formatDate() . " ----------------------------------------------------\n";

        echo $title;
    }

    public function section(string $message): void
    {
        echo $this->formatDate() . " --- {$message}\n";
    }

    public function success($message): void
    {
        echo "{$this->formatDate()} [SUCCESS] {$message}\n";
    }

    public function info($message): void
    {
        echo "{$this->formatDate()} [INFO] {$message}\n";
    }

    public function warning($message): void
    {
        echo "{$this->formatDate()} [WARNING] {$message}\n";
    }

    public function error($message): void
    {
        echo "{$this->formatDate()} [ERROR] {$message}\n";
    }

    public function spaces(int $number): void
    {
        for ($i=0; $i < $number; $i++) {
            echo "\n";
        }
    }

    private function formatDate(): string
    {
        return (new DateTime())->format("Y_m_d_H_i_s");
    }
}
