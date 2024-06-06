<?php

namespace App\Entity;

use App\Repository\BaseRepository;

class BaseEntity
{
    public function upsert(): void
    {
        (new BaseRepository())->upsert($this);
    }
}
