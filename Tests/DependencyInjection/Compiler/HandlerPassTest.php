<?php

namespace DavidKmenta\CommandSupervisorBundle\Tests\DependencyInjection\Compiler;

use DavidKmenta\CommandSupervisorBundle\DependencyInjection\Compiler\HandlerPass;
use DavidKmenta\CommandSupervisorBundle\Handler\SwiftMailerHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class HandlerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HandlerPass
     */
    private $handlerPass;

    protected function setUp()
    {
        $this->handlerPass = new HandlerPass();
    }

    public function testShouldThrowExceptionIfHandlersParameterIsNotPresent()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You have requested a non-existent parameter "command_supervisor.handlers".');

        $this->handlerPass->process(new ContainerBuilder());
    }

    public function testShouldThrowExceptionIfNoHandlerIsRegistered()
    {
        $container = new ContainerBuilder();
        $container->setParameter('command_supervisor.handlers', []);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You have register at least 1 handler!');

        $this->handlerPass->process($container);
    }

    public function testShouldThrowExceptionIfCommandsParameterIsNotPresent()
    {
        $container = new ContainerBuilder();
        $container->setParameter('command_supervisor.handlers', ['handler_name' => 'handler_service_id']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You have requested a non-existent parameter "command_supervisor.commands".');

        $this->handlerPass->process($container);
    }

    public function testShouldThrowExceptionIfDefaultHandlerParameterIsNotPresent()
    {
        $container = new ContainerBuilder();
        $container->setParameter('command_supervisor.handlers', ['handler_name' => 'handler_service_id']);
        $container->setParameter('command_supervisor.commands', []);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'You have requested a non-existent parameter "command_supervisor.default_handler".'
        );

        $this->handlerPass->process($container);
    }

    public function testShouldThrowExceptionIfRegisteredHandlerDoesNotExists()
    {
        $container = new ContainerBuilder();
        $container->setParameter('command_supervisor.default_handler', null);
        $container->setParameter('command_supervisor.handlers', ['handler_name' => 'handler_service_id']);
        $container->setParameter('command_supervisor.commands', []);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You have requested a non-existent service "handler_service_id".');

        $this->handlerPass->process($container);
    }

    public function testShouldThrowExceptionIfDefaultHandlerDoesNotImplementCorrectInterface()
    {
        $container = new ContainerBuilder();
        $container->setParameter('command_supervisor.default_handler', 'handler_service_id');
        $container->setParameter('command_supervisor.handlers', ['handler_name' => 'handler_service_id']);
        $container->setParameter('command_supervisor.commands', []);
        $container->setDefinition('handler_service_id', new Definition(\stdClass::class));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Default handler must implement the ' .
            'DavidKmenta\CommandSupervisorBundle\Handler\UnsuccessfulCommandHandlerInterface interface.'
        );

        $this->handlerPass->process($container);
    }

    public function testShouldThrowExceptionIfRegisteredHandlerDoesNotImplementCorrectInterface()
    {
        $container = new ContainerBuilder();
        $container->setParameter('command_supervisor.default_handler', null);
        $container->setParameter('command_supervisor.handlers', ['handler_name' => 'handler_service_id']);
        $container->setParameter('command_supervisor.commands', []);
        $container->setDefinition('handler_service_id', new Definition(\stdClass::class));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Handler "handler_name" must implement the ' .
            'DavidKmenta\CommandSupervisorBundle\Handler\UnsuccessfulCommandHandlerInterface interface.'
        );

        $this->handlerPass->process($container);
    }

    public function testShouldRegisterUnsuccessfulCommandSubscriber()
    {
        $container = new ContainerBuilder();
        $container->setParameter('command_supervisor.default_handler', null);
        $container->setParameter('command_supervisor.handlers', ['handler_name' => 'handler_service_id']);
        $container->setParameter('command_supervisor.commands', ['command:name', 'doctrine:migrations:diff']);
        $container->setDefinition('handler_service_id', new Definition(SwiftMailerHandler::class));

        $this->handlerPass->process($container);

        $this->assertTrue($container->hasDefinition(HandlerPass::UNSUCCESSFUL_COMMAND_SUBSCRIBER_SERVICE_ID));

        $definition = $container->getDefinition(HandlerPass::UNSUCCESSFUL_COMMAND_SUBSCRIBER_SERVICE_ID);

        $this->assertTrue($definition->hasTag('kernel.event_subscriber'));
        $this->assertEquals(
            [
                ['command:name', 'doctrine:migrations:diff'],
                ['handler_name' => new Reference('handler_service_id')],
            ],
            $definition->getArguments()
        );
    }
}
