<?php

namespace Tests\Commands;

use Tests\TestCase;
use App\Commands\GitCloneCommand;
use \phpmock\phpunit\PHPMock;

class GitCloneCommandTest extends TestCase
{
    use PHPMock;

    private $mockExec;
    private $mockFileExists;

    public function setUp()
    {
        parent::setUp();

        $this->mockExec = $this->getFunctionMock('App\\Traits', 'exec');
        $this->mockFileExists = $this->getFunctionMock('App\\Commands', 'file_exists');
    }

    /** @test */
    public function it_executes_git_clone()
    {
        $this->mockFileExists->expects($this->once())
            ->willReturn(false);

        $expectedCmd = "git clone -b feature/b1 --single-branch https://localhost foobar 2>&1";

        $this->mockExec->expects($this->once())->willReturnCallback(
            function ($command, &$output, &$exitCode) use ($expectedCmd) {
                $this->assertEquals($expectedCmd, $command);
                $exitCode = 0;
            }
        );

        $this->app->call(
            (new GitCloneCommand())->getName(),
            [
                'url' => 'https://localhost',
                'dir' => 'foobar',
                '--branch' => 'feature/b1'
            ]
        );
    }

    /** @test */
    public function it_requires_an_url()
    {
        $this->expectException(\Symfony\Component\Console\Exception\RuntimeException::class);

        $this->app->call(
            (new GitCloneCommand())->getName(),
            [
                'dir' => 'foobar',
                '--branch' => 'feature/b1'
            ]
        );
    }

    /** @test */
    public function it_has_defaults_for_dir_and_branch()
    {
        $this->mockFileExists->expects($this->once())
            ->willReturn(false);

        $expectedCmd = "git clone -b master --single-branch https://localhost localhost 2>&1";

        $this->mockExec->expects($this->once())->willReturnCallback(
            function ($command, &$output, &$exitCode) use ($expectedCmd) {
                $this->assertEquals($expectedCmd, $command);
                $exitCode = 0;
            }
        );

        $this->app->call(
            (new GitCloneCommand())->getName(),
            [
                'url' => 'https://localhost'
            ]
        );
    }

    /** @test */
    public function it_fails_if_folder_exists()
    {
        $this->mockFileExists->expects($this->once())
            ->willReturn(true);

        $this->expectException(\InvalidArgumentException::class);

        $this->app->call(
            (new GitCloneCommand())->getName(),
            [
                'url' => 'https://localhost'
            ]
        );
    }
}
