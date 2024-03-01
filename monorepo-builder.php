<?php

declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;
use Monorepo\Workers\ChangeStabilityToStable;

return static function (MBConfig $mbConfig): void {

    $mbConfig->packageDirectories([
        __DIR__ . '/apps/src',
        __DIR__ . '/global/src',
    ]);
    $mbConfig->defaultBranch('main');;
    $mbConfig->packageAliasFormat('<major>.<minor>.x-dev');

    $mbConfig->dataToAppend([
        ComposerJsonSection::AUTOLOAD => [
            'psr-4' => [
                'Monorepo\Workers\\' => 'workers/src/'
            ],
        ]
    ]);
    $mbConfig->dataToRemove([
        ComposerJsonSection::REQUIRE => [
            "magento/product-enterprise-edition" => '*',
            "magento/composer-root-update-plugin" => '*',
            "magento/composer-dependency-version-audit-plugin" => '*'
        ],
        ComposerJsonSection::EXTRA => [
            'patches' => '*'
        ],
        ComposerJsonSection::AUTOLOAD => [
            'psr-4' => [
                'Magento\Setup\\' => 'apps/src/commerce-rs/setup/src/Magento/Setup/'
            ],
            'files' => [
                '*' => 'apps/src/commerce-emea/app/etc/NonComposerComponentRegistration.php'
            ]
        ]
    ]);
    $mbConfig->workers([
        UpdateReplaceReleaseWorker::class,
        SetCurrentMutualDependenciesReleaseWorker::class,
        AddTagToChangelogReleaseWorker::class,
        TagVersionReleaseWorker::class,
        PushTagReleaseWorker::class,
        SetNextMutualDependenciesReleaseWorker::class,
        UpdateBranchAliasReleaseWorker::class,
        PushNextDevReleaseWorker::class,
        ChangeStabilityToStable::Class
    ]);
};
