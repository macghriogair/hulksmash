<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * The Laravel Zero application instance.
     *
     * @var \LaravelZero\Framework\Contracts\Application
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        $this->app = $this->createApplication();
    }

    protected function installCommandMock(string $command, callable $code = null) : void
    {
        if (is_null($code)) {
            $code = function () {
            };
        }

        $original = (new $command);
        $originalName = $original->getName();

        $mockClone = $this->getMockBuilder($command)
            ->disableOriginalConstructor()
            ->getMock();

        $this->app->register($originalName, $mockClone);

        $cmd = $this->app->get($originalName);
        $cmd->setCode($code);
        $cmd->setDefinition($original->getDefinition());
    }
}
