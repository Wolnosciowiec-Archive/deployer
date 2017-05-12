Heroku Deploy
=============

[![Build Status](https://travis-ci.org/Wolnosciowiec/heroku-deploy.svg?branch=master)](https://travis-ci.org/Wolnosciowiec/heroku-deploy)
[![Deploy](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy?template=https://github.com/Wolnosciowiec/heroku-deploy)

Handles incoming web-hooks from eg. github and starts a deployment.

Features:
- Multiple applications
- Different Heroku accounts for various applications
- Same repository can be deployed into multiple accounts and applications on Heroku
- Supports multiple branches
- Runs on Heroku

Free Software
-------------

Created for an anarchist portal, with aim to propagate the freedom and grass-roots social movements where the human and it's needs is on first place, not the capital and profit.

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
4. Set up a webserver to point to /web directory and rewrite everything through `app.php` (for testin use: `php bin/console server:start`)

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
