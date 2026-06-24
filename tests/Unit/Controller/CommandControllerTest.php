<?php

declare(strict_types=1);

namespace Softspring\Component\CommandController\Tests\Unit\Controller;

use LogicException;
use PHPUnit\Framework\TestCase;
use Softspring\Component\CommandController\Controller\CommandController;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class CommandControllerTest extends TestCase
{
    public function testItBuildsACommandResponseFromAllowedRequestValues(): void
    {
        $request = new Request([
            'limit' => '10',
            'ignored_query' => 'value',
        ]);
        $request->attributes->set('stream', false);
        $request->attributes->set('id', '42');
        $request->attributes->set('force', true);
        $request->attributes->set('ignored_attribute', 'value');

        $controller = new TestCommandController();
        $response = $controller->__invoke('app:sync', $request, ['id'], ['limit', 'force']);

        self::assertSame(202, $response->getStatusCode());
        self::assertSame([
            'app:sync',
            '--force' => true,
            '--limit' => '10',
            'id' => '42',
        ], $controller->lastBufferedCommand);
        self::assertSame([], $controller->lastBufferedOptions);
    }

    public function testItUsesRouteParametersWhenAllowedOptionsContainObjects(): void
    {
        $request = new Request();
        $request->attributes->set('stream', false);
        $request->attributes->set('entity', new stdClass());
        $request->attributes->set('_route_params', [
            'entity' => 'route-value',
        ]);

        $controller = new TestCommandController();
        $controller->__invoke('app:sync', $request, [], ['entity']);

        self::assertSame([
            'app:sync',
            '--entity' => 'route-value',
        ], $controller->lastBufferedCommand);
    }

    public function testItUsesQueryParametersWhenAllowedObjectOptionsHaveNoScalarRouteParameter(): void
    {
        $request = new Request([
            'entity' => 'query-value',
        ]);
        $request->attributes->set('stream', false);
        $request->attributes->set('entity', new stdClass());
        $request->attributes->set('_route_params', [
            'entity' => new stdClass(),
        ]);

        $controller = new TestCommandController();
        $controller->__invoke('app:sync', $request, [], ['entity']);

        self::assertSame([
            'app:sync',
            '--entity' => 'query-value',
        ], $controller->lastBufferedCommand);
    }

    public function testItBuildsAStreamedCommandResponseWithLoggerOutput(): void
    {
        $logger = new stdClass();
        $request = new Request();
        $request->attributes->set('loggerOutputService', 'logger.command_output');

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with('logger.command_output')->willReturn($logger);

        $controller = new TestCommandController($container);
        $response = $controller->__invoke('app:sync', $request);

        self::assertSame(203, $response->getStatusCode());
        self::assertSame(['app:sync'], $controller->lastStreamedCommand);
        self::assertSame(['outputLogger' => $logger], $controller->lastStreamedOptions);
    }

    public function testDeprecatedRunMethodDelegatesToInvoke(): void
    {
        $request = new Request();
        $request->attributes->set('stream', false);

        $controller = new TestCommandController();
        $response = @$controller->run('app:sync', $request);

        self::assertSame(202, $response->getStatusCode());
        self::assertSame(['app:sync'], $controller->lastBufferedCommand);
    }

    public function testItRequiresContainerWhenLoggerOutputServiceIsConfigured(): void
    {
        $request = new Request();
        $request->attributes->set('loggerOutputService', 'logger.command_output');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('To use loggerOutputService you must configure controller as service and inject the container in the controller constructor');

        (new CommandController())->__invoke('cache:clear', $request);
    }
}

final class TestCommandController extends CommandController
{
    /**
     * @var array<string|int, mixed>|null
     */
    public ?array $lastBufferedCommand = null;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $lastBufferedOptions = null;

    /**
     * @var array<string|int, mixed>|null
     */
    public ?array $lastStreamedCommand = null;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $lastStreamedOptions = null;

    protected function createRunCommandStreamedResponse(array $command, array $options = []): StreamedResponse
    {
        $this->lastStreamedCommand = $command;
        $this->lastStreamedOptions = $options;

        return new StreamedResponse(static function (): void {}, 203);
    }

    protected function createRunCommandResponse(array $command, array $options = []): Response
    {
        $this->lastBufferedCommand = $command;
        $this->lastBufferedOptions = $options;

        return new Response('', 202);
    }
}
