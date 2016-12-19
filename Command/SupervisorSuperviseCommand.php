<?php

namespace DavidKmenta\CommandSupervisorBundle\Command;

use DavidKmenta\CommandSupervisorBundle\Event\UnsuccessfulCommandEvent;
use DavidKmenta\CommandSupervisorBundle\Service\CommandSupervisor;
use DavidKmenta\CommandSupervisorBundle\Subscriber\UnsuccessfulCommandSubscriber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SupervisorSuperviseCommand extends Command
{
    /**
     * @var CommandSupervisor
     */
    private $commandSupervisor;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param CommandSupervisor $commandSupervisor
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(CommandSupervisor $commandSupervisor, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();

        $this->commandSupervisor = $commandSupervisor;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function configure()
    {
        $this->setName('command-supervisor:supervise');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->commandSupervisor->getCommandsStatuses() as $commandStatus) {
            if (!$commandStatus->getStatus()) {
                $this->eventDispatcher->dispatch(
                    UnsuccessfulCommandSubscriber::UNSUCCESSFUL_COMMAND_EVENT,
                    new UnsuccessfulCommandEvent($commandStatus)
                );
            }
        }
    }
}
