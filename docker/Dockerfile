FROM php:7.4-cli

ENV GOSU_VERSION=1.14

WORKDIR /app

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

RUN docker-php-ext-install \
        pdo_mysql \

    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \

    && curl -L https://github.com/tianon/gosu/releases/download/${GOSU_VERSION}/gosu-$(dpkg --print-architecture) --output /usr/local/bin/gosu && \
        chmod +x /usr/local/bin/gosu && \
        addgroup foo && \
        adduser --home=/home/foo --shell=/bin/bash --ingroup=foo --disabled-password --quiet foo

COPY entrypoint.sh /usr/local/bin/

ENTRYPOINT ["entrypoint.sh"]
