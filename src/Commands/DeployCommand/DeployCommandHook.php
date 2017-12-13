<?php

namespace WS\DeploymentAssistant\Commands\DeployCommand;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use WS\DeploymentAssistant\Helpers\ExecHelper;
use WS\DeploymentAssistant\Helpers\GitHelper;
use WS\DeploymentAssistant\RuntimeException;

/**
 * Class DeployHook
 * @package WS\DeployAssistant\Commands\DeployCommand
 */
abstract class DeployCommandHook
{
    /** @var HelperSet */
    private $helperSet;
    /** @var InputInterface */
    private $input;
    /** @var OutputInterface */
    private $output;

    /**
     * @return string
     */
    abstract public function getTitle();

    /**
     * @return void
     * @throws RuntimeException
     */
    abstract public function run();

    public static function getClassName()
    {
        return get_called_class();
    }

    /**
     * @param HelperSet $helperSet
     */
    public function setHelperSet(HelperSet $helperSet)
    {
        $this->helperSet = $helperSet;
    }

    /**
     * @param InputInterface $input
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return InputInterface
     */
    protected function getInput()
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * @param $name string
     * @return HelperInterface
     */
    protected function getHelper($name)
    {
        return $this->helperSet->get($name);
    }

    /**
     * @return ExecHelper|HelperInterface
     */
    protected function getExecHelper() {
        return $this->getHelper(ExecHelper::NAME);
    }

    /**
     * Runs an external process.
     *
     * @param string|array|Process $cmd       An instance of Process or an array of arguments to escape and run or a command to run
     * @param string|null          $error     An error message that must be displayed if something went wrong
     * @param callable|null        $callback  A PHP callback to run whenever there is some
     *                                        output available on STDOUT or STDERR
     * @param int                  $verbosity The threshold for verbosity
     *
     * @return string
     */
    protected function execCmd($cmd, $error = null, $callback = null, $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE) {
        return $this->getExecHelper()->exec($this->output, $cmd, $error, $callback, $verbosity);
    }

    /**
     * @return string
     */
    protected function getCurrentBranch()
    {
        return $this->getGitHelper()->getCurrentBranch($this->output);
    }

    /**
     * @return GitHelper|HelperInterface
     */
    protected function getGitHelper() {
        return $this->getHelper(GitHelper::NAME);
    }
}
