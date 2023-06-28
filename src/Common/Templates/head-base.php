<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo PageEncode ?>">
<title><?php

echo '<link href="'.$CFG->ROOT_DIR.'Common/Styles/colors.css" media="screen" rel="stylesheet" type="text/css">';
if($_SESSION['debug']) {
	echo '<link href="'.$CFG->ROOT_DIR.'Common/Styles/colors_debug.css" rel="stylesheet" type="text/css">';
}

if(!empty($PAGE_TITLE)) echo $PAGE_TITLE . ' - ';
print ProgramName . ' ' . ProgramVersion . (defined('ProgramBuild') ? ' ('.ProgramBuild.')' : '');?></title>
<link href="<?php echo $CFG->ROOT_DIR ?>Common/Styles/Blue_screen.css" media="screen" rel="stylesheet" type="text/css">
<?php

require_once('Common/Menu.php');

if(!empty($JS_SCRIPT)) {
	foreach($JS_SCRIPT as $script) echo "$script\n";
}

?>

</head>
<body<?php echo (!empty($ONLOAD)?$ONLOAD:'') ?>>
