<?php

namespace App\Serializer;

use App\Entity\History;

class HistorySerializer
{
    public function serialize(History $history): array
    {
        return [
            'id' => $history->getId(),
            'started_at' => $history->getStartedAt(),
            'finished_at' => $history->getFinishedAt(),
            'run_type' => $history->getRunType(),
            'backup_number' => $history->getBackupNumber(),
            'purged_number' => $history->getPurgedNumber(),
            'target_number' => $history->getTargetNumber(),
            'status' => $history->getStatus(),
        ];
    }
}
