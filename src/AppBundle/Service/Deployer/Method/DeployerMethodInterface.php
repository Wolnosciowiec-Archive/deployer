<?php declare(strict_types=1);

namespace AppBundle\Service\Deployer\Method;

use AppBundle\Entity\DeployResult;

/**
 * DeployerMethodInterface
 * =======================
 *
 * Allows to have multiple methods of handling incoming webhook
 * eg. passing the deploy request to the "Thin DeployHandler" service, or asking Heroku hosting to do the deployment
 */
interface DeployerMethodInterface
{
    /**
     * Handles the deployment of a repository
     *
     * @param array $repository
     * @param string $repositoryName
     *
     * @return DeployResult
     */
    public function deploy(array $repository, string $repositoryName): DeployResult;

    /**
     * Takes repository details as input and tells if it can handle it
     *
     * @param array $repository
     * @return bool
     */
    public function canHandleRepository(array $repository): bool;
}
