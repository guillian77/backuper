<?php

namespace App\Serializer;

use App\Entity\BackupHistory;

class BackupHistorySerializer
{
    public function serialize(BackupHistory $backupHistory)
    {
        return [
            'id' => $backupHistory->getId(),
            'started_at' => $backupHistory->getStartedAt(),
            'finished_at' => $backupHistory->getFinishedAt(),
            'run_type' => $backupHistory->getRunType(),
            'backup_number' => $backupHistory->getBackupNumber(),
            'purged_number' => $backupHistory->getPurgedNumber(),
            'target_number' => $backupHistory->getTargetNumber(),
            'status' => $backupHistory->getStatus(),
        ];
    }
}
