<?php

namespace DavidKmenta\CommandSupervisorBundle\Tests\Event;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;
use DavidKmenta\CommandSupervisorBundle\Event\UnsuccessfulCommandEvent;

class UnsuccessfulCommandEventTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldCreateEvent()
    {
        $event = new UnsuccessfulCommandEvent($commandStatus = new CommandStatus('command:name'));

        $this->assertSame($commandStatus, $event->getCommandStatus());
    }
}
