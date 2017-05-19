<?php declare(strict_types=1);

namespace Tests\AppBundle\Service\Deployer;

use AppBundle\Service\BranchMatcher;
use AppBundle\Service\Deployer\DeployHandler;
use AppBundle\Service\Deployer\Method\HerokuMethod;
use AppBundle\Service\Fetcher\GitFetcher;
use AppBundle\Service\TargetPreparation\HerokuClient;
use GitWrapper\GitWorkingCopy;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @see DeployHandler
 */
class DeployHandlerTest extends WebTestCase
{
    /**
     * With handler: Heroku
     *
     * @see Deployer::deploy()
     */
    public function testDeploy()
    {
        $deployer = $this->createDeployer();
        $this->injectHerokuMethod($deployer, 'Branch master set up to track remote branch master from origin');

        $result = $deployer->deploy(
            '{"ref": "refs/heads/master"}',
            'anarchifaq_test'
        );

        $this->assertTrue($result->isSuccess());
    }

    /**
     * With handler: Heroku
     *
     * @see Deployer::deploy()
     */
    public function testDeployFailure()
    {
        $deployer = $this->createDeployer();
        $this->injectHerokuMethod($deployer, 'Git process returned non-zero exit code...');

        $result = $deployer->deploy(
            '{"ref": "refs/heads/master"}',
            'anarchifaq_test'
        );

        $this->assertFalse($result->isSuccess());
    }

    protected function injectHerokuMethod(DeployHandler $deployer, string $gitOutput)
    {
        $git = $this->createMock(GitWorkingCopy::class);
        $git->method('getOutput')
            ->willReturn($gitOutput);

        $client = $this->createMock(HerokuClient::class);
        $client->method('prepareTarget')
            ->willReturn($git);

        $deployer->addMethod(
            new HerokuMethod($this->createMock(GitFetcher::class), $client)
        );
    }

    protected function createDeployer(): DeployHandler
    {
        $deployer = new DeployHandler(
            new BranchMatcher()
        );

        $deployer->setConfiguration([
            'repositories' => [
                'anarchifaq_test' => [
                    'git_url' => 'https://github.com/Wolnosciowiec/anarchi-faq-pl',
                    'git_branch' => 'master',
                    'git_branch_math' => 'exact',

                    'heroku' => [
                        'login' => 'test@gmail.com',
                        'token' => 'test123',
                        'name' => 'webproxy_1_test',
                    ],
                ],
            ],
        ]);

        return $deployer;
    }
}
