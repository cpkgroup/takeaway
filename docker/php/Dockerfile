FROM php:7.3-cli

RUN apt-get update && apt-get install -y git unzip curl librabbitmq-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Install pdo
RUN docker-php-ext-install pdo pdo_mysql

# Install amqp
RUN pecl install amqp && docker-php-ext-enable amqp

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Amsterdam /etc/localtime
RUN "date"

# Set up php
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini \
 && echo "display_startup_errors = On" >> /usr/local/etc/php.ini \
 && echo "display_errors = On" >> /usr/local/etc/php/php.ini \
 && echo "date.timezone=\"Europe/Amsterdam\"" >> /usr/local/etc/php/php.ini

# Install xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug \
 && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.idekey=\"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
 && echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ADD entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php","bin/console", "server:run", "0.0.0.0:80"]

VOLUME /www
WORKDIR /www
EXPOSE 80
