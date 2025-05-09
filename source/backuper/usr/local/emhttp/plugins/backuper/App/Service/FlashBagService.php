<?php

namespace app\service;

class FlashBagService
{
    const TYPE_INFO = "info";
    const TYPE_SUCCESS = "success";
    const TYPE_ERROR = "error";

    const FLASH_TYPES = [
        self::TYPE_INFO,
        self::TYPE_SUCCESS,
        self::TYPE_ERROR,
    ];

    private array $flash = [];

    public function __destruct()
    {
        $_SESSION['flash'] = $this->flash;
    }

    public function add(string $type, string $message): void
    {
        if (!in_array($type, self::FLASH_TYPES)) {
            throw new \Exception("$type is not a valid flash type.");
        }

        $this->flash[] = [
            'message' => $message,
            'type' => $type
        ];
    }

    public function read()
    {
        if (!isset($_SESSION['flash'])) {
            return [];
        }

        $flashes = $_SESSION['flash'];

        $_SESSION['flash'] = [];

        return $flashes;
    }
}
