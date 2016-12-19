<?php

namespace DavidKmenta\CommandSupervisorBundle\Handler;

use DavidKmenta\CommandSupervisorBundle\Entity\CommandStatus;
use DavidKmenta\CommandSupervisorBundle\Factory\SwiftMailerFactory;
use Webmozart\Assert\Assert;

class SwiftMailerHandler implements UnsuccessfulCommandHandlerInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $swiftMailer;

    /**
     * @var SwiftMailerFactory
     */
    private $swiftMailerFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $recipient;

    /**
     * @var string
     */
    private $subject;

    /**
     * @param \Swift_Mailer $swiftMailer
     * @param SwiftMailerFactory $swiftMailerFactory
     * @param \Twig_Environment $twig
     * @param string $recipient
     * @param string $subject
     */
    public function __construct(
        \Swift_Mailer $swiftMailer,
        SwiftMailerFactory $swiftMailerFactory,
        \Twig_Environment $twig,
        $recipient,
        $subject
    ) {
        Assert::notEmpty(
            $recipient,
            'Recipient could not be empty! Set the "command_supervisor.swift_mailer_handler.recipient" parameter!'
        );
        Assert::notEmpty(
            $subject,
            'Subject could not be empty! Set the "command_supervisor.swift_mailer_handler.subject" parameter!'
        );

        $this->swiftMailer = $swiftMailer;
        $this->swiftMailerFactory = $swiftMailerFactory;
        $this->twig = $twig;
        $this->recipient = $recipient;
        $this->subject = $subject;
    }

    public function handle(CommandStatus $commandStatus)
    {
        $body = $this->twig->render('CommandSupervisorBundle:SwiftMailer:message.html.twig', [
            'commandStatus' => $commandStatus,
        ]);

        $this->swiftMailer->send($this->swiftMailerFactory->getMessage($this->subject, $body, $this->recipient));
    }
}
