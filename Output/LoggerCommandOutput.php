<?php

namespace Softspring\CommandController\Utils;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\Output;

class LoggerCommandOutput extends Output
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger, ?int $verbosity = self::VERBOSITY_NORMAL, bool $decorated = false, OutputFormatterInterface $formatter = null)
    {
        parent::__construct($verbosity, $decorated, $formatter);
        $this->logger = $logger;
    }

    /**
     * @var string
     */
    protected $accumulatedMessage = '';

    protected function doWrite($message, $newline)
    {
        $this->accumulatedMessage .= $message;

        if ($newline) {
            $this->logger->info($this->accumulatedMessage);
            $this->accumulatedMessage = '';
        }
    }
}