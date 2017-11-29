<?php

namespace WS\DeploymentAssistant\Helpers;
use Raven_Client;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\EventDispatcher\EventDispatcher;
use WS\DeploymentAssistant\Application;
use WS\DeploymentAssistant\Listeners\SentryExceptionListener;

/**
 * Class SentryHelper
 * @package WS\DeployAssistant\Helpers
 */
class SentryHelper extends Helper
{
    const NAME = 'sentry';

    /** @var Raven_Client */
    private $client;

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
     * @return Raven_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * SentryHelper constructor.
     * @param $config $dsn
     * @throws \Symfony\Component\Console\Exception\RuntimeException
     */
    public function __construct($config)
    {
        if (empty($config) || empty($config["dsn"])) {
            throw new RuntimeException('config sentry.dsn is not set');
        }

        $this->client = new Raven_Client($config["dsn"]);
    }
}
