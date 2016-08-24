<?php
/**
 * This file is part of the nepada/presenter-mapping.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\PresenterMapping;

use Nette;


class PresenterMapper extends Nette\Object
{

    /** @var array of presenter name => class */
    private $presenterMapping = [];

    /** @var array[] of module => splitted mask */
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
     * @param array $mapping
     * @return self
     * @throws Nette\InvalidStateException
     */
    public function setMapping(array $mapping)
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
     * @return self
     * @throws Nette\InvalidStateException
     */
    public function setPresenterMapping($presenter, $class)
    {
        $presenter = trim($presenter, ':');
        $class = ltrim($class, '\\');

        if ($conflict = array_search($class, $this->presenterMapping, true)) {
            throw new Nette\InvalidStateException("Presenter class conflict: '$conflict' and '$presenter' both point to '$class'.");
        }

        $this->presenterMapping[$presenter] = $class;
        return $this;
    }

    /**
     * @param string $module
     * @param string $mask
     * @return self
     * @throws Nette\InvalidArgumentException
     */
    public function setModuleMapping($module, $mask)
    {
        $module = trim($module, ':');
        if (is_string($mask)) {
            if (!preg_match('#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\w*)\z#', $mask, $m)) {
                throw new Nette\InvalidStateException("Invalid mapping mask '$mask'.");
            }
            $this->moduleMapping[$module] = [$m[1], $m[2] ?: '*Module\\', $m[3]];
        } elseif (is_array($mask) && count($mask) === 3) {
            $this->moduleMapping[$module] = [$mask[0] ? $mask[0] . '\\' : '', $mask[1] . '\\', $mask[2]];
        } else {
            throw new Nette\InvalidStateException("Invalid mapping mask for module '$module'.");
        }
        uksort($this->moduleMapping, function ($a, $b) {return (substr_count($b, ':') - substr_count($a, ':')) ?: strcmp($b, $a);});
        return $this;
    }

    /**
     * Formats presenter class name from its name.
     *
     * @param string $presenter
     * @return string
     */
    public function formatPresenterClass($presenter)
    {
        if (isset($this->presenterMapping[$presenter])) {
            return $this->presenterMapping[$presenter];
        }

        $parts = explode(':', $presenter);
        $presenterName = array_pop($parts);
        $modules = [];
        while (!isset($this->moduleMapping[implode(':', $parts)])) {
            array_unshift($modules, array_pop($parts));
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
     * @return string
     */
    public function unformatPresenterClass($class)
    {
        if ($presenter = array_search($class, $this->presenterMapping, true)) {
            return $presenter;
        }

        foreach ($this->moduleMapping as $module => $mapping) {
            $mapping = str_replace(['\\', '*'], ['\\\\', '(\w+)'], $mapping);
            if (preg_match("#^\\\\?$mapping[0]((?:$mapping[1])*)$mapping[2]\\z#i", $class, $matches)) {
                return ($module === '' ? '' : $module . ':') . preg_replace("#$mapping[1]#iA", '$1:', $matches[1]) . $matches[3];
            }
        }

        return null;
    }

}
