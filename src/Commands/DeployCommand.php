<?php

namespace WS\DeploymentAssistant\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WS\DeploymentAssistant\Helpers\GitHelper;
use WS\DeploymentAssistant\Helpers\VersionHelper;

/**
 * Class DeployCommand
 * @package WS\DeployAssistant\Commands
 */
class DeployCommand extends Command
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var VersionHelper $updateHelper */
        $updateHelper = $this->getHelper(VersionHelper::NAME);
        $updateHelper->showWarningIfNewVersionAvailable($output);

        parent::interact($input, $output);
    }

    protected function configure()
    {
        $this
            ->setName('deploy')
            ->setDescription('Deploy changes')
            ->addArgument('remote', InputArgument::OPTIONAL, 'Remote repository name', 'origin')
            ->addArgument('branch', InputArgument::OPTIONAL, 'Remote branch name', 'master')
            ->setHelp('Command to safely deploy changes');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var GitHelper $gitHelper */
        $gitHelper = $this->getHelper(GitHelper::NAME);
        $gitHelper->setOutput($output);

        $remoteName = $input->getArgument('remote');
        $branchName = $input->getArgument('branch');

        $this->doPart($output, 'Checking that the work dir has no changes', function() use ($gitHelper) {
            if ($gitHelper->isWorkDirHasNotCommitChanges()) {
                throw new RuntimeException('Work dir is not clean. Please commit changes');
            }
        });

        $this->doPart($output, 'Fetching remote repo', function() use ($gitHelper) {
            $gitHelper->fetchRemoteRepo('origin');
        });

        $this->doPart($output, 'Checking that the local branch is not ahead of remote branch',
            function() use ($gitHelper, $remoteName, $branchName) {
                if ($gitHelper->isLocalRepoAheadRemoteRepo($remoteName, $branchName)) {
                    throw new RuntimeException('Your local branch is ahead of remote branch. Please push your changes to remote');
                }
            });

        $this->doPart($output, 'Checking that the local branch and remote branch are both modified',
            function () use ($gitHelper, $remoteName, $branchName) {
                if ($gitHelper->isLocalBranchAndRemoteBranchBothModified($remoteName, $branchName)) {
                    throw new RuntimeException(
'The local branch and remote branch are both modified.
There is risk of conflicts while deploying. 

Please push production changes to remote branch with force parameter, then pull changes locally, 
then resolve conflicts and try to deploy again.');
                }
            });

        $this->doPart($output, 'Checking that the local branch is behind of remote branch',
            function() use ($gitHelper, $remoteName, $branchName) {
                if (!$gitHelper->isLocalRepoBehindOfRemoteRepo($remoteName, $branchName)) {
                    throw new RuntimeException('There are nothing to pull');
                }
            });

        $this->doPart($output, 'Pulling changes', function() use ($gitHelper, $remoteName, $branchName) {
            $gitHelper->pullChanges($remoteName, $branchName);
        });
    }

    private function doPart(OutputInterface $output, $message, $func)
    {
        $output->write("{$message}...");

        try {
            $func();
            $output->writeln('<info>ok</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>fatal</error>');
            throw $e;
        }
    }
}
