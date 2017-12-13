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
            $this->setResultMessage('module is not installed');
            return;
        }

        $migrateCmdOutput = clone $this->getOutput();

        if ($this->getOutput()->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
            $this->setResultMessage(false);
            $migrateCmdOutput->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        $this->getOutput()->writeln('');
        $this->getExecHelper()->exec(
            $migrateCmdOutput,
            'php ' . $migrateTool . ' apply -f',
            null,
            null,
            OutputInterface::VERBOSITY_NORMAL
        );
    }
}
