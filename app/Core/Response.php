<?php
namespace App\Core;

class Response
{
    private int $status = 200;
    private array $headers = [];
    private mixed $body = '';

    public function status(int $code): self
    {
        $this->status = $code;
        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function json(mixed $data, int $status = 200): void
    {
        $this->status($status)
            ->header('Content-Type', 'application/json; charset=utf-8')
            ->securityHeaders()
            ->send(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function html(string $content, int $status = 200): void
    {
        $this->status($status)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->securityHeaders()
            ->send($content);
    }

    public function redirect(string $url, int $status = 302): void
    {
        $this->status($status)
            ->header('Location', $url)
            ->send('');
    }

    public function download(string $content, string $filename, string $mime = 'application/octet-stream'): void
    {
        $this->status(200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Length', (string) strlen($content))
            ->securityHeaders()
            ->send($content);
    }

    public function csv(string $content, string $filename): void
    {
        $this->download($content, $filename, 'text/csv; charset=utf-8');
    }

    public function securityHeaders(): self
    {
        return $this
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Frame-Options', 'SAMEORIGIN')
            ->header('X-XSS-Protection', '1; mode=block')
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function send(mixed $body = null): void
    {
        if ($body !== null) {
            $this->body = $body;
        }

        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->body;
    }
}
