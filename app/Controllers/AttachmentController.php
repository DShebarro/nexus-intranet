<?php
namespace App\Controllers;

use App\Core\{Auth, FileUploadService, Request};
use App\Exceptions\NotFoundException;
use App\Models\Attachment;

class AttachmentController extends BaseController
{
    public function __construct(
        private Attachment $attachment,
        private FileUploadService $uploadService
    ) {
        parent::__construct();
    }

    public function index(Request $req): void
    {
        $entityType = $req->get('entity_type');
        $entityId = (int) $req->get('entity_id');

        if (!$entityType || !$entityId) {
            $this->json(['error' => 'entity_type e entity_id são obrigatórios.'], 400);
            return;
        }

        if (!in_array($entityType, ['task', 'contract', 'message'], true)) {
            $this->json(['error' => 'entity_type inválido.'], 400);
            return;
        }

        $this->json($this->attachment->forEntity($entityType, $entityId));
    }

    public function store(Request $req): void
    {
        $entityType = $req->post('entity_type');
        $entityId = (int) $req->post('entity_id');

        if (!in_array($entityType, ['task', 'contract', 'message'], true) || !$entityId) {
            $this->json(['error' => 'Dados de entidade inválidos.'], 400);
            return;
        }

        if (empty($_FILES['file'])) {
            $this->json(['error' => 'Nenhum arquivo enviado.'], 400);
            return;
        }

        try {
            $data = $this->uploadService->store($_FILES['file'], $entityType, $entityId, Auth::id());
            $id = $this->attachment->create($data);
            $this->log('attachment', "Anexo #{$id} enviado para {$entityType}#{$entityId}");
            $this->json(['success' => true, 'id' => $id, 'attachment' => $this->attachment->findById($id)], 201);
        } catch (\InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function download(Request $req, array $params): void
    {
        $attachment = $this->attachment->findById((int) $params['id']);
        if (!$attachment) {
            throw new NotFoundException('Anexo não encontrado.');
        }

        $path = $this->uploadService->path($attachment['stored_name']);
        if (!file_exists($path)) {
            throw new NotFoundException('Arquivo não encontrado no disco.');
        }

        $this->response->download(
            file_get_contents($path),
            $attachment['original_name'],
            $attachment['mime_type']
        );
    }

    public function destroy(Request $req, array $params): void
    {
        $attachment = $this->attachment->findById((int) $params['id']);
        if (!$attachment) {
            throw new NotFoundException('Anexo não encontrado.');
        }

        $this->uploadService->delete($attachment['stored_name']);
        $this->attachment->delete((int) $params['id']);
        $this->log('warning', "Anexo #{$params['id']} excluído");
        $this->json(['success' => true]);
    }
}
