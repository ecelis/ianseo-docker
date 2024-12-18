<?php

require_once(dirname(__FILE__, 2) . '/config.php');
checkACL(AclRoot, AclReadWrite);
checkGPL(true);

if(!empty($_SESSION['AUTH_ENABLE']) AND empty($_SESSION['AUTH_ROOT'])) {
    CD_redirect($CFG->ROOT_DIR.'noAccess.php');
}

// check if a major update of Mysql is needed!
$NeedsUpdate='';
$UpdateMessage=UpdateToInnoDb(false);
if($UpdateMessage===true) {
    // DB needs to be updated!
    $NeedsUpdate='<div class="alert alert-danger bold">'.get_text('MysqlInnoDbProcess', 'Errors').'</div>';
    $UpdateMessage= '';
}

require_once('Common/Lib/CommonLib.php');
$IncludeJquery = true;
$JS_SCRIPT=array(
    phpVars2js(array(
        'cmdClose' => get_text('Close'),
        'cmdForceUpdate' => get_text('cmdForceUpdate','Install'),
    )),
    '<link rel="stylesheet" href="index.css">',
    '<script src="./index.js"></script>',
    '<script src="../Common/js/Fun_JS.inc.js"></script>',
);

include('Common/Templates/head.php');

$FileToCheck=__DIR__.'/check';

$f=@fopen($FileToCheck, 'w');
if($f) {
    echo '<div class="Center" style="width:50%;margin:auto;margin-top:2em">
        <div class="alert alert-warning bold">
            <div>'.get_text('BackupTournaments', 'Errors').'</div>';
    if(in_array('zip',get_loaded_extensions())) {
        echo '<div class="Button mt-3" onclick="exportAllCompetitions()">' . get_text('ExportAllComps', 'Install') . '</div>';
    } else {
        echo '<div class="mt-2">'.get_text('MissingZipExtension', 'Errors').'</div>';;
    }

    echo '</div>'
        .$UpdateMessage;
    if($NeedsUpdate) {
        echo '<div id="InnoDbUpdateTable">
            '.$NeedsUpdate.'
            <div><input type="button" value="' . get_text('CmdOk') . '" onclick="doInnoDbUpdate()"></div>
            </div>';
    }

    echo '<div id="UpdateTable" class="text-small '.($NeedsUpdate ? 'd-none' : '').'">
        <div class="alert alert-success">'.get_text('UpdatePrepared', 'Install') .'</div>';

    if(!in_array(ProgramRelease, array('STABLE','FITARCO')) or isset($_GET['testing'])) {
        echo '<div class="alert alert-info mt-2">
            <div>'.get_text('SpecialUpdate', 'Install').'</div>
            <div style="display:flex" class="mt-2"><div class="w-30 text-right">' . get_text('Email','Install') . ':</div><div class="w-70"><input type="text" name="Email" id="Email"  class="w-100"></div></div>
            </div>';
    }

    echo '<div class="alert"><input type="button" value="' . get_text('CmdOk') . '" onclick="doUpdate()"></div>';
	echo '</div>';
	echo '</div>';
	fclose($f);
	unlink($FileToCheck);
} else {
	echo '<div class="Center">';
	echo '<table class="Tabella" style="width:50%">';
	echo '<tr><td colspan="2">'.get_text('NotUpdatable', 'Install').'</td></tr>';
	echo '</table>';
	echo '</div>';
}


include('Common/Templates/tail.php');
