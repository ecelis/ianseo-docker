<?php
require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');

CheckTourSession(true);
checkACL(AclRobin, AclReadWrite);

$Team=($_REQUEST['team'] ?? '');
$Event=($_REQUEST['event'] ?? '');
$Level=($_REQUEST['level'] ?? '');
$Group=($_REQUEST['group'] ?? '');
$Round=($_REQUEST['round'] ?? '');
$Ties=($_REQUEST['ties'] ?? '');
$Details=($_REQUEST['details'] ?? '1');
$Byes=($_REQUEST['byes'] ?? '');


$JS_SCRIPT = array( phpVars2js(array(
		'ROOT_DIR'=>$CFG->ROOT_DIR,
		'MsgInitFinalGridsError'=>get_text('MsgInitFinalGridsError'),
		'MsgAttentionFinReset'=>get_text('MsgAttentionFinReset'),
		'CmdCancel' => get_text('CmdCancel'),
		'CmdConfirm' => get_text('Confirm', 'Tournament'),
		'Advanced' => get_text('Advanced'),
		'MsgForExpert' => get_text('MsgForExpert', 'Tournament'),
		'reqTeam' => $Team,
		'reqEvent' => $Event,
		'reqLevel' => $Level,
		'reqGroup' => $Group,
		'reqRound' => $Round,
	)),
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-3.2.1.min.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/jquery-confirm.min.js"></script>',
	'<script type="text/javascript" src="./Common.js"></script>',
	'<script type="text/javascript" src="./InsertPoint.js"></script>',
	'<link href="./RoundRobin.css" rel="stylesheet" type="text/css">',
	'<link href="'.$CFG->ROOT_DIR.'Common/css/jquery-confirm.min.css" media="screen" rel="stylesheet" type="text/css">',
);
include('Common/Templates/head.php');

echo '<table id="MainSOTable" class="Tabella">';
$FinRobinCalc=getModuleParameter('ISK','CalcFinRobin',0, 0, true);
if($FinRobinCalc) {
	echo '<tr class="warning"><td class="OneRow">'.get_text('RkCalcOffWarning', 'ISK').'</td></tr>';
}
echo '<tr><th class="Title OneRow">
	<select id="TeamSelector" onchange="selectEvent()"><option value="-1">---</option><option value="0">'.get_text('Individual').'</option><option value="1">'.get_text('Team').'</option></select>
	<select id="EventSelector" onchange="selectLevel()"></select>
	<select id="LevelSelector" onchange="selectGroup()"></select>
	<select id="GroupSelector" onchange="selectMain()"></select>
	<select id="RoundSelector" onchange="selectMain()"></select>
	<input type="checkbox" id="TieSelector" onclick="selectMain()"'.($Ties ? ' checked="checked"' : '').'>'.get_text('ManTie').'
	<input type="checkbox" id="DetailSelector" onclick="selectMain()"'.($Details ? ' checked="checked"' : '').'>'.get_text('ShowEndDetails', 'RoundRobin').'
	<input type="checkbox" id="ByeSelector" onclick="selectMain()"'.($Byes ? ' checked="checked"' : '').'>'.get_text('ManageByes', 'Tournament').'
	</th></tr>';

echo '<tbody id="tbody"></tbody>';
echo '</table>';
include('Common/Templates/tail.php');
