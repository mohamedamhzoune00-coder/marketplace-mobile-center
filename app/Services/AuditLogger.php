<?php

namespace App\Services;

use App\Models\JournalAudit;

class AuditLogger
{
    public static function log($action, $table, $recordId, $details = null)
    {
        JournalAudit::create([
            'user_id'         => auth()->id(),
            'action'          => $action,
            'table_concernee' => $table,
            'record_id'       => $recordId,
            'details'         => $details,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
        ]);
    }
}