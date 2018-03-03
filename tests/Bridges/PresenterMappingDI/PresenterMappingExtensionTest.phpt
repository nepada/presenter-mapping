<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\PresenterMappingDI;

use Nepada\PresenterMapping\PresenterFactory;
use Nepada\PresenterMapping\PresenterMapper;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class PresenterMappingExtensionTest extends Tester\TestCase
{

    /** @var Nette\DI\Container */
    private $container;

    public function testServices(): void
    {
        Assert::type(PresenterMapper::class, $this->container->getService('presenterMapping.presenterMapper'));
        Assert::type(PresenterFactory::class, $this->container->getService('application.presenterFactory'));
    }

    public function testMapping(): void
    {
        /** @var PresenterFactory $presenterFactory */
        $presenterFactory = $this->container->getByType(Nette\Application\IPresenterFactory::class);

        Assert::same('App\FooModule\BarPresenter', $presenterFactory->formatPresenterClass('Foo:Bar'));
    }

    protected function setUp(): void
    {
        $configurator = new Nette\Configurator();
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->setDebugMode(true);
        $configurator->addConfig(__DIR__ . '/fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

}


(new PresenterMappingExtensionTest())->run();
