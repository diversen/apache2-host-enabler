# apache2-host-enabler

Enable and disable virtual hosts on Linux (Only tested on Ubuntu)

It only works for apache2 version >= 2.4.x

It creates the apache2 configuration file, and add the host to /etc/hosts, 
and then enable the configuration.

It also creates `logs/` and `htdocs/` dir.

Only tested on Ubuntu, and it may not work for your setup. 

## Download phar

You can also just clone this repo and build it - or copy it from the repo. 

    wget https://github.com/diversen/apache2-host-enabler/a2host.phar

    cp a2host.phar /usr/local/bin/a2host.phar

    chmod +x /usr/local/bin/a2host.phar

## Or Build phar

You will need to have this great tool installed:

https://github.com/clue/phar-composer

    git clone https://github.com/diversen/apache2-host-enabler 

    phar-composer build apache2-host-enabler a2host.phar

## Usage

Enable:

    mkdir test.somesite.com

    cd test.somesite.com

    a2host.phar --enable --htdocs=www test.somesite.com

If the flag htdocs is not set then the default htdocs name is `htdocs`

If you need SSL (and if you have installed certbot):

    sudo certbot --apache

Disable:

    cd somesite.com

    a2host.phar --disable somesite.com

## https

Using certbot you can add ssl certificate and enable `https`:

    sudo certbot --apache

Install certbot: 

See: https://certbot.eff.org/all-instructions



License: MIT
