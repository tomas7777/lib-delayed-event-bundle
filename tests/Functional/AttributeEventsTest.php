<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional;

use stdClass;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Kernel;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Events;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Entity\Entity;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Event\SomeEvent;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Listener\AttributeEventListener;

class AttributeEventsTest extends FunctionalTestCase
{
    private MockObject|AttributeEventListener $attributeEventListener;
    private EventDispatcherInterface $eventDispatcher;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $container = $this->setUpContainer();

        $this->setUpDatabase();

        $this->eventDispatcher = $container->get('event_dispatcher');
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->attributeEventListener = $this->createMock(AttributeEventListener::class);

        $container->set('attribute_event_listener', $this->attributeEventListener);
    }

    public function testAttributesMethodIsNotDefined(): void
    {
        $this->attributeEventListener
            ->expects(static::once())
            ->method('onFoo')
        ;

        $this->eventDispatcher->dispatch(new stdClass(), 'foo');

        $this->entityManager->flush();
    }

    /**
     * @dataProvider dataProviderDefinedEvents
     */
    public function testListenerIsTriggeredOnMultipleOccasions(?string $event, string $method): void
    {
        $entity = new Entity();

        $this->entityManager->persist($entity);

        $this->eventDispatcher->dispatch(new SomeEvent($entity), $event);

        $this->attributeEventListener
            ->expects(static::once())
            ->method($method)
            ->willReturnCallback(function (SomeEvent $someEvent): void {
                $this->assertSame(1, $someEvent->getEntity()->getId());
            })
        ;

        $this->entityManager->flush();
    }

    public function dataProviderDefinedEvents(): array
    {
        $providers = [
            'listener is triggered on class defined event' => [
                null,
                'onClassDefinedEvent',
            ],
        ];

        if (Kernel::VERSION_ID >= 60000) {
            $providers = array_merge(
                $providers,
                [
                    'listener is triggered on method defined event' => [
                        Events::ON_EVENT,
                        'onMethodDefinedEvent',
                    ],
                ],
            );
        }

        return $providers;
    }
}
