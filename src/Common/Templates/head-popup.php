<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo PageEncode ?>">
<title><?php print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');;?></title>
<link href="<?php echo $CFG->ROOT_DIR ?>Common/Styles/colors.css" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo $CFG->ROOT_DIR ?>Common/Styles/Blue_screen.css" media="screen" rel="stylesheet" type="text/css">
<link href="<?php echo $CFG->ROOT_DIR ?>Common/Styles/Blue_screen-print.css" media="print" rel="stylesheet" type="text/css">
<?php

if(!empty($IncludeJquery)) {
    $JS_SCRIPT[]= '<script src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.6.4.min.js"></script>';
    $JS_SCRIPT[]= '<script src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>';
    $JS_SCRIPT[]= '<link href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css" rel="stylesheet" type="text/css">';
}

if(!empty($JS_SCRIPT)) {
	foreach($JS_SCRIPT as $script) echo "$script\n";
}
?>
</head>
<body<?php echo empty($ONLOAD)?' onload="window.focus()"':$ONLOAD ?>>
<div id="PopupContent">
