<?php declare(strict_types=1);

namespace Tests\AppBundle\Service\Deployer\Method;

use AppBundle\Service\Deployer\Method\ThinDeployerMethod;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\Client;

/**
 * @see ThinDeployerMethod
 */
class ThinDeployerMethodTest extends WebTestCase
{
    /**
     * @see ThinDeployerMethod::deploy()
     */
    public function testDeploy()
    {
        /**
         * @var Client|\PHPUnit_Framework_MockObject_MockObject $client
         * @var ThinDeployerMethod $deployer
         */
        list($client, $deployer) = $this->createService();

        $client->expects($this->once())
            ->method('request')
            ->willReturn(new Response());

        $result = $deployer->deploy([
            'thin_deployer' => [
                'url' => 'http://example.org',
                'service_name' => 'test',
                'token' => '123',
            ]
        ], 'repository-name');

        $this->assertTrue($result->isSuccess());
    }

    public function testFailureDeploy()
    {
        /**
         * @var Client|\PHPUnit_Framework_MockObject_MockObject $client
         * @var ThinDeployerMethod $deployer
         */
        list($client, $deployer) = $this->createService();

        $client->expects($this->once())
            ->method('request')
            ->willThrowException(
                new BadResponseException(
                    'Validation failure...',
                    new Request('POST', 'http://example.org')
                )
            );

        $result = $deployer->deploy([
            'thin_deployer' => [
                'url' => 'http://example.org',
                'service_name' => 'test',
                'token' => '123',
            ]
        ], 'repository-name');

        $this->assertFalse($result->isSuccess());
    }

    protected function createService(): array
    {
        $client = $this->createMock(Client::class);
        $deployer = new ThinDeployerMethod($client);

        return [
            $client,
            $deployer,
        ];
    }
}
