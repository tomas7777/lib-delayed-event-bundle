<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Listener;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Tjovaisas\Bundle\DelayedEventBundle\Service\QueueReleaser;

class DoctrineListener
{
    public function __construct(private QueueReleaser $queueReleaser)
    {
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->queueReleaser->release();
    }
}
