<?php

namespace DavidKmenta\CommandSupervisorBundle\Tests\Subscriber;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;
use DavidKmenta\CommandSupervisorBundle\Event\UnsuccessfulCommandEvent;
use DavidKmenta\CommandSupervisorBundle\Handler\UnsuccessfulCommandHandlerInterface;
use DavidKmenta\CommandSupervisorBundle\Subscriber\UnsuccessfulCommandSubscriber;

class UnsuccessfulCommandSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UnsuccessfulCommandSubscriber
     */
    private $subscriber;

    /**
     * @var UnsuccessfulCommandHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $handlerMock;

    protected function setUp()
    {
        $this->handlerMock = $this->createMock(UnsuccessfulCommandHandlerInterface::class);

        $commands = [
            'command:commandos' => ['handler' => 'my_handler'],
            'command:awesome' => ['handler' => 'awesome_handler'],
        ];
        $handlers = [
            'my_handler' => $this->handlerMock,
        ];

        $this->subscriber = new UnsuccessfulCommandSubscriber($commands, $handlers);
    }

    public function testShouldReturnSubscribedEvents()
    {
        $this->assertSame(
            [UnsuccessfulCommandSubscriber::UNSUCCESSFUL_COMMAND_EVENT => 'onUnsuccessfulCommand'],
            UnsuccessfulCommandSubscriber::getSubscribedEvents()
        );
    }

    public function testShouldHandleEventIfHandlerIsKnown()
    {
        $commandStatus = new CommandStatus('command:commandos');
        $event = new UnsuccessfulCommandEvent($commandStatus);

        $this->handlerMock
            ->expects($this->once())
            ->method('handle')
            ->with($commandStatus);

        $this->subscriber->onUnsuccessfulCommand($event);
    }

    public function testShouldNotHandlerEventIfHandlerIsUnknown()
    {
        $event = new UnsuccessfulCommandEvent(new CommandStatus('command:awesome'));

        $this->handlerMock
            ->expects($this->never())
            ->method('handle');

        $this->subscriber->onUnsuccessfulCommand($event);
    }

    public function testShouldNotHandleEventIfCommandIsUnknown()
    {
        $event = new UnsuccessfulCommandEvent(new CommandStatus('command:unknown'));

        $this->handlerMock
            ->expects($this->never())
            ->method('handle');

        $this->subscriber->onUnsuccessfulCommand($event);
    }
}
