<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
class DeployController extends Controller
{
    public function indexAction(Request $request, string $apiKey, string $repositoryName)
    {
        $result = $this->get('wolnosciowiec.api.heroku.deployer')
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
}
