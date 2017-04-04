<?php declare(strict_types=1);

namespace AppBundle\Entity;

/**
 * DeployResult
 * ============
 *
 * Represents a result of a deployer execution
 * Provides multiple attributes instead of only a output or boolean
 */
class DeployResult
{
    /**
     * @var bool $success
     */
    protected $success;

    /**
     * @var string $output
     */
    protected $output;

    public function __construct(bool $success, string $output)
    {
        $this->success = $success;
        $this->output = $output;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }
}
