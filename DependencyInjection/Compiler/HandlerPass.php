<?php

namespace DavidKmenta\CommandSupervisorBundle\DependencyInjection\Compiler;

use DavidKmenta\CommandSupervisorBundle\Handler\UnsuccessfulCommandHandlerInterface;
use DavidKmenta\CommandSupervisorBundle\Subscriber\UnsuccessfulCommandSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

class HandlerPass implements CompilerPassInterface
{
    const DEFAULT_HANDLER_NAME = 'default';
    const UNSUCCESSFUL_COMMAND_SUBSCRIBER_SERVICE_ID = 'command_supervisor.subscriber.unsuccessful_command_subscriber';

    public function process(ContainerBuilder $container)
    {
        $handlers = $container->getParameter('command_supervisor.handlers');

        Assert::notEmpty($handlers, 'You have register at least 1 handler!');

        $container->setDefinition(
            self::UNSUCCESSFUL_COMMAND_SUBSCRIBER_SERVICE_ID,
            $this->getUnsuccessfulCommandSubscriberDefinition($handlers, $container)
        );
    }

    /**
     * @param array $handlers
     * @param ContainerBuilder $container
     * @return Definition
     */
    private function getUnsuccessfulCommandSubscriberDefinition(array $handlers, ContainerBuilder $container)
    {
        $subscriberDefinition = new Definition(UnsuccessfulCommandSubscriber::class);
        $subscriberDefinition->addTag('kernel.event_subscriber');
        $subscriberDefinition->setArguments([
            $container->getParameter('command_supervisor.commands'),
            $this->getHandlers($handlers, $container),
        ]);

        return $subscriberDefinition;
    }

    /**
     * @param array $handlers
     * @param ContainerBuilder $container
     *
     * @throws InvalidArgumentException
     * @return Reference[]
     */
    private function getHandlers(array $handlers, ContainerBuilder $container)
    {
        $references = [];

        if ($defaultHandler = $this->getDefaultHandler($container)) {
            if (!$this->implementsInterface($defaultHandler, $container)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Default handler must implement the %s interface.',
                        UnsuccessfulCommandHandlerInterface::class
                    )
                );
            }

            $references[self::DEFAULT_HANDLER_NAME] = $defaultHandler;
        }

        foreach ($handlers as $handlerName => $handlerId) {
            if (!$this->implementsInterface($handlerId, $container)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Handler "%s" must implement the %s interface.',
                        $handlerName,
                        UnsuccessfulCommandHandlerInterface::class
                    )
                );
            }

            $references[$handlerName] = new Reference($handlerId);
        }

        return $references;
    }

    /**
     * @param ContainerBuilder $container
     * @return Reference|null
     */
    private function getDefaultHandler(ContainerBuilder $container)
    {
        $defaultHandlerId = $container->getParameter('command_supervisor.default_handler');

        if ($container->has($defaultHandlerId)) {
            return new Reference($defaultHandlerId);
        }

        return null;
    }

    /**
     * @param string $handlerId
     * @param ContainerBuilder $container
     * @return bool
     */
    private function implementsInterface($handlerId, ContainerBuilder $container)
    {
        $definition = $container->findDefinition($handlerId);
        $interfaces = class_implements($definition->getClass());

        return isset($interfaces[UnsuccessfulCommandHandlerInterface::class]);
    }
}
