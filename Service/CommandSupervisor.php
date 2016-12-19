<?php

namespace DavidKmenta\CommandSupervisorBundle\Service;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;
use DavidKmenta\CommandSupervisorBundle\Factory\CommandStatusFactory;
use Symfony\Component\Finder\Finder;

class CommandSupervisor
{
    const FILE_EXTENSION = '.supervisor';

    /**
     * @var array
     */
    private $commands;

    /**
     * @var string
     */
    private $path;

    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var CommandStatusFactory
     */
    private $commandStatusFactory;

    /**
     * @param array $commands
     * @param string $path
     * @param Finder $finder
     * @param CommandStatusFactory $commandStatusFactory
     */
    public function __construct(array $commands, $path, Finder $finder, CommandStatusFactory $commandStatusFactory)
    {
        $this->commands = $commands;
        $this->path = $path;
        $this->finder = $finder;
        $this->commandStatusFactory = $commandStatusFactory;
    }

    /**
     * @return CommandStatus[]
     */
    public function getCommandsStatuses()
    {
        $statuses = [];

        $this->finder->files()->in($this->path)->name('*' . self::FILE_EXTENSION);

        foreach ($this->finder as $file) {
            $commandName = $file->getBasename(self::FILE_EXTENSION);

            // Command is no longer supervised
            if (!isset($this->commands[$commandName])) {
                continue;
            }

            $threshold = $this->commands[$commandName]['threshold'];
            $lastRun = $this->getLastRun($file);
            $status = time() - ($mtime = $file->getMTime()) <= $threshold;

            $statuses[$commandName] = $this->commandStatusFactory->getCommandStatus(
                $commandName,
                $threshold,
                $lastRun,
                $status
            );
        }

        $this->setMissingCommands($statuses);

        return $statuses;
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @return \DateTime
     */
    private function getLastRun(\SplFileInfo $fileInfo)
    {
        $mtime = $fileInfo->getMTime();

        return (new \DateTime())->setTimestamp($mtime);
    }

    /**
     * @param array $statuses
     */
    private function setMissingCommands(array &$statuses)
    {
        $missingCommandsStatuses = array_diff_key($this->commands, $statuses);

        foreach ($missingCommandsStatuses as $commandName => $missingCommandStatus) {
            $statuses[$commandName] = $this->commandStatusFactory->getCommandStatus($commandName);
        }
    }
}
