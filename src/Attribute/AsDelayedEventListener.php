<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class AsDelayedEventListener
{
    public function __construct(
        public string $event,
        public ?string $method = null,
        public int $priority = 0,
    ) {
    }
}
