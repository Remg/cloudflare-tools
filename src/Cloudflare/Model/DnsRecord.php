<?php

namespace Remg\Cloudflare\Model;

class DnsRecord
{
    private ?string $id;
    private string $type;
    private string $name;
    private string $content;
    private int $ttl;
    private bool $proxied;

    public function __construct(?string $id, string $type, string $name, string $content, int $ttl, bool $proxied)
    {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->content = $content;
        $this->ttl = $ttl;
        $this->proxied = $proxied;
    }

    public static function createFromResult(object $result): self
    {
        return new self(
            $result->id,
            $result->type,
            $result->name,
            $result->content,
            $result->ttl,
            $result->proxied
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getShortContent(): string
    {
        return strlen($this->content) > 50 ? substr($this->content, 0, 50).'...' : $this->content;
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }

    public function isProxied(): bool
    {
        return $this->proxied;
    }

    public function match(string $type, string $name): bool
    {
        return $this->type === $type && $this->name === $name;
    }

    public function matchContent(string $content): bool
    {
        return $this->content === $content;
    }
}
