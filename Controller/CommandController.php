<?php

namespace Softspring\CommandController\Controller;

use Softspring\CommandController\Utils\StreamedCommandRunner;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CommandController extends AbstractController
{
    public function run(string $command, Request $request, array $arguments = [], array $options = []): StreamedResponse
    {
        $options = array_filter($request->attributes->all(), function ($key) use ($options) { return in_array($key, $options); }, ARRAY_FILTER_USE_KEY);
        $arguments = array_filter($request->attributes->all(), function ($key) use ($arguments) { return in_array($key, $arguments); }, ARRAY_FILTER_USE_KEY);

        foreach ($options as $key => $value) {
            $options["--$key"] = $value;
            unset($options[$key]);
        }

        $commandConfig = array_merge([$command], $options, $arguments);

        return StreamedCommandRunner::createRunCommandStreamedResponse($commandConfig)->send();
    }
}