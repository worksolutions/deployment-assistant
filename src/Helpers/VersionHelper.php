<?php

namespace WS\DeploymentAssistant\Helpers;

use Phar;
use RuntimeException;
use Symfony\Component\Console\Helper\Helper;

/**
 * Class VersionHelper
 * @package WS\DeployAssistant\Helpers
 */
class VersionHelper extends Helper
{
    const NAME = 'version';

    private $pharUrl;
    private $sumUrl;

    public function __construct($pharUrl, $sumUrl)
    {
        $this->pharUrl = $pharUrl;
        $this->sumUrl = $sumUrl;
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getCurrentVersion()
    {
        return md5_file($this->getPharPath());
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    private function getPharPath()
    {
        $path = Phar::running(false);

        if (!$path) {
            throw new RuntimeException('Not started from phar');
        }

        return $path;
    }

    /**
     * @return string
     */
    private function getLastVersion()
    {
        return trim(file_get_contents($this->sumUrl));
    }

    /**
     * @return bool
     */
    public function isNewVersionAvailable()
    {
        try {
            return $this->getLastVersion() !== $this->getCurrentVersion();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update()
    {
        $newPhar = file_get_contents($this->pharUrl);
        file_put_contents($this->getPharPath(), $newPhar);
    }
}
