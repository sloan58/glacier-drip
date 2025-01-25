<?php

namespace App\Logging;

use Monolog\Level;
use Monolog\Processor\IntrospectionProcessor;

class CustomFormatter
{
    public function __invoke($logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->pushProcessor(
                new IntrospectionProcessor(level: Level::Debug, skipClassesPartials: ['Illuminate'])
            );
        }
    }
}
