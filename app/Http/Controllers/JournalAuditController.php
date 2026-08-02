<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JournalAudit;

class JournalAuditController extends Controller
{
    // عرض جميع سجلات التدقيق
    public function index()
    {
        return JournalAudit::with('user')->get();
    }

    // إنشاء سجل جديد
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'action' => 'required|string|max:255',
            'table_concernee' => 'required|string|max:255',
            'record_id' => 'nullable|integer',
            'details' => 'nullable|string',
            'ip_address' => 'nullable|string|max:45',
            'user_agent' => 'nullable|string',
        ]);

        $journal = JournalAudit::create($request->all());

        return response()->json([
            'message' => 'Journal créé avec succès',
            'data' => $journal->load('user')
        ], 201);
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

        return response()->json($journal);
    }

    // تعديل سجل
    public function update(Request $request, $id)
    {
        $journal = JournalAudit::with('user')->find($id);

        if (!$journal) {
            return response()->json([
                'message' => 'Journal introuvable'
            ], 404);
        }

        $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'action' => 'sometimes|required|string|max:255',
            'table_concernee' => 'sometimes|required|string|max:255',
            'record_id' => 'nullable|integer',
            'details' => 'nullable|string',
            'ip_address' => 'nullable|string|max:45',
            'user_agent' => 'nullable|string',
        ]);

        $journal->update($request->all());

        return response()->json([
            'message' => 'Journal mis à jour avec succès',
            'data' => $journal->fresh()->load('user')
        ]);
    }

    // حذف سجل
    public function destroy($id)
    {
        $journal = JournalAudit::find($id);

        if (!$journal) {
            return response()->json([
                'message' => 'Journal introuvable'
            ], 404);
        }

        $journal->delete();

        return response()->json([
            'message' => 'Journal supprimé avec succès'
        ]);
    }
}
