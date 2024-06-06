<?php

namespace App\Serializer;

use App\Entity\Configuration;
use Exception;

class ConfigurationSerializer
{
    /**
     * Convert entity to serialized data.
     *
     * @param Configuration $configuration Configuration entity to serialize.
     *
     * @return array
     */
    public function serialize(Configuration $configuration): array
    {
        return [
            'id' => $configuration->getId(),
            'backup_enabled' => $configuration->getBackupEnabled(),
            'purge_enabled' => $configuration->getPurgeEnabled(),
            'encrypt_enabled' => $configuration->getEncryptEnabled(),
            'encryption_key' => $configuration->getEncryptionKey(),
            'retention_days' => $configuration->getRetentionDays(),
            'schedule_type' => $configuration->getScheduleType(),
        ];
    }

    public function deserialize(array $configuration): Configuration
    {
        return (new Configuration)
            ->setId(1)
            ->setEncryptEnabled(isset($configuration['encrypt_enabled']))
            ->setBackupEnabled(isset($configuration['backup_enabled']))
            ->setPurgeEnabled(isset($configuration['purge_enabled']))
            ->setEncryptionKey($configuration['encryption_key'])
            ->setRetentionDays($configuration['retention_days'])
            ->setScheduleType($configuration['schedule_type'])
        ;
    }
}
