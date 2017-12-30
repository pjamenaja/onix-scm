#!/bin/bash

COMPANY=wintechthai
IP=35.227.147.247

USER=seubpong
SSH_DIR=/home/${USER}/.ssh
KEY_FILE=/home/${USER}/.ssh/authorized_keys
PUB_KEY='ssh-rsa AAAAB3NzaC1yc2EAAAABJQAAAQEAqO1jMSJtkzsSrah9QDJD+QHqwXLNCf8C0+JOJeRb5L0dQnNjHhiuGQOOT6h6htaTg8T94I5e6Zf28O+R9/jl0pAeHjxN1ghwzqz5Qjr3fXjAsrbrUs4wEFx4FBMT9v1K/GG5TZxl0M+5UIuXM4j+Yjj0uWQUD3AVUwZbUPWuK2sUfRpE8ycFheIm77h5oCFkyAiHIhpk0wRSqfcX2tPqH23LdQVB06M35zPja8ydhFpzV21rb1h8C8GD33E0Nf0BAt6rdrU+A8PDjikMRRix8W5xhAGKwtfKrVjsovIRN8EdAw4eEUaSCWtSoCSGt1oxAFArEl4MHtaBJWk3dBl6vw== rsa-key-20171230'

export WIS_KEY_FILE=/etc/httpd/ssl/${COMPANY}.key
export WIS_CERT_FILE=/etc/httpd/ssl/${COMPANY}.crt
export WIS_IP_ADDR=${IP}

useradd ${USER}
passwd ${USER}

TEMP_PUB_FILE=./pubkey.pub

sudo -u ${USER} mkdir ${SSH_DIR}
sudo -u ${USER} chmod 700 ${SSH_DIR}
echo ${PUB_KEY} > ${TEMP_PUB_FILE}
cp ${TEMP_PUB_FILE} ${KEY_FILE}
chown ${USER}:${USER} ${KEY_FILE}
chmod 600 ${KEY_FILE}
rm ${TEMP_PUB_FILE}

yum install php71w-fpm php71w-opcache
yum install php71w-common
yum install php71w-cli

php wis_server_init.php
