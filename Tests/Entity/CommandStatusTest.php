<?php

namespace DavidKmenta\CommandSupervisorBundle\Tests\Entity;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;

class CommandStatusTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldCreateEntityWithDefaults()
    {
        $entity = new CommandStatus('command:name');

        $this->assertSame('command:name', $entity->getName());
        $this->assertNull($entity->getThreshold());
        $this->assertNull($entity->getLastRun());
        $this->assertNull($entity->getStatus());
    }

    public function testShould()
    {
        $entity = new CommandStatus('command:name', 60, new \DateTime('2016-01-01 11:00'), false);

        $this->assertSame('command:name', $entity->getName());
        $this->assertSame(60, $entity->getThreshold());
        $this->assertSame('2016-01-01 11:00:00', $entity->getLastRun()->format('Y-m-d H:i:s'));
        $this->assertFalse($entity->getStatus());
    }
}
