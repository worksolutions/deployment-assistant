<?php

namespace WS\DeploymentAssistant\Helpers;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WS\DeploymentAssistant\RuntimeException;

/**
 * Class GitHelper
 * @package WS\DeployAssistant\Helpers
 */
class GitHelper extends Helper
{
    const NAME = 'git';

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
     * @return HelperInterface|ExecHelper
     * @throws RuntimeException
     */
    private function execHelper() {
        if (!$this->getHelperSet()) {
            throw new RuntimeException("helperSet is not set");
        }

        return $this->getHelperSet()->get(ExecHelper::NAME);
    }

    /**
     * @param OutputInterface $output
     * @param string $name
     */
    public function fetchRemoteRepo(OutputInterface $output, $name)
    {
        $this->execHelper()->exec($output, "git fetch {$name}");
    }

    /**
     * @param OutputInterface $output
     * @param string $origin
     * @param string $branch
     */
    public function pullChanges(OutputInterface $output, $origin, $branch)
    {
        $this->execHelper()->exec($output, "git pull {$origin} {$branch}");
    }

    /**
     * @param OutputInterface $output
     * @return string
     */
    public function getCurrentBranch(OutputInterface $output)
    {
        return trim($this->execHelper()->exec($output, 'git rev-parse --abbrev-ref HEAD'));
    }
}
