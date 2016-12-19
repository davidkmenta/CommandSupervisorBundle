<?php

namespace DavidKmenta\CommandSupervisorBundle\Tests\Listener;

use DavidKmenta\CommandSupervisorBundle\Listener\CommandTerminateListener;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class CommandTerminateListenerTest extends \PHPUnit_Framework_TestCase
{
    const CORRECT_EXIT_CODE = 1;
    const INCORRECT_EXIT_CODE = 0;

    /**
     * @var CommandTerminateListener
     */
    private $listener;

    /**
     * @var Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystemMock;

    protected function setUp()
    {
        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->listener = new CommandTerminateListener(['supervised:command' => []], '/path/', $this->filesystemMock);
    }

    public function testShouldNotCreateSupervisingFileIfCommandIsNotSupervised()
    {
        $this->filesystemMock->expects($this->never())
            ->method('touch');

        $this->listener->onConsoleTerminate(
            $this->getConsoleTerminateEvent('unknown:command', self::INCORRECT_EXIT_CODE)
        );
    }

    public function testShouldNotCreateSupervisingFileIfCommandTerminateCorrectly()
    {
        $this->filesystemMock->expects($this->never())
            ->method('touch');

        $this->listener->onConsoleTerminate(
            $this->getConsoleTerminateEvent('supervised:command', self::CORRECT_EXIT_CODE)
        );
    }

    public function testShouldCreateSupervisingFileIfSupervisedCommandTerminateIncorrectly()
    {
        $this->filesystemMock->expects($this->once())
            ->method('touch')
            ->with('/path/supervised:command.supervisor');

        $this->listener->onConsoleTerminate(
            $this->getConsoleTerminateEvent('supervised:command', self::INCORRECT_EXIT_CODE)
        );
    }

    /**
     * @param string $commandName
     * @param int $exitCode
     * @return ConsoleTerminateEvent
     */
    private function getConsoleTerminateEvent($commandName, $exitCode)
    {
        $event = new ConsoleTerminateEvent(
            new Command($commandName),
            $this->createMock(InputInterface::class),
            $this->createMock(OutputInterface::class),
            $exitCode
        );

        return $event;
    }
}
