<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo PageEncode ?>">
<title><?php print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');;?></title>
<?php
if(empty($NOSTYLE)) {
	echo '<link href="'.$CFG->ROOT_DIR.'Common/Styles/colors.css" media="screen" rel="stylesheet" type="text/css">';
	if($_SESSION['debug']) {
		echo '<link href="'.$CFG->ROOT_DIR.'Common/Styles/colors_debug.css" rel="stylesheet" type="text/css">';
	}
	echo '<link href="' . $CFG->ROOT_DIR . 'Common/Styles/Blue_screen.css" media="screen" rel="stylesheet" type="text/css">';
}


if(!empty($JS_SCRIPT)) {
	foreach($JS_SCRIPT as $script) echo "$script\n";
}
?>
<body<?php echo (!empty($ONLOAD)?$ONLOAD:'') ?>>
