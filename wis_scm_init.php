<?php

declare(strict_types=1);

$_ENV['BIN_DIR'] = dirname(__FILE__);

require_once "phar://wis_scm_framework.phar/controllers/Environment/Environment.php";
require_once "phar://onix_core_framework.phar/CTable.php";

InitDirectory('onix', 'dev', 'tma', 'development');

exit(0);

function InitDirectory($prod, $stage, $client, $system)
{
    $f = new CTable("");
    $f->SetFieldValue('PRODUCT', $prod);
    $f->SetFieldValue('STAGE', $stage);
    $f->SetFieldValue('CLIENT', $client);
    $f->SetFieldValue('SYSTEM', $system);

    Environment::CreateDirectory(null, null, $f);
    Environment::InitDirectory(null, null, $f);
}

?>