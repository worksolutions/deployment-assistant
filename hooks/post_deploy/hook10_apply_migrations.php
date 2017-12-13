<?php

use Symfony\Component\Console\Output\OutputInterface;
use WS\DeploymentAssistant\Commands\DeployCommand\DeployCommandHook;

class hook10_apply_migrations extends DeployCommandHook {

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Check and apply migrations by bitrix module reduce-migrations';
    }

    /**
     * @return string|void
     * @throws \WS\DeploymentAssistant\RuntimeException
     */
    public function run()
    {
        $cwd = getcwd();
        $migrateTool = $cwd . "/bitrix/tools/migrate";

        if (!file_exists($migrateTool)) {
            $this->setResult('module is not installed');
            return;
        }

        $output = clone $this->getOutput();
        $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $this->getOutput()->writeln('');
        $this->getExecHelper()->exec(
            $output,
            'php ' . $migrateTool . ' apply -f --skip-optional',
            null,
            null,
            OutputInterface::VERBOSITY_QUIET
        );
    }
}