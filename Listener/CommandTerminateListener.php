<?php

namespace DavidKmenta\CommandSupervisorBundle\Listener;

use DavidKmenta\CommandSupervisorBundle\Service\CommandSupervisor;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Filesystem\Filesystem;

class CommandTerminateListener
{
    /**
     * Array of commands where key is command name and value threshold in seconds
     * @var array
     */
    private $commands;

    /**
     * Place for storing supervisor files
     * @var string
     */
    private $path;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param array $commands
     * @param string $path
     * @param Filesystem $filesystem
     */
    public function __construct(array $commands, $path, Filesystem $filesystem)
    {
        $this->commands = $commands;
        $this->path = $path;
        $this->filesystem = $filesystem;
    }

    /**
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        if ($event->getExitCode() || !isset($this->commands[$event->getCommand()->getName()])) {
            return;
        }

        $this->filesystem->touch(
            $this->path . $event->getCommand()->getName() . CommandSupervisor::FILE_EXTENSION
        );
    }
}
