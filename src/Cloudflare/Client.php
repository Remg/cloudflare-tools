<?php

namespace Remg\Cloudflare;

use Cloudflare\API\Adapter\Guzzle;
use Cloudflare\API\Auth\APIToken;
use Cloudflare\API\Endpoints\DNS;
use Cloudflare\API\Endpoints\Zones;
use Remg\Cloudflare\Model\DnsRecord;
use Remg\Cloudflare\Model\Zone;

class Client
{
    private string $apiToken;
    private Guzzle $adapter;

    private ?Zones $zonesClient = null;
    private ?DNS $dnsClient = null;

    /** @var Zone[]|array */
    private array $cachedZones = [];

    /** @var DnsRecord[]|array */
    private $cachedDnsRecords = [];

    public function __construct(string $cloudflareApiToken)
    {
        $this->apiToken = $cloudflareApiToken;
        $apiToken = new APIToken($cloudflareApiToken);
        $this->adapter = new Guzzle($apiToken);
    }

    public function listZones(): array
    {
        if (empty($this->cachedZones)) {
            $this->fetchZones();
        }

        return $this->cachedZones;
    }

    public function listDns(
        string $zoneName,
        string $type = null,
        string $name = null,
        string $content = null,
        int $perPage = 1000
    ): array {
        if (empty($this->cachedDnsRecords)) {
            $this->fetchDns($zoneName, $type, $name, $content, $perPage);
        }

        return $this->cachedDnsRecords;
    }

    public function hasRecord(string $zoneName, string $type, string $name): bool
    {
        if (empty($this->cachedDnsRecords)) {
            $this->fetchDns($zoneName);
        }

        foreach ($this->cachedDnsRecords as $dnsRecord) {
            if ($dnsRecord->match($type, $name)) {
                return true;
            }
        }

        return false;
    }

    public function importDns(string $zoneName, string $type, string $name, string $content, bool $isProxied): void
    {
        $zoneId = $this->getZoneId($zoneName);

        $this->getDnsClient()->addRecord($zoneId, $type, $name, $content, 0, $isProxied);
    }

    private function getZoneId(string $zoneName): string
    {
        if (empty($this->cachedZones)) {
            $this->fetchZones();
        }

        if (!array_key_exists($zoneName, $this->cachedZones)) {
            throw new \InvalidArgumentException(sprintf('No zone found with name "%s".', $zoneName));
        }

        $zone = $this->cachedZones[$zoneName];

        return $zone->getId();
    }

    private function fetchZones(): void
    {
        $zoneList = $this->getZonesClient()->listZones();

        $this->cachedZones = [];
        foreach ($zoneList->result as $result) {
            $zone = Zone::createFromAPIResult($result);

            $this->cachedZones[$zone->getName()] = $zone;
        }
    }

    private function fetchDns(
        string $zoneName,
        string $type = null,
        string $name = null,
        string $content = null,
        int $perPage = 1000
    ): void {
        $zoneId = $this->getZoneId($zoneName);

        $dnsList = $this->getDnsClient()->listRecords($zoneId, (string) $type, (string) $name, (string) $content, 1, $perPage);

        $this->cachedDnsRecords = [];
        foreach ($dnsList->result as $result) {
            $dnsRecord = DnsRecord::createFromResult($result);

            $this->cachedDnsRecords[] = $dnsRecord;
        }
    }

    private function getZonesClient(): Zones
    {
        if (!$this->zonesClient) {
            $this->zonesClient = new Zones($this->adapter);
        }

        return $this->zonesClient;
    }

    private function getDnsClient(): DNS
    {
        if (!$this->dnsClient) {
            $this->dnsClient = new DNS($this->adapter);
        }

        return $this->dnsClient;
    }
}
