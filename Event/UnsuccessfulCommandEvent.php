<?php

namespace DavidKmenta\CommandSupervisorBundle\Event;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;
use Symfony\Component\EventDispatcher\Event;

class UnsuccessfulCommandEvent extends Event
{
    /**
     * @var CommandStatus
     */
    private $commandStatus;

    /**
     * @param CommandStatus $commandStatus
     */
    public function __construct(CommandStatus $commandStatus)
    {
        $this->commandStatus = $commandStatus;
    }

    /**
     * @return CommandStatus
     */
    public function getCommandStatus()
    {
        return $this->commandStatus;
    }
}
