<?php declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Exception\Deployer\NotEnoughPermissionsException;
use GitWrapper\GitWorkingCopy;

/**
 * HerokuClient
 * ============
 *
 * Prepares the upstream to do a git push into
 */
class HerokuClient
{
    /**
     * Set up the git upstream for the repository
     * to point to the heroku for a git push
     *
     * @param GitWorkingCopy $git
     * @param string $herokuLogin
     * @param string $herokuToken
     * @param string $herokuAppName
     *
     * @throws NotEnoughPermissionsException
     * @return GitWorkingCopy
     */
    public function setUpUpstream(
        GitWorkingCopy $git,
        string $herokuLogin,
        string $herokuToken,
        string $herokuAppName
    ): GitWorkingCopy {
        $this->saveAuthentication($herokuLogin, $herokuToken);

        system($git->getWrapper()->getGitBinary() . ' config credential.helper "netrc -d -v"');
        $git->removeRemote('origin');
        $git->addRemote('origin', 'https://git.heroku.com/' . $herokuAppName . '.git');

        return $git;
    }

    /**
     * Put authorization data to the .netrc
     * so the cURL used by GIT will use those authentication data
     *
     * @param string $login
     * @param string $token
     * @throws NotEnoughPermissionsException
     */
    protected function saveAuthentication(string $login, string $token)
    {
        $netrcContent = $this->readNetRc();
        $netrcContent = $this->removeAuthenticationLinePosition($netrcContent);

        $netrcContent[] = "machine git.heroku.com\n";
        $netrcContent[] = "  password " . $token . "\n";
        $netrcContent[] = "  login " . $login . "\n";

        $this->writeNetRc($netrcContent);
    }

    protected function readNetRc(): array
    {
        $homeDir = posix_getpwuid(posix_getuid())['dir'];

        if (!is_writable($homeDir . '/.netrc')) {
            throw new NotEnoughPermissionsException('Not enough permissions to write to "' . $homeDir . '/.netrc"');
        }

        return file($homeDir . '/.netrc') ?? [];
    }

    protected function writeNetRc(array $netrcContent)
    {
        $homeDir = posix_getpwuid(posix_getuid())['dir'];

        $netrc = fopen($homeDir . '/.netrc', 'w');
        fwrite($netrc, implode('', $netrcContent));
        fclose($netrc);
    }

    /**
     * Remove any authentication data related to git.heroku.com
     * from the .netrc
     *
     * @param array $netrcContent
     * @return array
     */
    protected function removeAuthenticationLinePosition(array $netrcContent)
    {
        $found = 0;

        foreach ($netrcContent as $position => $line) {
            if (strpos($line, 'machine git.heroku.com') !== false) {
                $found++;
                unset($netrcContent[$position]);

            } elseif ($found === true
                && (strpos($line, 'password ') !== false || strpos($line, 'login ') !== false)) {
                $found++;
                unset($netrcContent[$position]);
            }

            if ($found === 3) {
                break;
            }
        }

        return $netrcContent;
    }
}
