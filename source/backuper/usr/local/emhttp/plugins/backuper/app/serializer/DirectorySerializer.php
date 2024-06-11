<?php

namespace App\Serializer;

use App\Entity\Directory;

class DirectorySerializer
{
    /**
     * Convert entity to serialized data.
     *
     * TODO: Create a default serializer.
     *
     * @param Directory|Directory[] $directory Directory entity to serialize.
     *
     * @return array
     */
    public function serialize(Directory|array $directory): array
    {
        if (!is_array($directory)) {
            return $this->handleSerialize($directory);
        }

        return array_map([$this, "handleSerialize"], $directory);
    }

    private function handleSerialize(Directory $directory): array
    {
        return [
            "id" => $directory->getId(),
            "path" => $directory->getPath(),
            "type" => $directory->getType(),
            "paused" => $directory->getPaused(),
        ];
    }

    /**
     * Convert standard data to entity.
     *
     * TODO: Create a default deserializer.
     *
     * @param array $directory Directory to deserialize.
     *
     * @return Directory
     */
    public function deserialize(array $directory): Directory
    {
        $directoryEntity = new Directory();

        (!str_starts_with($directory['id'], "new")) && $directoryEntity->setId($directory['id']);

        return $directoryEntity
            ->setPath($directory['path'])
            ->setType($directory['type'])
        ;
    }
}
