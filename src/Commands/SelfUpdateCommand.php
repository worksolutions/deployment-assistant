<?php

namespace WS\DeploymentAssistant\Commands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WS\DeploymentAssistant\Helpers\VersionHelper;

/**
 * Class SelfUpdateCommand
 * @package WS\DeployAssistant\Commands
 */
class SelfUpdateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Update deployment tool')
            ->setHelp('Command to update deployment tool');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var VersionHelper $versionHelper */
        $versionHelper = $this->getHelper(VersionHelper::NAME);

        if (!$versionHelper->isNewVersionAvailable()) {
            $output->writeln('<info>You are using the latest version</info>');
            return;
        }

        $versionHelper->update();

        $output->writeln('<info>Update successfully</info>');
    }
}
