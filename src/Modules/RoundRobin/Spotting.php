<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
checkACL(AclRobin, AclReadWrite);

require_once('Common/Lib/CommonLib.php');



$Team=intval($_REQUEST['team'] ?? 0);
$Event=($_REQUEST['event']??'');
$Level=($_REQUEST['level']??'');
$Group=($_REQUEST['group']??'');
$Round=($_REQUEST['round']??'');
$Match=($_REQUEST['match']??'');

$PAGE_TITLE=get_text('R-Session', 'Tournament');
$IncludeJquery = true;

$JS_SCRIPT = array(
    '<script src="'.$CFG->ROOT_DIR.'Common/js/keypress-2.1.5.min.js"></script>',
    '<script src="'.$CFG->ROOT_DIR.'Modules/RoundRobin/Spotting.js"></script>',
    '<link href="'.$CFG->ROOT_DIR.'Modules/RoundRobin/Spotting.css" rel="stylesheet" type="text/css">',
    phpVars2js(
    	array(
    		'WebDir' => $CFG->ROOT_DIR,
    		'CompCode' => $_SESSION["TourCode"],
    		'TurnLiveOn' => get_text('LiveOn'),
    		'TurnLiveOff' => get_text('LiveOff'),
		    'PreTeam' => $Team,
		    'PreEvent' => $Event,
			'PreLevel' => $Level,
			'PreGroup' => $Group,
			'PreRound' => $Round,
			'PreMatch' => $Match,
			'ConfirmIrmMsg' => get_text('ConfirmIrmMsg','Tournament'),
	    )
    ),
);

include('Common/Templates/head' . (isset($_REQUEST["hideMenu"]) ? '-min': '') . '.php');

echo '<table class="Tabella" id="MatchSelector">';
    echo '<tr><th class="Title" colspan="9">'.get_text('MenuLM_Spotting').'</th></tr>';
    echo '<tr>
            <th colspan="2">'.get_text('Event').'</th>
            <th>'.get_text('Level', 'RoundRobin').'</th>
            <th>'.get_text('Round', 'RoundRobin').'</th>
            <th>'.get_text('Group', 'RoundRobin').'</th>
            <th>'.get_text('Match', 'Tournament').'</th>
            <th class="SwapOpponents d-none">'.get_text('SwapOpponents', 'Tournament').'</th>
            <th>'.get_text('Target').'</th>
            <th></th>
        </tr>';
    echo '<tr>
            <td class="Center"><select id="spotTeam" onchange="updateComboEvent();">
            	<option value="0" '. ($Team==0 ? ' selected="selected"' : '') .'>'.get_text('Individual').'</option>
            	<option value="1" '. ($Team==1 ? ' selected="selected"' : '') .'>'.get_text('Team').'</option></select></td>
            <td class="Center"><select id="spotEvent" onchange="updateComboLevel();"></select></td>
            <td class="Center"><select id="spotLevel" onchange="updateComboGroup();"></select></td>
            <td class="Center"><select id="spotRound" onchange="updateComboMatch();"></select></td>
            <td class="Center"><select id="spotGroup" onchange="updateComboMatch();"></select></td>
            <td class="Center"><select id="spotMatch"></select></td>
            <td class="Center SwapOpponents d-none"><input type="checkbox" id="swapOpponents" onclick="swapOpponents()" /></td>
            <td class="Center"><input type="checkbox" id="spotTarget" onclick="toggleTarget()" /></td>
            <td class="Center"><input type="button" value="'.get_text('CmdOk').'" onclick="buildScorecard()"></td>
        </tr>';
echo '</table>';

$IrmStatus='<option value="0">' . get_text('IrmStatus','Tournament') . '</option>';
$q=safe_r_sql("select * from IrmTypes where IrmId>0 order by IrmId");
while($r=safe_fetch($q)) {
	$IrmStatus.='<option value="'.$r->IrmId.'">' . $r->IrmType . ' - ' . get_text($r->IrmType,'Tournament') . '</option>';
}



echo '<table class="Tabella Hiddens" id="Spotting">
	<tr>
		<td class="Opponents OpponentL OppTitle"><span id="OpponentNameL">Name Left</span><select id="IrmSelectL" onchange="updateIrm(this)" initial="0">'.$IrmStatus.'</select></td>
		<td class="Target Hidden" id="Target" rowspan="2"><svg></svg></td>
		<td class="Opponents OpponentR OppTitle"><select id="IrmSelectR" onchange="updateIrm(this)" initial="0">'.$IrmStatus.'</select><span id="OpponentNameR">Name Right</span></td>
	</tr>
	<tr>
		<td class="Opponents Scores" id="ScorecardL">Scorecard Left</td>
		<td class="Opponents Scores" id="ScorecardR">Scorecard Right</td>
	</tr>
		';

if(!empty($GoBack)) {
	echo '<tr><td colspan="3" class="Opponents CmdRow"><div class="SpotBackButton" onclick="document.location.href=\''.$GoBack.'\'" >'.get_text('BackBarCodeCheck','Tournament').'</a></div></td></tr>';
} else {
    echo '<tr><td colspan="3" class="Opponents CmdRow">';
    echo '<div class="btn btn-end d-none my-2" id="confirmEnd" onclick="confirmEnd(this)">'.get_text('ConfirmEnd', 'Tournament').'</div>';
    echo '<div class="btn btn-match d-none my-2" id="confirmMatch" onclick="confirmMatch(this)">'.get_text('ConfirmMatch', 'Tournament').'</div>';
    echo '</td></tr>';
    echo '<tr><td colspan="3" class="Opponents CmdRow">';
    echo '<div style="display:flex;justify-content: space-between">
		<div><input type="checkbox" id="MatchAlternate" onclick="toggleAlternate(this)"/>'.get_text('AlternateMatch', 'Tournament').'</div>
		<div><input type="checkbox" id="ActivateKeys" onclick="toggleKeypress()" />'.get_text('KeyPress', 'Tournament').'</div>
		<div><input type="checkbox" id="MoveNext" checked="checked" />'.get_text('AutoMoveNext', 'Tournament').'</div>
		<div><input type="button" id="liveButton" value="" onclick="setLive()"/></div>
		<div id="buttonMove2Next"><input type="button" id="moveWinner" onclick="moveToNextPhase(this)" value="'.get_text('MoveWinner2NextPhase','Tournament').'"></div>
		</div>';
    echo ''.
		'';
    echo '';
    echo '</td></tr>';

}
echo '<tr id="keypadLegenda" class="Hidden"><td colspan="3">'.
    '<div class="Legenda"><div class="value">0</div>: 0, numpad_0, m, M</div>'.
    '<div class="Legenda"><div class="value">1 - 9</div>: 1...9, numpad_1 ... numpad_9</div>'.
    '<div class="Legenda"><div class="value">10</div>: numpad_-, T, t</div>'.
    '<div class="Legenda"><div class="value">11</div>: E, e</div>'.
    '<div class="Legenda"><div class="value">12</div>: F, f</div>'.
    '<div class="Legenda"><div class="value">X</div>: numpad_+, X, x</div>'.
    '<div class="Legenda"><div class="value">*</div>: *, numpad_*, D, d</div>'.
    '<div class="Legenda"><div class="value">[DEL]</div>: numpad_., [DEL], [ESC]</div>'.
    '<div class="Legenda"><div class="value">[--&gt;]</div>: numpad_/, [--&gt;], [TAB]</div>'.
    '<div class="Legenda"><div class="value">[&lt;--]</div>: [&lt;--], [SHIFT+TAB]</div>'.
    '</td></tr>';
echo '</table>';


include('Common/Templates/tail' . (isset($_REQUEST["hideMenu"]) ? '-min' : '') . '.php');
