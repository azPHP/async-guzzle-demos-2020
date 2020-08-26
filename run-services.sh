#!/bin/bash

php -S localhost:9001 ./services/orgs.php &
php -S localhost:9002 ./services/people.php &
php -S localhost:9003 ./services/sections.php &
