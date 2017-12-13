<?php

use WS\DeploymentAssistant\Commands\DeployCommand\DeployCommandHook;


/**
 * Class hook30_check_local_branch_is_behind_remote_branch
 */
class hook30_check_local_branch_is_behind_remote_branch extends DeployCommandHook
{

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Checking that the local branch is behind of remote branch';
    }

    /**
     * @return void
     * @throws \WS\DeploymentAssistant\RuntimeException
     */
    public function run()
    {
        $remoteName = $this->getInput()->getArgument('remote');
        $branchName = $this->getInput()->getArgument('branch');

        if (!$this->isLocalRepoBehindOfRemoteRepo($remoteName, $branchName)) {
            throw new RuntimeException('There are nothing to pull');
        }
    }

    /**
     * @param string $remoteName
     * @param string $remoteBranch
     * @return bool
     */
    public function isLocalRepoBehindOfRemoteRepo($remoteName, $remoteBranch)
    {
        $currentBranch = $this->getCurrentBranch();
        $result = trim($this->execCmd(
            "git rev-list --left-right --count {$currentBranch}...{$remoteName}/{$remoteBranch}"));
        $result = explode("\t", $result);

        return !(bool) $result[0] && (bool) $result[1];
    }
}
