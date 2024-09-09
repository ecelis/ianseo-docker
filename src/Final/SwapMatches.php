<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

CheckTourSession(true);

checkACL(array(AclIndividuals, AclTeams), AclReadWrite);

$PRINT_MENU=true;
$IncludeJquery = true;
$JS_SCRIPT=array(
	'<script src="./SwapMatches.js"></script>',
	'<style>
		.ev-bold {font-weight:bold;}
		.match-match {display:flex; align-items: center;margin-bottom:0.25em;}
		.match-schedule {flex:1 0 auto;text-align: center;}
		.match-opponents {flex:10 0 25em;}
		.match-swap {flex:1 0 auto; border:1px solid gray; cursor: pointer;padding:0.5em 1em;margin:0 1em; text-align: center;}
		.match-closed {font-style: italic;}
		.match-closed .match-swap {cursor: not-allowed;text-decoration: line-through;}
	</style>'
	);
require_once('Common/Templates/head.php');

echo '<table class="Tabella">
	<tr>
		<th class="Title" width="20%">'.get_text('Event').'</th>
		<th class="Title" width="20%">'.get_text('Phase').'</th>
		<th class="Title" width="60%">'.get_text('ScheduledMatches', 'Tournament').'</th>
	</tr>
	<tr valign="top">
		<td><select id="Events" onchange="getPhases(this.value)"></select></td>
		<td><select id="Phases" onchange="getMatches(this.value)"></select></td>
		<td id="Matches"></td>
	</tr>
	</table>';


require_once('Common/Templates/tail.php');