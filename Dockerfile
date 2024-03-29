FROM php:7.4
MAINTAINER Jakub Matejka <jakub@keboola.com>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update && apt-get install unzip git libxml2-dev -y
RUN cd && curl -sS https://getcomposer.org/installer | php && ln -s /root/composer.phar /usr/local/bin/composer

ADD . /code

RUN cd /code && composer install --prefer-dist --no-interaction

WORKDIR /code

CMD php ./src/run.php --data=/data
