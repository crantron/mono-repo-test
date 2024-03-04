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

    //set location of packages / apps
    $mbConfig->packageDirectories([
        __DIR__ . '/apps/src',
        __DIR__ . '/global/src',
    ]);

    //set the git default branch. Defaults to master - which is outdated.
    $mbConfig->defaultBranch('main');;

    //set dev alias for dev-main, so composers update/install/require will pull dev-main when
    //minimum stability is set to dev in root composer.jsons
    $mbConfig->packageAliasFormat('<major>.<minor>.x-dev');

    //add psr-4 autoload for custom release workers
    $mbConfig->dataToAppend([
        ComposerJsonSection::AUTOLOAD => [
            'psr-4' => [
                'Monorepo\Workers\\' => 'workers/src/'
            ],
        ]
    ]);

    //remove adobe commerce packages, these need to be removed as when running composer install
    //in the monorepo root installs commerce.
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

    //default release workers, provided by monorepo library
    //see: vendor/symplify/monorepo-builder/packages/Release/ReleaseWorker/*
    $originalWorkers = [
        UpdateReplaceReleaseWorker::class,
        SetCurrentMutualDependenciesReleaseWorker::class,
        AddTagToChangelogReleaseWorker::class,
        TagVersionReleaseWorker::class,
        PushTagReleaseWorker::class,
        SetNextMutualDependenciesReleaseWorker::class,
        UpdateBranchAliasReleaseWorker::class,
        PushNextDevReleaseWorker::class
    ];

    //configured custom workers
    //see workers/src/*
    $customWorkers = [
        ChangeStabilityToStable::Class
    ];

    $workers = array_merge($originalWorkers, $customWorkers);

    $mbConfig->workers($workers);
};
