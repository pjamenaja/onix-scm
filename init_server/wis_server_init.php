<?php
/*
    PHP need to be available at this point
*/

declare(strict_types=1);

$_ENV['BIN_DIR'] = dirname(__FILE__);
$_ENV['WIS_KEY_FILE'] = getenv('WIS_KEY_FILE');
$_ENV['WIS_CERT_FILE'] = getenv('WIS_CERT_FILE');

$PHP_COMMAND_LISTS_7_1 = [
                     'rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm',
                     'rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm',
                     'yum install mod_php71w php71w-opcache',
                     'yum install php71w-pdo',
                     'yum install php71w-pgsql',
                     'yum install php71w-xml',
                     'yum install php71w-fpm php71w-opcache',
                     'yum install php71w-common',
                     'yum install php71w-cli',
                ];

$APACHE_COMMAND_LISTS_2_4 = [
                    'yum install httpd',
                    'yum install mod_ssl',                    
                    'mkdir /etc/httpd/ssl',
                    "openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout $_ENV[WIS_KEY_FILE] -out $_ENV[WIS_CERT_FILE]",      
                    'apache_func_call(httpd.conf_2.4)',
                    'echo HelloWorld > /var/www/html/index.html',
                ];

$PGSQL_COMMAND_LISTS_10_0 = [
                    'yum install https://download.postgresql.org/pub/repos/yum/10/redhat/rhel-7-x86_64/pgdg-redhat10-10-1.noarch.rpm',
                    'yum install postgresql10',
                    'yum install postgresql10-server',
                    '/usr/pgsql-10/bin/postgresql-10-setup initdb',
                    'pgsql_func_call(pg_hba.conf_10.0)',                    
                ];

$GENERIC_COMMAND_LISTS_1_0 = [
                    'mkdir /wis',
                    'chmod 777 /wis',                                        
                ];

$PROFILE=[$PHP_COMMAND_LISTS_7_1, $APACHE_COMMAND_LISTS_2_4, $PGSQL_COMMAND_LISTS_10_0, $GENERIC_COMMAND_LISTS_1_0];

runCommands($PROFILE);
exit(0);

function runCommands($profiles)
{
    foreach ($profiles as $arr)
    {
        foreach ($arr as $cmd)
        {
            print("==== Executing [$cmd]...\n");

            if (preg_match_all('/^(.*)_func_call\((.*)\)$/', $cmd, $matches))
            {
                $func_key = $matches[1][0];
                $param = $matches[2][0];

                print("Calling function [$func_key] with param [$param]\n");
                if ($func_key == 'apache')
                {
                    configSslApache($param);
                }
                else if ($func_key == 'pgsql')
                {
                    configPostgreSQL($param);
                }                
            }
            else
            {
                exec($cmd, $outputs, $retCode);
            }
        }
    }    
}

function configSELinux()
{
    print("==== Configuring SELinux ...\n");
}

function configPostgreSQL($template)
{
    print("==== Configuring PostgreSQL ...\n");
}

function configSslApache()
{
    print("==== Configuring SSL ...\n");
}

?>