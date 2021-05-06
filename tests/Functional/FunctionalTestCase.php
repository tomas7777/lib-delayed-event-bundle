<?php

declare(strict_types=1);

namespace Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tjovaisas\Bundle\DelayedEventBundle\Tests\Functional\Fixtures\TestKernel;

abstract class FunctionalTestCase extends TestCase
{
    /**
     * @var TestKernel
     */
    protected $kernel;

    protected function setUpContainer(): ContainerInterface
    {
        $this->kernel = new TestKernel(uniqid(), true);
        $this->kernel->boot();

        return $this->kernel->getContainer();
    }

    protected function tearDown(): void
    {
        $this->kernel->shutdown();

        $filesystem = new Filesystem();
        $filesystem->remove($this->kernel->getCacheDir());
    }

    protected function setUpDatabase(): void
    {
        $entityManager = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadata, true);
    }
}
