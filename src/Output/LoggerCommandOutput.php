<?php

namespace Softspring\Component\CommandController\Output;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\Output;

class LoggerCommandOutput extends Output
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, ?int $verbosity = self::VERBOSITY_NORMAL, bool $decorated = false, OutputFormatterInterface $formatter = null)
    {
        parent::__construct($verbosity, $decorated, $formatter);
        $this->logger = $logger;
    }

    protected string $accumulatedMessage = '';

    protected function doWrite(string $message, bool $newline): void
    {
        $this->accumulatedMessage .= $message;

        if ($newline) {
            $this->logger->info($this->accumulatedMessage);
            $this->accumulatedMessage = '';
        }
    }
}
