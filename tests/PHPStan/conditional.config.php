<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Nette\DI\Expression;

$config = ['parameters' => ['ignoreErrors' => []]];

if (InstalledVersions::satisfies(new VersionParser(), 'nette/application', '<3.2.10')) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Parameter \\#1 \\$mapping \\(array<string, array\\{string, string, string\\}\\|string>\\) of method Nepada\\\\PresenterMapping\\\\PresenterFactory::setMapping\\(\\) should be contravariant~',
        'path' => __DIR__ . '/../../src/PresenterMapping/PresenterFactory.php',
        'count' => 1,
    ];
}

// nette/di <3.3 has no Expression base class; InstalledVersions can't be used, it also sees the nette/di bundled in phpstan.phar
if (! class_exists(Expression::class)) {
    $config['parameters']['ignoreErrors'][] = [
        'identifier' => 'instanceof.alwaysTrue',
        'path' => __DIR__ . '/../../src/Bridges/PresenterMappingDI/PresenterMappingExtension.php',
        'count' => 1,
    ];
}

return $config;
