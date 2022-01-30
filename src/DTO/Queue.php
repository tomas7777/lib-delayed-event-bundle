<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\DTO;

class Queue
{
    public function __construct(private mixed $event, private array $listeners)
    {
    }

    public function getEvent(): mixed
    {
        return $this->event;
    }

    public function getListeners(): array
    {
        return $this->listeners;
    }
}
