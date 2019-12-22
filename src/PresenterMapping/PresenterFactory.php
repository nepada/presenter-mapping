<?php
declare(strict_types = 1);

namespace Nepada\PresenterMapping;

use Nette;

class PresenterFactory extends Nette\Application\PresenterFactory
{

    private PresenterMapper $presenterMapper;

    public function __construct(PresenterMapper $presenterMapper, ?callable $factory = null)
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
    public function formatPresenterClass(string $presenter): string
    {
        return $this->presenterMapper->formatPresenterClass($presenter);
    }

    /**
     * Formats presenter name from class name.
     *
     * @param string $class
     * @return string|null
     */
    public function unformatPresenterClass(string $class): ?string
    {
        return $this->presenterMapper->unformatPresenterClass($class);
    }

    /**
     * BC with Nette PresenterFactory, use PresenterMapping::setMapping() instead.
     *
     * @deprecated
     * @param string[]|string[][] $mapping
     * @return static
     */
    public function setMapping(array $mapping): self
    {
        $this->presenterMapper->setMapping($mapping);
        return $this;
    }

}
