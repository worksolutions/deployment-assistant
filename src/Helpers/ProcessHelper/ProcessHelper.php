<?php

namespace WS\DeploymentAssistant\Helpers\ProcessHelper;

use Symfony\Component\Console\Helper\Helper;
use WS\DeploymentAssistant\RuntimeException;


/**
 * Class ProcessHelper
 * @package WS\DeploymentAssistant\Helpers\ProcessHelper\ProcessHelper
 */
class ProcessHelper extends Helper
{
    const NAME = 'process';

    /**
     * @param $command
     * @return Process
     */
    public function create($command)
    {
        return new Process($command);
    }

    /**
     * @param string $command
     * @return string
     * @throws RuntimeException
     */
    public function run($command)
    {
        $process = $this->create($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getOutput());
        }

        return $process->getOutput();
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return self::NAME;
    }
}
