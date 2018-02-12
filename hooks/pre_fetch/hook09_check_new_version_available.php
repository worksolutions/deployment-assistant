<?php

use WS\DeploymentAssistant\Commands\DeployCommand\DeployCommandHook;
use WS\DeploymentAssistant\Helpers\VersionHelper;

/**
 * @author Afanasyev Pavel <afanasev@worksolutions.ru>
 */
class hook09_check_new_version_available extends DeployCommandHook {
    /**
     * @return string
     */
    public function getTitle() {
        return 'Check new version available';
    }

    /**
     * @return void
     * @throws \WS\DeploymentAssistant\RuntimeException
     */
    public function run() {
        /** @var VersionHelper $versionHelper */
        $versionHelper = $this->getHelper(VersionHelper::NAME);

        if ($versionHelper->isNewVersionAvailable()) {
            throw new \WS\DeploymentAssistant\RuntimeException('A new version is available. Please run "dep.phar self-update" to update');
        }
    }
}