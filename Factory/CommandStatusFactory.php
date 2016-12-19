<?php

namespace DavidKmenta\CommandSupervisorBundle\Factory;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;

class CommandStatusFactory
{
    /**
     * @param string $commandName
     * @param int|null $threshold
     * @param \DateTime|null $dateTime
     * @param bool|null $status
     *
     * @return CommandStatus
     */
    public function getCommandStatus($commandName, $threshold = null, \DateTime $dateTime = null, $status = null)
    {
        return new CommandStatus($commandName, $threshold, $dateTime, $status);
    }
}
