<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Service;

use Closure;
use Symfony\Contracts\EventDispatcher\Event;
use Tjovaisas\Bundle\DelayedEventBundle\DTO\Queue;

class EventRegistrar
{
    public $events;

    /**
     * @var Queue[]
     */
    public $queue;

    public function __construct()
    {
        $this->events = [];
        $this->queue = [];
    }

    public function addEvent(string $eventName, int $priority, callable $listener): self
    {
        $this->events[$eventName][$priority][] = $listener;

        return $this;
    }

    /**
     * @param mixed $event
     * @param string $eventName
     */
    public function onEvent($event, string $eventName): void
    {
        if (!isset($this->events[$eventName])) {
            return;
        }

        $listeners = $this->events[$eventName];
        krsort($listeners);
        $this->queue[] = new Queue($event, $listeners);
    }

    public function release(): void
    {
        foreach ($this->queue as $queue) {
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

        $this->queue = [];
    }
}
