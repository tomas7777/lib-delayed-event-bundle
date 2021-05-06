<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Listener;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Tjovaisas\Bundle\DelayedEventBundle\Service\EventRegistrar;

class DoctrineListener
{
    private $eventRegistrar;

    public function __construct(EventRegistrar $eventRegistrar)
    {
        $this->eventRegistrar = $eventRegistrar;
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->eventRegistrar->release();
    }
}
