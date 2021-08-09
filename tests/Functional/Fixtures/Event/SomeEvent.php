<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\Entity\Entity;

class SomeEvent extends Event
{
    private Entity $entity;

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }
}
