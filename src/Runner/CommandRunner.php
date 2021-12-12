<?php

namespace Softspring\CommandController\Runner;

use App\Kernel;
use Psr\Log\LoggerInterface;
use Softspring\CommandController\Output\LoggerCommandOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Response;

class CommandRunner extends StreamedCommandRunner
{
    public static function createRunCommandResponse(array $command, array $options = []): Response
    {
        try {
            [$content, $exitCode] = self::_doRunCommand(new ArrayInput($command), $options);

            return new Response($content, $exitCode ? 500 : 200, [
                'Content-Type' => 'text/plain',
            ]);
        } catch (\Exception $e) {
            return new Response('Error running command', 500, [
                'Content-Type' => 'text/plain',
            ]);
        }
    }

    private static function _doRunCommand(InputInterface $input, array $options = []): array
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
            $output = new BufferedOutput();
        }

        $kernel = new Kernel($env, $debug);
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $exitCode = $application->run($input, $output);

        return [$output instanceof BufferedOutput ? $output->fetch() : '', $exitCode];
    }
}
