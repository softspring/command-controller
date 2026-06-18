<?php

declare(strict_types=1);

namespace Softspring\Component\CommandController\Tests\Unit\Runner;

use PHPUnit\Framework\TestCase;
use Softspring\Component\CommandController\Runner\StreamedCommandRunner;

final class StreamedCommandRunnerTest extends TestCase
{
    public function testItCreatesPlainTextStreamedResponse(): void
    {
        $response = StreamedCommandRunner::createRunCommandStreamedResponse(['cache:clear']);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('text/plain', $response->headers->get('Content-Type'));
        self::assertSame('no', $response->headers->get('X-Accel-Buffering'));
    }

    public function testItCreatesPlainTextStreamedResponseForSeveralCommands(): void
    {
        $response = StreamedCommandRunner::createRunCommandsStreamedResponse([
            ['cache:clear'],
            ['assets:install'],
        ]);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('text/plain', $response->headers->get('Content-Type'));
        self::assertSame('no', $response->headers->get('X-Accel-Buffering'));
    }
}
