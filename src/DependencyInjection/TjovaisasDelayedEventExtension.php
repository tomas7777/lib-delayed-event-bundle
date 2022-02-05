<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\DependencyInjection;

use Reflector;
use ReflectionMethod;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Tjovaisas\Bundle\DelayedEventBundle\Attribute\AsDelayedEventListener;

class TjovaisasDelayedEventExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->registerAttributeForAutoconfiguration(
            AsDelayedEventListener::class,
            static function (
                ChildDefinition $definition,
                AsDelayedEventListener $attribute,
                Reflector $reflector,
            ): void {
                $tagAttributes = get_object_vars($attribute);
                if ($reflector instanceof ReflectionMethod) {
                    if (isset($tagAttributes['method'])) {
                        throw new LogicException(
                            sprintf(
                                'AsDelayedEventListener attribute cannot declare a method on "%s::%s()".',
                                $reflector->class,
                                $reflector->name,
                            ),
                        );
                    }
                    $tagAttributes['method'] = $reflector->getName();
                }
                $definition->addTag(RegisterListenersPass::TAG_NAME, $tagAttributes);
            },
        );
    }
}
