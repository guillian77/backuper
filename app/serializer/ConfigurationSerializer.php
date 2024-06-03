<?php

namespace App\Serializer;

use App\Entity\Configuration;

class ConfigurationSerializer
{
    /**
     * Convert entity to serialized data.
     *
     * TODO: Create a default serializer.
     *
     * @param Configuration|Configuration[] $conf Configuration entity to serialize.
     *
     * @return array
     */
    public function serialize(Configuration|array $conf): array
    {
        if (!is_array($conf)) {
            return $this->handleSerialize($conf);
        }

        return array_map([$this, "handleSerialize"], $conf);
    }

    private function handleSerialize(Configuration $conf): array
    {
        return [
            "key" => $conf->getKey(),
            "value" => $conf->getValue(),
        ];
    }

    /**
     * Convert standard data to entity.
     *
     * TODO: Create a default deserializer.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return Configuration
     */
    public function deserialize(string $key, mixed $value): Configuration
    {
        return (new Configuration)
            ->setKey($key)
            ->setValue($value)
        ;
    }
}
