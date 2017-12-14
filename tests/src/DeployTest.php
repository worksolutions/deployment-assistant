<?php

namespace WS\DeploymentAssistant\Tests;
use WS\DeploymentAssistant\Tests\Helpers\Cmd;

/**
 * Class DeployTest
 * @package WS\DeployAssistant\Tests
 */
class DeployTest extends TestCase
{
    private $repo;
    private $workdir;

    protected function setUp()
    {
        parent::setUp();

        $this->repo = $repo = static::getUniqueTmpDirectory();
        $this->workdir = $workdir = static::getUniqueTmpDirectory();
        Cmd::run('git init --bare', $repo);
        Cmd::run('git init', $workdir);
        Cmd::run('git config user.email "you@example.com"', $workdir);
        Cmd::run('git config user.name "Your Name"', $workdir);
        Cmd::run('touch test', $workdir);
        Cmd::run('git add .', $workdir);
        Cmd::run('git commit -a -m "init commit"', $workdir);
        Cmd::run("git remote add origin {$repo}", $workdir);
        Cmd::run('git push origin master', $workdir);
        chdir($workdir);
    }


    protected function tearDown()
    {
        parent::tearDown();

        Cmd::run("rm -rf {$this->repo} {$this->workdir}");
    }

    public function testCheckLocalAndRemoteBranchHasSameNames()
    {
        $output = $this->runCommand('deploy origin dev');
        $this->assertContains('Checking that the remote and local branches has same names...fatal', $output);
    }

    public function testCheckThatWorkDirHasNoChanges()
    {
        Cmd::run('touch test2', $this->workdir);
        $this->assertContains('Checking that the work dir has no changes...fatal',
            $this->runCommand('deploy'));
    }

    public function testCheckThatTheLocalBranchAndRemoteBranchAreBothModified()
    {
        $workdir = $this->workdir;

        Cmd::run('touch test2', $workdir);
        Cmd::run('git add test2', $workdir);
        Cmd::run('git commit -m "second commit"', $workdir);
        Cmd::run('git push origin master', $workdir);
        Cmd::run('git reset --hard HEAD~1', $workdir);
        Cmd::run('touch test3', $workdir);
        Cmd::run('git add .', $workdir);
        Cmd::run('git commit -m "another second commit"', $workdir);

        $output = $this->runCommand('deploy');
        $this->assertContains('Checking that the work dir has no changes...ok', $output);
        $this->assertContains('Checking that the local branch and remote branch are both modified...fatal', $output);
    }

    public function testCheckThatTheLocalBranchIsBotAheadOfRemoteBranch()
    {
        $workdir = $this->workdir;

        Cmd::run('touch test2', $workdir);
        Cmd::run('git add test2', $workdir);
        Cmd::run('git commit -m "second commit"', $workdir);
        Cmd::run('git push origin master', $workdir);
        Cmd::run('git reset --hard HEAD~1', $workdir);
        Cmd::run('touch test3', $workdir);
        Cmd::run('git add .', $workdir);
        Cmd::run('git commit -m "another second commit"', $workdir);
        Cmd::run('git reset --hard HEAD~1', $workdir);
        Cmd::run('git pull origin master', $workdir);
        Cmd::run('touch test5', $workdir);
        Cmd::run('git add test5', $workdir);
        Cmd::run('git commit -m "third commit"', $workdir);

        $output = $this->runCommand('deploy');
        $this->assertContains('Checking that the work dir has no changes...ok', $output);
        $this->assertContains('Checking that the local branch and remote branch are both modified...ok', $output);
        $this->assertContains('Checking that the local branch is not ahead of remote branch...fatal', $output);
    }

    public function testPullChanges()
    {
        $workdir = $this->workdir;

        Cmd::run('touch test2', $workdir);
        Cmd::run('git add test2', $workdir);
        Cmd::run('git commit -m "second commit"', $workdir);
        Cmd::run('git push origin master', $workdir);
        Cmd::run('git reset --hard HEAD~1', $workdir);
        Cmd::run('touch test3', $workdir);
        Cmd::run('git add .', $workdir);
        Cmd::run('git commit -m "another second commit"', $workdir);
        Cmd::run('git reset --hard HEAD~1', $workdir);
        Cmd::run('git pull origin master', $workdir);
        Cmd::run('touch test5', $workdir);
        Cmd::run('git add test5', $workdir);
        Cmd::run('git commit -m "third commit"', $workdir);
        Cmd::run('git push origin master', $workdir);
        Cmd::run('git reset --hard HEAD~1', $workdir);

        $output = $this->runCommand('deploy');
        $this->assertContains('Checking that the work dir has no changes...ok', $output);
        $this->assertContains('Checking that the local branch is not ahead of remote branch...ok', $output);
        $this->assertContains('Checking that the local branch and remote branch are both modified...ok', $output);
        $this->assertContains('Checking that the local branch is behind of remote branch...ok', $output);
        $this->assertContains('Pulling changes...ok', $output);
    }

    public function testCheckThatTheLocalBranchIsBehindOfRemoteBranch()
    {
        $workdir = $this->workdir;

        Cmd::run('touch test2', $workdir);
        Cmd::run('git add test2', $workdir);
        Cmd::run('git commit -m "second commit"', $workdir);
        Cmd::run('git push origin master', $workdir);
        Cmd::run('git reset --hard HEAD~1', $workdir);
        Cmd::run('touch test3', $workdir);
        Cmd::run('git add .', $workdir);
        Cmd::run('git commit -m "another second commit"', $workdir);
        Cmd::run('git reset --hard HEAD~1', $workdir);
        Cmd::run('git pull origin master', $workdir);
        Cmd::run('touch test5', $workdir);
        Cmd::run('git add test5', $workdir);
        Cmd::run('git commit -m "third commit"', $workdir);
        Cmd::run('git push origin master', $workdir);
        Cmd::run('git reset --hard HEAD~1', $workdir);
        Cmd::run('git pull origin master', $workdir);

        // Test checking that the local branch is behind of remote branch
        $output = $this->runCommand('deploy');
        $this->assertContains('Checking that the work dir has no changes...ok', $output);
        $this->assertContains('Checking that the local branch is not ahead of remote branch...ok', $output);
        $this->assertContains('Checking that the local branch and remote branch are both modified...ok', $output);
        $this->assertContains('Checking that the local branch is behind of remote branch...fatal', $output);
    }
}
