# apache2-host-enabler

Enable and disable hosts on Linux (Only tested on Ubuntu)

It only works for apache2 version >= 2.4.x

It creates the configuration file, and add the host to /etc/hosts, 
and then enable the configuration.

It also creates logs/ and htdocs/ dir.

Only tested on Ubuntu, and it may not work for your setup. 

## Install

It requires the ssl-cert package. Because it uses the /etc/ssl/certs/ssl-cert-snakeoil.pem

    sudo apt install ssl-cert

    cd ~

    git clone https://github.com/diversen/apache2-host-enabler

    cd apache2-host-enabler/

    composer install

Using certbot you can add ssl certificate and enable `https`:

    sudo certbot --apache


## Usage

Enable:

    cd somesite.com

    ~/apache2-host-enabler/a2host --enable somesite.com

Disable:

    cd somesite.com

    ~/apache2-host-enabler/a2host --disable somesite.com

License:

MIT

