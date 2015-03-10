#!/bin/sh
./vendor/bin/phpunit
./vendor/bin/phpcs --standard=PSR1 src/ test/
./vendor/bin/phpcs --standard=PSR2 src/ test/
