<?php
namespace App\Controllers;

use App\Core\{Auth, AIService, Request, Database};
use App\Exceptions\NotFoundException;
use PDO;

class ChatController extends BaseController
{
    private PDO $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function index(Request $req): void
    {
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
        $limit = min(100, max(1, (int) $req->get('limit', 50)));
        $before = $req->get('before');

        $sql = "
            SELECT m.* FROM messages m
            JOIN chats c ON m.chat_id = c.id
            WHERE c.slug = :slug
        ";
        $queryParams = ['slug' => $slug];

        if ($before) {
            $sql .= " AND m.id < :before";
            $queryParams['before'] = (int) $before;
        }

        $sql .= " ORDER BY m.sent_at DESC LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        foreach ($queryParams as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $messages = array_reverse($stmt->fetchAll());
        $this->json($messages);
    }

    public function storeMessage(Request $req, array $params): void
    {
        $slug = $params['slug'] ?? '';
        $data = $req->json();

        $errors = \App\Core\Validator::validate($data, [
            'content' => 'required|string|max:5000',
        ]);

        if ($errors) {
            $this->json(['error' => reset($errors)[0]], 422);
            return;
        }

        $content = trim($data['content']);

        $stmt = $this->db->prepare("SELECT * FROM chats WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $chat = $stmt->fetch();

        if (!$chat) {
            throw new NotFoundException('Chat não encontrado.');
        }

        $user = Auth::user();
        $senderName = $user['name'] ?? 'Usuário';

        $stmt = $this->db->prepare("
            INSERT INTO messages (chat_id, sender_name, sender_type, content)
            VALUES (:chat_id, :sender_name, 'user', :content)
        ");
        $stmt->execute([
            'chat_id'     => $chat['id'],
            'sender_name' => $senderName,
            'content'     => $content,
        ]);

        $msgId = $this->db->lastInsertId();

        if ($chat['type'] === 'ai') {
            $aiResponse = AIService::respond($content, $senderName);
            $stmt = $this->db->prepare("
                INSERT INTO messages (chat_id, sender_name, sender_type, content)
                VALUES (:chat_id, 'Nexus AI', 'ai', :content)
            ");
            $stmt->execute([
                'chat_id' => $chat['id'],
                'content' => $aiResponse,
            ]);
        }

        if ($chat['type'] === 'channel' && $slug === 'geral') {
            (new \App\Core\NotificationService(
                new \App\Models\Notification(),
                new \App\Models\User()
            ))->notifyAllUsers(
                'chat',
                "Nova mensagem no {$chat['title']}",
                "{$senderName}: " . mb_substr($content, 0, 80),
                '/chat'
            );
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
}
