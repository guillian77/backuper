<?php

namespace App\Entity;

use App\Repository\DirectoryRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: DirectoryRepository::class)]
#[Table(name: 'directory')]
class Directory
{
    public const TYPE_TARGET = "target";
    public const TYPE_BACKUP = "backup";

    #[Id, Column(type: 'integer'), GeneratedValue]
    private int $id;

    #[Column(length: 255)]
    private string $path;

    #[Column(length: 255)]
    private string $type;

    #[Column(type: 'boolean')]
    private bool $paused;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPaused(): bool
    {
        return $this->paused;
    }

    public function setPaused(bool $paused): self
    {
        $this->paused = $paused;

        return $this;
    }
}
