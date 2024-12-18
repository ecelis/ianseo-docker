FROM php:8.2.23-apache
ENV APACHE_DOCUMENT_ROOT /opt/ianseo
# Required dependencies
RUN apt-get update && apt-get install -y \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  default-libmysqlclient-dev \
  libcurl4-openssl-dev \
  libmcrypt-dev \
  libzip-dev \
  libmagick++-dev \
  unzip \
  mariadb-client \
  zlib1g zlib1g-dev \
  libpng16-16 libpng-dev \
  libonig5 libonig-dev \
  && pecl update-channels \
  && docker-php-ext-configure gd \
  && docker-php-ext-install gd \
  && docker-php-ext-install mysqli \
  && docker-php-ext-install curl \
  && docker-php-ext-install mbstring \
  && docker-php-ext-install intl \
  && docker-php-ext-install zip \
  && /usr/bin/yes '' | /usr/local/bin/pecl install mcrypt-1.0.6 \
  && /usr/bin/yes '' | /usr/local/bin/pecl install imagick \
  && apt-get remove \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  default-libmysqlclient-dev \
  libcurl4-openssl-dev \
  libmcrypt-dev \
  libzip-dev \
  libmagick++-dev \
  zlib1g-dev \
  libpng-dev \
  libonig-dev \
  apt-get clean all \
  && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
  rm -rf /var/lib/apt/lists/* /var/cache/apt/archives ; \
  rm -rf /usr/local/src ; \
  rm -rf /tmp/pear ~/.pearrc
# ianseo setup
COPY src/ /opt/ianseo
RUN chmod -R a+wX /opt/ianseo \
  && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
  /etc/apache2/sites-available/*.conf && \
  sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' \
  /etc/apache2/apache2.conf /etc/apache2/conf-enabled/*.conf
COPY apache/ /etc/apache2/conf-enabled/
COPY php/php.ini /usr/local/etc/php
COPY php/docker-php-ext-ianseo.ini /usr/local/etc/php/conf.d
# COPY php/ianseo.config.inc.php /opt/ianseo/Common/config.inc.php
