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
    public const TAG_NAME = 'tjovaisas.event_listener.post_flush';
    private const EVENT_REGISTRAR_ID = 'tjovaisas.delayed_event.service.event_registrar';

    public function process(ContainerBuilder $container): void
    {
        $registrarDefinition = $container->findDefinition(self::EVENT_REGISTRAR_ID);
        $delayedEvents = [];

        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $id => $events) {
            foreach ($events as $event) {
                if (!isset($event['event'])) {
                    throw new InvalidArgumentException(sprintf('"%s" must have event defined', $id));
                }

                $delayedEvents[] = $event['event'];
                $method = $this->attemptMethodExtraction($container, $event, $id);
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
                ],
            );
        }
    }

    private function attemptMethodExtraction(ContainerBuilder $container, array $event, string $id): string
    {
        if (isset($event['method'])) {
            $this->checkMethodExists($container, $event['method'], $id);

            return $event['method'];
        }

        $method = 'on' . preg_replace_callback([
            '/(?<=\b|_)[a-z]/i',
            '/[^a-z0-9]/i',
        ], fn ($matches) => strtoupper($matches[0]), $event['event']);
        $method = preg_replace('/[^a-z0-9]/i', '', $method);

        try {
            $this->checkMethodExists($container, $method, $id);
        } catch (InvalidArgumentException) {
            $method = '__invoke';

            $this->checkMethodExists($container, $method, $id);
        } finally {
            return $method;
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
