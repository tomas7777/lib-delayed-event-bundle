<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Entity\Entity;

class SomeEvent extends Event
{
    public function __construct(private Entity $entity)
    {
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }
}
