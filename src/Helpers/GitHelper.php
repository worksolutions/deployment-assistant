<?php

namespace WS\DeploymentAssistant\Helpers;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GitHelper
 * @package WS\DeployAssistant\Helpers
 */
class GitHelper extends Helper
{
    const NAME = 'git';

    /** @var NullOutput */
    private $output;

    /**
     * GitHelper constructor.
     */
    public function __construct()
    {
        $this->output = new NullOutput();
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

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param $command
     * @return string
     */
    private function run($command) {
        $output = array();
        $process = $this->processHelper()->run($this->output, $command, null,
            function ($type, $line) use (&$output) {
                $output[] = $line;
            });
        $output = implode("\n", $output);

        if (!$process->isSuccessful()) {
            throw new RuntimeException($output);
        }

        return $output;
    }

    /**
     * @return \Symfony\Component\Console\Helper\HelperInterface|ProcessHelper
     */
    private function processHelper() {
        return $this->getHelperSet()->get('process');
    }

    /**
     * @return bool
     */
    public function isWorkDirHasNotCommitChanges()
    {
        return strpos($this->run('git status'), 'nothing to commit') === false;
    }

    /**
     * @return bool
     */
    public function isLocalRepoAheadRemoteRepo($remoteName, $remoteBranch)
    {
        $currentBranch = $this->getCurrentBranch();
        $result = trim($this->run("git rev-list --left-right --count {$currentBranch}...{$remoteName}/{$remoteBranch}"));
        $result = explode("\t", $result);

        return (bool) $result[0] && !(bool)$result[1];
    }

    /**
     * @param string $remoteName
     * @param string $remoteBranch
     * @return bool
     */
    public function isLocalBranchAndRemoteBranchBothModified($remoteName, $remoteBranch)
    {
        $currentBranch = $this->getCurrentBranch();
        $result = trim($this->run("git rev-list --left-right --count {$currentBranch}...{$remoteName}/{$remoteBranch}"));
        $result = explode("\t", $result);

        return (bool) $result[0] && (bool) $result[1];
    }

    /**
     * @param string $remoteName
     * @param string $remoteBranch
     * @return bool
     */
    public function isLocalRepoBehindOfRemoteRepo($remoteName, $remoteBranch)
    {
        $currentBranch = $this->getCurrentBranch();
        $result = trim($this->run("git rev-list --left-right --count {$currentBranch}...{$remoteName}/{$remoteBranch}"));
        $result = explode("\t", $result);

        return !(bool) $result[0] && (bool) $result[1];
    }

    /**
     * @param string $name
     */
    public function fetchRemoteRepo($name)
    {
        $this->run("git fetch {$name}");
    }

    /**
     * @param string $origin
     * @param string $branch
     */
    public function pullChanges($origin, $branch)
    {
        $this->run("git pull {$origin} {$branch}");
    }

    /**
     * @return string
     */
    public function getCurrentBranch()
    {
        return trim($this->run('git rev-parse --abbrev-ref HEAD'));
    }
}
