<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WS\DeploymentAssistant\Commands\DeployCommand\DeployCommandHook;
use WS\DeploymentAssistant\RuntimeException;


/**
 * Class hook10_check_remote_and_local_branches_both_modified
 */
class hook10_check_remote_and_local_branches_both_modified extends DeployCommandHook
{

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Checking that the local branch and remote branch are both modified';
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

        if ($this->isLocalBranchAndRemoteBranchBothModified($remoteName, $branchName)) {
            throw new RuntimeException(
                'The local branch and remote branch are both modified.
There is risk of conflicts while deploying.

Please push production changes to remote branch with force parameter, then pull changes locally,
then resolve conflicts and try to deploy again.');
        }
    }

    private function isLocalBranchAndRemoteBranchBothModified($remoteName, $branchName)
    {
        $currentBranch = $this->getGitHelper()->getCurrentBranch();
        $result = trim($this->getProcessHelper()->run(
            "git rev-list --left-right --count {$currentBranch}...{$remoteName}/{$branchName}"));
        $result = explode("\t", $result);

        return (bool) $result[0] && (bool) $result[1];
    }
}
