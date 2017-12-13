<?php

namespace WS\DeploymentAssistant\Helpers;


use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use WS\DeploymentAssistant\RuntimeException;

class ExecHelper extends Helper
{
    const NAME = 'exec';

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Runs an external process.
     *
     * @param OutputInterface      $output    An OutputInterface instance
     * @param string|array|Process $cmd       An instance of Process or an array of arguments to escape and run or a command to run
     * @param string|null          $error     An error message that must be displayed if something went wrong
     * @param callable|null        $callback  A PHP callback to run whenever there is some
     *                                        output available on STDOUT or STDERR
     * @param int                  $verbosity The threshold for verbosity
     *
     * @return string
     */
    public function exec(OutputInterface $output, $cmd, $error = null, $callback = null, $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE) {
        $process = $this->getProcessHelper()->run($output, $cmd, $error, $callback, $verbosity);

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getOutput());
        }

        return $process->getOutput();
    }

    /**
     * @return \Symfony\Component\Console\Helper\ProcessHelper|HelperInterface
     */
    protected function getProcessHelper() {
        return $this->getHelperSet()->get('process');
    }
}