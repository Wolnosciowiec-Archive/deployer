<?php declare(strict_types=1);

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @see BranchMatcher
 */
class BranchMatcherTest extends WebTestCase
{
    public function provideData(): array
    {
        return [
            'valid / exact match' => [
                '{"ref": "refs/heads/master"}',
                [
                    'git_branch_math' => 'exact',
                    'git_branch' => 'master',
                ],
                true,
            ],

            'valid / regexp match' => [
                '{"ref": "refs/heads/feature/WOLNOSCIOWIEC-1"}',
                [
                    'git_branch_math' => 'regexp',
                    'git_branch' => '/feature\/WOLNOSCIOWIEC\-([0-9]+)/',
                ],
                true,
            ],

            'not valid / exact match' => [
                '{"ref": "refs/heads/other-branch-name"}',
                [
                    'git_branch_math' => 'exact',
                    'git_branch' => 'master',
                ],
                false,
            ],

            'not valid / regexp match' => [
                '{"ref": "refs/heads/feature/WOLNOSCIOWIEC-XYZ"}',
                [
                    'git_branch_math' => 'regexp',
                    'git_branch' => '/feature\/WOLNOSCIOWIEC\-([0-9]+)/',
                ],
                false,
            ],

            'not valid / invalid payload' => [
                ' ',
                [
                    'git_branch_math' => 'regexp',
                    'git_branch' => '/feature\/WOLNOSCIOWIEC\-([0-9]+)/',
                ],
                false,
            ],
        ];
    }

    /**
     * @param string $payload
     * @param array $repositoryData
     * @param bool $isValid
     *
     * @dataProvider provideData
     */
    public function testIsBranchMatching(string $payload, array $repositoryData, bool $isValid)
    {
        $this->assertSame($isValid, (new BranchMatcher())->isBranchMatching($payload, $repositoryData));
    }
}
