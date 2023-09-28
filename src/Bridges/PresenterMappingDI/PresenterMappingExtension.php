<?php
declare(strict_types = 1);

namespace Nepada\Bridges\PresenterMappingDI;

use Nepada\PresenterMapping;
use Nette;

class PresenterMappingExtension extends Nette\DI\CompilerExtension
{

    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::structure([]);
    }

    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();

        $container->addDefinition($this->prefix('presenterMapper'))
            ->setType(PresenterMapping\PresenterMapper::class);

        $presenterFactory = $this->getNettePresenterFactory();
        $factory = $presenterFactory->getFactory();
        $arguments = $factory->getEntity() !== null ? $factory->arguments : [];
        array_unshift($arguments, $this->prefix('@presenterMapper'));
        $presenterFactory->setFactory(PresenterMapping\PresenterFactory::class, $arguments);
    }

    /**
     * Make sure that ApplicationExtension is loaded before us and return its PresenterFactory definition.
     */
    private function getNettePresenterFactory(): Nette\DI\Definitions\ServiceDefinition
    {
        /** @var Nette\Bridges\ApplicationDI\ApplicationExtension[] $applicationExtension */
        $applicationExtension = $this->compiler->getExtensions(Nette\Bridges\ApplicationDI\ApplicationExtension::class);
        if ($applicationExtension === []) {
            throw new \LogicException('ApplicationExtension not found, did you register it in your configuration?');
        }

        $container = $this->getContainerBuilder();
        $presenterFactory = reset($applicationExtension)->prefix('presenterFactory');
        if (! $container->hasDefinition($presenterFactory)) {
            throw new \LogicException('PresenterFactory service from ApplicationExtension not found. Make sure ApplicationExtension is loaded before PresenterMappingExtension.');
        }

        $serviceDefinition = $container->getDefinition($presenterFactory);
        assert($serviceDefinition instanceof Nette\DI\Definitions\ServiceDefinition);

        return $serviceDefinition;
    }

}
