FROM ubuntu:22.04

LABEL maintainer="David"

# Build args
ARG WWWUSER=1000
ARG WWWGROUP=1001
ARG NODE_VERSION=18

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
WORKDIR /var/www/html

# Set timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Install dependencies, PHP 8.2, Node, Yarn, Composer
RUN apt-get update && \
    apt-get install -y software-properties-common gnupg curl zip unzip git supervisor sqlite3 libpng-dev nodejs yarn cron && \
    add-apt-repository ppa:ondrej/php -y && \
    apt-get update && \
    apt-get install -y \
        php8.2-cli php8.2-mbstring php8.2-mysql php8.2-xml php8.2-curl php8.2-zip \
        php8.2-bcmath php8.2-intl php8.2-gd php8.2-soap && \
    php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer && \
    apt-get -y autoremove && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set capabilities for PHP to allow low port binding
RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.2

# Create sail user
RUN groupadd -g ${WWWGROUP} sail && \
    useradd -ms /bin/bash -u ${WWWUSER} -g ${WWWGROUP} sail

# Prepare storage & cache folders
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R sail:sail /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy start script, supervisor config, PHP ini
COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.2/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8000

ENTRYPOINT ["start-container"]
