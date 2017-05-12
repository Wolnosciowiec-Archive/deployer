<?php declare(strict_types=1);

namespace AppBundle\Service\Fetcher;

use GitWrapper\GitWrapper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @see GitFetcher
 */
class GitFetcherTest extends WebTestCase
{
    /**
     * @group integration
     * @see GitFetcher::cloneRepository()
     */
    public function testCloneRepository()
    {
        $git = $this->createFetcher()->cloneRepository('https://github.com/blackandred/anarchist-images', 'anarchist_images', 'master');

        $this->assertFileExists('/tmp/git_fetcher/anarchist_images/.git');
        $this->assertFileExists('/tmp/git_fetcher/anarchist_images/README.md');
        $this->assertFileExists('/tmp/git_fetcher/anarchist_images/some/deep/directory/structure/file.md');

        // files were overridden/added to the cloned repository
        $this->assertSame("This is an overrided file\n", file_get_contents('/tmp/git_fetcher/anarchist_images/README.md'));
        $this->assertSame("Hello :-)\n", file_get_contents('/tmp/git_fetcher/anarchist_images/some/deep/directory/structure/file.md'));

        // override was commited
        $this->assertTrue($git->isAhead());
        $this->assertFalse($git->hasChanges());
    }

    protected function createFetcher()
    {
        @mkdir('/tmp/git_fetcher');

        return new GitFetcher(
            new GitWrapper(),
            '/tmp/git_fetcher',
            __DIR__ . '/../../../Examples/repositories_override'
        );
    }
}
