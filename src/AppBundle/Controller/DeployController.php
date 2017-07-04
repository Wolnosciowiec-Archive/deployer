<?php declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Exception\Deployer\RepositoryNotFoundException;
use AppBundle\Service\Deployer\DeployHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * DeployController
 * ================
 *
 * Provides an endpoint to deploy the service
 */
class DeployController extends Controller
{
    public function indexAction(Request $request, string $apiKey, string $repositoryName): Response
    {
        try {
            $result = $this->getDeployer()
                ->deploy(
                    $request->getContent(),
                    $repositoryName
                );

        } catch (RepositoryNotFoundException $exception) {
            return $this->createFailureResponse($exception);
        }

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

    protected function createFailureResponse(\Throwable $exception): Response
    {
        return new JsonResponse(
            [
                'errors' => [
                    [
                        'status' => 400,
                        'title' => get_class($exception),
                        'detail' => $exception->getMessage(),
                    ]
                ],
            ],
            400
        );
    }
}
