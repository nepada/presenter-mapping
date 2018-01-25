<?php
/**
 * This file is part of the nepada/presenter-mapping.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace Nepada\Bridges\PresenterMappingDI;

use Nepada\PresenterMapping;
use Nette;


class PresenterMappingExtension extends Nette\DI\CompilerExtension
{

    public function loadConfiguration(): void
    {
        $this->validateConfig([]);
        $container = $this->getContainerBuilder();

        $container->addDefinition($this->prefix('presenterMapper'))
            ->setClass(PresenterMapping\PresenterMapper::class);

        $presenterFactory = $this->getNettePresenterFactory();
        $factory = $presenterFactory->getFactory();
        $arguments = $factory !== null ? $factory->arguments : [];
        array_unshift($arguments, $this->prefix('@presenterMapper'));
        $presenterFactory->setFactory(PresenterMapping\PresenterFactory::class, $arguments);
    }

    /**
     * Make sure that ApplicationExtension is loaded before us and return its PresenterFactory definition.
     *
     * @return Nette\DI\ServiceDefinition
     */
    public function getNettePresenterFactory(): Nette\DI\ServiceDefinition
    {
        $applicationExtension = $this->compiler->getExtensions(Nette\Bridges\ApplicationDI\ApplicationExtension::class);
        if ($applicationExtension === []) {
            throw new PresenterMapping\InvalidStateException('ApplicationExtension not found, did you register it in your configuration?');
        }

        $container = $this->getContainerBuilder();
        $presenterFactory = reset($applicationExtension)->prefix('presenterFactory');
        if (!$container->hasDefinition($presenterFactory)) {
            throw new PresenterMapping\InvalidStateException('PresenterFactory service from ApplicationExtension not found. Make sure ApplicationExtension is loaded before PresenterMappingExtension.');
        }

        return $container->getDefinition($presenterFactory);
    }

}
