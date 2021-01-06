FROM amashukov/php:7.4-xdebug
# Development image. Use `amashukov/php:7.4-base` for production (without xdebug).

# Define workdir
WORKDIR /srv/www

# Copy project files into Docker container
COPY . /srv/www

# Install Supervisor
RUN apk update && apk add --no-cache supervisor apache-ant

# Copy Supervisor configuration
COPY .docker/services/* /etc/supervisor/conf.d/
COPY .docker/etc/supervisord/supervisord.conf /etc/supervisor/supervisord.conf

# Configure PHP 7.4
RUN rm /usr/local/etc/php/conf.d/99-svt.ini
COPY .docker/etc/php/php-cli.ini /usr/local/etc/php/conf.d/60-crassula.ini

# Prepare Symfony
RUN cd /srv/www \
    && composer install -q \
    && composer dump-autoload --optimize \
    && bin/console cache:clear \
    && bin/console cache:warmup

# Download RoadRunner, faster than php-fpm + nginx.
# See: https://habr.com/ru/post/431818
ENV RR_VERSION 1.4.7
RUN mkdir /tmp/rr \
  && cd /tmp/rr \
  && echo "{\"require\":{\"spiral/roadrunner\":\"${RR_VERSION}\"}}" >> composer.json \
  && composer install \
  && vendor/bin/rr get-binary -l /usr/local/bin \
  && rm -rf /tmp/rr

# Copy RoadRunner config
COPY .docker/etc/roadrunner /etc/roadrunner

# Allow anyone to get access from outside to this port
EXPOSE 80

# Run Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
