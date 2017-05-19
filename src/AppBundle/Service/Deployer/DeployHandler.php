<?php declare(strict_types=1);

namespace AppBundle\Service\Deployer;

use AppBundle\Entity\DeployResult;
use AppBundle\Exception\Deployer\RepositoryNotFoundException;
use AppBundle\Exception\UnexpectedStateException;
use AppBundle\Service\BranchMatcher;
use AppBundle\Service\Deployer\Method\DeployerMethodInterface;

/**
 * DeployHandler
 * =============
 *
 * Deploys code from git/github to the Heroku's git triggering the deployment
 * automatically on push
 *
 * 1. A "Fetcher" is cloning the original repository, applying local patches (repository override per repository)
 * 2. "Client" is setting up the target git upstream
 * 3. "DeployHandler" is pushing to the repository
 */
class DeployHandler
{
    /**
     * Configuration from the key "heroku_deploy" that is placed in deploy.yml
     *
     * @var array $configuration
     */
    protected $configuration = [];

    /**
     * @var BranchMatcher $branchMatcher
     */
    protected $branchMatcher;

    /**
     * @var DeployerMethodInterface[] $methods
     */
    protected $methods = [];

    public function __construct(BranchMatcher $branchMatcher)
    {
        $this->branchMatcher = $branchMatcher;
    }

    /**
     * Inject configuration from container
     *
     * @param array $configuration
     * @throws UnexpectedStateException
     * @return DeployHandler
     */
    public function setConfiguration(array $configuration): DeployHandler
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

        foreach ($this->methods as $method) {
            if ($method->canHandleRepository($repository)) {
                return $method->deploy($repository, $repositoryName);
            }
        }

        return new DeployResult(false, 'Invalid configuration: No method is available to handle this service deployment');
    }

    /**
     * Used by the IoC container to add tagged services
     *
     * @param DeployerMethodInterface $method
     * @return DeployHandler
     */
    public function addMethod(DeployerMethodInterface $method): DeployHandler
    {
        $this->methods[] = $method;
        return $this;
    }

    protected function getRepository(string $repositoryName): array
    {
        if (!isset($this->configuration[$repositoryName])) {
            throw new RepositoryNotFoundException('Repository "' . $repositoryName . '" not found');
        }

        return $this->configuration[$repositoryName];
    }
}
