<?php

/**
 * @date    2018-02-07
 * @file    CreateProjectCommandTest.php
 * @author  Patrick Mac Gregor <macgregor.porta@gmail.com>
 */

namespace Tests\Commands;

use App\Commands\CreateProjectCommand;
use App\Commands\GitCloneCommand;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use \phpmock\phpunit\PHPMock;
use Symfony\Component\Console\Command\Command;

class CreateProjectCommandTest extends TestCase
{
    use PHPMock;

    protected $mockExec;
    protected $mockChdir;
    protected $mockFileExists;

    public function setUp()
    {
        parent::setUp();

        $this->mockExec = $this->getFunctionMock('App\\Traits', 'exec');
        $this->mockChdir = $this->getFunctionMock('App\\Commands', 'chdir');
        $this->mockFileExists = $this->getFunctionMock('App\\Commands', 'file_exists');
    }

    /** @test */
    public function it_runs_with_defaults()
    {
        $cfgStub = [
            'default' => [
                'url' => 'https://examle.org/foobar.git',
                'versions' => ['master']
            ]
        ];
        Config::set('boilerplates', $cfgStub);

        $this->installCommandMock(GitCloneCommand::class, function ($in, $out) use ($cfgStub) {
            $this->assertEquals($cfgStub['default']['url'], $in->getArgument('url'));
            $this->assertEquals($cfgStub['default']['versions'][0], $in->getOption('branch'));
            $this->assertEquals('new-project', $in->getArgument('dir'));
        });

        $this->mockExec->expects($this->any())->willReturnCallback(
            function ($command, &$output, &$exitCode) {
                $exitCode = 0;
            }
        );

        $cmd = (new CreateProjectCommand());
        $cmd->setLaravel($this->app);

        $this->app->call(
            $cmd->getName(),
            ['--no-interaction' => true]
        );

        $this->assertContains(join([
            'Entering directory new-project...',
            'Install composer dependencies...',
            'Clean up...',
            'Ready to build something awesome!'
        ], "\n"), $this->app->output());
    }
}
