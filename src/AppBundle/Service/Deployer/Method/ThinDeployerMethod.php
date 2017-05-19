<?php declare(strict_types=1);

namespace AppBundle\Service\Deployer\Method;

use AppBundle\Entity\DeployResult;
use AppBundle\Service\Fetcher\GitFetcher;
use AppBundle\Service\TargetPreparation\HerokuClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * ThinDeployerMethod
 * ==================
 *
 * Passes the deploy request to the Thin DeployHandler service
 */
class ThinDeployerMethod implements DeployerMethodInterface
{
    /**
     * @var Client $client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritdoc
     */
    public function deploy(array $repository, string $repositoryName): DeployResult
    {
        $details = $repository['thin_deployer'];

        try {
            $response = $this->getClient()->request('POST', $details['url'] . '/deploy/' . $details['service_name'], [
                'headers' => [
                    'X-Auth-Token' => $details['token'],

                    // optional information for debugging/tracking the request later
                    'X-Caller-Source'      => 'DeployHandler',
                    'X-Caller-Address'     => $_SERVER['SERVER_ADDR'] ?? '',
                    'X-Caller-Name'        => $_SERVER['SERVER_NAME'] ?? '',
                    'X-Caller-ServiceName' => $repositoryName,
                ]
            ]);

        } catch (RequestException $exception) {
            $details = $exception->getMessage();

            if ($exception->getResponse() instanceof ResponseInterface) {
                $details .= "\n\n" . $exception->getResponse()->getBody()->getContents();
            }

            return new DeployResult(false, $details);
        }

        return new DeployResult(true, $response->getBody()->getContents());
    }

    /**
     * @inheritdoc
     */
    public function canHandleRepository(array $repository): bool
    {
        return isset($repository['thin_deployer']) && count($repository['thin_deployer']) > 0;
    }

    protected function getClient(): Client
    {
        return $this->client;
    }
}
