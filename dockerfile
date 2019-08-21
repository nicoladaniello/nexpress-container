FROM wordpress:php7.1-apache

RUN sed -i 's/80/8000/' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

RUN apt-get update
RUN apt-get install -y git unzip

EXPOSE 8000