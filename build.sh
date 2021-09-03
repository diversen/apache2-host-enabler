#!/bin/sh
# Build a new version of the phar
composer install
cd ..
phar-composer build apache2-host-enabler a2host.phar
cp a2host.phar apache2-host-enabler
cd apache2-host-enabler