<?php

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WS\DeploymentAssistant\Commands\DeployCommand\DeployCommandHook;
use WS\DeploymentAssistant\RuntimeException;


/**
 * Class CheckWorkDirIsCleanPreDeployHook
 */
class hook20_check_work_dir_is_clean extends DeployCommandHook
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Checking that the work dir has no changes';
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \WS\DeploymentAssistant\RuntimeException
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        if ($this->isWorkDirHasNotCommitChanges()) {
            throw new RuntimeException('Work dir is not clean. Please commit changes');
        }
    }

    /**
     * @return bool
     */
    private function isWorkDirHasNotCommitChanges()
    {
        return strpos($this->getProcessHelper()
                ->run('git status'), 'nothing to commit') === false;
    }
}
