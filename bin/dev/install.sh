#!/usr/bin/env bash
set -e

bold=$(tput bold)
normal=$(tput sgr0)
PHP_IN_CONTAINER="docker-compose exec php-fpm php"

# Start up environment and run symfony console within container to ensure DB has the schema loaded up
docker-compose up -d

# Install composer deps
composer -o install

# Load up DB schema
${PHP_IN_CONTAINER} bin/console doctrine:schema:update --force

# Make directory for user data
mkdir var/data

# Ensure both container and host can write into the var/ folder
${FIX_OWNERSHIP}
sudo chmod -Rf 777 var/*

# Done!
echo -e "\n${bold} ## Application is now set up ${normal} for localdev, and the environment is up; head off to ${bold}>>> http://localhost:8000 <<< ## \n${normal}"
