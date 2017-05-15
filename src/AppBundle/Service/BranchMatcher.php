<?php declare(strict_types=1);

namespace AppBundle\Service;

class BranchMatcher
{
    public function isBranchMatching(string $inputPayload, array $repository)
    {
        $branchName = $repository['git_branch'];

        foreach ($this->getPatterns() as $pattern) {
            if (preg_match($pattern, $inputPayload, $match)) {

                // exact match
                if ($repository['git_branch_math'] === 'exact' && $match[1] === $branchName) {
                    return true;

                // match by regexp specified as a branch name eg. /feature\/([A-Z0-9]+)/
                } elseif ($repository['git_branch_math'] === 'regexp' && preg_match($branchName, $match[1])) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function getPatterns()
    {
        return [
            '/\"ref\"\:\ ?\"refs\/heads\/([A-Za-z0-9\-\_\/\+\.\,\*\:\;\?]+)\"/i',
            '/"branch": "([A-Za-z0-9\-\_\/\+\.\,\*\:\;\?]+)"/i',
        ];
    }
}
