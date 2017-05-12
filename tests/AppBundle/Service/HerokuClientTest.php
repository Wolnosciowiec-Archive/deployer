<?php declare(strict_types=1);

namespace Tests\AppBundle\Service;

use AppBundle\Service\HerokuClient;
use GitWrapper\GitWorkingCopy;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @see HerokuClient
 */
class HerokuClientTest extends WebTestCase
{
    public function provideAuthDetails(): array
    {
        return [
            'first' => [
                'login' => 'test',
                'password' => '123',
                'appName' => 'app_name_test',

                'expected' => [
                    "machine git.heroku.com\n",
                    "  password 123\n",
                    "  login test\n",
                ],
            ],

            'second' => [
                'login' => 'bakunin@wolnosciowiec.net',
                'password' => '0sYodVhPRB5dVVGyEAdTAn1uNV7vrjPCrqmbWr6p319aFfqSp0A6V',
                'appName' => 'app_name_test',

                'expected' => [
                    "machine git.heroku.com\n",
                    "  password 0sYodVhPRB5dVVGyEAdTAn1uNV7vrjPCrqmbWr6p319aFfqSp0A6V\n",
                    "  login bakunin@wolnosciowiec.net\n",
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideAuthDetails
     * @see          HerokuClientTest::saveAuthentication()
     *
     * @param string $login
     * @param string $password
     * @param string $appName
     * @param array  $expected
     */
    public function testSaveAuthentication(string $login, string $password, string $appName, array $expected)
    {
        $client = $this->createHerokuClient();
        $client
            ->expects($this->once())
            ->method('writeNetRc')
                ->with($expected);

        $client->setUpUpstream(
            $this->createMock(GitWorkingCopy::class),
            $login,
            $password,
            $appName
        );
    }

    /**
     * @return HerokuClient|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createHerokuClient(): HerokuClient
    {
        $builder = $this->getMockBuilder(HerokuClient::class);
        $builder->setMethods(['writeNetRc', 'readNetRc', 'setUpCredentialsHelper']);
        $client = $builder->getMock();

        $client->method('readNetRc')
            ->willReturn([
                "machine git.heroku.com\n",
                "  password previous_password\n",
                "  login previous_login\n",
            ]);

        $client->method('setUpCredentialsHelper')
            ->willReturn('');

        return $client;
    }
}
