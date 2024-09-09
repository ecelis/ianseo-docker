<?php

require_once(dirname(dirname(__FILE__)).'/config.php');
checkACL(AclRoot, AclReadWrite);

require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');
$Options=GetParameter('AccessApp', '', array(), true);

$PAGE_TITLE=get_text('MenuLM_GateSituation');

$IncludeJquery = true;
$JS_SCRIPT=array(
	'<script type="text/javascript" src="./GateSituation.js"></script>',
);

require_once('Common/Templates/head.php');

echo '<table class="Tabella">';

foreach($Options as $ToId => $Sessions) {
	echo '<tr>';
	echo '<th>
		<div>'.getCodeFromId($ToId).'</div>
		<div><input type="button" value="'.get_text('Reload', 'Tournament').'" onclick="getSituation('.$ToId.')" </div>
		</th>';

	echo '<td id="Sit-'.$ToId.'" width="90%"></td>';
	echo '</tr>';
}
echo '</table>';

require_once('Common/Templates/tail.php');
