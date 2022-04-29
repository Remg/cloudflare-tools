<?php

namespace Remg\Command\Cloudflare;

use Remg\Cloudflare\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractCommand extends Command
{
    protected Client $cloudflare;
    protected ?SymfonyStyle $io = null;

    public function __construct(Client $cloudflare)
    {
        $this->cloudflare = $cloudflare;

        parent::__construct();
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }
}
