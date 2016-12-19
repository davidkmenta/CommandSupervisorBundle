<?php

namespace DavidKmenta\CommandSupervisorBundle\Subscriber;

use DavidKmenta\CommandSupervisorBundle\Event\UnsuccessfulCommandEvent;
use DavidKmenta\CommandSupervisorBundle\Handler\UnsuccessfulCommandHandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UnsuccessfulCommandSubscriber implements EventSubscriberInterface
{
    const UNSUCCESSFUL_COMMAND_EVENT = 'unsuccessful_command_event';

    /**
     * @var array
     */
    private $commands;

    /**
     * @var UnsuccessfulCommandHandlerInterface[]
     */
    private $handlers;

    /**
     * @param array $commands
     * @param UnsuccessfulCommandHandlerInterface[] $handlers
     */
    public function __construct(array $commands, array $handlers)
    {
        $this->commands = $commands;
        $this->handlers = $handlers;
    }

    public static function getSubscribedEvents()
    {
        return [
            self::UNSUCCESSFUL_COMMAND_EVENT => 'onUnsuccessfulCommand',
        ];
    }

    /**
     * @param UnsuccessfulCommandEvent $event
     */
    public function onUnsuccessfulCommand(UnsuccessfulCommandEvent $event)
    {
        $commandName = $event->getCommandStatus()->getName();

        if ($handler = $this->getHandler($commandName)) {
            $handler->handle($event->getCommandStatus());
        }
    }

    /**
     * @param string $commandName
     * @return UnsuccessfulCommandHandlerInterface|null
     */
    private function getHandler($commandName)
    {
        if (isset($this->commands[$commandName])) {
            $handlerName = $this->commands[$commandName]['handler'];

            if ($handlerName !== null && isset($this->handlers[$handlerName])) {
                return $this->handlers[$handlerName];
            }
        }

        return null;
    }
}
