FROM php:8.0.17-fpm-alpine3.15

MAINTAINER Baleghsefat <m.baleghsefat@gmail.com>

ENV GOSU_VERSION=1.11 \
    USER=app \
    HOME=/home/app \
    ACCEPT_EULA=Y

# Install prerequisites required for tools and extensions installed later on.
RUN apk add --update bash gnupg libpng-dev libzip-dev su-exec unzip mysql mysql-client \
    && apk add --no-cache \
       zsh \
       git

RUN set -x \
    && apk add --no-cache --virtual .gosu-deps \
        dpkg \
        gnupg \
        openssl \
    && dpkgArch="$(dpkg --print-architecture | awk -F- '{ print $NF }')" \
    && wget -O /usr/local/bin/gosu "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch" \
    && wget -O /usr/local/bin/gosu.asc "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch.asc" \
    && export GNUPGHOME="$(mktemp -d)" \
    && rm -r "$GNUPGHOME" /usr/local/bin/gosu.asc \
    && chmod +x /usr/local/bin/gosu \
    && gosu nobody true \
    && apk del .gosu-deps

# Retrieve the script used to install PHP extensions from the source container.
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/install-php-extensions


# Install required PHP extensions and all their prerequisites available via apt.
RUN chmod uga+x /usr/bin/install-php-extensions \
    && sync \
    && install-php-extensions bcmath ds gd intl opcache pcntl zip

RUN curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer


RUN apk add --no-cache shadow \
    && addgroup -S app \
    && adduser -S -G app -s /bin/zsh -h $HOME $USER \
    && usermod -aG $USER www-data \
    && chown app:app -R $HOME/.composer \
    && gosu $USER sh -c "SHELL=/bin/zsh && $(curl -fsSL https://raw.github.com/robbyrussell/oh-my-zsh/master/tools/install.sh)" \
    && gosu $USER sed -i 's/ZSH_THEME=".*"/ZSH_THEME="candy"/g' $HOME/.zshrc
