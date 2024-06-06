<?php

namespace App\Entity;

use DateInterval;
use DateTime;

class BackupHistory extends BaseEntity
{
    const RUN_TYPE_WEB = "web";
    const RUN_TYPE_MANUAL = "manual";
    const RUN_TYPE_CRON = "cron"; // TODO

    const STATUS_START = "start";
    const STATUS_RUNNING = "running";
    const STATUS_BACKUP = "running_backup";
    const STATUS_PURGE = "running_purge";
    const STATUS_SUCCESS = "success";
    const STATUS_ERROR = "error";

    private ?int $id = null;

    private DateTime $startedAt;

    private DateTime $finishedAt;

    private string $runType;

    private ?int $backupNumber;

    private ?int $purgedNumber;

    private ?int $targetNumber;

    private string $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getStartedAt(): DateTime
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTime $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): DateTime
    {
        return $this->finishedAt;
    }

    public function getDuration(): DateInterval
    {
        return $this->finishedAt->diff($this->startedAt);
    }

    public function setFinishedAt(DateTime $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getRunType(): string
    {
        return $this->runType;
    }

    public function setRunType(): self
    {
        $runType = (php_sapi_name() === "cli")
            ? self::RUN_TYPE_MANUAL
            : self::RUN_TYPE_WEB;

        $this->runType = $runType;

        return $this;
    }

    public function getBackupNumber(): ?int
    {
        return $this->backupNumber;
    }

    public function setBackupNumber(string $backupNumber): self
    {
        $this->backupNumber = $backupNumber;

        return $this;
    }

    public function getPurgedNumber(): ?int
    {
        return $this->purgedNumber;
    }

    public function setPurgedNumber(int $purgedNumber): self
    {
        $this->purgedNumber = $purgedNumber;

        return $this;
    }

    public function getTargetNumber(): ?int
    {
        return $this->targetNumber;
    }

    public function setTargetNumber(?int $targetNumber): self
    {
        $this->targetNumber = $targetNumber;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
