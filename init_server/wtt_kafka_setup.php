#!/bin/php

<?php

/*
    PHP need to be available at this point
*/

$SUDO='sudo -u kafka';
$KAFKA_USER='kafka';
$KAFKA_HOME='/home/kafka';
$KAFKA_TGZ='kafka_2.11-2.1.0.tgz';
$KAFKA_LINK="https://www-eu.apache.org/dist/kafka/2.1.0/$KAFKA_TGZ";
$KAFKA_LISTENER_MAP='listener.security.protocol.map=PLAINTEXT:PLAINTEXT,SSL:SSL,SASL_PLAINTEXT:SASL_PLAINTEXT,SASL_SSL:SASL_SSL';
$KAFKA_SERVER_CFG="$KAFKA_HOME/kafka/config/server.properties";
$JAVA_COMMAND_LIST = [   
                         'yum -y install java-1.8.0-openjdk',
                         'yum -y install java-1.8.0-openjdk-devel',
                     ];

$GENERIC_COMMAND_LISTS = [
                    "useradd $KAFKA_USER -m",
                    "usermod -aG wheel $KAFKA_USER",

                    "$SUDO mkdir -p $KAFKA_HOME/downloads", 
                    "$SUDO mkdir -p $KAFKA_HOME/kafka", 
                    "wget $KAFKA_LINK",
                    "mv $KAFKA_TGZ $KAFKA_HOME/downloads", 
                    "cd $KAFKA_HOME/kafka ; $SUDO tar -xvzf $KAFKA_HOME/downloads/$KAFKA_TGZ --strip 1",

                    "echo '' >> $KAFKA_SERVER_CFG",
                    "echo '#================== Start added by script' >> $KAFKA_SERVER_CFG",
                    "echo 'advertised.listeners=PLAINTEXT://kafka-server.wintech-thai.com:9092' >> $KAFKA_SERVER_CFG",
                    "echo '$KAFKA_LISTENER_MAP' >> $KAFKA_SERVER_CFG",
                    "echo 'delete.topic.enable=true' >> $KAFKA_SERVER_CFG",
                    "echo '#================== End added by script' >> $KAFKA_SERVER_CFG",
                ];

$ZOO_KEEPER_UNIT = <<<EOT
[Unit]
Requires=network.target remote-fs.target
After=network.target remote-fs.target

[Service]
Type=simple
User=$KAFKA_USER
ExecStart=$KAFKA_HOME/kafka/bin/zookeeper-server-start.sh $KAFKA_HOME/kafka/config/zookeeper.properties
ExecStop=$KAFKA_HOME/kafka/bin/zookeeper-server-stop.sh
Restart=on-abnormal

[Install]
WantedBy=multi-user.target
EOT;

$KAFKA_SERVER_UNIT = <<<EOT
[Unit]
Requires=zookeeper.service
After=zookeeper.service

[Service]
Type=simple
User=$KAFKA_USER
ExecStart=/bin/sh -c "$KAFKA_HOME/kafka/bin/kafka-server-start.sh $KAFKA_SERVER_CFG > $KAFKA_HOME/kafka/kafka.log 2>&1"
ExecStop=$KAFKA_HOME/kafka/bin/kafka-server-stop.sh
Restart=on-abnormal

[Install]
WantedBy=multi-user.target
EOT;
                   
$SERVICE_COMMAND_LISTS = [
                    "echo '$ZOO_KEEPER_UNIT' > /etc/systemd/system/zookeeper.service",
                    "echo '$KAFKA_SERVER_UNIT' > /etc/systemd/system/kafka.service",
                    "systemctl start kafka",
                    "systemctl enable kafka",
        ];
             
$PROFILE=[$JAVA_COMMAND_LIST, $GENERIC_COMMAND_LISTS, $SERVICE_COMMAND_LISTS];
#$PROFILE=[$SERVICE_COMMAND_LISTS];

runCommands($PROFILE);
exit(0);

function runCommands($profiles)
{
    foreach ($profiles as $arr)
    {
        foreach ($arr as $cmd)
        {
            print("==== Executing [$cmd]...\n");
            exec($cmd, $outputs, $retCode);
        }
    }    
}

