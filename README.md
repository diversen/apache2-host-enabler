# apache2-host-enabler

Enable and disable hosts on Linux (Only tested on Ubuntu)

It only works for apache2 version >= 2.4.x

It creates the configuration (with https enabled), and add the host to /etc/hosts

It also creates logs/ and htdocs/ dir.

Only tested on Ubuntu.

This may not work for your setup. 

## Install

    cd ~

    git clone https://github.com/diversen/apache2-host-enabler

    cd apache2-host-enabler/

    composer install

## Usage

Enable:

    ~/apache2-host-enabler/a2host --enable somesite.com

Disable:

    ~/apache2-host-enabler/a2host --disable somesite.com

License:

MIT

