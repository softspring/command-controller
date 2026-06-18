<?php

declare(strict_types=1);

namespace Softspring\Component\CommandController\Tests\Unit\Controller;

use LogicException;
use PHPUnit\Framework\TestCase;
use Softspring\Component\CommandController\Controller\CommandController;
use Symfony\Component\HttpFoundation\Request;

final class CommandControllerTest extends TestCase
{
    public function testItRequiresContainerWhenLoggerOutputServiceIsConfigured(): void
    {
        $request = new Request();
        $request->attributes->set('loggerOutputService', 'logger.command_output');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('To use loggerOutputService you must configure controller as service and inject the container in the controller constructor');

        (new CommandController())->__invoke('cache:clear', $request);
    }
}
