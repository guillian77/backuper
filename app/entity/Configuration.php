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

    private string $configurationKey;

    private string $configurationValue;

    public function getConfigurationKey(): string
    {
        return $this->configurationKey;
    }

    public function setConfigurationKey(string $configurationKey): self
    {
        $this->configurationKey = $configurationKey;

        return $this;
    }

    public function getConfigurationValue(): string
    {
        return $this->configurationValue;
    }

    public function setConfigurationValue(string $configurationValue): self
    {
        $this->configurationValue = $configurationValue;

        return $this;
    }
}
