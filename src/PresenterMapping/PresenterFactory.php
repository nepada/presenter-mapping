<?php
/**
 * This file is part of the nepada/presenter-mapping.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\PresenterMapping;

use Nette;


class PresenterFactory extends Nette\Application\PresenterFactory
{

    /** @var PresenterMapper */
    private $presenterMapper;


    /**
     * @param PresenterMapper $presenterMapper
     * @param callable $factory function (string $class): IPresenter
     */
    public function __construct(PresenterMapper $presenterMapper, callable $factory = null)
    {
        parent::__construct($factory);
        $this->presenterMapper = $presenterMapper;
    }

    /**
     * Formats presenter class name from its name.
     *
     * @param string $presenter
     * @return string
     */
    public function formatPresenterClass($presenter)
    {
        return $this->presenterMapper->formatPresenterClass($presenter);
    }

    /**
     * Formats presenter name from class name.
     *
     * @param string $class
     * @return string
     */
    public function unformatPresenterClass($class)
    {
        return $this->presenterMapper->unformatPresenterClass($class);
    }

    /**
     * BC with Nette PresenterFactory, use PresenterMapping::setMapping() instead.
     *
     * @deprecated
     * @param array $mapping
     * @return Nette\Application\PresenterFactory
     */
    public function setMapping(array $mapping)
    {
        $this->presenterMapper->setMapping($mapping);
        return $this;
    }

}
