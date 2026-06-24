<?php

namespace Softspring\Component\CommandController\Runner;

use App\Kernel;
use Psr\Log\LoggerInterface;
use Softspring\Component\CommandController\Output\LoggerCommandOutput;
use Softspring\Component\CommandController\Output\StreamedCommandOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamedCommandRunner
{
    public static function createRunCommandStreamedResponse(array $command, array $options = []): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/plain',
            'X-Accel-Buffering' => 'no',
        ];

        return new StreamedResponse(function () use ($command, $options): void {
            self::_doRunCommand(new ArrayInput($command), $options);
        }, 200, $headers);
    }

    public static function runCommand(array $command, array $options = []): void
    {
        self::createRunCommandStreamedResponse($command, $options)->send();
    }

    /**
     * @param array[] $commands
     */
    public static function createRunCommandsStreamedResponse(array $commands, array $options = []): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/plain',
            'X-Accel-Buffering' => 'no',
        ];

        return new StreamedResponse(function () use ($commands): void {
            foreach ($commands as $command) {
                self::_doRunCommand(new ArrayInput($command));
            }
        }, 200, $headers);
    }

    /**
     * @param array[] $commands
     */
    public static function runCommands(array $commands, array $options = []): void
    {
        self::createRunCommandsStreamedResponse($commands, $options)->send();
    }

    private static function _doRunCommand(InputInterface $input, array $options = []): void
    {
        $input->setInteractive(false);

        $env = $input->getParameterOption(['--env', '-e'], $_SERVER['APP_ENV'] ?? 'dev', true);
        $debug = (bool) ($_SERVER['APP_DEBUG'] ?? ('prod' !== $env)) && !$input->hasParameterOption('--no-debug', true);

        if ($debug) {
            umask(0000);

            if (class_exists(Debug::class)) {
                Debug::enable();
            }
        }

        if ($options['outputLogger'] ?? false) {
            /** @var LoggerInterface $outputLogger */
            $outputLogger = $options['outputLogger'];
            $output = new LoggerCommandOutput($outputLogger);
        } else {
            $output = new StreamedCommandOutput(fopen('php://stdout', 'w'));
        }

        /** @var \Symfony\Component\HttpKernel\KernelInterface $kernel */
        /** @phpstan-ignore-next-line  */
        $kernel = new Kernel($env, $debug);
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $application->run($input, $output);
    }
}
