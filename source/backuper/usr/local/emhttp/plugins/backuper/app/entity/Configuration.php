<?php

namespace App\Entity;

use Exception;

class Configuration extends BaseEntity
{
    public const SCHEDULE_TYPE_HOURLY  = "hourly";
    public const SCHEDULE_TYPE_DAILY   = "daily";
    public const SCHEDULE_TYPE_MONTHLY = "monthly";
    public const SCHEDULE_TYPE_YEARLY  ="yearly";

    public const SCHEDULE_TYPES = [
        self::SCHEDULE_TYPE_HOURLY,
        self::SCHEDULE_TYPE_DAILY,
        self::SCHEDULE_TYPE_MONTHLY,
        self::SCHEDULE_TYPE_YEARLY,
    ];

    private int $id;

    private bool $backupEnabled;

    private bool $purgeEnabled;

    private bool $encryptEnabled;

    private string $encryptionKey;

    private int $retentionDays;

    private string $scheduleType;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getBackupEnabled(): bool
    {
        return $this->backupEnabled;
    }

    public function setBackupEnabled(bool $backupEnabled): self
    {
        $this->backupEnabled = $backupEnabled;

        return $this;
    }

    public function getPurgeEnabled(): bool
    {
        return $this->purgeEnabled;
    }

    public function setPurgeEnabled(bool $purgeEnabled): self
    {
        $this->purgeEnabled = $purgeEnabled;

        return $this;
    }

    public function getEncryptEnabled(): bool
    {
        return $this->encryptEnabled;
    }

    public function setEncryptEnabled(bool $encryptEnabled): self
    {
        $this->encryptEnabled = $encryptEnabled;

        return $this;
    }

    public function getEncryptionKey(): string
    {
        return $this->encryptionKey;
    }

    public function setEncryptionKey(string $encryptionKey): self
    {
        $this->encryptionKey = $encryptionKey;

        return $this;
    }

    public function getRetentionDays(): int
    {
        return $this->retentionDays;
    }

    public function setRetentionDays(int $retentionDays): self
    {
        $this->retentionDays = $retentionDays;

        return $this;
    }

    public function getScheduleType(): string
    {
        return $this->scheduleType;
    }

    /**
     * @throws Exception
     */
    public function setScheduleType(string $scheduleType): self
    {
        if (!in_array($scheduleType, self::SCHEDULE_TYPES)) {
            throw new Exception("$scheduleType not allowed.");
        }

        $this->scheduleType = $scheduleType;

        return $this;
    }
}
