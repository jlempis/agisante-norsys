# See https://github.com/docker-library/php/blob/4677ca134fe48d20c820a19becb99198824d78e3/7.0/fpm/Dockerfile
FROM php:7.0-fpm

MAINTAINER jean-Loup EMPIS <jean-loup.empis@gmail.com>

RUN apt-get update && apt-get install -qq -y \
    git \
    unzip \
    software-properties-common \
    wget

#Symfony demande php-xml qui se trouve dans ce dépot
RUN echo "deb http://packages.dotdeb.org jessie all" > /etc/apt/sources.list.d/dotdeb.list
RUN wget -O- https://www.dotdeb.org/dotdeb.gpg | apt-key add -

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime
RUN "date"

# Type docker-php-ext-install to see available extensions
RUN docker-php-ext-install pdo pdo_mysql
RUN \
    apt-get update && \
    apt-get install libldap2-dev -y && \
    apt-get install php7.0-zip -y && \
    apt-get install php7.0-gd -y && \
    apt-get install php-xml -y && \
    rm -rf /var/lib/apt/lists/*

#Permissions Symfony
#HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
#setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
#setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var

RUN echo 'alias sf="php app/console"' >> ~/.bashrc

WORKDIR /var/www/agisante
