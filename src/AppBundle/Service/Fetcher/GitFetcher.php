<?php declare(strict_types=1);

namespace AppBundle\Service\Fetcher;

use GitWrapper\GitException;
use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * GitFetcher
 * ==========
 *
 * Fetches a repository from git by both HTTPS and git protocol
 */
class GitFetcher
{
    /**
     * @var GitWrapper $wrapper
     */
    protected $wrapper;

    /**
     * Path where all repositories are cloned
     *
     * @var string $storagePath
     */
    protected $storagePath;

    /**
     * Path where potentially could be placed files
     * to copy right after the repository will be cloned
     *
     * @var string $repositoriesOverridePath
     */
    protected $repositoriesOverridePath;

    public function __construct(
        GitWrapper $wrapper,
        string $repositoriesStoragePath,
        string $repositoriesOverridePath
    ) {
        $this->wrapper = $wrapper;
        $this->storagePath = $repositoriesStoragePath;
        $this->repositoriesOverridePath = $repositoriesOverridePath;
    }

    public function cloneRepository(string $url, string $repositoryName, string $branchName): GitWorkingCopy
    {
        $path = $this->storagePath . '/' . $repositoryName;

        if (is_dir($path)) {
            (new Filesystem())->remove($path);
        }

        $repository = $this->wrapper->cloneRepository($url, $path);
        $repository->checkout($branchName);
        $this->copyOverrideFiles($path, $repositoryName);
        $this->commitUntrackedFiles($repository);

        return $repository;
    }

    protected function copyOverrideFiles(string $repositoryPath, string $repositoryName)
    {
        $filesystem = new Filesystem();
        $finder = new Finder();
        $finder->files()->in($this->repositoriesOverridePath . '/' . $repositoryName);

        /**
         * @var SplFileInfo $file
         */
        foreach ($finder as $file) {
            if (!is_dir($repositoryPath . '/' . $file->getRelativePath())) {
                $filesystem->mkdir($repositoryPath . '/' . $file->getRelativePath());
            }

            $filesystem->copy($file->getRealPath(), $repositoryPath . '/' . $file->getRelativePathname(), true);
        }
    }

    /**
     * Commit all untracked files
     *
     * @param GitWorkingCopy $git
     */
    protected function commitUntrackedFiles(GitWorkingCopy $git)
    {
        $git->add('.');

        try {
            $git->commit('[Wolnosciowiec][Deployer] ' . date('Y-m-d H:i:s'));

        } catch (GitException $e) { };
    }
}
