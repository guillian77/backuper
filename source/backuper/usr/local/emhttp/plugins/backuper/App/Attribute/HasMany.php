<?php

namespace app\attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class HasMany
{
    private string $entity;

    public function __construct(string $entity)
    {
        $this->entity = $entity;
    }
}
