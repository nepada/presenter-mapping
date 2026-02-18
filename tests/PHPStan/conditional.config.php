<?php
declare(strict_types = 1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;

$config = ['parameters' => ['ignoreErrors' => []]];

if (InstalledVersions::satisfies(new VersionParser(), 'nette/application', '<3.2.10')) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#Parameter \#1 \$mapping \(array<string, list<string>\|string>\) of method Nepada\\PresenterMapping\\PresenterFactory::setMapping\(\) should be contravariant#',
        'path' => __DIR__ . '/../../src/PresenterMapping/PresenterFactory.php',
        'count' => 1,
    ];
}

return $config;
