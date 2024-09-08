<?php
declare(strict_types=1);
namespace Monorepo\Workers;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Utils\VersionUtils;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class UpdatePackageVersion implements ReleaseWorkerInterface
{
    public function __construct(
        private DevMasterAliasUpdater $devMasterAliasUpdater,
        private ComposerJsonProvider $composerJsonProvider,
        private VersionUtils $versionUtils,
    ) { }

    public function work(Version $version): void
    {
        $composerFiles = $this->getComposerJsonFiles();

        foreach ($composerFiles as $composerJsonFile) {
            if (!file_exists($composerJsonFile)) {
                echo sprintf('File not found: %s', $composerJsonFile) . PHP_EOL;
                continue;
            }

            $composerJsonContent = json_decode(file_get_contents($composerJsonFile), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                echo sprintf('Invalid JSON in %s', $composerJsonFile) . PHP_EOL;
                continue;
            }

            $composerJsonContent['version'] = $version->getVersionString();

            $newContent = json_encode($composerJsonContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            try {
                file_put_contents($composerJsonFile, $newContent);
                echo sprintf('Updated version to "%s" in %s', $version->getVersionString(), $composerJsonFile) . PHP_EOL;
            } catch (\Exception $exception) {
                echo sprintf('Failed to update %s due to an error: %s', $composerJsonFile, $exception->getMessage()) . PHP_EOL;
            }
        }
    }

    public function getDescription(Version $version): string
    {
        return 'Updating composer.json version foreach package';
    }

    private function getComposerJsonFiles(): array
    {
        // Assuming the packages are stored in a packages directory.
        return glob(__DIR__ . '/../../packages/*/composer.json');
    }
}
