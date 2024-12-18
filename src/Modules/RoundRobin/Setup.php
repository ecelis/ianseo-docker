<?php

require_once(dirname(dirname(__DIR__)) . '/config.php');

CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite, false);

require_once('Common/Lib/CommonLib.php');

$Team=intval($_REQUEST['Team']??0);
$Event=($_REQUEST['Event']??'');
$PAGE_TITLE=get_text('Setup', 'RoundRobin');

/*
 * Tiebreak System Values
 *
 * 1: Number of sets won
 * 2: Total set points
 * 3: Total points
 * 4: Shoot off
 * 5: Sum (set)points - Sum opponents (set)points
 *
 * */

/* Combos for Team/Individual, Event, Level plus a hidden "copy from" */
$Teams='';
$q=safe_r_SQL("select distinct EvTeamEvent from Events where EvElimType=5 and EvTournament={$_SESSION['TourId']} order by EvTeamEvent");
while($r=safe_fetch($q)) {
	if($r->EvTeamEvent) {
		$Teams.='<option value="1"'.($Team==1 ? ' selected="selected"' : '').'>'.get_text('Team').'</option>';
	} else {
		$Teams.='<option value="0"'.($Team==0 ? ' selected="selected"' : '').'>'.get_text('Individual').'</option>';
	}
}
if($Teams) {
	$Teams='<select id="EvTeam" onchange="getEventDetail()">'.$Teams.'</select>';
}

$Events='';
$q=safe_r_sql("select EvCode, EvEventName from Events where EvTeamEvent=$Team and EvElimType=5 and EvTournament={$_SESSION['TourId']} order by EvProgr");
while($r=safe_fetch($q)) {
	$Events.='<option value="'.$r->EvCode.'"'.($Event==$r->EvCode ? ' selected="selected"' : '').'>'.$r->EvCode.'-'.$r->EvEventName.'</option>';
}

$IncludeJquery = true;
$JS_SCRIPT=array(
	phpVars2js(array(
		'strWarningTitle' => get_text('Warning', 'Tournament'),
		'strSetupChangeWarning' => get_text('SetupChangeWarning', 'RoundRobin'),
		'strSetupCopyWarning' => get_text('SetupCopyWarning', 'RoundRobin'),
		'cmdConfirm' => get_text('Confirm', 'Tournament'),
		'cmdCancel' => get_text('CmdCancel'),
	)),
	'<script type="text/javascript" src="./Common.js"></script>',
	'<script type="text/javascript" src="./Setup.js"></script>',
	'<link rel="stylesheet" href="./RoundRobin.css">',
);

include('Common/Templates/head.php');

echo '<table class="Tabella" id="MyTable">';
echo '<tr><th class="Main" colspan="2">'.get_text('R-Session','Tournament').'</th></tr>';

echo '<tr>
	<td colspan="2" class="navtab">
		<div class="tab active" onclick="switchTab(\'Setup.php\')">'.get_text('Setup','RoundRobin').'</div>
		<div class="tab" onclick="switchTab(\'Grouping.php\')">'.get_text('GroupAssignment','RoundRobin').'</div>
		<div class="tab" onclick="switchTab(\'Targets.php\')">'.get_text('TargetAssignment','RoundRobin').'</div>
	</td>
	</tr>';

echo '<tr>';
echo '<th class="Title" colspan="2">';
echo $Teams;
echo '<select id="EvCode" onchange="getEventDetail()">'.$Events.'</select>';
echo '<select id="EvLevel" onchange="getEventDetail()"></select>';
echo '<select id="EvCopyFrom" onchange="copyFromEvent(this)"></select>';
echo '</th>';
echo '</tr>';

echo '<tr><th>'.get_text('QualifiedArchers', 'RoundRobin').'</th><TD id="QualifiedArchers"></TD></tr>';
echo '<tr><th>'.get_text('Rounds', 'RoundRobin').'</th><TD id="RoundsPerGroup"></TD></tr>';
echo '<tbody id="LevDetails"></tbody>';

echo '</table>';

include('Common/Templates/tail.php');
