<?php declare(strict_types=1);

namespace AppBundle\Exception;

/**
 * Raised when some data is trying to be tricky replaced
 * when read-only
 */
class UnexpectedStateException extends \Exception
{

}
