<?php

namespace App\Serializer;

use App\Entity\Directory;

class DirectorySerializer
{
    /**
     * TODO: Create a global entity serializer.
     *
     * @param Directory $directory
     *
     * @return array
     */
    public static function serialize(Directory $directory): array
    {
        return [
            "id" => $directory->getId(),
            "path" => $directory->getPath(),
            "type" => $directory->getType(),
        ];
    }

    public function deserialize(string $directory): Directory
    {
        return "";
    }
}
