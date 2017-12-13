<?php

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use WS\DeploymentAssistant\Commands\DeployCommand\DeployCommandHook;
use WS\DeploymentAssistant\RuntimeException;

/**
 * Class hook10_check_remote_and_local_branch_has_same_names
 */
class hook10_check_remote_and_local_branch_has_same_names extends DeployCommandHook
{

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Checking is remote and local branches has same names';
    }

    /**
     * @return void
     */
    public function run()
    {
        $branchName = $this->getInput()->getArgument('branch');
        $currentBranch = $this->getCurrentBranch();

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        if ($currentBranch === $branchName) {
            return;
        }

        $question = new ConfirmationQuestion(
            'Local branch and remote branch has different names. Continue?', false);

        if (!$questionHelper->ask($this->getInput(), $this->getOutput(), $question)) {
            throw new RuntimeException('Local branch and remote branch has different names');
        }
    }
}
