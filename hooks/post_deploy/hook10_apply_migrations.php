<?php

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
     * @return void
     * @throws \WS\DeploymentAssistant\RuntimeException
     */
    public function run()
    {
        $cwd = getcwd();
        $migrateTool = $cwd . "/bitrix/module/tools/migrate";

        if (file_exists($migrateTool)) {
            $this->execCmd('php ' . $migrateTool . ' apply -f --skip-optional');
        }
    }
}