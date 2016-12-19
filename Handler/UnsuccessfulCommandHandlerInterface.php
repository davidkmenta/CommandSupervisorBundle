<?php

namespace DavidKmenta\CommandSupervisorBundle\Handler;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;

interface UnsuccessfulCommandHandlerInterface
{
    public function handle(CommandStatus $commandStatus);
}
