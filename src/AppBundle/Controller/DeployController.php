<?php

namespace AppBundle\Controller;

use AppBundle\Service\Deployer\DeployHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * DeployController
 * ================
 *
 * Provides an endpoint to deploy the service
 */
class DeployController extends Controller
{
    public function indexAction(Request $request, string $apiKey, string $repositoryName)
    {
        $result = $this->getDeployer()
            ->deploy(
                $request->getContent(),
                $repositoryName
            );

        return new JsonResponse(
            [
                'data' => [
                    'type' => 'output',
                    'id'   => 1,
                    'attributes' => [
                        'output'  => $result->getOutput(),
                        'success' => $result->isSuccess(),
                    ]
                ]
            ],
        $result->isSuccess() ? 200 : 500);
    }

    protected function getDeployer(): DeployHandler
    {
        return $this->get('wolnosciowiec.api.deployer.handler');
    }
}
