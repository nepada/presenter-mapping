<?php
/**
 * This file is part of the nepada/presenter-mapping.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace NepadaTests\PresenterMapping;

use Nepada\PresenterMapping\PresenterMapper;
use Nette;
use Tester;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';


class PresenterMapperTest extends Tester\TestCase
{

    /** @var PresenterMapper */
    private $presenterMapper;


    public function setUp()
    {
        $this->presenterMapper = new PresenterMapper;
        $this->presenterMapper->setMapping([
                '*' => 'App\*Module\*Presenter',
                'Foo' => 'Foo\*FooModule\*FooPresenter',
                'Foo:Bar' => 'FooBar\*FooBarModule\*FooBarPresenter',
                'Foo:Bar:Baz' => 'BazPresenter',
            ]);
    }

    public function getMapping()
    {
        return [
            ['Foo:Bar:Baz', 'BazPresenter'],
            ['Foo:Bar:Xyz', 'FooBar\XyzFooBarPresenter'],
            ['Foo:Bar:Abc:Xyz', 'FooBar\AbcFooBarModule\XyzFooBarPresenter'],
            ['Foo:Abc:Xyz', 'Foo\AbcFooModule\XyzFooPresenter'],
            ['Foo:Xyz', 'Foo\XyzFooPresenter'],
            ['Abc:Xyz', 'App\AbcModule\XyzPresenter'],
            ['Xyz', 'App\XyzPresenter'],
        ];
    }

    /**
     * @dataProvider getMapping
     * @param string $presenter
     * @param string $class
     */
    public function testMapping($presenter, $class)
    {
        Assert::same($class, $this->presenterMapper->formatPresenterClass($presenter));
        Assert::same($presenter, $this->presenterMapper->unformatPresenterClass($class));
    }

}


\run(new PresenterMapperTest());
