<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Produit;
use App\Models\Boutique;

class GroqService
{
    private string $apiKey;
    // Modèle Groq actif avec support Tool Calling (Function Calling)
    private string $model = 'qwen/qwen3.8-27b';
    private string $baseUrl = 'https://api.groq.com/openai/v1';

    public function __construct()
    {
        $this->apiKey = config('services.groq.key', env('GROQ_API_KEY'));
    }

    /**
     * System prompt — comportement du chatbot
     */
    private function systemPrompt(): string
    {
        return <<<PROMPT
أنت "Smsar Bot" (سمسار بوت)، المساعد الذكي الرسمي لـ Marketplace Mobile Center فـ مكناس (المغرب).

## دورك
- كتعاون الزوار فـ البحث على الهواتف، الإكسسوارات، وخدمات الإصلاح
- كترد بالدارجة المغربية ولا بالفرنسية (حسب لغة الزائر)
- كتكون مهذب، مختصر، ومفيد

## القواعد
1. إلا سولوك على منتج → استعمل الدالة search_produit
2. إلا سولوك على بوتيك → استعمل الدالة search_boutique
3. إلا سولوك على حاجة ماشي فـ الـ marketplace → گول "ما عنديش هاد المعلومة، عافاك سول على شي حاجة أخرى"
4. ما تعطيش أبداً معلومات شخصية (email, phone) ديال شي حد
5. إلا بغى يشري → گولو: "باش تشري، دخل لصفحة المنتج وكليكي على زر Commander"
6. استعمل الإيموجي بشوية (📱, 💰, 🏪)
7. الأثمنة بالدرهم المغربي (MAD)
PROMPT;
    }

    /**
     * Declarations des fonctions (Function Calling)
     */
    private function tools(): array
    {
        return [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_produit',
                    'description' => 'Recherche des produits par nom, catégorie ou mots-clés',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Terme de recherche (ex: iPhone, Samsung, chargeur)',
                            ],
                            'max_price' => [
                                'type' => 'number',
                                'description' => 'Prix maximum en MAD (optionnel)',
                            ],
                        ],
                        'required' => ['query'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'search_boutique',
                    'description' => 'Recherche une boutique par nom ou emplacement',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Nom de la boutique ou emplacement',
                            ],
                        ],
                        'required' => ['query'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Envoyer un message à Groq
     */
    public function chat(array $history, string $userMessage): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
        ];

        // Historique
        foreach ($history as $msg) {
            $messages[] = [
                'role'    => $msg['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => $msg['content'],
            ];
        }

        // Message actuel
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        // 1er appel
        $response = $this->callGroq($messages);

        $choice = $response['choices'][0]['message'] ?? null;
        $toolCalls = $choice['tool_calls'] ?? null;

        // Vérifier tool calls
        if ($toolCalls && count($toolCalls) > 0) {
            // Ajouter le message assistant avec les tool_calls
            $messages[] = $choice;

            // Exécuter chaque tool call
            foreach ($toolCalls as $toolCall) {
                $functionName = $toolCall['function']['name'];
                $functionArgs = json_decode($toolCall['function']['arguments'], true) ?? [];

                $functionResult = $this->executeFunction($functionName, $functionArgs);

                $messages[] = [
                    'role'         => 'tool',
                    'tool_call_id' => $toolCall['id'],
                    'content'      => json_encode($functionResult, JSON_UNESCAPED_UNICODE),
                ];
            }

            // 2ème appel
            $response = $this->callGroq($messages);
        }

        $text = $response['choices'][0]['message']['content']
            ?? 'سمح ليا، ما قدرتش نجاوب. عاود جرب.';

        return [
            'text' => $text,
            'raw'  => $response,
        ];
    }

    /**
     * Appel HTTP à Groq
     */
    private function callGroq(array $messages): array
    {
        $url = "{$this->baseUrl}/chat/completions";

        // force_ip_resolve => v4 باش نتفاداو cURL error 6 f Windows
        $response = Http::timeout(30)
            ->withOptions([
                'force_ip_resolve' => 'v4',
            ])
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])
            ->post($url, [
                'model'       => $this->model,
                'messages'    => $messages,
                'tools'       => $this->tools(),
                'tool_choice' => 'auto',
                'temperature' => 0.7,
                'max_tokens'  => 500,
            ]);

        if ($response->failed()) {
            Log::error('Groq API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \Exception('Erreur Groq API: ' . $response->status() . ' - ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Exécuter une fonction demandée
     */
    private function executeFunction(string $name, array $args): array
    {
        if ($name === 'search_produit') {
            $query = $args['query'] ?? '';
            $maxPrice = $args['max_price'] ?? null;

            $q = Produit::with('boutique')
                ->where('disponible', true)
                ->where(function ($sub) use ($query) {
                    $sub->where('nom', 'LIKE', "%{$query}%")
                        ->orWhere('description', 'LIKE', "%{$query}%")
                        ->orWhere('marque', 'LIKE', "%{$query}%");
                });

            if ($maxPrice) {
                $q->where('prix', '<=', $maxPrice);
            }

            $produits = $q->limit(5)->get()->map(function ($p) {
                return [
                    'nom'        => $p->nom,
                    'prix'       => (float) $p->prix,
                    'stock'      => $p->stock,
                    'boutique'   => $p->boutique->nom ?? '—',
                    'disponible' => $p->stock > 0,
                ];
            })->toArray();

            return [
                'resultats' => $produits,
                'count'     => count($produits),
            ];
        }

        if ($name === 'search_boutique') {
            $query = $args['query'] ?? '';

            $boutiques = Boutique::where('actif', true)
                ->where(function ($sub) use ($query) {
                    $sub->where('nom', 'LIKE', "%{$query}%")
                        ->orWhere('emplacement', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($b) {
                    return [
                        'nom'         => $b->nom,
                        'emplacement' => $b->emplacement,
                        'telephone'   => $b->telephone,
                        'produits'    => $b->produits()->count(),
                    ];
                })
                ->toArray();

            return [
                'resultats' => $boutiques,
                'count'     => count($boutiques),
            ];
        }

        return ['error' => 'Fonction inconnue'];
    }
}