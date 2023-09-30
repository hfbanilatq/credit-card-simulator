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
RUN apt-get install php-dom -y
RUN apt-get install php-curl -y
RUN apt-get install php-gd -y
RUN apt-get install php-bcmath -y
RUN apt-get install php-cgi -y
RUN apt-get install php-ldap -y
RUN apt-get install php-mbstring -y
RUN apt-get install php-soap -y
RUN apt-get install php-zip -y
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN apt-get install -y nodejs
RUN apt install npm -y
RUN apt-get install php-xdebug -y
RUN apt-get install php-sqlite3 -y
RUN curl -LO "https://dl.k8s.io/release/$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl" && \
    chmod +x kubectl && \
    mv kubectl /usr/local/bin/
USER jenkins