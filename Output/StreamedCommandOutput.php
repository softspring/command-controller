<?php

namespace Softspring\CommandController\Utils;

use Symfony\Component\Console\Output\StreamOutput;

class StreamedCommandOutput extends StreamOutput
{
    protected function doWrite($message, $newline)
    {
        ob_start();
        if (
            false === @fwrite($this->getStream(), $message) ||
            (
                $newline &&
                (false === @fwrite($this->getStream(), PHP_EOL))
            )
        ) {
            throw new \RuntimeException('Unable to write output.');
        }

        echo $message.($newline?"\n":'');

        ob_flush();
        flush();
    }
}