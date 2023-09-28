FROM jenkins/inbound-agent:jdk11

USER root

# install packages
RUN apt-get -y update \
    && apt-get -y upgrade \
    && apt-get -y install sudo curl bash jq python3 python3-pip awscli

#Instalar php y composer

#Instalar php y composer
RUN apt install php-cli -y
RUN apt-get install php-dom -y
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y nodejs

USER jenkins
