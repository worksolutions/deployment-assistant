<?php

namespace WS\DeploymentAssistant\Commands\DeployCommand;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WS\DeploymentAssistant\Helpers\GitHelper;
use WS\DeploymentAssistant\Helpers\ProcessHelper\ProcessHelper;
use WS\DeploymentAssistant\RuntimeException;

/**
 * Class DeployHook
 * @package WS\DeployAssistant\Commands\DeployCommand
 */
abstract class DeployCommandHook
{
    /** @var HelperSet */
    private $helperSet;

    /**
     * @return string
     */
    abstract public function getTitle();

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \WS\DeploymentAssistant\RuntimeException
     */
    abstract public function run(InputInterface $input, OutputInterface $output);

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
     * @return HelperInterface
     */
    protected function getHelper($name)
    {
        return $this->helperSet->get($name);
    }

    /**
     * @return ProcessHelper|HelperInterface
     */
    protected function getProcessHelper() {
        return $this->getHelper(ProcessHelper::NAME);
    }

    /**
     * @return GitHelper|HelperInterface
     */
    protected function getGitHelper() {
        return $this->getHelper(GitHelper::NAME);
    }
}
