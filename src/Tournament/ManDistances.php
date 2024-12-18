<?php
global $CFG;
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_FormatText.inc.php');
require_once('Common/Lib/CommonLib.php');

checkACL(AclCompetition, AclReadWrite);
CheckTourSession(true); // will print the crack error string if not inside a tournament!

$rs=safe_r_sql("SELECT ToType,ToNumDist AS TtNumDist
	FROM Tournament
	WHERE ToId={$_SESSION['TourId']}");
if(!safe_num_rows($rs)) {
	CD_redirect($CFG->ROOT_DIR);
}

$r=safe_fetch($rs);
$tourType=$r->ToType;
$numDist=$r->TtNumDist;

$rsDist=null;

$IncludeFA=true;
$IncludeJquery=true;
$PAGE_TITLE=get_text('ManDistances','Tournament');

$JS_SCRIPT = array(
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/ManDistances.js"></script>',
    '<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Tournament/ManDistancesSessions.js"></script>',
	phpVars2js(array(
		'StrConfirm'=>get_text('MsgAreYouSure'),
		'NumDist'=>$numDist,
		'TourType'=>$tourType,
		)),
    );

include('Common/Templates/head.php');

echo '<div style="margin:auto">';
echo '<table class="Tabella freeWidth mb-5">
	<tr><th class="Title" colspan="'.($numDist+3).'">'.(get_text('ManDistances','Tournament')).'</th></tr>
	<tr>
	    <th>'.(get_text('AvailableValues','Tournament')).'</th>
	    <th>'.(get_text('FilterOnDivCl','Tournament')).'</th>';
for ($i=1;$i<=$numDist;++$i) {
    echo '<th>.'.($i).'.</th>';
}
echo '<th></th>
    </tr>';

echo '<tr id="edit">
	<td id="categories"></td>
	<td class="Center"><input type="text" class="CheckDisabled" name="cl" size="12" maxlength="10" value=""></td>';
for ($i=1;$i<=$numDist;++$i) {
    echo '<td class="Center"><input type="text" class="CheckDisabled" name="td-'.($i).'" size="12" maxlength="10" value="" dist="'.$i.'"></td>';
}
echo '<td class="Center">
    <input type="button" name="command" class="CheckDisabled" value="'.(get_text('CmdOk')).'" onclick="save(this);">&nbsp;&nbsp;
    <input type="button" name="command" class="CheckDisabled" value="'.(get_text('CmdCancel')).'" onclick="resetInput(this)">
    </td>
    </tr>
    <tr class="Spacer"><td colspan="'.($numDist+3).'"></td></tr>
    <tbody id="tbody"></tbody>
    </table>';

// DISTANCE INFORMATION MANAGEMENT
// Based on SESSIONS!!!!
require_once('./ManDistancesSessions.php');

echo '</div>
    <div id="idOutput"></div>';

include('Common/Templates/tail.php');

