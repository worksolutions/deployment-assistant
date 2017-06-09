<?php

namespace WS\DeploymentAssistant\Helpers;
use Raven_Client;
use Symfony\Component\Console\Helper\Helper;

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
     * @param string $dsn
     */
    public function __construct($dsn)
    {
        $this->client = new Raven_Client($dsn);
    }
}
