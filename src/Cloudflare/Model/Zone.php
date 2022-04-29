<?php

namespace Remg\Cloudflare\Model;

class Zone
{
    private string $id;
    private string $name;
    private string $status;
    private array $nameServers;
    private array $originalNameServers;

    public function __construct(string $id, string $name, string $status, array $nameServers = [], array $originalNameServers = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
        $this->nameServers = $nameServers;
        $this->originalNameServers = $originalNameServers;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public static function createFromAPIResult(object $result): self
    {
        return new self(
            $result->id,
            $result->name,
            $result->status,
            $result->name_servers,
            $result->original_name_servers
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getNameServers(): array
    {
        return $this->nameServers;
    }

    public function getOriginalNameServers(): array
    {
        return $this->originalNameServers;
    }
}
