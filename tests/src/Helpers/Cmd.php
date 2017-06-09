<?php

namespace WS\DeploymentAssistant\Tests\Helpers;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Class Cmd
 * @package Tests\Helpers
 */
class Cmd
{
    /**
     * @param string|array $command
     * @param null $workdir
     * @return string
     */
    public static function run($command, $workdir = null)
    {
        $command = (array) $command;
        $process = new Process(implode(" ", $command), $workdir);

        $outputParts = array();
        $process->run(function ($type, $line) use (&$outputParts) {
            $outputParts[] = $line;
        });
        $output = implode("\n", $outputParts);

        if (!$process->isSuccessful()) {
            throw new RuntimeException($output);
        }

        return $output;
    }
}
