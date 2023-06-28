<?php
/*
Run Archery events are made of a "qualification", then based on the settings of the events,
a semifinal (eventually) and a final.

All "rounds" are made of Laps, after each lap a series of arrows: missing the target means a penalty "loop".
Any error in arrow number or loops made adds a time penalty

 *
 *
 *
 * */

global $CFG;
$IncludeJquery=true;
$IncludeFA=true;

require_once(dirname(__DIR__) . '/config.php');
require_once('Common/Lib/CommonLib.php');
// require_once('Common/Fun_FormatText.inc.php');
// require_once('Common/Fun_Various.inc.php');
// require_once('Common/Fun_Sessions.inc.php');

CheckTourSession(true);
checkACL(AclQualification, AclReadWrite);

// prepare the selectors...
$Event=$_REQUEST['event']??'';
$Phase=$_REQUEST['phase']??'-1';
$Lap=$_REQUEST['lap']??'0';

$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
	'<script type="text/javascript" src="./SetTarget.js"></script>',
	'<link href="./index.css" media="screen" rel="stylesheet" type="text/css" />',
	phpVars2js(array(
		'curEvent'=>$Event,
		'curPhase'=>$Phase,
		'curLap'=>$Lap,
        'curAction'=>'getDraw',
		'msgCreateTeams'=>get_text('CreateTeamsWarning', 'RunArchery'),
		'btnConfirm'=>get_text('Confirm','Tournament'),
		'btnCancel'=>get_text('CmdCancel'),
		'gender0'=>get_text('ShortMale','Tournament'),
		'gender1'=>get_text('ShortFemale','Tournament'),
		'gender'=>'',
		// 'PostUpdateEnd'=>get_text('PostUpdateEnd'),
		// 'RootDir'=>$CFG->ROOT_DIR.'Qualification/',
		// 'MsgAreYouSure' => get_text('MsgAreYouSure'),
		// 'MsgWent2Home' => get_text('Went2Home', 'Tournament'),
		// 'MsgBackFromHome' => get_text('BackFromHome', 'Tournament'),
		// 'MsgSetDSQ' => get_text('Set-DSQ', 'Tournament'),
		// 'MsgUnsetDSQ' => get_text('Unset-DSQ', 'Tournament'),
		// 'TxtIrmDns' => get_text('DNS', 'Tournament'),
		// 'TxtIrmDnf' => get_text('DNF', 'Tournament'),
		// 'TxtIrmUnset' => get_text('CmdUnset', 'Tournament', ''),
		// 'TxtCancel' => get_text('CmdCancel'),
	)),
);

$PAGE_TITLE=get_text('MenuLM_Draw');

include('Common/Templates/head.php');


echo '<table class="Tabella">';
echo '<tr><th colspan="7" class="Title">'.$PAGE_TITLE.'</th></tr>';
echo '<tr>
    <th class="Title">'.get_text('Event').'</th>
    <th class="Title">'.get_text('Phase').'</th>
    <th class="Title">'.get_text('DrawType', 'Tournament').'</th>
    <th class="Title">'.get_text('StartTime', 'RunArchery').'</th>
    <th class="Title">'.get_text('DelayBetweenStarts', 'RunArchery').'</th>
    <th class="Title">'.get_text('GroupArchers', 'RoundRobin').'</th>
    <th class="Title"></th>
    </tr>';
echo '<tr>
    <th class="Header" id="headEvent"></th>
    <th class="Header" id="headPhase"></th>
    <th class="Header">
    	<select id="drawType" onchange="setDrawType(this)">
    	<option value="">---</option>
    	<option value="1">'.get_text('SingleStart','RunArchery').'</option>
    	<option value="0">'.get_text('GroupedStart','RunArchery').'</option>
		</select>
	</th>
    <th class="Header"><input type="datetime-local" id="start"></th>
    <th class="Header"><input type="number" id="delay"></th>
    <th class="Header" id="groupNum"></th>
    <th class="Header">
    	<button onclick="assignRandom()">'.get_text('RandomAssign', 'RunArchery').'</button>
    </th>
    </tr>';
echo '</table>';
echo '<table id="tableBody" class="Tabella"></table>';

include('Common/Templates/tail.php');
