<?php

declare(strict_types=1);

$_ENV['BIN_DIR'] = dirname(__FILE__);

require_once "phar://wis_scm_framework.phar/controllers/Environment/Environment.php";
require_once "phar://onix_core_framework.phar/CTable.php";

$zip = './ONIX_ERP_1.0.1.102320170516.zip';

Install('onix', 'dev', 'wis', 'development', $zip);

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
}

?>