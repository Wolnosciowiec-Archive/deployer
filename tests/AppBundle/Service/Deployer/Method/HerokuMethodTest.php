<?php declare(strict_types=1);

namespace Tests\AppBundle\Service\Deployer\Method;

use AppBundle\Service\Deployer\Method\HerokuMethod;
use AppBundle\Service\Fetcher\GitFetcher;
use AppBundle\Service\TargetPreparation\HerokuClient;
use GitWrapper\GitWorkingCopy;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @see HerokuMethod
 */
class HerokuMethodTest extends WebTestCase
{
    /**
     * @see HerokuMethod::deploy()
     */
    public function testDeploy()
    {
        $method = $this->createHerokuMethod('Branch master set up to track remote branch master from origin');
        $result = $method->deploy([
            'git_url' => '...',
            'git_branch' => 'master',
            'heroku' => [
                'login' => 'xxx',
                'token' => 'yyy',
                'name'  => 'zzz',
            ]
        ], 'some-repository-name');

        $this->assertTrue($result->isSuccess());
    }

    /**
     * @see HerokuMethod::deploy()
     */
    public function testFailureDeploy()
    {
        $method = $this->createHerokuMethod('FATAL: Cannot push to the remote repository, no access');
        $result = $method->deploy([
            'git_url' => '...',
            'git_branch' => 'master',
            'heroku' => [
                'login' => 'xxx',
                'token' => 'yyy',
                'name'  => 'zzz',
            ]
        ], 'some-repository-name');

        $this->assertFalse($result->isSuccess());
    }

    protected function createHerokuMethod(string $gitOutput): HerokuMethod
    {
        $git = $this->createMock(GitWorkingCopy::class);
        $git->method('getOutput')
            ->willReturn($gitOutput);

        $client = $this->createMock(HerokuClient::class);
        $client->method('prepareTarget')
            ->willReturn($git);

        return new HerokuMethod($this->createMock(GitFetcher::class), $client);
    }
}
