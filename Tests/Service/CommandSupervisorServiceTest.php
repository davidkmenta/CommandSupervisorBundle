<?php

namespace DavidKmenta\CommandSupervisorBundle\Tests\Service;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;
use DavidKmenta\CommandSupervisorBundle\Factory\CommandStatusFactory;
use DavidKmenta\CommandSupervisorBundle\Service\CommandSupervisor;
use phpmock\phpunit\PHPMock;
use Symfony\Component\Finder\Finder;

class CommandSupervisorServiceTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    /**
     * @var CommandSupervisor
     */
    private $service;

    /**
     * @var Finder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $finderMock;

    protected function setUp()
    {
        $this->finderMock = $this->createMock(Finder::class);
        $this->finderMock->expects($this->once())
            ->method('files')
            ->willReturnSelf();
        $this->finderMock->expects($this->once())
            ->method('in')
            ->with('/cache/path')
            ->willReturnSelf();
        $this->finderMock->expects($this->once())
            ->method('name')
            ->with('*.supervisor')
            ->willReturnSelf();

        $this->service = new CommandSupervisor(
            [
                'supervised:command:not-ok' => ['threshold' => 300],
                'supervised:command:ok' => ['threshold' => 600],
                'missing:command' => ['threshold' => 1],
            ],
            '/cache/path',
            $this->finderMock,
            new CommandStatusFactory()
        );
    }

    public function testShouldGetStatusesOfSupervisedCommands()
    {
        $dateTime = new \DateTimeImmutable('2016-01-01');

        $timeMock = $this->getFunctionMock('DavidKmenta\CommandSupervisorBundle\Service', 'time');
        $timeMock->expects($this->exactly(2))
            ->willReturn($dateTime->setTime(0, 15)->getTimestamp());

        $this->mockFiles([
            $this->getFileMock('supervised:command:not-ok', $dateTime->getTimestamp()),
            $this->getFileMock('supervised:command:ok', $dateTime->setTime(0, 10)->getTimestamp()),
        ]);

        $statuses = $this->service->getCommandsStatuses();

        /** @var CommandStatus $notOkCommandStatus */
        $notOkCommandStatus = $statuses['supervised:command:not-ok'];

        $this->assertSame('supervised:command:not-ok', $notOkCommandStatus->getName());
        $this->assertSame(300, $notOkCommandStatus->getThreshold());
        $this->assertSame('2016-01-01 00:00:00', $notOkCommandStatus->getLastRun()->format('Y-m-d H:i:s'));
        $this->assertFalse($notOkCommandStatus->getStatus());

        /** @var CommandStatus $okCommandStatus */
        $okCommandStatus = $statuses['supervised:command:ok'];

        $this->assertSame('supervised:command:ok', $okCommandStatus->getName());
        $this->assertSame(600, $okCommandStatus->getThreshold());
        $this->assertSame('2016-01-01 00:10:00', $okCommandStatus->getLastRun()->format('Y-m-d H:i:s'));
        $this->assertTrue($okCommandStatus->getStatus());

        /** @var CommandStatus $missingCommandStatus */
        $missingCommandStatus = $statuses['missing:command'];

        $this->assertSame('missing:command', $missingCommandStatus->getName());
        $this->assertNull($missingCommandStatus->getThreshold());
        $this->assertNull($missingCommandStatus->getLastRun());
        $this->assertNull($missingCommandStatus->getStatus());
    }

    /**
     * @param \SplFileInfo[] $files
     */
    private function mockFiles(array $files)
    {
        $this->finderMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($files));
    }

    /**
     * @param string $basename
     * @param int|null $lastModifiedTime
     * @return \SplFileInfo|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getFileMock($basename, $lastModifiedTime = null)
    {
        $splFileInfoMock = $this->createMock(\SplFileInfo::class);
        $splFileInfoMock->expects($this->once())
            ->method('getBasename')
            ->with('.supervisor')
            ->willReturn($basename);

        if ($lastModifiedTime) {
            $splFileInfoMock->expects($this->exactly(2))
                ->method('getMTime')
                ->willReturn($lastModifiedTime);
        }

        return $splFileInfoMock;
    }
}
