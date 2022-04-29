<?php

namespace Remg;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct(iterable $commands, string $version)
    {
        parent::__construct('Remg', $version);

        foreach ($commands as $command) {
            $this->add($command);
        }
    }
}
