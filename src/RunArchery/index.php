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

$IRM=[];
$q=safe_r_sql("select IrmId from IrmTypes order by IrmId");
while($r=safe_fetch($q)) {
	$r->IrmType=($r->IrmId ? get_text('IRM-'.$r->IrmId, 'Tournament') : '');
	$IRM[]=$r;
}

$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
    '<script type="text/javascript" src="./index.js"></script>',
    '<link href="./index.css" media="screen" rel="stylesheet" type="text/css" />',
    phpVars2js(array(
    	'curEvent'=>$Event,
    	'curPhase'=>$Phase,
    	'curLap'=>$Lap,
	    'curAction'=>'getData',
    	'importTitle'=>get_text('ImportTimes', 'RunArchery'),
    	'btnCancel'=>get_text('CmdCancel'),
    	'btnOk'=>get_text('CmdOk'),
		'msgMoveToNext'=>get_text('MoveToNextDisclaimer', 'RunArchery'),
		'msgUpdateIrmDisclaimer'=>get_text('UpdateIrmDisclaimer', 'RunArchery'),
    	'IRM'=>$IRM,
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

$PAGE_TITLE=get_text('MenuLM_RunArchery');

include('Common/Templates/head.php');


$q=safe_w_SQL("select EvTeamEvent, EvCode, EvEventName, EvElim1 as HasSemi, EvElim2 as HasFinals from Events where EvTournament={$_SESSION['TourId']} order by EvTeamEvent, EvProgr");

echo '<table class="Tabella">';
echo '<tr><th colspan="5" class="Title">'.$PAGE_TITLE.'</th></tr>';
echo '<tr>
    <th class="Title">'.get_text('Event').'</th>
    <th class="Title">'.get_text('Phase').'</th>
    <th class="Title PoolSel d-none">'.get_text('MenuLM_Pools').'</th>
    <th class="Title">'.get_text('Laps', 'RunArchery').'</th>
    <th class="Title"></th>
    </tr>';
echo '<tr>
    <th class="Header" id="headEvent"></th>
    <th class="Header" id="headPhase"></th>
    <th class="Header PoolSel d-none" id="headPool"></th>
    <th class="Header" id="headLap"></th>
    <th class="Header" id="headIO">
    	<div class="Button mx-5" onclick="importArrows()"><i class="fa fa-compress-arrows-alt mr-2"></i>'.get_text('ImportArrows', 'RunArchery').'</div>
    	<div class="Button mx-5" onclick="importLoops()"><i class="fa fa-redo mr-2"></i>'.get_text('ImportLoops', 'RunArchery').'</div>
    	<div class="Button mx-5" onclick="importTimes()"><i class="fa fa-upload mr-2"></i>'.get_text('ImportTimes', 'RunArchery').'</div>
    	<div class="Button mx-5 d-none" id="sendNextPhase" onclick="sendNextPhase()"><i class="far fa-circle-right mr-2"></i>'.get_text('MoveToNextPhase', 'RunArchery').'</div>
    </th>
    </tr>';
echo '</table>';
echo '<table class="Tabella" id="tableBody"></table>';

include('Common/Templates/tail.php');
