# Jenkins Manager
FROM jenkins/jenkins:lts

USER root

# install packages
RUN apt-get -y update \
    && apt-get -y upgrade \
    && apt-get -y install sudo curl bash jq python3 python3-pip awscli


# list installed software versions
RUN set +x && echo ''; echo '*** INSTALLED SOFTWARE VERSIONS ***';echo ''; \
    cat /etc/*release; python3 --version; \
    pip3 --version; aws --version;

#Instalar php y composer
RUN apt install php-cli -y

RUN apt-get install php-dom -y
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer



# change directory owner for jenkins home
RUN chown -R jenkins:jenkins /var/jenkins_home

# drop back to the regular jenkins user - good practice
USER jenkins