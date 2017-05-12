<?php declare(strict_types=1);

namespace AppBundle\Controller;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * ExceptionController
 * ===================
 *
 * Shows a JSON information on 404 Not Found error and on invalid token
 */
class ExceptionController extends \Symfony\Bundle\TwigBundle\Controller\ExceptionController
{
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        if (strpos($exception->getMessage(), 'No route found for') !== false) {
            return new JsonResponse(['message' => 'Request token is invalid, or 404 Not Found error occurred.'], 400);
        }

        return parent::showAction($request, $exception, $logger);
    }
}
