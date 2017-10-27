<?php
/**
 * Test: Nepada\PresenterMapping\PresenterMapper
 *
 * This file is part of the nepada/presenter-mapping.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace NepadaTests\PresenterMapping;

use Nepada\PresenterMapping\PresenterMapper;
use Nette;
use Tester;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class PresenterMapperTest extends Tester\TestCase
{

    /** @var PresenterMapper */
    private $presenterMapper;


    /**
     * @return mixed[]
     */
    public function getMapping(): array
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
     * @throws Nette\InvalidStateException Invalid mapping mask for module 'invalid'.
     */
    public function testInvalidMapping(): void
    {
        $presenterMapper = new PresenterMapper;
        $presenterMapper->setMapping(['invalid' => ['*', '*']]);
    }

    protected function setUp(): void
    {
        $this->presenterMapper = new PresenterMapper;
        $this->presenterMapper->setMapping([
            '*' => ['App', 'Module\*', 'Presenter\*'],
            'Foo' => 'Foo\*FooModule\*FooPresenter',
            'Foo:Bar' => 'FooBar\*FooBarModule\*FooBarPresenter',
            'Foo:Bar:Baz' => 'BazPresenter',
        ]);
    }

}


\run(new PresenterMapperTest());
