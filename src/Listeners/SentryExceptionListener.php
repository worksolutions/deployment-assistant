<?php

namespace WS\DeploymentAssistant\Listeners;
use Raven_Client;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use WS\DeploymentAssistant\Helpers\SentryHelper;

/**
 * Class SentryExceptionListener
 * @package WS\DeployAssistant\Listeners
 */
class SentryExceptionListener
{
    public function __invoke(ConsoleExceptionEvent $event)
    {
        $exception = $event->getException();

        /** @var SentryHelper $sentryHelper */
        $sentryHelper = $event->getCommand()->getHelper(SentryHelper::NAME);
        /** @var Raven_Client $client */
        $client = $sentryHelper->getClient();
        $client->captureException($exception);
    }
}
