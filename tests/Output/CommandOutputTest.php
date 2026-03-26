<?php

declare(strict_types=1);

namespace Softspring\Component\CommandController\Tests\Output;

use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Softspring\Component\CommandController\Output\LoggerCommandOutput;
use Softspring\Component\CommandController\Output\StreamedCommandOutput;

final class CommandOutputTest extends TestCase
{
    public function testLoggerCommandOutputAccumulatesUntilNewline(): void
    {
        $logger = new ArrayLoggerStub();
        $output = new LoggerCommandOutput($logger);

        $output->write('Hello');
        $output->writeln(' world');

        self::assertSame(['Hello world'], $logger->messages);
    }

    public function testStreamedCommandOutputWritesToStreamAndEchoesOutput(): void
    {
        $stream = fopen('php://memory', 'w+');
        self::assertNotFalse($stream);

        $output = new StreamedCommandOutput($stream);
        $initialBufferLevel = ob_get_level();

        ob_start();
        $output->writeln('Hello world');

        while (ob_get_level() > $initialBufferLevel) {
            ob_end_clean();
        }

        rewind($stream);
        $streamContents = stream_get_contents($stream);
        fclose($stream);

        self::assertSame("Hello world\n", $streamContents);
    }
}

final class ArrayLoggerStub extends AbstractLogger
{
    /**
     * @var string[]
     */
    public array $messages = [];

    public function log($level, $message, array $context = []): void
    {
        $this->messages[] = (string) $message;
    }
}
