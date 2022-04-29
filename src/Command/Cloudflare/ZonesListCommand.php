<?php

namespace Remg\Command\Cloudflare;

use Remg\Cloudflare\Model\Zone;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ZonesListCommand extends AbstractCommand
{
    public static $defaultName = 'cloudflare:zones:list';

    protected function configure()
    {
        $this
            ->setDescription('List available zones from Cloudflare')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Listing zones managed by API Token');

        $zones = $this->cloudflare->listZones();

        $rows = array_map(function (Zone $zone): array {
            return [
                $zone->getName(),
                $zone->getId(),
                $zone->getStatus(),
            ];
        }, $zones);

        $this->io->table(['Name', 'ID', 'Status'], $rows);

        return self::SUCCESS;
    }
}
