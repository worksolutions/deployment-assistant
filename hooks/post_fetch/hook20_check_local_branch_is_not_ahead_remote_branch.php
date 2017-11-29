<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WS\DeploymentAssistant\Commands\DeployCommand\DeployCommandHook;
use WS\DeploymentAssistant\RuntimeException;


/**
 * Class hook20_check_local_branch_is_not_ahead_remote_branch
 */
class hook20_check_local_branch_is_not_ahead_remote_branch extends DeployCommandHook
{

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Checking that the local branch is not ahead of remote branch';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \WS\DeploymentAssistant\RuntimeException
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $remoteName = $input->getArgument('remote');
        $branchName = $input->getArgument('branch');

        if ($this->isLocalRepoAheadRemoteRepo($remoteName, $branchName)) {
            throw new RuntimeException(
                'Your local branch is ahead of remote branch. Please push your changes to remote');
        }
    }

    /**
     * @return bool
     */
    public function isLocalRepoAheadRemoteRepo($remoteName, $remoteBranch)
    {
        $currentBranch = $this->getGitHelper()->getCurrentBranch();
        $result = trim($this->getProcessHelper()->run(
            "git rev-list --left-right --count {$currentBranch}...{$remoteName}/{$remoteBranch}"));
        $result = explode("\t", $result);

        return (bool) $result[0] && !(bool)$result[1];
    }
}
