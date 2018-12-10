<?php
/*
    PHP need to be available at this point
*/

declare(strict_types=1);

$SUDOERS_FILE='/etc/sudoers';

$_ENV['BIN_DIR'] = dirname(__FILE__);
$_ENV['WIS_KEY_FILE'] = getenv('WIS_KEY_FILE');
$_ENV['WIS_CERT_FILE'] = getenv('WIS_CERT_FILE');
$_ENV['WIS_IP_ADDR'] = getenv('WIS_IP_ADDR');
$_ENV['WIS_USER'] = getenv('WIS_USER');

$PHP_COMMAND_LISTS_7_1 = [   
                     'rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm',
                     'rpm -Uvh https://mirror.webtatic.com/yum/el7/webtatic-release.rpm',
                     'yum -y install mod_php72w',
                     'yum -y install php72w-pdo',
                     'yum -y install php72w-pgsql',
                     'yum -y install php72w-xml',
                     'yum -y install php72w-common',
                     'yum -y install php72w-gd',
                     'yum -y install php72w-cli',
                     'echo "extension=rdkafka.so" > /etc/php.d/rdkafka.ini',
                ];

$SUBJ='-subj "/C=TH/ST=Bangkok/L=Dindang/O=Wintech Thai/OU=COM/CN=www.wintechthai.com"';

$APACHE_COMMAND_LISTS_2_4 = [
                    'yum -y install httpd',
                    'yum -y install mod_ssl',                    
                    'mkdir /etc/httpd/ssl',
                    "openssl req $SUBJ -x509 -nodes -days 365 -newkey rsa:2048 -keyout $_ENV[WIS_KEY_FILE] -out $_ENV[WIS_CERT_FILE]",      
                    'apache_func_call(./httpd.conf_2.4|/etc/httpd/conf/httpd.conf)',
                    'echo "<?php phpinfo();" > /var/www/html/index.php',
                ];

$PGSQL_COMMAND_LISTS_10_0 = [
                    'yum -y install https://download.postgresql.org/pub/repos/yum/10/redhat/rhel-7-x86_64/pgdg-centos10-10-2.noarch.rpm',
                    'yum -y install postgresql10',
                    'yum -y install postgresql10-server',
                    '/usr/pgsql-10/bin/postgresql-10-setup initdb',
                    'pgsql_func_call(/var/lib/pgsql/10/data/pg_hba.conf)',                    
                ];

$GENERIC_COMMAND_LISTS_1_0 = [
                    'mkdir /wis',
                    'chmod 777 /wis',
                    'systemctl enable httpd.service',
                    'systemctl start httpd.service',
                    'systemctl enable postgresql-10',
                    'systemctl start postgresql-10',
                    'setsebool -P httpd_can_network_connect=1',
                    "chmod 600 $SUDOERS_FILE",
                    "echo '$_ENV[WIS_USER] ALL=(ALL) NOPASSWD: /usr/sbin/semanage' >> $SUDOERS_FILE ",
                    "echo '$_ENV[WIS_USER] ALL=(ALL) NOPASSWD: /usr/sbin/restorecon' >> $SUDOERS_FILE ",
                    "chmod 400 $SUDOERS_FILE",
                ];
                                
$PGSQL_HBA_CONFIG = 
            [                
                    ['local',   'all',         'all',  '',             'trust'],
                    ['host' ,   'all',         'all',  '127.0.0.1/32', 'trust'],
                    ['host' ,   'all',         'all',  '::1/128',      'trust'],
                    ['local',   'replication', 'all',  '',             'peer' ],
                    ['host',    'replication', 'all',  '127.0.0.1/32', 'ident'],
                    ['host',    'replication', 'all',  '::1/128',      'ident'],
            ];

$PROFILE=[$PHP_COMMAND_LISTS_7_1, $PGSQL_COMMAND_LISTS_10_0, $APACHE_COMMAND_LISTS_2_4, $GENERIC_COMMAND_LISTS_1_0];

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
                    list($template, $output) = explode ('|', $param);
                    configSslApache($template, $output);
                }
                else if ($func_key == 'pgsql')
                {
                    global $PGSQL_HBA_CONFIG;
                    configPostgreSQL($param, $PGSQL_HBA_CONFIG);
                }                               
            }
            else
            {
                exec($cmd, $outputs, $retCode);
            }
        }
    }    
}

function configSudoers()
{
    print("==== Configuring Sudoers ...\n");
}

function configPostgreSQL($fname, $arr)
{
    print("==== Configuring PostgreSQL ...\n");

    $buffer = '';

    foreach($arr as $cfg)
    {
        list($type, $db, $user, $address, $action) = $cfg;
        $row = sprintf("%s    %s    %s    %s     %s\n", str_pad($type, 12), str_pad($db, 12),
            str_pad($user, 12), str_pad($address, 12), str_pad($action, 12));

        $buffer = $buffer . $row;
    }

    file_put_contents($fname, $buffer);
}

function configSslApache($template, $output)
{
    print("==== Configuring SSL ...\n");

    $fh = fopen($template, "r");
    $oh = fopen($output, 'w');

    while ($line = fgets($fh)) 
    {
        $newStr = $line;

        foreach ($_ENV as $name => $value)
        {
            if (preg_match('/^WIS_.*$/', $name))
            {
                $var = '${' . $name . '}';
                $newStr = str_replace($var, $value, $newStr); 
            }
        }

        fwrite($oh, $newStr);
    }

    fclose($fh);
    fclose($oh);
}

?>
