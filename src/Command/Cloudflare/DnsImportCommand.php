<?php

namespace Remg\Command\Cloudflare;

use League\Csv\Reader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DnsImportCommand extends AbstractCommand
{
    protected static $defaultName = 'cloudflare:dns:import';

    protected function configure()
    {
        $this
            ->setDescription('List DNS records for a given zone')
            ->addArgument('file', InputArgument::REQUIRED, 'Location of the file to import (relative or URL).')
            ->addArgument('zone', InputArgument::OPTIONAL, 'Zone name for DNS lsiting')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $zone = $input->getArgument('zone');

        if (!$zone) {
            $zone = $this->io->choice('Select a zone:', array_values($this->cloudflare->listZones()));
        }

        $this->io->comment(sprintf('Importing DNS records for zone "%s":', $zone));

        $filename = $input->getArgument('file');

        $csv = Reader::createFromString(file_get_contents($filename));

        $csv->setHeaderOffset(0);
        $csv->setDelimiter(',');

        $total = $csv->count();

        if (!$this->io->confirm(sprintf('Are you sure to import %d DNS records?', $total))) {
            return 1;
        }

        $this->io->progressStart($total);

        foreach ($csv as $row) {
            $this->io->progressAdvance();

            if ($this->cloudflare->hasRecord($zone, $row['type'], $row['name'])) {
                continue;
            }

            $this->cloudflare->importDns(
                $zone,
                $row['type'],
                $row['name'],
                $row['content'],
                true
            );
        }

        $this->io->progressFinish();
    }
}
