<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo PageEncode ?>">
<title><?php print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');;?></title>
<?php

$local_JS = array();
if(empty($NOSTYLE)) {
	$local_JS[]='<link href="'.$CFG->ROOT_DIR.'Common/Styles/colors.css" media="screen" rel="stylesheet" type="text/css">';
	if($_SESSION['debug']) {
		$local_JS[]='<link href="'.$CFG->ROOT_DIR.'Common/Styles/colors_debug.css" media="screen" rel="stylesheet" type="text/css">';
	}
	$local_JS[]='<link href="'.$CFG->ROOT_DIR.'Common/Styles/Blue_screen.css" media="screen" rel="stylesheet" type="text/css">';

	if(SelectLanguage()=='tlh'){
		$local_JS[]= '<link href="'.$CFG->ROOT_DIR.'Common/Styles/klingon.css" rel="stylesheet" type="text/css">';
	}

	if(!empty($IncludeFA)) {
		$local_JS[]= '<link href="'.$CFG->ROOT_DIR.'Common/css/font-awesome.css" rel="stylesheet" type="text/css">';
	}

	if(!empty($IncludeJquery)) {
		$local_JS[]= '<script src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.6.4.min.js"></script>';
		$local_JS[]= '<script src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>';
		$local_JS[]= '<link href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css" rel="stylesheet" type="text/css">';
	}
}

if(empty($JS_SCRIPT)) {
	$JS_SCRIPT=array();
}

$JS_SCRIPT = array_merge($local_JS, $JS_SCRIPT);

foreach($JS_SCRIPT as $script) echo "$script\n";

?>
<body<?php echo (!empty($ONLOAD)?$ONLOAD:'') ?>>
