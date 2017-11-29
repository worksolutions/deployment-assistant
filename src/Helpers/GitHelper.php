<?php

namespace WS\DeploymentAssistant\Helpers;
use Symfony\Component\Console\Helper\Helper;
use WS\DeploymentAssistant\Helpers\ProcessHelper\ProcessHelper;
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
     * @return \Symfony\Component\Console\Helper\HelperInterface|ProcessHelper
     * @throws \WS\DeploymentAssistant\RuntimeException
     */
    private function processHelper() {
        if (!$this->getHelperSet()) {
            throw new RuntimeException("helperSet is not set");
        }

        return $this->getHelperSet()->get(ProcessHelper::NAME);
    }

    /**
     * @param string $name
     */
    public function fetchRemoteRepo($name)
    {
        $this->processHelper()->run("git fetch {$name}");
    }

    /**
     * @param string $origin
     * @param string $branch
     */
    public function pullChanges($origin, $branch)
    {
        $this->processHelper()->run("git pull {$origin} {$branch}");
    }

    /**
     * @return string
     */
    public function getCurrentBranch()
    {
        return trim($this->processHelper()->run('git rev-parse --abbrev-ref HEAD'));
    }
}
