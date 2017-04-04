<?php declare(strict_types=1);

namespace AppBundle\Service\Deployer;

use AppBundle\Entity\DeployResult;
use AppBundle\Exception\Deployer\RepositoryNotFoundException;
use AppBundle\Exception\UnexpectedStateException;
use AppBundle\Service\BranchMatcher;
use AppBundle\Service\Fetcher\GitFetcher;
use AppBundle\Service\HerokuClient;

/**
 * Deployer
 * ========
 *
 * Deploys code from git/github to the Heroku's git triggering the deployment
 * automatically on push
 *
 * 1. A "Fetcher" is cloning the original repository, applying local patches (repository override per repository)
 * 2. "Client" is setting up the target git upstream
 * 3. "Deployer" is pushing to the repository
 */
class Deployer
{
    /**
     * Configuration from the key "heroku_deploy" that is placed in deploy.yml
     *
     * @var array $configuration
     */
    protected $configuration = [];

    /**
     * @var GitFetcher $fetcher
     */
    protected $fetcher;

    /**
     * @var HerokuClient $client
     */
    protected $client;

    /**
     * @var BranchMatcher $branchMatcher
     */
    protected $branchMatcher;

    public function __construct(GitFetcher $fetcher, HerokuClient $client, BranchMatcher $branchMatcher)
    {
        $this->fetcher = $fetcher;
        $this->client = $client;
        $this->branchMatcher = $branchMatcher;
    }

    /**
     * Inject configuration from container
     *
     * @param array $configuration
     * @throws UnexpectedStateException
     * @return Deployer
     */
    public function setConfiguration(array $configuration): Deployer
    {
        if (!empty($this->configuration)) {
            throw new UnexpectedStateException('Configuration could be injected only once, do not create tricks');
        }

        $this->configuration = $configuration['repositories'];
        return $this;
    }

    public function deploy(string $inputPayload, string $repositoryName): DeployResult
    {
        $repository = $this->getRepository($repositoryName);

        if (!$this->branchMatcher->isBranchMatching($inputPayload, $repository)) {
            return new DeployResult(false, 'Branch name does not match');
        }

        $git = $this->fetcher->cloneRepository($repository['git_url'], $repositoryName, $repository['git_branch']);
        $git = $this->client->setUpUpstream(
            $git,
            $repository['heroku_login'],
            $repository['heroku_password'],
            $repository['heroku_name']
        );

        $git->push('-u', 'origin', 'master', '--force');
        $isSuccess = strpos($git->getOutput(), 'Branch master set up to track remote branch master from origin') !== false;

        return new DeployResult($isSuccess, $git->getOutput());
    }

    protected function getRepository(string $repositoryName): array
    {
        if (!isset($this->configuration[$repositoryName])) {
            throw new RepositoryNotFoundException('Repository "' . $repositoryName . '" not found');
        }

        return $this->configuration[$repositoryName];
    }
}
