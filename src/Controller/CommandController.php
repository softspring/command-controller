<?php

namespace Softspring\Component\CommandController\Controller;

use Softspring\Component\CommandController\Runner\CommandRunner;
use Softspring\Component\CommandController\Runner\StreamedCommandRunner;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommandController
{
    public function __construct(protected ?ContainerInterface $container = null)
    {
    }

    public function run(string $command, Request $request, array $arguments = [], array $options = []): Response
    {
        $options = array_filter($request->attributes->all() + $request->query->all(), function ($key) use ($options) { return in_array($key, $options); }, ARRAY_FILTER_USE_KEY);
        $arguments = array_filter($request->attributes->all() + $request->query->all(), function ($key) use ($arguments) { return in_array($key, $arguments); }, ARRAY_FILTER_USE_KEY);

        foreach ($options as $key => $value) {
            if (is_object($value)) {
                if (isset($request->attributes->get('_route_params')[$key]) && !is_object($request->attributes->get('_route_params')[$key])) {
                    $value = $request->attributes->get('_route_params')[$key];
                } elseif ($request->query->has($key)) {
                    $value = $request->query->get($key);
                }
            }

            $options["--$key"] = $value;
            unset($options[$key]);
        }

        $commandConfig = array_merge([$command], $options, $arguments);

        $commandOptions = [];

        if ($loggerOutputService = $request->attributes->get('loggerOutputService')) {
            if (!$this->container) {
                throw new \LogicException('To use loggerOutputService you must configure controller as service and inject the container in the controller constructor');
            }

            $commandOptions['outputLogger'] = $this->container->get($loggerOutputService);
        }

        $stream = (bool) $request->attributes->get('stream', true);

        if ($stream) {
            return StreamedCommandRunner::createRunCommandStreamedResponse($commandConfig, $commandOptions)->send();
        } else {
            return CommandRunner::createRunCommandResponse($commandConfig, $commandOptions);
        }
    }
}
