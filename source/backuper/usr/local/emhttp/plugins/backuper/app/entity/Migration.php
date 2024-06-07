<?php

namespace app\entity;

use DateTime;

class Migration extends BaseEntity
{
    private int $id;

    private string $name;

    private DateTime $execution;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getExecution(): DateTime
    {
        return $this->execution;
    }

    public function setExecution(DateTime $execution): self
    {
        $this->execution = $execution;

        return $this;
    }
}
