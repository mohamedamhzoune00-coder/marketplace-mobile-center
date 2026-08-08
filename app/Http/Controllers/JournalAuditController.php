<?php

namespace App\Http\Controllers;

use App\Models\JournalAudit;

class JournalAuditController extends Controller
{
    // عرض جميع السجلات
    public function index()
    {
        $this->authorize('viewAny', JournalAudit::class);

        return JournalAudit::with('user')->paginate(10);
    }

    // عرض سجل واحد
    public function show($id)
    {
        $journal = JournalAudit::with('user')->find($id);

        if (!$journal) {
            return response()->json([
                'message' => 'Journal introuvable'
            ], 404);
        }

        $this->authorize('view', $journal);

        return response()->json($journal);
    }
}