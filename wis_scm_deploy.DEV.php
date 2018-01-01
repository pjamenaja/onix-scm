<?php

declare(strict_types=1);

$_ENV['BIN_DIR'] = dirname(__FILE__);

require_once "phar://wis_scm_framework.phar/controllers/Environment/Environment.php";
require_once "phar://onix_core_framework.phar/CTable.php";

$zip = './ONIX_ERP_1.0.1.123120170510.zip';

Install('onix', 'dev', 'wtt', 'development', $zip);
Install('onix', 'dev', 'wtt', 'framework', $zip);

exit(0);

function Install($prod, $stage, $client, $system, $pkg)
{
    $f = new CTable("");
    $f->SetFieldValue('PACKAGE_FILE', $pkg);
    $f->SetFieldValue('PRODUCT', $prod);
    $f->SetFieldValue('STAGE', $stage);
    $f->SetFieldValue('CLIENT', $client);
    $f->SetFieldValue('SYSTEM', $system);

    Environment::CleanDirectory(null, null, $f);
    Environment::InstallPackage(null, null, $f);

    $path = "/wis/$prod/$stage/$client/$system";
    $pattern = "$path(/.*)?";

    $cmd1 = "sudo semanage fcontext -a -t httpd_sys_rw_content_t '$pattern' ";
    print("Executing [$cmd1]...\n");
    exec($cmd1, $outputs, $retCode);

    $cmd2 = "sudo restorecon -Rv $path ";
    print("Executing [$cmd2]...\n");
    exec($cmd2, $outputs, $retCode);    
}

?>
