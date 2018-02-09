<?php

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
     * @return void
     * @throws \WS\DeploymentAssistant\RuntimeException
     */
    public function run()
    {
        $remoteName = $this->getInput()->getArgument('remote');
        $branchName = $this->getInput()->getArgument('branch');

        if ($this->isLocalRepoAheadRemoteRepo($remoteName, $branchName)) {
            throw new RuntimeException(
                'Your local branch is ahead of remote branch. Please push your changes to remote');
        }
    }

    /**
     * @param $remoteName string
     * @param $remoteBranch string
     * @return bool
     */
    public function isLocalRepoAheadRemoteRepo($remoteName, $remoteBranch)
    {
        $currentBranch = $this->getCurrentBranch();
        $output = trim($this->execCmd(
            "git rev-list --left-right {$currentBranch}...{$remoteName}/{$remoteBranch}")
        );

        $result = array(0, 0);
        if (!empty($output)) {
            $items = explode(PHP_EOL, $output);
            foreach ($items as $item) {
                if ($item[0] === '<') {
                    $result[0]++;
                }
                if ($item[0] === '>') {
                    $result[1]++;
                }
            }
        }

        return (bool) $result[0] && !(bool)$result[1];
    }
}
