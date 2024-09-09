<?php

require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');
// require_once('Common/Lib/Obj_RankFactory.php');
// require_once('Common/Fun_Phases.inc.php');
// require_once('Final/Fun_ChangePhase.inc.php');
// require_once('Common/Fun_FormatText.inc.php');
// require_once('Common/Lib/ArrTargets.inc.php');
// require_once('Common/Fun_Sessions.inc.php');

CheckTourSession(true);
checkACL(AclRobin, AclReadWrite);

$Team=($_REQUEST['team']??-1);

$IncludeJquery = true;
$JS_SCRIPT = array( phpVars2js(array(
        'ROOT_DIR'=>$CFG->ROOT_DIR,
        'MsgInitFinalGridsError'=>get_text('MsgInitFinalGridsError'),
        'MsgAttentionFinReset'=>get_text('MsgAttentionFinReset'),
        'CmdCancel' => get_text('CmdCancel'),
        'CmdConfirm' => get_text('Confirm', 'Tournament'),
        'Advanced' => get_text('Advanced'),
        'MsgForExpert' => get_text('MsgForExpert', 'Tournament')
    )),
    '<script type="text/javascript" src="./AbsRobin.js"></script>',
    '<link href="./RoundRobin.css" rel="stylesheet" type="text/css">',
    '<link href="./AbsRobin.css" rel="stylesheet" type="text/css">',
    );
include('Common/Templates/head.php');

echo '<table id="MainSOTable" class="Tabella">';
echo '<tr><th class="Title OneRow">'.get_text('ShootOff4Final').'&nbsp;<select id="TeamSelector" onchange="selectMain()">
	<option '.($Team==-1 ? ' selected="selected"' : '').'value="-1">---</option>
	<option '.($Team==0 ? ' selected="selected"' : '').'value="0">'.get_text('Individual').'</option>
	<option '.($Team==1 ? ' selected="selected"' : '').'value="1">'.get_text('Team').'</option>
	</select></th></tr>';

echo '<tbody id="tbody"></tbody>';
echo '</table>';
include('Common/Templates/tail.php');
