FROM joseluisq/php-fpm:8.2

ENV SITE_PATH /var/www/html

# 配置阿里云镜像源，加快构建速度
RUN sed -i "s/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g" /etc/apk/repositories

ENV ENV_SUBSTITUTION_ENABLE true
ENV PHP_MEMORY_LIMIT 512M
ENV PHP_FPM_LISTEN 9000

WORKDIR ${SITE_PATH}
COPY . ${SITE_PATH}
COPY ./docker/Caddyfile /etc/caddy/Caddyfile
COPY ./docker/supervisord.conf /etc/supervisord.conf

RUN mkdir -p ./tmp/config
COPY ./config ./tmp/config

# 设置时区
RUN sed -i "s/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g" /etc/apk/repositories  \
    && apk add tzdata && cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
    && echo "Asia/Shanghai" > /etc/timezone \
    && apk del tzdata \
    && apk add \
        caddy \
        supervisor \
        curl \
        nodejs \
        bash \
    && rm -rf /var/cache/apk/* \
    && rm /usr/local/etc/php/conf.d/docker-php-ext-swoole.ini \
    && rm /usr/local/etc/php/conf.d/docker-php-ext-phalcon.ini \
    && rm /usr/local/etc/php/conf.d/docker-php-ext-psr.ini \
    && rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN chown -R www-data:www-data ${SITE_PATH} \
    && chmod -R 755 ${SITE_PATH} \
    && chmod +x ${SITE_PATH}/docker/run.sh

VOLUME ["${SITE_PATH}/data", "${SITE_PATH}/config"]
EXPOSE 80

CMD ["/bin/bash", "/var/www/html/docker/run.sh"]

