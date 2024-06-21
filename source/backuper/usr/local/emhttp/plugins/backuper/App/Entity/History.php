<?php

namespace App\Entity;

use App\Repository\HistoryRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: HistoryRepository::class)]
#[Table(name: 'history')]
class History
{
    const RUN_TYPE_BACKUP = "backup";
    const RUN_TYPE_BACKUP_ENCRYPTED = "backup-encrypted";
    const RUN_TYPE_PURGE = "purge";
    const RUN_TYPE_ALL = "all";
    const RUN_TYPE_ALL_ENCRYPTED = "all-encrypted";

    const STATUS_START = "start";
    const STATUS_RUNNING = "running";
    const STATUS_BACKUP = "running_backup";
    const STATUS_PURGE = "running_purge";
    const STATUS_END = "end";
    const STATUS_ERROR = "error";

    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int $id;

    #[Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $startedAt;

    #[Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $finishedAt;

    #[Column(length: 255)]
    private string $runType;

    #[Column(type: Types::INTEGER)]
    private ?int $backupNumber = 0;

    #[Column(type: Types::INTEGER)]
    private ?int $purgedNumber = 0;

    #[Column(type: Types::INTEGER)]
    private ?int $targetNumber = 0;

    #[Column(length: 255)]
    private string $status;

    public function getId(): int
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
        if (isset($this->startedAt)) { return $this; }

        $this->startedAt = $startedAt;

        return $this;
    }

    public function getFinishedAt(): ?DateTime
    {
        return $this->finishedAt;
    }

    public function getDuration(): string
    {
        $duration = $this->finishedAt->diff($this->startedAt);

        return $duration->format("%H:%I:%S");
    }

    public function setFinishedAt(?DateTime $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getRunType(): string
    {
        return $this->runType;
    }

    public function setRunType(string $runType): self
    {
        $this->runType = $runType;

        return $this;
    }

    public function getBackupNumber(): ?int
    {
        return $this->backupNumber;
    }

    public function incrementBackupNumber(): self
    {
        $this->backupNumber++;

        return $this;
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

    public function incrementPurgedNumber(): self
    {
        $this->purgedNumber++;

        return $this;
    }

    public function setPurgedNumber(?int $purgedNumber): self
    {
        $this->purgedNumber = $purgedNumber;

        return $this;
    }

    public function getTargetNumber(): ?int
    {
        return $this->targetNumber;
    }

    public function incrementTargetNumber(): self
    {
        $this->targetNumber++;

        return $this;
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

    public function addDirectory(Directory $directory): self
    {
        $this->dirs[] = $directory;

        return $this;
    }

    /**
     * @return Directory[]
     */
    public function removeDirectory(): self
    {
        // TODO: .
        return $this->dirs;
    }

    public function listDirectory(): array
    {
        return $this->dirs;
    }
}
