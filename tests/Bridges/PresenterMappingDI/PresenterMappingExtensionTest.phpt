<?php
/**
 * Test: Nepada\Bridges\PresenterMappingDI\PresenterMappingExtension
 *
 * This file is part of the nepada/presenter-mapping.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace NepadaTests\Bridges\PresenterMappingDI;

use Nepada\PresenterMapping\PresenterFactory;
use Nepada\PresenterMapping\PresenterMapper;
use Nette;
use Tester;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class PresenterMappingExtensionTest extends Tester\TestCase
{

    /** @var Nette\DI\Container */
    private $container;


    public function setUp()
    {
        $configurator = new Nette\Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->setDebugMode(true);
        $configurator->addConfig(__DIR__ . '/fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

    public function testServices()
    {
        Assert::type(PresenterMapper::class, $this->container->getService('presenterMapping.presenterMapper'));
        Assert::type(PresenterFactory::class, $this->container->getService('application.presenterFactory'));
    }

    public function testMapping()
    {
        /** @var PresenterFactory $presenterFactory */
        $presenterFactory = $this->container->getByType(Nette\Application\IPresenterFactory::class);

        Assert::same('App\FooModule\BarPresenter', $presenterFactory->formatPresenterClass('Foo:Bar'));
    }

}


\run(new PresenterMappingExtensionTest());
