<?php

declare (strict_types=1);
namespace MonorepoBuilderPrefix202408;

use MonorepoBuilderPrefix202408\Symfony\Component\Console\Style\SymfonyStyle;
use MonorepoBuilderPrefix202408\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use MonorepoBuilderPrefix202408\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use MonorepoBuilderPrefix202408\Symplify\PackageBuilder\Parameter\ParameterProvider;
use MonorepoBuilderPrefix202408\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use MonorepoBuilderPrefix202408\Symplify\SmartFileSystem\FileSystemFilter;
use MonorepoBuilderPrefix202408\Symplify\SmartFileSystem\FileSystemGuard;
use MonorepoBuilderPrefix202408\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use MonorepoBuilderPrefix202408\Symplify\SmartFileSystem\Finder\SmartFinder;
use MonorepoBuilderPrefix202408\Symplify\SmartFileSystem\SmartFileSystem;
use function MonorepoBuilderPrefix202408\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    // symfony style
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
    // filesystem
    $services->set(FinderSanitizer::class);
    $services->set(SmartFileSystem::class);
    $services->set(SmartFinder::class);
    $services->set(FileSystemGuard::class);
    $services->set(FileSystemFilter::class);
    $services->set(ParameterProvider::class)->args([service('service_container')]);
    $services->set(PrivatesAccessor::class);
};
