<?php

declare(strict_types=1);

$_ENV['BIN_DIR'] = dirname(__FILE__);

require_once "phar://wis_scm_framework.phar/controllers/SCM/Package.php";
require_once "phar://onix_core_framework.phar/CTable.php";

$buildProfile = CreateBuildProfile();

Package::BuildPackage(null, null, $buildProfile);
exit(0);

function CreateModule($file, $src, $dest)
{
    $m1 = new CTable("MODULE");
    $m1->SetFieldValue("SOURCE", "$src/$file");
    $m1->SetFieldValue("DESTINATION", "$dest/$file");

    return($m1);
}

function CreateBuildSpec($url, $branch, $tag)
{
    $bs1 = new CTable("BUILD_SPEC");
    $bs1->SetFieldValue('GIT_URL', $url);
    $bs1->SetFieldValue('GIT_BRANCH', $branch);
    $bs1->SetFieldValue('GIT_TAG', $tag);

    return($bs1);
}

function CreateScript($script)
{
    $s = new CTable("SCRIPT");
    $s->SetFieldValue('SCRIPT', $script);

    return($s);
}

function CreateEnvironmentVariable($envName, $value)
{
    $en = new CTable("ENVIRONMENT");
    $en->SetFieldValue($envName, $value);

    return($en);
}

function CreateAppBuildSpec()
{
    $bs1 = CreateBuildSpec('https://github.com/pjamenaja/onixlegacy.git',
                           'trunk',
                           'trunk'); //Get latest

    $modules = [];

    $mb = CreateModule('onix_core_framework.phar', 'onixlegacy/lib_wis_core_framework/build', 'system/bin');
    array_push($modules, $mb);

    $ma = CreateModule('onix_erp_framework.phar', 'onixlegacy/lib_wis_erp_framework/build', 'system/bin');
    array_push($modules, $ma);

    $m1 = CreateModule('config.php', 'onixlegacy/app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $m1);

    $m2 = CreateModule('dispatcher.php', 'onixlegacy/app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $m2);

    $m3 = CreateModule('downloader.php', 'onixlegacy/app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $m3);

    $ma_1 = CreateModule('content.php', 'onixlegacy/app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $ma_1);

    $ma_2 = CreateModule('uploader.php', 'onixlegacy/app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $ma_2);

    $m4_1 = CreateModule('build.php', 'onixlegacy/app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $m4_1);

    $m4_2 = CreateModule('init_script.bash', 'onixlegacy/app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $m4_2);

    $m5 = CreateModule('OnixCenter.zip', 'onixlegacy/app_onix', 'windows');
    array_push($modules, $m5);

    $bs1->AddChildArray('MODULES', $modules);    

    $scripts = [];

    $s1 = CreateScript('php ./onixlegacy/app_onix/onix.build.php onix');
    array_push($scripts, $s1);
    $bs1->AddChildArray('SCRIPTS', $scripts);  

    return($bs1);
}

function CreateBuildProfile()
{
    $bp = new CTable("BUILD_PROFILE");
    $bp->SetFieldValue('APP_VERSION_LABEL', 'ONIX_ERP_1.0.1');

    $bspecs = [];    

    $bs3 = CreateAppBuildSpec();
    array_push($bspecs, $bs3);

    $bp->AddChildArray('BUILD_SPECS', $bspecs);

    return($bp);
}

?>