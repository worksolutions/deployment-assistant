<?php

namespace WS\DeploymentAssistant;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Yaml;
use WS\DeploymentAssistant\Commands\DeployCommand\DeployCommand;
use WS\DeploymentAssistant\Commands\SelfUpdateCommand;
use WS\DeploymentAssistant\Helpers\ExecHelper;
use WS\DeploymentAssistant\Helpers\GitHelper;
use WS\DeploymentAssistant\Helpers\SentryHelper;
use WS\DeploymentAssistant\Helpers\VersionHelper;
use WS\DeploymentAssistant\Listeners\SentryExceptionListener;

/**
 * Class Application
 * @package WS\DeployAssistant
 */
class Application extends \Symfony\Component\Console\Application
{
    public function __construct($version = 'UNKNOWN')
    {
        parent::__construct('Deployment assistant', $version);
        $dispatcher = new EventDispatcher();
        $this->setDispatcher($dispatcher);

        $config = $this->getConfig();

        $this->getHelperSet()->set(new GitHelper());
        $this->getHelperSet()->set(new ExecHelper());

        if (isset($config['sentry'])) {
            $this->getHelperSet()->set(new SentryHelper($config['sentry']));
            $dispatcher->addListener(ConsoleEvents::EXCEPTION, new SentryExceptionListener());
        }

        $this->getHelperSet()->set(new VersionHelper(
            $config['update']['phar_url'],
            $config['update']['sum_url']));

        $this->addCommands(array(
            new DeployCommand(),
            new SelfUpdateCommand()
        ));
    }

    /**
     * @return array
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     */
    private function getConfig()
    {
        $path = dirname(__DIR__) . '/config.yml';
        if (!file_exists($path)) {
            $path = dirname(__DIR__) . '/config.example.yml';
        }

        $config = Yaml::parse(
            file_get_contents($path)
        );

        $processor = new Processor();
        $configuration = new Configuration();
        return $processor->processConfiguration(
            $configuration,
            array($config)
        );
    }

}
