<?php

declare(strict_types=1);

$_ENV['BIN_DIR'] = dirname(__FILE__);

require_once "phar://wis_scm_framework.phar/controllers/Environment/Environment.php";
require_once "phar://onix_core_framework.phar/CTable.php";

Initialize('onix', 'dev', 'wtt', 'framework');
Initialize('onix', 'dev', 'wtt', 'development');

exit(0);

function Initialize($prod, $stage, $client, $system)
{
    $f = new CTable("");
    $f->SetFieldValue('PRODUCT', $prod);
    $f->SetFieldValue('STAGE', $stage);
    $f->SetFieldValue('CLIENT', $client);
    $f->SetFieldValue('SYSTEM', $system);

    $f->SetFieldValue('USER_NAME', 'ROOT');
    $f->SetFieldValue('PASSWORD', '5januryXYZ!234jame');
    $f->SetFieldValue('EMAIL', 'pjame.fb@gmail.com');
    $f->SetFieldValue('IS_ADMIN', 'Y');
    $f->SetFieldValue('IS_ENABLE', 'Y');

    chdir("/wis/$prod/$stage/$client/$system/system/bin");
    
    Environment::PatchDatabase(null, new CTable(''), $f);
    Environment::InitializeUserData(null, new CTable(''), $f);
}

?>
