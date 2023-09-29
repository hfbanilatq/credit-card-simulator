# Utiliza la imagen jenkins/jnlp-agent-docker como base
FROM jenkins/jnlp-agent-docker:latest

# Cambiar al usuario root para instalar paquetes
USER root


# Actualiza el índice de paquetes de Alpine y luego instala todas las herramientas
RUN apk --no-cache update && \
    apk --no-cache upgrade && \
    apk --no-cache add \
        sudo \
        curl \
        bash \
        jq \
        python3 \
        py3-pip \
        aws-cli 
RUN apk add --no-cache php82 \
    php82-common \
    php82-fpm \
    php82-pdo \
    php82-opcache \
    php82-zip \
    php82-phar \
    php82-iconv \
    php82-cli \
    php82-curl \
    php82-openssl \
    php82-mbstring \
    php82-tokenizer \
    php82-fileinfo \
    php82-json \
    php82-xml \
    php82-xmlwriter \
    php82-simplexml \
    php82-dom \
    php82-pdo_mysql \
    php82-pdo_sqlite \
    php82-tokenizer \
    php82-pecl-redis
RUN apk add --no-cache php82-gd nodejs npm
RUN ln -s /usr/bin/php82 /usr/bin/php

RUN  curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

# Copiar el script de entrada y establecer los permisos
COPY entrypoint.sh /entrypoint.sh
RUN chown jenkins:jenkins /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Cambiar de nuevo al usuario jenkins
USER jenkins

# Establecer el punto de entrada
ENTRYPOINT ["/entrypoint.sh"]
