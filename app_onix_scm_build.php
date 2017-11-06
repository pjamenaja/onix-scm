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

function CreateCoreBuildSpec()
{
    $bs1 = CreateBuildSpec('https://pjamenaja@bitbucket.org/pjamenaja/lib_wis_core_framework.git',
                           'master',
                           ''); //Get latest 

    $modules = [];
    $m1 = CreateModule('onix_core_framework.phar', 'lib_wis_core_framework/build', 'system/bin');
    array_push($modules, $m1);

    $bs1->AddChildArray('MODULES', $modules);   
    return($bs1);
}

function CreateErpBuildSpec()
{
    $bs1 = CreateBuildSpec('https://pjamenaja@bitbucket.org/pjamenaja/lib_wis_erp_framework.git',
                           'master',
                           ''); //Get latest 

    $modules = [];
    $m1 = CreateModule('onix_erp_framework.phar', 'lib_wis_erp_framework/build', 'system/bin');
    array_push($modules, $m1);

    $bs1->AddChildArray('MODULES', $modules);   
    return($bs1);
}

function CreateAppBuildSpec()
{
    $bs1 = CreateBuildSpec('https://pjamenaja@bitbucket.org/pjamenaja/app_onix.git',
                           'master',
                           ''); //Get latest
//ONIX_1.0.3

    $modules = [];

    $m1 = CreateModule('config.php', 'app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $m1);

    $m2 = CreateModule('dispatcher.php', 'app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $m2);

    $m3 = CreateModule('downloader.php', 'app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $m3);

    $m4 = CreateModule('file.php', 'app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $m4);

    $m4_1 = CreateModule('build.php', 'app_onix/onix_server/scripts', 'system/bin');
    array_push($modules, $m4_1);

    $m5 = CreateModule('OnixCenter.zip', 'app_onix', 'windows');
    array_push($modules, $m5);

    $m6 = CreateModule('OnixPOS.zip', 'app_onix', 'windows');
    array_push($modules, $m6);

    $bs1->AddChildArray('MODULES', $modules);    

    $scripts = [];
    $s1 = CreateScript('php ./app_onix/onix.build.php');
    array_push($scripts, $s1);
    $bs1->AddChildArray('SCRIPTS', $scripts);  

    return($bs1);
}

function CreateBuildProfile()
{
    $bp = new CTable("BUILD_PROFILE");
    $bp->SetFieldValue('APP_VERSION_LABEL', 'ONIX_ERP_1.0.1');

    $bspecs = [];
    
    $bs1 = CreateCoreBuildSpec();
    array_push($bspecs, $bs1);

    $bs2 = CreateErpBuildSpec();
    array_push($bspecs, $bs2);

    $bs3 = CreateAppBuildSpec();
    array_push($bspecs, $bs3);

    $bp->AddChildArray('BUILD_SPECS', $bspecs);

    return($bp);
}

?>