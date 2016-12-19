<?php

namespace DavidKmenta\CommandSupervisorBundle\Tests\Factory;

use DavidKmenta\CommandSupervisorBundle\Factory\CommandStatusFactory;

class CommandStatusFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CommandStatusFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new CommandStatusFactory();
    }

    public function testShouldCreateEmptyCommandStatus()
    {
        $commandStatus = $this->factory->getCommandStatus('command:name');

        $this->assertSame('command:name', $commandStatus->getName());
        $this->assertNull($commandStatus->getThreshold());
        $this->assertNull($commandStatus->getLastRun());
        $this->assertNull($commandStatus->getStatus());
    }

    public function testShouldCreateCommandStatus()
    {
        $commandStatus = $this->factory->getCommandStatus('command:name', 600, new \DateTime('2016-01-01 20:15'), true);

        $this->assertSame('command:name', $commandStatus->getName());
        $this->assertSame(600, $commandStatus->getThreshold());
        $this->assertSame('2016-01-01 20:15:00', $commandStatus->getLastRun()->format('Y-m-d H:i:s'));
        $this->assertTrue($commandStatus->getStatus());
    }
}
