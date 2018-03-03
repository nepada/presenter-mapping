<?php
declare(strict_types = 1);

namespace Nepada\PresenterMapping;

use Nette;

class PresenterFactory extends Nette\Application\PresenterFactory
{

    /** @var PresenterMapper */
    private $presenterMapper;

    /**
     * @param PresenterMapper $presenterMapper
     * @param callable|null $factory function (string $class): IPresenter
     */
    public function __construct(PresenterMapper $presenterMapper, ?callable $factory = null)
    {
        parent::__construct($factory);
        $this->presenterMapper = $presenterMapper;
    }

    /**
     * Formats presenter class name from its name.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
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
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @param string $class
     * @return string|null
     */
    public function unformatPresenterClass($class)
    {
        return $this->presenterMapper->unformatPresenterClass($class);
    }

    /**
     * BC with Nette PresenterFactory, use PresenterMapping::setMapping() instead.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @deprecated
     * @param string[]|string[][] $mapping
     * @return static
     */
    public function setMapping(array $mapping)
    {
        $this->presenterMapper->setMapping($mapping);
        return $this;
    }

}
