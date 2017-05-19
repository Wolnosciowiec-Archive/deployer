Wolnościowiec Deployer
======================

[![Build Status](https://travis-ci.org/Wolnosciowiec/heroku-deploy.svg?branch=master)](https://travis-ci.org/Wolnosciowiec/heroku-deploy)
[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/Wolnosciowiec/heroku-deploy)

Handles incoming web-hooks from eg. github and starts a deployment.
Works perfectly with Heroku and Thin Deploy.

Features:
- Multiple handlers: deployment on Heroku hosting or a call to the "[Thin Deployer](https://github.com/Wolnosciowiec/thin-deployer)" service
- Multiple applications
- Different Heroku accounts for various applications
- Same repository can be deployed into multiple accounts and applications on Heroku
- Supports multiple branches
- Runs on Heroku
- Allows to append custom configuration and/or patches to the deployed repositories eg. configuration files or other overrides

Free software
-------------

Created for an anarchist portal, with aim to propagate the freedom and grass-roots social movements where the human and it's needs is on first place, not the capital and profit.

- https://wolnosciowiec.net
- http://iwa-ait.org
- http://zsp.net.pl

Requirements
------------

- PHP7
- Web server
- Access to shell for the PHP
- Installed git

Quick start
-----------

1. Clone the repository and do the `composer install`
2. Look at the `app/config/parameters.yml` to eventually correct the API key or other settings
3. Create a `app/config/deploy.yml` basing on the `app/config/deploy.yml.dist`.
4. Optionally put your files that you want to deploy additionally with the application (eg. configuration files) into the `var/repositories_override/{repository name}`
5. Set up a webserver to point to /web directory and rewrite everything through `app.php` (for testin use: `php bin/console server:start`)

Adding configuration files, overriding files
--------------------------------------------

By putting custom files into a override directory there is a possibility to deploy additional files
to the destination server.

To achieve that you need to create a `var/repositories_override/{repository name}` directory and put your files in it.
Everything will be copied to the repository, commited and pushed to the destination server.

Example request
---------------

**POST** http://localhost:8000/test/deploy/my_web_proxy

```
{
    "ref": "refs/heads/master"
}
```

Explanation:
- /test/ - API key should be inserted instead o "test"
- /my_web_proxy - this is the name of a service, see [deploy.yml](./app/config/deploy.yml.dist)
- `"ref": "refs/heads/master"` - that's a part of github's payload, it's a reference name which includes a branch name
  you can submit also `"branch": "master"` if you like to
