<?php
namespace WS\DeploymentAssistant\Tests;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use WS\DeploymentAssistant\Tests\Helpers\Cmd;
use \WS\DeploymentAssistant\Application;

/**
 * Class TestCase
 * @package WS\DeployAssistant\Tests
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Runs a command and returns it output
     */
    public function runCommand($command)
    {
        $application = new Application(false, null);
        $application->setAutoExit(false);

        $fp = tmpfile();
        $input = new StringInput($command);
        $output = new StreamOutput($fp);

        $application->run($input, $output);

        fseek($fp, 0);
        $output = '';
        while (!feof($fp)) {
            $output = fread($fp, 4096);
        }
        fclose($fp);

        return $output;
    }

    protected static function getUniqueTmpDirectory()
    {
        $attempts = 5;
        $root = sys_get_temp_dir();
        do {
            $unique = $root . DIRECTORY_SEPARATOR . uniqid('composer-test-' . rand(1000, 9000));
            if (!file_exists($unique) && @mkdir($unique, 0777, true)) {
                return realpath($unique);
            }
        } while (--$attempts);
        throw new \RuntimeException('Failed to create a unique temporary directory.');
    }
}
