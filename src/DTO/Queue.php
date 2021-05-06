<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\DTO;

class Queue
{
    private $event;
    private $listeners;

    /**
     * @param mixed $event
     * @param array $listeners
     */
    public function __construct($event, array $listeners)
    {
        $this->event = $event;
        $this->listeners = $listeners;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function getListeners(): array
    {
        return $this->listeners;
    }
}
