<?php

namespace DavidKmenta\CommandSupervisorBundle\Tests\Factory;

use DavidKmenta\CommandSupervisorBundle\Factory\SwiftMailerFactory;

class SwiftMailerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SwiftMailerFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->factory = new SwiftMailerFactory();
    }

    public function testShouldCreateSwiftMessage()
    {
        $message = $this->factory->getMessage('The subject', 'The body', 'to@info.com', 'from@info.com');

        $this->assertInstanceOf(\Swift_Message::class, $message);
        $this->assertSame('The subject', $message->getSubject());
        $this->assertSame('The body', $message->getBody());
        $this->assertSame(['to@info.com' => null], $message->getTo());
        $this->assertSame(['from@info.com' => null], $message->getFrom());
    }

    public function testShouldAddRecipientAsSenderIfSenderIsMissing()
    {
        $message = $this->factory->getMessage('The subject', 'The body', 'to@info.com');

        $this->assertSame(['to@info.com' => null], $message->getTo());
        $this->assertSame(['to@info.com' => null], $message->getFrom());
    }
}
