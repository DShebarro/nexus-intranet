<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Database;
use PDO;

class ChatController extends BaseController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index(Request $req): void
    {
        // Garante que o canal Nexus AI e Geral existem no banco
        $this->ensureChatExists('nexus-ai', 'Nexus AI', 'ai');
        $this->ensureChatExists('geral', 'Canal Geral', 'channel');

        $stmt = $this->db->query("SELECT * FROM chats ORDER BY type DESC, title ASC");
        $chats = $stmt->fetchAll();

        $this->render('chat/index', compact('chats'));
        $this->log('navigation', 'Página de Chat carregada');
    }

    public function getMessages(Request $req, array $params): void
    {
        $slug = $params['slug'] ?? '';
        
        $stmt = $this->db->prepare("
            SELECT m.* FROM messages m 
            JOIN chats c ON m.chat_id = c.id
            WHERE c.slug = :slug 
            ORDER BY m.sent_at ASC
        ");
        $stmt->execute(['slug' => $slug]);
        $messages = $stmt->fetchAll();

        $this->json($messages);
    }

    public function storeMessage(Request $req, array $params): void
    {
        $slug = $params['slug'] ?? '';
        $data = $req->json();
        
        $content = trim($data['content'] ?? '');
        if ($content === '') {
            $this->json(['error' => 'Mensagem vazia'], 400);
            return;
        }

        // Buscar chat
        $stmt = $this->db->prepare("SELECT * FROM chats WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $chat = $stmt->fetch();

        if (!$chat) {
            $this->json(['error' => 'Chat não encontrado'], 404);
            return;
        }

        // Salvar mensagem do usuário
        $stmt = $this->db->prepare("
            INSERT INTO messages (chat_id, sender_name, sender_type, content)
            VALUES (:chat_id, :sender_name, 'user', :content)
        ");
        $stmt->execute([
            'chat_id'     => $chat['id'],
            'sender_name' => 'Carlos Silva',
            'content'     => $content,
        ]);

        $msgId = $this->db->lastInsertId();

        // Se for chat com a IA (Nexus AI), gerar resposta automática
        if ($chat['type'] === 'ai') {
            $aiResponse = $this->generateAIResponse($content);
            $stmt = $this->db->prepare("
                INSERT INTO messages (chat_id, sender_name, sender_type, content)
                VALUES (:chat_id, 'Nexus AI', 'ai', :content)
            ");
            $stmt->execute([
                'chat_id'     => $chat['id'],
                'content'     => $aiResponse,
            ]);
        }

        $this->json(['success' => true, 'message_id' => $msgId]);
    }

    private function ensureChatExists(string $slug, string $title, string $type): void
    {
        $stmt = $this->db->prepare("SELECT id FROM chats WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        if (!$stmt->fetch()) {
            $stmt = $this->db->prepare("INSERT INTO chats (slug, title, type) VALUES (:slug, :title, :type)");
            $stmt->execute(['slug' => $slug, 'title' => $title, 'type' => $type]);
        }
    }

    private function generateAIResponse(string $userMessage): string
    {
        $msg = strtolower($userMessage);

        if (str_contains($msg, 'olá') || str_contains($msg, 'oi') || str_contains($msg, 'bom dia') || str_contains($msg, 'boa tarde')) {
            return "Olá Carlos! Eu sou o assistente virtual da Nexus Intranet. Como posso ajudar você hoje?";
        }
        if (str_contains($msg, 'contrato')) {
            return "Atualmente temos 4 contratos ativos cadastrados no banco de dados, totalizando mais de R$ 61.000,00 em valor acumulado. Você pode visualizar todos eles acessando a aba 'Contratos' no menu lateral.";
        }
        if (str_contains($msg, 'tarefa') || str_contains($msg, 'kanban')) {
            return "No nosso Gerenciador de Tarefas Kanban, você pode criar novas tarefas, arrastá-las entre as colunas ('A Fazer', 'Em Andamento', 'Revisão', 'Concluído') e excluí-las quando necessário. Seus dados são atualizados em tempo real no banco.";
        }
        if (str_contains($msg, 'site') || str_contains($msg, 'link') || str_contains($msg, 'sistema')) {
            return "Temos 5 links/sistemas corporativos configurados na aba 'Sites'. Lá você encontra atalhos rápidos tanto para sistemas internos quanto para portais externos importantes.";
        }
        if (str_contains($msg, 'ajuda') || str_contains($msg, 'suporte')) {
            return "Você pode me perguntar sobre os Contratos, sobre as Tarefas Kanban, ou sobre os Sites corporativos. Se precisar de suporte técnico da TI, fique à vontade para entrar em contato com o ramal 4002.";
        }

        return "Entendi! Como assistente integrado da Nexus Intranet, eu posso ajudar você a monitorar contratos, tarefas e sites internos do nosso departamento. Se precisar de alguma informação específica sobre essas áreas, basta me perguntar!";
    }
}
