<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);

$TeamEvent=isset($_REQUEST['Team']) ? $_REQUEST['Team'] : 0;
checkACL(($TeamEvent ? AclTeams : AclIndividuals), AclReadWrite);

require_once('Common/Lib/CommonLib.php');

$PreTeamEvent = isset($_REQUEST['Team']) ? $_REQUEST['Team'] : 0;
$PreEvent = isset($_REQUEST['d_Event']) ? $_REQUEST['d_Event'] : '';
$PreMatchno = isset($_REQUEST['d_Match']) ? $_REQUEST['d_Match'] : -1;
$PrePhase=-1;
if($PreMatchno>=0) {
	if($PreMatchno<2) {
		$PrePhase=0;
	} else {
		$PrePhase=pow(2, intval(log($PreMatchno/2, 2)));
	}
}

$PAGE_TITLE=get_text($TeamEvent ? 'TeamFinal':'IndFinal');
$IncludeJquery = true;
$JS_SCRIPT = array(
    '<script src="'.$CFG->ROOT_DIR.'Common/js/keypress-2.1.5.min.js"></script>',
    '<script src="'.$CFG->ROOT_DIR.'Final/Spotting.js"></script>',
    '<link href="'.$CFG->ROOT_DIR.'Final/Spotting.css" rel="stylesheet" type="text/css">',
    phpVars2js(
    	array(
    		'WebDir' => $CFG->ROOT_DIR,
    		'CompCode' => $_SESSION["TourCode"],
    		'TurnLiveOn' => get_text('LiveOn'),
    		'TurnLiveOff' => get_text('LiveOff'),
		    'PreTeamEvent' => $PreTeamEvent,
		    'PreEvent' => $PreEvent,
			'PreMatchno' => $PreMatchno,
			'PrePhase' => $PrePhase,
			'ConfirmIrmMsg' => get_text('ConfirmIrmMsg','Tournament'),
	    )
    ),
);

include('Common/Templates/head.php');

echo '<table class="Tabella" id="MatchSelector">';
    echo '<tr><th class="Title" colspan="6">'.get_text('MenuLM_Spotting').'</th></tr>';
    echo '<tr>
            <th colspan="2">'.get_text('Event').'</th>
            <th>'.get_text('Phase').'</th>
            <th>'.get_text('Match', 'Tournament').'</th>
            <th>'.get_text('Target').'</th>
            <th></th>
        </tr>';
    echo '<tr>
            <td class="Center"><select id="spotType" onchange="updateComboEvents();">
            	<option value="Ind" '. ($PreTeamEvent==0 ? ' selected="selected"' : '') .'>'.get_text('Individual').'</option>
            	<option value="Team" '. ($PreTeamEvent==1 ? ' selected="selected"' : '') .'>'.get_text('Team').'</option></select></td>
            <td class="Center"><select id="spotCode" onchange="updateComboPhases();"></select></td>
            <td class="Center"><select id="spotPhase" onchange="updateComboMatches();"></select></td>
            <td class="Center"><select id="spotMatch"></select></td>
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
    echo '<input disabled="disabled" type="button" id="confirmMatch" onclick="confirmMatch(this)" value="'.get_text('ConfirmMatch', 'Tournament').'"/>';
    echo '</td></tr>';
    echo '<tr><td colspan="3" class="Opponents CmdRow">';
    echo '<div style="display:flex;justify-content: space-between">
		<div><input type="checkbox" id="MatchAlternate" onclick="toggleAlternate(this)"/>'.get_text('AlternateMatch', 'Tournament').'</div>
		<div><input type="checkbox" id="ActivateKeys" onclick="toggleKeypress()" />'.get_text('KeyPress', 'Tournament').'</div>
		<div class="d-none" id="ClickMovesPositionDiv"><input type="checkbox" id="ClickMovesPosition" onclick="toggleClickMovesPosition(this)"/>'.get_text('ClickMovesPosition', 'Tournament').'</div>
		<div><input type="checkbox" id="MoveNext" checked="checked" onclick="checkClickMovesPosition()" />'.get_text('AutoMoveNext', 'Tournament').'</div>
		<div><input type="button" id="liveButton" value="" onclick="setLive()"/></div>
		<div id="buttonMove2Next"></div>
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
    '<div class="Legenda"><div class="value">X</div>: numpad_+, X, x</div>'.
    '<div class="Legenda"><div class="value">*</div>: *, numpad_*, D, d</div>'.
    '<div class="Legenda"><div class="value">[DEL]</div>: numpad_., [DEL], [ESC]</div>'.
    '<div class="Legenda"><div class="value">[--&gt;]</div>: numpad_/, [--&gt;], [TAB]</div>'.
    '<div class="Legenda"><div class="value">[&lt;--]</div>: [&lt;--], [SHIFT+TAB]</div>'.
    '</td></tr>';
echo '</table>';


include('Common/Templates/tail.php');
