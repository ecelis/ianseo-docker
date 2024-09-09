<?php

require_once(dirname(__FILE__, 2) . '/config.php');
checkACL(AclRoot, AclReadWrite);
checkGPL(true);

if(!empty($_SESSION['AUTH_ENABLE']) AND empty($_SESSION['AUTH_ROOT'])) {
    CD_redirect($CFG->ROOT_DIR.'noAccess.php');
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
);

include('Common/Templates/head.php');

$FileToCheck=__DIR__.'/check';

$f=@fopen($FileToCheck, 'w');
if($f) {
	echo '<div class="Center">';
	echo '<table class="Tabella" style="width:50%">';
    echo '<tr><td colspan="2">'.get_text('UpdatePrepared', 'Install').'</td></tr>';
	if(!in_array(ProgramRelease, array('STABLE','FITARCO')) or isset($_GET['testing'])) {
		//@include('Modules/IanseoTeam/IanseoFeatures/isIanseoTeam.php');
    	echo '<tr><th colspan="2">'.get_text('SpecialUpdate', 'Install').'</th></tr>';
		echo '<tr><th>' . get_text('Email','Install') . '</th><td><input type="text" name="Email" id="Email"  class="w-100"></td></tr>';
	}
	echo '<tr><td class="Center" colspan="2"><input type="button" value="' . get_text('CmdOk') . '" onclick="doUpdate()"></td></tr>';
	echo '</table>';
	echo '</form>';
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
