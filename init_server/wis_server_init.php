<?php
/*
    PHP need to be available at this point
*/

declare(strict_types=1);
$_ENV['BIN_DIR'] = dirname(__FILE__);

$COMMAND_LISTS = [
                     'rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm',
                     'rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm',
                     'yum install mod_php71w php71w-opcache',
                     'yum install php71w-pdo',
                     'yum install php71w-pgsql',
                     'yum install php71w-xml',
                     'yum install php71w-fpm php71w-opcache',
                     'yum install php71w-common',
                     'yum install php71w-cli',

                     'yum install https://download.postgresql.org/pub/repos/yum/10/redhat/rhel-7-x86_64/pgdg-redhat10-10-1.noarch.rpm',
                     'yum install postgresql10',
                     'yum install postgresql10-server',
                     '/usr/pgsql-10/bin/postgresql-10-setup initdb',

                     'yum install httpd',
                     'yum install mod_ssl', 
                     'mkdir /wis',
                     'chmod 777 /wis'                    
                ];

runCommands($COMMAND_LISTS);
exit(0);

function configSELinux()
{
    print("==== Configuring SELinux ...\n");
}

function configPostgreSQL()
{
    print("==== Configuring PostgreSQL ...\n");
}

function configSSL()
{
    print("==== Configuring SSL ...\n");
}

function runCommands($cmds)
{
    foreach ($cmds as $cmd)
    {
        print("==== Executing [$cmd]...\n");
        exec($cmd, $outputs, $retCode);
        //if ($retCode)
        //{
        //    throw new Exception('Error : ' . $outputs[0]);
        //}    
    }    
}

?>