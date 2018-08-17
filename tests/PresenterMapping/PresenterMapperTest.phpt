<?php
declare(strict_types = 1);

namespace NepadaTests\PresenterMapping;

use Nepada\PresenterMapping\PresenterMapper;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class PresenterMapperTest extends TestCase
{

    /** @var PresenterMapper */
    private $presenterMapper;

    protected function setUp(): void
    {
        $this->presenterMapper = new PresenterMapper();
        $this->presenterMapper->setMapping([
            '*' => ['App', 'Module\*', 'Presenter\*'],
            'Foo' => 'Foo\*FooModule\*FooPresenter',
            'Foo:Bar' => 'FooBar\*FooBarModule\*FooBarPresenter',
            'Foo:Bar:Baz' => 'BazPresenter',
        ]);
    }

    /**
     * @dataProvider getMapping
     * @param string $presenter
     * @param string $class
     */
    public function testMapping(string $presenter, string $class): void
    {
        Assert::same($class, $this->presenterMapper->formatPresenterClass($presenter));
        Assert::same($presenter, $this->presenterMapper->unformatPresenterClass($class));
    }

    /**
     * @return mixed[]
     */
    protected function getMapping(): array
    {
        return [
            ['Foo:Bar:Baz', 'BazPresenter'],
            ['Foo:Bar:Xyz', 'FooBar\XyzFooBarPresenter'],
            ['Foo:Bar:Abc:Xyz', 'FooBar\AbcFooBarModule\XyzFooBarPresenter'],
            ['Foo:Abc:Xyz', 'Foo\AbcFooModule\XyzFooPresenter'],
            ['Foo:Xyz', 'Foo\XyzFooPresenter'],
            ['Abc:Xyz', 'App\Module\Abc\Presenter\Xyz'],
            ['Xyz', 'App\Presenter\Xyz'],
        ];
    }

    public function testInvalidMapping(): void
    {
        $presenterMapper = new PresenterMapper();

        Assert::throws(
            function () use ($presenterMapper): void {
                $presenterMapper->setMapping(['invalid' => ['*', '*']]);
            },
            \InvalidArgumentException::class,
            'Invalid mapping mask for module \'invalid\'.'
        );
    }

}


(new PresenterMapperTest())->run();
