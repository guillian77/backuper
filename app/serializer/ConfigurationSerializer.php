<?php

namespace App\Serializer;

use App\Entity\Configuration;
use Exception;

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
     * @param array $conf
     *
     * @return Configuration
     *
     * @throws Exception
     */
    public function deserialize(array $conf): Configuration
    {
        return (new Configuration)
            ->setId(1)
            ->setEncryptEnabled(isset($conf['encrypt_enabled']))
            ->setBackupEnabled(isset($conf['backup_enabled']))
            ->setPurgeEnabled(isset($conf['purge_enabled']))
            ->setEncryptionKey($conf['encryption_key'])
            ->setRetentionDays($conf['retention_days'])
            ->setScheduleType($conf['schedule_type'])
        ;
    }
}
