<?php

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws RuntimeException
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        $branchName = $input->getArgument('branch');
        $currentBranch = $this->getGitHelper()->getCurrentBranch();

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        if ($currentBranch === $branchName) {
            return;
        }

        $question = new ConfirmationQuestion(
            'Local branch and remote branch has different names. Continue?', false);

        if (!$questionHelper->ask($input, $output, $question)) {
            throw new RuntimeException('Local branch and remote branch has different names');
        }
    }
}
