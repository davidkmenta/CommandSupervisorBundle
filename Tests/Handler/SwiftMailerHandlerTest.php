<?php

namespace DavidKmenta\CommandSupervisorBundle\Tests\Handler;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;
use DavidKmenta\CommandSupervisorBundle\Factory\SwiftMailerFactory;
use DavidKmenta\CommandSupervisorBundle\Handler\SwiftMailerHandler;

class SwiftMailerHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SwiftMailerHandler
     */
    private $handler;

    /**
     * @var \Swift_Mailer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $swiftMailerMock;

    /**
     * @var SwiftMailerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $swiftMailerFactoryMock;

    /**
     * @var \Twig_Environment|\PHPUnit_Framework_MockObject_MockObject
     */
    private $twigMock;

    protected function setUp()
    {
        $this->swiftMailerMock = $this->createMock(\Swift_Mailer::class);
        $this->swiftMailerFactoryMock = $this->createMock(SwiftMailerFactory::class);
        $this->twigMock = $this->createMock(\Twig_Environment::class);

        $this->handler = new SwiftMailerHandler(
            $this->swiftMailerMock,
            $this->swiftMailerFactoryMock,
            $this->twigMock,
            'info@supervisor.info',
            'The subject'
        );
    }

    public function testShouldSendEmail()
    {
        $commandStatus = new CommandStatus('command:name');

        $this->swiftMailerFactoryMock->expects($this->once())
            ->method('getMessage')
            ->with('The subject', 'Text body', 'info@supervisor.info')
            ->willReturn($message = \Swift_Message::newInstance());

        $this->twigMock->expects($this->once())
            ->method('render')
            ->with('CommandSupervisorBundle:SwiftMailer:message.html.twig', ['commandStatus' => $commandStatus])
            ->willReturn('Text body');

        $this->swiftMailerMock->expects($this->once())
            ->method('send')
            ->with($message);

        $this->handler->handle($commandStatus);
    }

    public function testShouldThrowExceptionIfSubjectIsEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Subject could not be empty! Set the "command_supervisor.swift_mailer_handler.subject" parameter!'
        );

        new SwiftMailerHandler($this->swiftMailerMock, $this->swiftMailerFactoryMock, $this->twigMock, 'info@com', '');
    }

    public function testShouldThrowExceptionIfRecipientIsEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Recipient could not be empty! Set the "command_supervisor.swift_mailer_handler.recipient" parameter!'
        );

        new SwiftMailerHandler($this->swiftMailerMock, $this->swiftMailerFactoryMock, $this->twigMock, '', 'Subject');
    }
}
