<?php declare(strict_types=1);

namespace Tests\AppBundle\Service\Deployer;

use AppBundle\Service\BranchMatcher;
use AppBundle\Service\Deployer\Deployer;
use AppBundle\Service\Fetcher\GitFetcher;
use AppBundle\Service\HerokuClient;
use GitWrapper\GitWorkingCopy;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @see Deployer
 */
class DeployerTest extends WebTestCase
{
    /**
     * @see Deployer::deploy()
     */
    public function testDeploy()
    {
        $deployer = $this->createDeployer(
            'Branch master set up to track remote branch master from origin'
        );
        $result = $deployer->deploy(
            '{"ref": "refs/heads/master"}',
            'anarchifaq_test'
        );

        $this->assertTrue($result->isSuccess());
    }

    /**
     * @see Deployer::deploy()
     */
    public function testDeployFailure()
    {
        $deployer = $this->createDeployer(
            'Git process returned non-zero exit code...'
        );
        $result = $deployer->deploy(
            '{"ref": "refs/heads/master"}',
            'anarchifaq_test'
        );

        $this->assertFalse($result->isSuccess());
    }

    protected function createDeployer(string $gitOutput): Deployer
    {
        $git = $this->createMock(GitWorkingCopy::class);
        $git->method('getOutput')
            ->willReturn($gitOutput);

        $client = $this->createMock(HerokuClient::class);
        $client->method('setUpUpstream')
            ->willReturn($git);

        $deployer = new Deployer(
            $this->createMock(GitFetcher::class),
            $client,
            new BranchMatcher()
        );

        $deployer->setConfiguration([
            'repositories' => [
                'anarchifaq_test' => [
                    'git_url' => 'https://github.com/Wolnosciowiec/anarchi-faq-pl',
                    'git_branch' => 'master',
                    'git_branch_math' => 'exact',
                    'heroku_login' => 'test@gmail.com',
                    'heroku_token' => 'test123',
                    'heroku_name' => 'webproxy_1_test',
                ],
            ],
        ]);

        return $deployer;
    }
}
