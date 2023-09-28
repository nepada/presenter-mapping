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
     */
    public function formatPresenterClass(string $presenter): string
    {
        return $this->presenterMapper->formatPresenterClass($presenter);
    }

    /**
     * Formats presenter name from class name.
     */
    public function unformatPresenterClass(string $class): ?string
    {
        return $this->presenterMapper->unformatPresenterClass($class);
    }

    /**
     * @deprecated BC with Nette PresenterFactory, use PresenterMapping::setMapping() instead.
     * @param string[]|string[][] $mapping
     * @return $this
     */
    public function setMapping(array $mapping): static
    {
        $this->presenterMapper->setMapping($mapping);
        return $this;
    }

}
