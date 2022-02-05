<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Events;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Entity\Entity;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Event\SomeEvent;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Listener\InvokeEventListener;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Listener\ConfiguredEventListener;

class ConfiguredEventsTest extends FunctionalTestCase
{
    private MockObject|ConfiguredEventListener $configuredEventListener;
    private MockObject|InvokeEventListener $invokeEventListener;
    private EventDispatcherInterface $eventDispatcher;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $container = $this->setUpContainer();

        $this->setUpDatabase();

        $this->eventDispatcher = $container->get('event_dispatcher');
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->configuredEventListener = $this->createMock(ConfiguredEventListener::class);
        $this->invokeEventListener = $this->createMock(InvokeEventListener::class);

        $container->set('configured_event_listener', $this->configuredEventListener);
        $container->set('invoke_event_listener', $this->invokeEventListener);
    }

    public function testListenerHasNotBeenTriggeredWithoutAFlush(): void
    {
        $entity = new Entity();

        $this->entityManager->persist($entity);

        $this->eventDispatcher->dispatch(new SomeEvent($entity), Events::ON_EVENT);

        $this->configuredEventListener
            ->expects(static::never())
            ->method('onEvent')
        ;
    }

    public function testListenersBeenTriggeredInACorrectOrder(): void
    {
        $entity = new Entity();

        $this->entityManager->persist($entity);

        $this->eventDispatcher->dispatch(new SomeEvent($entity), Events::ON_EVENT);

        $isTriggered = false;
        $this->configuredEventListener
            ->expects(static::once())
            ->method('onEvent')
            ->willReturnCallback(function () use (&$isTriggered): void {
                $this->assertTrue($isTriggered);
            })
        ;

        $this->invokeEventListener
            ->expects(static::once())
            ->method('__invoke')
            ->willReturnCallback(function () use (&$isTriggered): void {
                $isTriggered = true;
            })
        ;

        $this->entityManager->flush();
    }

    public function testStopPropagationIfListenerInstructsToDoSo(): void
    {
        $entity = new Entity();

        $this->entityManager->persist($entity);

        $this->eventDispatcher->dispatch(new SomeEvent($entity), Events::ON_EVENT);

        $this->invokeEventListener
            ->expects(static::once())
            ->method('__invoke')
            ->willReturnCallback(function (SomeEvent $someEvent): void {
                $someEvent->stopPropagation();
            })
        ;

        $this->configuredEventListener
            ->expects(static::never())
            ->method('onEvent')
        ;

        $this->entityManager->flush();
    }

    /**
     * @dataProvider dataProviderTriggerListenerOnDifferentConfigurations
     */
    public function testListenersAreTriggeredOnDifferentConfigurations(?string $event, string $method): void
    {
        $entity = new Entity();

        $this->entityManager->persist($entity);

        $this->eventDispatcher->dispatch(new SomeEvent($entity), $event);

        $this->configuredEventListener
            ->expects(static::once())
            ->method($method)
            ->willReturnCallback(function (SomeEvent $someEvent): void {
                $this->assertSame(1, $someEvent->getEntity()->getId());
            })
        ;

        $this->entityManager->flush();
    }

    public function dataProviderTriggerListenerOnDifferentConfigurations(): array
    {
        return [
            'listener is triggered after defining exact method from listener' => [
                Events::ON_EVENT,
                'onEvent',
            ],
            'listener is triggered without specifying event name' => [
                null,
                'onFQCNEvent',
            ],
        ];
    }
}
