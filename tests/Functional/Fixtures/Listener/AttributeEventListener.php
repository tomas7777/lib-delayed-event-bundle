<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Listener;

use Tjovaisas\Bundle\DelayedEventBundle\Attribute\AsDelayedEventListener;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Events;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Event\SomeEvent;

#[AsDelayedEventListener(event: SomeEvent::class, method: 'onClassDefinedEvent')]
#[AsDelayedEventListener(event: 'foo')]
class AttributeEventListener
{
    public function onClassDefinedEvent(SomeEvent $event): void
    {
        // intentionally left empty
    }

    #[AsDelayedEventListener(event: Events::ON_EVENT)]
    public function onMethodDefinedEvent(): void
    {
        // intentionally left empty
    }

    public function onFoo(): void
    {
        // intentionally left empty
    }
}
