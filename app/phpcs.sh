#!/bin/bash
cd `dirname $0`
vendor/bin/phpcs --config-set installed_paths vendor/escapestudios/symfony2-coding-standard
vendor/bin/phpcs --standard=Symfony2 src/AppBundle