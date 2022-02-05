<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Service;

use Closure;
use Symfony\Contracts\EventDispatcher\Event;

class QueueReleaser
{
    public function __construct(private EventRegistrar $eventRegistrar)
    {
    }

    public function release(): void
    {
        $queue = $this->eventRegistrar->getQueue();
        $this->eventRegistrar->resetQueue();

        foreach ($queue as $queueItem) {
            $event = $queueItem->getEvent();
            $stoppable = $event instanceof Event;

            foreach ($queueItem->getListeners() as $listeners) {
                foreach ($listeners as $listener) {
                    if ($stoppable && $event->isPropagationStopped()) {
                        break 2;
                    }

                    Closure::fromCallable($listener)($event);
                }
            }
        }
    }
}
