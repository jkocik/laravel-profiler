#!/bin/sh

redis-server --daemonize yes

setfacl -dR -m u:www-data:rwX /var/www/html
setfacl -R -m u:www-data:rwX /var/www/html

docker-php-entrypoint $@
