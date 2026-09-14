<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

        if (empty($this->apiKey)) {
            throw new RuntimeException('Falta GEMINI_API_KEY en el .env');
        }
    }

    public function generate(string $documentText, string $contentType, array $options = []): array
    {
        $prompt = $this->buildPrompt($documentText, $contentType, $options);
        $schema = $this->schemaFor($contentType);

        $response = Http::timeout(120)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'responseSchema' => $schema,
                    'temperature' => 0.4,
                ],
            ]);

        if ($response->failed()) {
            Log::error('Gemini API error', ['body' => $response->body()]);
            throw new RuntimeException('Gemini no ha podido generar el contenido. Inténtalo de nuevo.');
        }

        $rawText = $response->json('candidates.0.content.parts.0.text');

        if (! $rawText) {
            throw new RuntimeException('Respuesta de Gemini vacía o con formato inesperado.');
        }

        $decoded = json_decode($rawText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Gemini devolvió JSON inválido', ['raw' => $rawText]);
            throw new RuntimeException('Gemini devolvió un formato inválido.');
        }

        return $decoded;
    }

    protected function buildPrompt(string $documentText, string $contentType, array $options): string
    {
        $base = <<<PROMPT
        Eres un asistente educativo. A partir del siguiente documento, genera contenido
        de estudio en ESPAÑOL, preciso y fiel al contenido del documento (no inventes
        datos que no aparezcan o no se puedan inferir razonablemente).

        DOCUMENTO:
        \"\"\"
        {$documentText}
        \"\"\"

        PROMPT;

        return match ($contentType) {
            'curso' => $base . "Genera un CURSO completo: un título general, una descripción breve, "
                . "y una lista de unidades/temario (entre 4 y 10 unidades), cada una con título, "
                . "resumen y puntos clave.",

            'temario' => $base . "Genera únicamente un TEMARIO/índice de contenidos: una lista de unidades "
                . "(entre 4 y 12), cada una con título, un resumen de 2-3 frases y una lista de puntos clave.",

            'test' => $base . 'Genera un TEST tipo test de opción múltiple con '
                . ($options['num_preguntas'] ?? 10) . ' preguntas, dificultad "'
                . ($options['dificultad'] ?? 'media') . '". Cada pregunta debe tener 4 opciones (A, B, C, D), '
                . 'indicar cuál es la correcta y una breve explicación de por qué lo es.',

            'formulario' => $base . 'Genera un FORMULARIO DE ESTUDIO/PRÁCTICA con '
                . ($options['num_items'] ?? 12) . ' elementos, mezclando flashcards (pregunta corta/respuesta corta) '
                . 'y preguntas abiertas de desarrollo, para practicar y repasar el documento.',

            default => throw new RuntimeException("Tipo de contenido no soportado: {$contentType}"),
        };
    }

    protected function schemaFor(string $contentType): array
    {
        return match ($contentType) {
            'curso' => [
                'type' => 'object',
                'properties' => [
                    'titulo' => ['type' => 'string'],
                    'descripcion' => ['type' => 'string'],
                    'unidades' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'titulo' => ['type' => 'string'],
                                'resumen' => ['type' => 'string'],
                                'puntos_clave' => ['type' => 'array', 'items' => ['type' => 'string']],
                            ],
                            'required' => ['titulo', 'resumen', 'puntos_clave'],
                        ],
                    ],
                ],
                'required' => ['titulo', 'descripcion', 'unidades'],
            ],

            'temario' => [
                'type' => 'object',
                'properties' => [
                    'titulo' => ['type' => 'string'],
                    'unidades' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'titulo' => ['type' => 'string'],
                                'resumen' => ['type' => 'string'],
                                'puntos_clave' => ['type' => 'array', 'items' => ['type' => 'string']],
                            ],
                            'required' => ['titulo', 'resumen', 'puntos_clave'],
                        ],
                    ],
                ],
                'required' => ['titulo', 'unidades'],
            ],

            'test' => [
                'type' => 'object',
                'properties' => [
                    'titulo' => ['type' => 'string'],
                    'preguntas' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'pregunta' => ['type' => 'string'],
                                'opciones' => ['type' => 'array', 'items' => ['type' => 'string']],
                                'respuesta_correcta' => ['type' => 'string'],
                                'explicacion' => ['type' => 'string'],
                            ],
                            'required' => ['pregunta', 'opciones', 'respuesta_correcta'],
                        ],
                    ],
                ],
                'required' => ['titulo', 'preguntas'],
            ],

            'formulario' => [
                'type' => 'object',
                'properties' => [
                    'titulo' => ['type' => 'string'],
                    'instrucciones' => ['type' => 'string'],
                    'items' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'tipo' => ['type' => 'string', 'enum' => ['flashcard', 'pregunta_abierta']],
                                'prompt' => ['type' => 'string'],
                                'respuesta_esperada' => ['type' => 'string'],
                            ],
                            'required' => ['tipo', 'prompt'],
                        ],
                    ],
                ],
                'required' => ['titulo', 'items'],
            ],

            default => throw new RuntimeException("Tipo de contenido no soportado: {$contentType}"),
        };
    }
}