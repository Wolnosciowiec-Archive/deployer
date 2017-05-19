<?php declare(strict_types=1);

namespace AppBundle\Service\Deployer\Method;

use AppBundle\Entity\DeployResult;
use AppBundle\Service\Fetcher\GitFetcher;
use AppBundle\Service\TargetPreparation\HerokuClient;

/**
 * HerokuMethod
 * ============
 *
 * Deploys code to the Heroku
 */
class HerokuMethod implements DeployerMethodInterface
{
    /**
     * @var GitFetcher $fetcher
     */
    protected $fetcher;

    /**
     * @var HerokuClient $client
     */
    protected $client;

    public function __construct(GitFetcher $fetcher, HerokuClient $client)
    {
        $this->fetcher = $fetcher;
        $this->client  = $client;
    }

    public function deploy(array $repository, string $repositoryName): DeployResult
    {
        $git = $this->fetcher->cloneRepository($repository['git_url'], $repositoryName, $repository['git_branch']);
        $git = $this->client->prepareTarget(
            $git,
            $repository['heroku']['login'],
            $repository['heroku']['token'],
            $repository['heroku']['name']
        );

        $git->push('-u', 'origin', 'master', '--force');
        $isSuccess = preg_match('/Branch (.*) set up to track remote branch (.*) from origin/i', $git->getOutput()) > 0;

        return new DeployResult($isSuccess, $git->getOutput());
    }

    public function canHandleRepository(array $repository): bool
    {
        return isset($repository['heroku']) && count($repository['heroku']) > 0;
    }
}
