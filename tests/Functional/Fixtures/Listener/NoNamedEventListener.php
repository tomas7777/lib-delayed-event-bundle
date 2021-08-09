<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Listener;

use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Event\SomeEvent;

class NoNamedEventListener
{
    public function onEvent(SomeEvent $someEvent): void
    {
        // intentionally left empty
    }
}
