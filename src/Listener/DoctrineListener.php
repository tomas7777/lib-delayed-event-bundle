<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Listener;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Tjovaisas\Bundle\DelayedEventBundle\Service\QueueReleaser;

class DoctrineListener
{
    private QueueReleaser $queueReleaser;

    public function __construct(QueueReleaser $queueReleaser)
    {
        $this->queueReleaser = $queueReleaser;
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        $this->queueReleaser->release();
    }
}
