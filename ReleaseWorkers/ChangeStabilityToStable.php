<?php
declare(strict_types=1);
namespace ReleaseWorkers;

use Magento\Tests\NamingConvention\true\string;
use PharIo\Version\Version;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Utils\VersionUtils;

final class ChangeStabilityToStable implements ReleaseWorkerInterface
{
    public function __construct(
        private DevMasterAliasUpdater $devMasterAliasUpdater,
        private ComposerJsonProvider $composerJsonProvider,
        private VersionUtils $versionUtils
    ) {

    }

    public function work(Version $version): void
    {
        // nothing for now
    }

    public function getDescription(Version $version): string
    {
        return \sprintf('this is custom stuff: version - %s', 1);
    }
}