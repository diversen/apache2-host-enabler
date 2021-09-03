# apache2-host-enabler

Enable and disable virtual hosts on Linux (Only tested on Ubuntu)

It only works for apache2 version >= 2.4.x

It creates the apache2 configuration file, and add the host to /etc/hosts, 
and then enable the configuration.

It also creates `logs/` and `htdocs/` dir.

Only tested on Ubuntu, and it may not work for your setup. 

## Download phar

You can also just clone this repo and build it - or copy it from the repo. 

    wget https://github.com/diversen/apache2-host-enabler/raw/master/a2host.phar

    sudo cp a2host.phar /usr/local/bin/a2host.phar

    sudo chmod +x /usr/local/bin/a2host.phar

## Or Build phar

You will need to have this great tool installed: https://github.com/clue/phar-composer

    git clone https://github.com/diversen/apache2-host-enabler 

    cd apache2-host-enabler 

    composer install

    cd ..

    phar-composer build apache2-host-enabler a2host.phar

`a2host.phar` then created.

## Usage

Enable:

    mkdir test.somesite.com

    cd test.somesite.com

    sudo a2host.phar --enable --htdocs=www test.somesite.com

If the flag htdocs is not set then the default htdocs name is `htdocs`

After the host has been enabled you can visit: http://test.somesite.com

Disable:

    cd somesite.com

    sudo a2host.phar --disable somesite.com

This does not delete any files. It just disables the host. 

## https

Using certbot you can add ssl certificate and enable `https`:

    sudo certbot --apache

Install certbot: 

See: https://certbot.eff.org/all-instructions


License: MIT
