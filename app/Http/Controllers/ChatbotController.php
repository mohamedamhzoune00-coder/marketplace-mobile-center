<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroqService;
use App\Models\ChatMessage;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    private GroqService $groq;

    public function __construct(GroqService $groq)
    {
        $this->groq = $groq;
    }

    /**
     * POST /api/chatbot/message
     * Envoie un message au chatbot et retourne la réponse
     */
    public function message(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:500',
            'session_id' => 'nullable|string|max:100',
        ]);

        $sessionId = $request->session_id ?? (string) Str::uuid();
        $userId = $request->user()?->id;

        // Récupérer l'historique (10 derniers messages)
        $history = ChatMessage::where('session_id', $sessionId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->reverse()
            ->map(function ($m) {
                return [
                    'role'    => $m->role,
                    'content' => $m->content,
                ];
            })
            ->values()
            ->toArray();

        // Sauvegarder le message utilisateur
        ChatMessage::create([
            'user_id'    => $userId,
            'session_id' => $sessionId,
            'role'       => 'user',
            'content'    => $request->message,
        ]);

        // Appeler Groq
        try {
            $result = $this->groq->chat($history, $request->message);
            $reply = $result['text'];
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur du chatbot',
                'error'   => $e->getMessage(),
            ], 500);
        }

        // Sauvegarder la réponse
        ChatMessage::create([
            'user_id'    => $userId,
            'session_id' => $sessionId,
            'role'       => 'assistant',
            'content'    => $reply,
        ]);

        return response()->json([
            'reply'      => $reply,
            'session_id' => $sessionId,
        ]);
    }

    /**
     * GET /api/chatbot/history
     * Récupère l'historique d'une session
     */
    public function history(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return response()->json(['messages' => []]);
        }

        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at')
            ->get()
            ->map(function ($m) {
                return [
                    'role'      => $m->role,
                    'content'   => $m->content,
                    'timestamp' => $m->created_at->toIso8601String(),
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    /**
     * DELETE /api/chatbot/history
     * Efface l'historique d'une session
     */
    public function clear(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string|max:100',
        ]);

        ChatMessage::where('session_id', $request->session_id)->delete();

        return response()->json(['message' => 'Historique effacé']);
    }
}