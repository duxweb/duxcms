FROM joseluisq/php-fpm:8.3

ARG timezone

ENV TIMEZONE=${timezone:-"Asia/Shanghai"}


# 创建www用户
RUN addgroup -g 1000 -S www && adduser -s /sbin/nologin -S -D -u 1000 -G www www
# 配置阿里云镜像源，加快构建速度
RUN sed -i "s/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g" /etc/apk/repositories

#  PHPIZE_DEPS 包含 gcc g++ 等编译辅助类库
RUN apk add --no-cache $PHPIZE_DEPS \
    && apk add --no-cache libstdc++ libzip-dev vim\
    && apk update \
    && pecl install redis \
    && pecl install zip \
    && pecl install imagick \
    && docker-php-ext-enable redis zip imagick\
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    && docker-php-ext-install -j$(nproc) gd && \
    && docker-php-ext-install -j$(nproc) opcache \
    && docker-php-ext-install -j$(nproc) bcmath \
    && apk del $PHPIZE_DEPS

# nodejs
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apk update \
    && apt add -y --no-install-recommends nodejs \
    && apt-get clean \
    && npm install -g yarn

# composer
RUN php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');" && \
    # Redis Mongo
    pecl install redis imagick && \
    rm -rf /tmp/pear && \
    docker-php-ext-enable redis imagick && \
    # GD Library
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    docker-php-ext-install -j$(nproc) gd && \
    # Timezone
    cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
    echo "${TIMEZONE}" > /etc/timezone && \
    echo "[Date]\ndate.timezone=${TIMEZONE}" > /usr/local/etc/php/conf.d/timezone.ini && \
    # Clean
    apt-get clean && rm -rf /var/cache/apt/*

ADD . /var/www/html
ADD ./nginx.conf /etc/nginx/sites-enabled/default

COPY ./supervisord.conf /etc/supervisor/

RUN composer install

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data
#RUN chown -R 1000:1000 /var/www/html
RUN chmod -R 777 /var/www/html

WORKDIR /var/www/html

EXPOSE 80
EXPOSE 443

VOLUME ["/var/www/html/data/log", "/var/www/html/data/cache", "/var/www/html/data/storage", "/var/www/html/upload"]


CMD ["/usr/bin/supervisord"]