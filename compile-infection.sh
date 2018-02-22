#!/usr/bin/env bash

bin/php-scoper add-prefix vendor/infection/infection -obuild/infection -f
composer dump-autoload -dbuild/infection

php build/infection/vendor/infection/infection/bin/infection

# config file for INFECTION_COMPOSER_INSTALL
