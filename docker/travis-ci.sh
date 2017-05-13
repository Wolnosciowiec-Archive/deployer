#!/bin/bash

cd "$( dirname "${BASH_SOURCE[0]}" )"
export DOCKER_COMPOSE_VERSION=1.8.0

sudo apt-get update
sudo apt-get install -o Dpkg::Options::="--force-confold" --force-yes -y docker-engine

# debug
docker-compose --version

# upgrade docker-compose
sudo rm /usr/local/bin/docker-compose
curl -L https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-`uname -s`-`uname -m` > docker-compose
chmod +x docker-compose
sudo mv docker-compose /usr/local/bin
docker-compose --version

# run
exec docker-compose -f docker-compose.x86.yml up  --build --force-recreate -d
