<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\PresenterMappingDI;

use Nepada\PresenterMapping\PresenterFactory;
use Nepada\PresenterMapping\PresenterMapper;
use NepadaTests\Environment;
use NepadaTests\TestCase;
use Nette;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class PresenterMappingExtensionTest extends TestCase
{

    private Nette\DI\Container $container;

    public function testServices(): void
    {
        Assert::type(PresenterMapper::class, $this->container->getService('presenterMapping.presenterMapper'));
        Assert::type(PresenterFactory::class, $this->container->getService('application.presenterFactory'));
    }

    public function testMapping(): void
    {
        $presenterFactory = $this->container->getByType(Nette\Application\IPresenterFactory::class);

        Assert::type(PresenterFactory::class, $presenterFactory);
        Assert::same('App\FooModule\BarPresenter', $presenterFactory->formatPresenterClass('Foo:Bar'));
    }

    protected function setUp(): void
    {
        $configurator = new Nette\Configurator();
        $configurator->setTempDirectory(Environment::getTempDir());
        $configurator->setDebugMode(true);
        $configurator->addConfig(__DIR__ . '/fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

}


(new PresenterMappingExtensionTest())->run();
