<?php

namespace Remg\Command\Cloudflare;

use Remg\Cloudflare\Model\DnsRecord;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DnsListCommand extends AbstractCommand
{
    protected static $defaultName = 'cloudflare:dns:list';

    protected function configure()
    {
        $this
            ->setDescription('List DNS records for a given zone')
            ->addArgument('zone', InputArgument::OPTIONAL, 'Zone name for DNS lsiting')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, 'DNS records type to filter')
            ->addOption('name', null, InputOption::VALUE_OPTIONAL, 'DNS records name to filter')
            ->addOption('content', null, InputOption::VALUE_OPTIONAL, 'DNS records content to filter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title('Listing DNS records');

        $zone = $input->getArgument('zone');

        if (!$zone) {
            $zone = $this->io->choice('Select a zone:', array_values($this->cloudflare->listZones()));
        }

        $this->io->comment(sprintf('Listing DNS records for zone "%s":', $zone));

        $dnsRecords = $this->cloudflare->listDns(
            $zone,
            $input->getOption('type'),
            $input->getOption('name'),
            $input->getOption('content')
        );

        $rows = array_map(function (DnsRecord $dnsRecord): array {
            return [
                $dnsRecord->getType(),
                $dnsRecord->getName(),
                $dnsRecord->getShortContent(),
            ];
        }, $dnsRecords);

        $this->io->table(['Type', 'Name', 'Content'], $rows);

        return self::SUCCESS;
    }
}
