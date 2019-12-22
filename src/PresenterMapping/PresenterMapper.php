<?php
declare(strict_types = 1);

namespace Nepada\PresenterMapping;

use Nette;
use Nette\Utils\Strings;

class PresenterMapper
{

    use Nette\SmartObject;

    /** @var string[] of presenter name => class */
    private $presenterMapping = [];

    /** @var string[][] of module => splitted mask */
    private $moduleMapping = [
        '' => ['', '*Module\\', '*Presenter'],
        'Nette' => ['NetteModule\\', '*\\', '*Presenter'],
    ];

    /**
     * Sets mapping as pairs [module:* => mask, presenter => class]:
     * Example 1 - set class of specific presenter:
     *      ['Bar' => 'App\BarPresenter', 'Module:SubModule:Presenter' => 'Namespace\Whatever\PresenterClass']
     * Example 2 - set mapping for modules:
     *      ['App:Foo' => 'App\FooModule\*Module\*Presenter', '*' => '*Module\*Presenter']
     *
     * @param string[]|string[][] $mapping
     * @return static
     */
    public function setMapping(array $mapping): self
    {
        foreach ($mapping as $name => $mask) {
            if (is_string($mask) && strpos($mask, '*') === false) {
                $this->setPresenterMapping($name, $mask);
            } else {
                $this->setModuleMapping(rtrim($name, '*'), $mask);
            }
        }
        return $this;
    }

    /**
     * @param string $presenter
     * @param string $class
     * @return static
     */
    public function setPresenterMapping(string $presenter, string $class): self
    {
        $presenter = trim($presenter, ':');
        $class = ltrim($class, '\\');

        $conflict = array_search($class, $this->presenterMapping, true);
        if ($conflict !== false) {
            throw new \InvalidArgumentException("Presenter class conflict: '$conflict' and '$presenter' both point to '$class'.");
        }

        $this->presenterMapping[$presenter] = $class;
        return $this;
    }

    /**
     * @param string $module
     * @param string|string[] $mask
     * @return static
     */
    public function setModuleMapping(string $module, $mask): self
    {
        $module = trim($module, ':');
        if (is_array($mask) && count($mask) === 3) {
            $this->moduleMapping[$module] = [$mask[0] !== '' ? $mask[0] . '\\' : '', $mask[1] . '\\', $mask[2]];
        } elseif (is_string($mask)) {
            $m = Strings::match($mask, '#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\w*)\z#');
            if ($m === null) {
                throw new \InvalidArgumentException("Invalid mapping mask '$mask' for module '$module'.");
            }
            $this->moduleMapping[$module] = [$m[1], $m[2] ?? '*Module\\', $m[3]];
        } else {
            throw new \InvalidArgumentException("Invalid mapping mask for module '$module'.");
        }
        uksort(
            $this->moduleMapping,
            function (string $a, string $b): int {
                return [substr_count($b, ':'), $b] <=> [substr_count($a, ':'), $a];
            }
        );
        return $this;
    }

    /**
     * Formats presenter class name from its name.
     *
     * @param string $presenter
     * @return string
     */
    public function formatPresenterClass(string $presenter): string
    {
        if (isset($this->presenterMapping[$presenter])) {
            return $this->presenterMapping[$presenter];
        }

        $parts = explode(':', $presenter);
        $presenterName = (string) array_pop($parts);
        $modules = [];
        while (! isset($this->moduleMapping[implode(':', $parts)])) {
            array_unshift($modules, (string) array_pop($parts));
        }
        $mapping = $this->moduleMapping[implode(':', $parts)];

        $class = $mapping[0];
        foreach ($modules as $module) {
            $class .= str_replace('*', $module, $mapping[1]);
        }
        $class .= str_replace('*', $presenterName, $mapping[2]);

        return $class;
    }

    /**
     * Formats presenter name from class name.
     *
     * @param string $class
     * @return string|null
     */
    public function unformatPresenterClass(string $class): ?string
    {
        $presenter = array_search($class, $this->presenterMapping, true);
        if ($presenter !== false) {
            return (string) $presenter;
        }

        foreach ($this->moduleMapping as $module => $mapping) {
            $mapping = str_replace(['\\', '*'], ['\\\\', '(\w+)'], $mapping);
            $matches = Strings::match($class, "#^\\\\?$mapping[0]((?:$mapping[1])*)$mapping[2]\\z#i");
            if ($matches !== null) {
                return ($module === '' ? '' : $module . ':') . Strings::replace($matches[1], "#$mapping[1]#iA", '$1:') . $matches[3];
            }
        }

        return null;
    }

}
