<?php
declare(strict_types = 1);

namespace NepadaTests\PresenterMapping;

use Nepada\PresenterMapping\PresenterFactory;
use Nepada\PresenterMapping\PresenterMapper;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * Based on Nette test cases to ensure compatibility with default implementation.
 *
 * @testCase
 */
class PresenterFactoryTest extends TestCase
{

    private PresenterFactory $presenterFactory;

    protected function setUp(): void
    {
        $mapper = new PresenterMapper();
        $this->presenterFactory = new PresenterFactory($mapper);
    }

    public function testStandardMapping(): void
    {
        $factory = $this->presenterFactory;
        $factory->setMapping([
            'Foo2' => 'App2\*\*Presenter',
            'Foo3' => 'My\App\*Mod\*Presenter',
        ]);

        Assert::same('FooPresenter', $factory->formatPresenterClass('Foo'));
        Assert::same('FooModule\BarPresenter', $factory->formatPresenterClass('Foo:Bar'));
        Assert::same('FooModule\BarModule\BazPresenter', $factory->formatPresenterClass('Foo:Bar:Baz'));

        Assert::same('Foo2Presenter', $factory->formatPresenterClass('Foo2'));
        Assert::same('App2\BarPresenter', $factory->formatPresenterClass('Foo2:Bar'));
        Assert::same('App2\Bar\BazPresenter', $factory->formatPresenterClass('Foo2:Bar:Baz'));

        Assert::same('My\App\BarPresenter', $factory->formatPresenterClass('Foo3:Bar'));
        Assert::same('My\App\BarMod\BazPresenter', $factory->formatPresenterClass('Foo3:Bar:Baz'));

        Assert::same('NetteModule\FooPresenter', $factory->formatPresenterClass('Nette:Foo'));
    }

    public function testMappingWithUnspecifiedModule(): void
    {
        $factory = $this->presenterFactory;
        $factory->setMapping([
            'Foo2' => 'App2\*Presenter',
            'Foo3' => 'My\App\*Presenter',
        ]);

        Assert::same('Foo2Presenter', $factory->formatPresenterClass('Foo2'));
        Assert::same('App2\BarPresenter', $factory->formatPresenterClass('Foo2:Bar'));
        Assert::same('App2\BarModule\BazPresenter', $factory->formatPresenterClass('Foo2:Bar:Baz'));

        Assert::same('My\App\BarPresenter', $factory->formatPresenterClass('Foo3:Bar'));
        Assert::same('My\App\BarModule\BazPresenter', $factory->formatPresenterClass('Foo3:Bar:Baz'));
    }

    public function testMappingToSubnamespaces(): void
    {
        $factory = $this->presenterFactory;
        $factory->setMapping([
            '*' => ['App', 'Module\*', 'Presenter\*'],
        ]);
        Assert::same('App\Module\Foo\Presenter\Bar', $factory->formatPresenterClass('Foo:Bar'));
        Assert::same('App\Module\Universe\Module\Foo\Presenter\Bar', $factory->formatPresenterClass('Universe:Foo:Bar'));
    }

    public function testMappingDirectToNamespace(): void
    {
        $factory = $this->presenterFactory;
        $factory->setMapping([
            '*' => ['', '*', '*'],
        ]);
        Assert::same('Module\Foo\Bar', $factory->formatPresenterClass('Module:Foo:Bar'));
    }

    public function testInvalidMapping(): void
    {
        Assert::exception(
            function (): void {
                $factory = $this->presenterFactory;
                $factory->setMapping([
                    '*' => ['*', '*'],
                ]);
            },
            \InvalidArgumentException::class,
            "Invalid mapping mask for module '*'.",
        );
    }

}


(new PresenterFactoryTest())->run();
