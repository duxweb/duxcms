FROM joseluisq/php-fpm:8.2

ENV SITE_PATH /var/www/html

RUN apk add tzdata && cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
        && echo "Asia/Shanghai" > /etc/timezone \
        && apk del tzdata

RUN apk add \
        caddy \
        supervisor \
        nodejs \
        yarn \
    && rm -rf /var/cache/apk/*

RUN rm -f /usr/local/etc/php/conf.d/docker-php-ext-swoole.ini \
      /usr/local/etc/php/conf.d/docker-php-ext-phalcon.ini \
      /usr/local/etc/php/conf.d/docker-php-ext-psr.ini \
      /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ENV ENV_SUBSTITUTION_ENABLE true
ENV PHP_MEMORY_LIMIT 512M
ENV PHP_FPM_LISTEN 9000

WORKDIR ${SITE_PATH}
COPY --chown=www-data:www-data . ${SITE_PATH}
COPY ./docker/Caddyfile /etc/caddy/Caddyfile
COPY ./docker/supervisord.conf /etc/supervisord.conf

RUN mkdir -p ./tmp/config && chmod +x ${SITE_PATH}/docker/run.sh
COPY ./config ./tmp/config

VOLUME ["${SITE_PATH}/data", "${SITE_PATH}/config"]
EXPOSE 80

ENTRYPOINT ["/var/www/html/docker/run.sh"]

