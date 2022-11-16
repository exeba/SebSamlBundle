#!/bin/sh

XDEBUG_MODE=coverage vendor/bin/phpunit  --coverage-html test-coverage/html --coverage-clover test-coverage/clover.xml