<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Service;

use Tjovaisas\Bundle\DelayedEventBundle\DTO\Queue;

class EventRegistrar
{
    private array $events;

    /**
     * @var Queue[]
     */
    private array $queue;

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

    /**
     * @return Queue[]
     */
    public function getQueue(): array
    {
        return $this->queue;
    }

    public function resetQueue(): void
    {
        $this->queue = [];
    }
}
