<?php

namespace App\Entity;

use ReflectionClass;

class BaseEntity
{
    public function save(): self
    {
        $changes = $this->currentEntityValues();

        dump($changes);


//        dump(get_class($this));
//        dump(get_object_vars($this));

        return $this;
    }

    private function currentEntityValues(): array
    {
        $objectVars = get_mangled_object_vars($this);

        $cleanedProperties = [];

        foreach ($objectVars as $propertyName => $propertyValue) {
            $clean = str_replace(get_class($this), "", $propertyName);
            $cleanedProperties[$clean] = $propertyValue;
        }

        return $cleanedProperties;
    }
}