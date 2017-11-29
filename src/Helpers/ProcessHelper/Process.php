<?php

namespace WS\DeploymentAssistant\Helpers\ProcessHelper;
use WS\DeploymentAssistant\RuntimeException;


/**
 * Class ExecCommand
 * @package WS\DeploymentAssistant
 */
class Process
{
    private $output;
    private $returnValue;
    private $command;

    /**
     * ExecCommand constructor.
     * @param string $command
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * @return void
     * @throws \WS\DeploymentAssistant\RuntimeException
     */
    public function run()
    {
        if (!function_exists('exec')) {
            throw new RuntimeException();
        }

        exec($this->command, $this->output, $this->returnValue);
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->returnValue === 0;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return implode("\n", $this->output);
    }
}
