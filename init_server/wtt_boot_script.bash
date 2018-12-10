#!/bin/bash

COMPANY=wintechthai
IP=wintechthai.com

USER=seubpong

export WIS_KEY_FILE=/etc/httpd/ssl/${COMPANY}.key
export WIS_CERT_FILE=/etc/httpd/ssl/${COMPANY}.crt
export WIS_IP_ADDR=${IP}
export WIS_USER=${USER}

useradd ${USER}
passwd ${USER}

rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm

#yum install php72w-fpm --- NO NEED 
yum -y install php72w-common
yum -y install php72w-cli
yum -y install php72w-pdo
yum -y install php72w-mbstring
yum -y install php72w-gd
yum -y install php72w-pear #need for pecl - php extension community lib
yum -y install php72w-devel #php header, need to compile extension
yum -y install wget
yum -y group install "Development Tools"

cd ../..
git clone https://github.com/edenhill/librdkafka.git
cd librdkafka
./configure; make; make install

cd ..
git clone https://github.com/arnaud-lb/php-rdkafka.git 
cd php-rdkafka
pecl install rdkafka

cd ..

cd scm/init_server

php wtt_server_init.php

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
