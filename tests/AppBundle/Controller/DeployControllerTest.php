<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Controller\DeployController;
use AppBundle\Entity\DeployResult;
use AppBundle\Service\Deployer\DeployHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @see DeployController
 */
class DeployControllerTest extends WebTestCase
{
    /**
     * @see DeployController::indexAction()
     */
    public function testIndex()
    {
        /**
         * @var DeployHandler|\PHPUnit_Framework_MockObject_MockObject $deployer
         * @var DeployController|\PHPUnit_Framework_MockObject_MockObject $controller
         */
        list($deployer, $controller) = $this->getController();

        $deployer->method('deploy')
            ->willReturn(new DeployResult(true, 'Branch deployed'));

        $response = $controller->indexAction(new Request(), '123', 'test_repository_name');

        $this->assertSame('{"data":{"type":"output","id":1,"attributes":{"output":"Branch deployed","success":true}}}', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @see DeployController::indexAction()
     */
    public function testFailureIndex()
    {
        /**
         * @var DeployHandler|\PHPUnit_Framework_MockObject_MockObject $deployer
         * @var DeployController|\PHPUnit_Framework_MockObject_MockObject $controller
         */
        list($deployer, $controller) = $this->getController();

        $deployer->method('deploy')
            ->willReturn(new DeployResult(false, 'Cannot deploy repository...'));

        $response = $controller->indexAction(new Request(), '123', 'test_repository_name');

        $this->assertSame('{"data":{"type":"output","id":1,"attributes":{"output":"Cannot deploy repository...","success":false}}}', $response->getContent());
        $this->assertSame(500, $response->getStatusCode());
    }

    protected function getController()
    {
        $deployer = $this->createMock(DeployHandler::class);

        $builder = $this->getMockBuilder(DeployController::class);
        $builder->setMethods(['getDeployer']);
        $controller = $builder->getMock();
        $controller->setContainer(static::$kernel->getContainer());
        $controller->method('getDeployer')
            ->willReturn($deployer);

        return [
            $deployer,
            $controller,
        ];
    }
}
