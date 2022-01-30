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
        foreach ($this->eventRegistrar->getQueue() as $queue) {
            $event = $queue->getEvent();
            $stoppable = $event instanceof Event;

            foreach ($queue->getListeners() as $listeners) {
                foreach ($listeners as $listener) {
                    if ($stoppable && $event->isPropagationStopped()) {
                        break 2;
                    }

                    Closure::fromCallable($listener)($event);
                }
            }
        }

        $this->eventRegistrar->resetQueue();
    }
}
