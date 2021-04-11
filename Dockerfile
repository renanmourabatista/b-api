ARG  IMAGE_NAME=php
ARG  IMAGE_VERSION=7.4.16-alpine

FROM ${IMAGE_NAME}:${IMAGE_VERSION} AS BUILD_IMAGE

LABEL version="1.0"

RUN apk update \
  && apk upgrade \
  && apk add curl bash python3 py3-pip g++ make autoconf\
  && apk add --update nodejs npm \
  && rm -rf /var/cache/apk/* \
  && npm install apidoc -g \
  && curl -sS http://getcomposer.org/installer | php -- --filename=composer \
  && chmod a+x composer \
  && mv composer /usr/local/bin/composer \
  && docker-php-ext-install mysqli \
  && docker-php-ext-install pdo \
  && docker-php-ext-install pdo_mysql

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions xdebug

RUN chmod -R 777 /var/www

WORKDIR  /var/www

COPY . .

CMD bash -c "composer install && php artisan key:generate && php artisan serve --port=80 --host=0.0.0.0"