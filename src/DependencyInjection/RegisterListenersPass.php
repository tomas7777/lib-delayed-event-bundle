<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class RegisterListenersPass implements CompilerPassInterface
{
    private const EVENT_REGISTRAR_ID = 'tjovaisas.delayed_event.service.event_registrar';

    public function process(ContainerBuilder $container): void
    {
        $registrarDefinition = $container->findDefinition(self::EVENT_REGISTRAR_ID);
        $delayedEvents = [];

        foreach ($container->findTaggedServiceIds('tjovaisas.event_listener.post_flush') as $id => $events) {
            foreach ($events as $event) {
                if (!isset($event['event'])) {
                    throw new InvalidArgumentException(sprintf('"%s" must have event defined', $id));
                }

                $delayedEvents[] = $event['event'];
                $method = $event['method'] ?? '__invoke';
                $priority = $event['priority'] ?? 0;

                $this->checkMethodExists($container, $method, $id);

                $registrarDefinition->addMethodCall(
                    'addEvent',
                    [$event['event'], $priority, [new Reference($id), $method]]
                );
            }
        }

        $eventDispatcherDefinition = $container->findDefinition('event_dispatcher');
        foreach (array_unique($delayedEvents) as $event) {
            $eventDispatcherDefinition->addMethodCall(
                'addListener',
                [
                    $event,
                    [
                        new ServiceClosureArgument(new Reference(self::EVENT_REGISTRAR_ID)),
                        'onEvent',
                    ],
                ]
            );
        }
    }

    private function checkMethodExists(ContainerBuilder $container, string $method, string $id): void
    {
        $class = $container->getDefinition($id)->getClass();
        if ($class === null) {
            throw new InvalidArgumentException(sprintf('Could not been able to resolve class of "%s"', $id));
        }

        $reflection = $container->getReflectionClass($class, false);
        if ($reflection === null || !$reflection->hasMethod($method)) {
            throw new InvalidArgumentException(sprintf('"%s" does not have defined method "%s"', $id, $method));
        }
    }
}
