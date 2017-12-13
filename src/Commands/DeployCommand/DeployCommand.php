<?php

namespace WS\DeploymentAssistant\Commands\DeployCommand;
use FilesystemIterator;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WS\DeploymentAssistant\Helpers\GitHelper;
use WS\DeploymentAssistant\Helpers\VersionHelper;
use WS\DeploymentAssistant\RuntimeException;

/**
 * Class DeployCommand
 * @package WS\DeployAssistant\Commands\DeployCommand
 */
class DeployCommand extends Command
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws InvalidArgumentException
     * @throws LogicException
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
     * @throws LogicException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var GitHelper $gitHelper */
        $gitHelper = $this->getHelper(GitHelper::NAME);

        $remoteName = $input->getArgument('remote');
        $branchName = $input->getArgument('branch');

        $hooksDir = __DIR__ . '/../../../hooks';

        $this->runHooksFromDir($input, $output, $hooksDir . '/pre_fetch');

        $this->doPart($output, 'Fetching remote repo',
            function() use ($gitHelper, $output) {
                $gitHelper->fetchRemoteRepo($output, 'origin');
            }
        );

        $this->runHooksFromDir($input, $output, $hooksDir . '/post_fetch');

        $this->doPart($output, 'Pulling changes',
            function() use ($gitHelper, $remoteName, $branchName, $output) {
                $gitHelper->pullChanges($output, $remoteName, $branchName);
            }
        );

        $this->runHooksFromDir($input, $output, $hooksDir . '/post_deploy');
    }

    private function doPart(OutputInterface $output, $message, $func)
    {
        $output->write("{$message}...");

        try {
            $result = $func();
            if (!is_null($result) && $result !== false) {
                $output->writeln('<comment>'. $result .'</comment>');
            }

            if (is_null($result)) {
                $output->writeln('<info>ok</info>');
            }
        } catch (\Exception $e) {
            $output->writeln('<error>fatal</error>');
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $e;
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $dir
     * @throws \Exception
     */
    private function runHooksFromDir(InputInterface $input, OutputInterface $output, $dir)
    {
        $iterator = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);

        $files = array();
        foreach ($iterator as $file) {
            /** @var SplFileInfo $file */

            if ($file->isDir()) {
                continue;
            }

            if ($file->getExtension() !== "php") {
                continue;
            }

            $files[] = $file;
        }

        usort($files, function(SplFileInfo $a, SplFileInfo $b) {
            return $a->getBasename() > $b->getBasename();
        });

        foreach ($files as $file) {
            $this->runHook($input, $output, $file);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param SplFileInfo $file
     * @throws \Exception
     */
    private function runHook(InputInterface $input, OutputInterface $output, SplFileInfo $file)
    {
        $hookClassName = $file->getBasename('.php');

        if (!class_exists($hookClassName)) {
            /** @noinspection PhpIncludeInspection */
            require $file->getPathname();
        }

        /** @var DeployCommandHook $hook */
        $hook = new $hookClassName;
        $hook->setInput($input);
        $hook->setOutput($output);
        $hook->setHelperSet($this->getHelperSet());

        $this->doPart($output, $hook->getTitle(), function () use ($hook) {
            $hook->run();

            return $hook->getResult();
        });
    }
}
