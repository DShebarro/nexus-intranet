<?php
namespace App\Core;

class FileUploadService
{
    private const MAX_SIZE = 5_242_880; // 5 MB

    private const ALLOWED = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
        'text/plain',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    private string $uploadDir;

    public function __construct(?string $uploadDir = null)
    {
        $this->uploadDir = $uploadDir ?? __DIR__ . '/../../storage/uploads';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function store(array $file, string $entityType, int $entityId, ?int $userId): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('Erro no upload do arquivo.');
        }

        if (($file['size'] ?? 0) > self::MAX_SIZE) {
            throw new \InvalidArgumentException('Arquivo excede o limite de 5 MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, self::ALLOWED, true)) {
            throw new \InvalidArgumentException('Tipo de arquivo não permitido.');
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $storedName = bin2hex(random_bytes(16)) . ($ext ? ".{$ext}" : '');
        $dest = "{$this->uploadDir}/{$storedName}";

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new \RuntimeException('Falha ao salvar o arquivo.');
        }

        return [
            'entity_type'   => $entityType,
            'entity_id'     => $entityId,
            'user_id'       => $userId,
            'original_name' => basename($file['name']),
            'stored_name'   => $storedName,
            'mime_type'     => $mime,
            'size_bytes'    => (int) $file['size'],
        ];
    }

    public function path(string $storedName): string
    {
        return "{$this->uploadDir}/{$storedName}";
    }

    public function delete(string $storedName): void
    {
        $path = $this->path($storedName);
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
