<?php
declare(strict_types=1);
namespace Monorepo\Workers;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\DevMasterAliasUpdater;
use Symplify\MonorepoBuilder\FileSystem\ComposerJsonProvider;
use Symplify\MonorepoBuilder\Utils\VersionUtils;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class UpdatePackageVersion implements ReleaseWorkerInterface
{
    private SymfonyStyle $symfonyStyle;

    public function __construct(
        private DevMasterAliasUpdater $devMasterAliasUpdater,
        private ComposerJsonProvider $composerJsonProvider,
        private VersionUtils $versionUtils,
        private Filesystem $filesystem
    ) {
        $input = new Symfony\Component\Console\Input\ArgvInput();
        $output = new Symfony\Component\Console\Output\ConsoleOutput();
        $this->symfonyStyle = new SymfonyStyle($input, $output);
    }

    public function work(Version $version): void
    {
        $composerFiles = $this->getComposerJsonFiles();

        foreach ($composerFiles as $composerJsonFile) {
            if (!file_exists($composerJsonFile)) {
                $this->symfonyStyle->error(sprintf('File not found: %s', $composerJsonFile));
                continue;
            }

            $composerJsonContent = json_decode(file_get_contents($composerJsonFile), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->symfonyStyle->error(sprintf('Invalid JSON in %s', $composerJsonFile));
                continue;
            }

            $composerJsonContent['version'] = $version->getVersionString();

            $newContent = json_encode($composerJsonContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            try {
                $this->filesystem->dumpFile($composerJsonFile, $newContent);
                $this->symfonyStyle->success(sprintf('Updated version to "%s" in %s', $version->getVersionString(), $composerJsonFile));
            } catch (\Exception $exception) {
                $this->symfonyStyle->error(sprintf('Failed to update %s due to an error: %s', $composerJsonFile, $exception->getMessage()));
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
