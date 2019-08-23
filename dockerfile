FROM wordpress:php7.1-apache

# change default port to 8000
RUN sed -i 's/80/8000/' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

# install git
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

# wp-cli
RUN curl -sL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o wp; \
    chmod +x wp; \
    mv wp /usr/local/bin/; \
    mkdir /var/www/.wp-cli; \
    chown www-data:www-data /var/www/.wp-cli

EXPOSE 8000