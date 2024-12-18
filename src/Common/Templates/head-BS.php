<?php

echo '<!DOCTYPE html>';
echo '<html lang="en" translate="no">';
echo '<head>';
echo '<meta name="google" content="notranslate">';
//<!-- Required meta tags -->
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
echo '<meta charset="utf-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
//<!-- Bootstrap & FontAwesome CSS -->
echo '<link rel="stylesheet" href="'.$CFG->ROOT_DIR.'Common/css/bootstrap.min.css">';
echo '<link rel="stylesheet" href="'.$CFG->ROOT_DIR.'Common/css/font-awesome.css">';
echo '<link rel="stylesheet" href="'.$CFG->ROOT_DIR.'Common/css/bs4-switch.css">';
echo '<link rel="stylesheet" href="'.$CFG->ROOT_DIR.'Common/css/bootstrap-toggle.min.css">';
echo '<link rel="stylesheet" href="'.$CFG->ROOT_DIR.'Common/css/ianseo.css">';
//<!-- jQuery first, then Popper.js, then Bootstrap JS -->
echo '<script src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.6.4.min.js"></script>';
echo '<script src="'.$CFG->ROOT_DIR.'Common/js/popper.min.js"></script>';
echo '<script src="'.$CFG->ROOT_DIR.'Common/js/bootstrap.min.js"></script>';
echo '<script src="'.$CFG->ROOT_DIR.'Common/js/bootstrap-toggle.min.js"></script>';
echo '<title>';
if(!empty($PAGE_TITLE)) echo $PAGE_TITLE . ' - ';
print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');
echo '</title>';

if(!empty($JS_SCRIPT)) {
    require_once(dirname(__DIR__).'/Lib/CommonLib.php');
    $JS_SCRIPT[]= phpVars2js([
        'wwwdir'=>$CFG->ROOT_DIR,
    ]);
	foreach($JS_SCRIPT as $script) echo "$script\n";
}

echo '</head>';
echo '<body>';

echo '<div id="Content">';
