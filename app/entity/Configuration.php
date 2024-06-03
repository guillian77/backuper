<?php

namespace App\Entity;

class Configuration
{
    public const SCHEDULE_TYPE_HOURLY  = "hourly";
    public const SCHEDULE_TYPE_DAILY   = "daily";
    public const SCHEDULE_TYPE_MONTHLY = "monthly";
    public const SCHEDULE_TYPE_YEARLY  ="yearly";
    public const SCHEDULE_TYPE_CUSTOM  ="custom";

    public const SCHEDULE_TYPES = [
        self::SCHEDULE_TYPE_HOURLY,
        self::SCHEDULE_TYPE_DAILY,
        self::SCHEDULE_TYPE_MONTHLY,
        self::SCHEDULE_TYPE_YEARLY,
        self::SCHEDULE_TYPE_CUSTOM,
    ];

    private string $key;

    private mixed $value;

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }
}
