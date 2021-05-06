<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tjovaisas\Bundle\DelayedEventBundle\DependencyInjection\RegisterListenersPass;

class TjovaisasDelayedEventBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterListenersPass());
    }
}
