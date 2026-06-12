<?php
namespace App\Core;

class AIService
{
    public static function respond(string $userMessage, ?string $userName = null): string
    {
        $config = require __DIR__ . '/../../config/app.php';
        $apiKey = $config['ai_key'] ?? '';

        if ($apiKey) {
            $response = self::callExternalAPI($userMessage, $apiKey);
            if ($response) {
                return $response;
            }
        }

        return self::fallbackResponse($userMessage, $userName);
    }

    private static function callExternalAPI(string $message, string $apiKey): ?string
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . urlencode($apiKey);

        $payload = json_encode([
            'contents' => [['parts' => [['text' => $message]]]],
        ]);

        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 10,
            ],
        ]);

        $result = @file_get_contents($url, false, $ctx);
        if (!$result) {
            return null;
        }

        $data = json_decode($result, true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    private static function fallbackResponse(string $userMessage, ?string $userName): string
    {
        $name = $userName ?? 'usuário';
        $msg = strtolower($userMessage);

        if (str_contains($msg, 'olá') || str_contains($msg, 'oi') || str_contains($msg, 'bom dia') || str_contains($msg, 'boa tarde')) {
            return "Olá {$name}! Eu sou o assistente virtual da Nexus Intranet. Como posso ajudar você hoje?";
        }
        if (str_contains($msg, 'contrato')) {
            return "Você pode visualizar todos os contratos cadastrados acessando a aba 'Contratos' no menu lateral.";
        }
        if (str_contains($msg, 'tarefa') || str_contains($msg, 'kanban')) {
            return "No Gerenciador de Tarefas Kanban, você pode criar novas tarefas, arrastá-las entre colunas e acompanhar o progresso em tempo real.";
        }
        if (str_contains($msg, 'site') || str_contains($msg, 'link') || str_contains($msg, 'sistema')) {
            return "Na aba 'Sites' você encontra atalhos rápidos para sistemas internos e portais externos importantes.";
        }
        if (str_contains($msg, 'ajuda') || str_contains($msg, 'suporte')) {
            return "Posso ajudar com informações sobre Contratos, Tarefas Kanban e Sites corporativos. Para suporte técnico da TI, entre em contato com o ramal 4002.";
        }

        return "Como assistente da Nexus Intranet, posso ajudar você a monitorar contratos, tarefas e sites internos. Basta me perguntar!";
    }
}
