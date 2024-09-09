<?php
require_once(dirname(dirname(__DIR__)) . '/config.php');
CheckTourSession(true);
// require_once('Common/Fun_FormatText.inc.php');
checkACL(array(AclRobin), AclReadOnly);

$IncludeJquery = true;
$JS_SCRIPT=array(
	'<script src="PrintOut.js"></script>',
	);

$PAGE_TITLE=get_text('PrintList','Tournament');

include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="2">' . get_text('PrintList','Tournament')  . '</th></tr>';
echo '<tr>
	<th class="SubTitle w-50">' . get_text('BracketsInd')  . '</th>
	<th class="SubTitle w-50">' . get_text('BracketsSq')  . '</th>
	</tr>';

//Filtri per l' Individuale
echo '<tr class="Divider"><td  colspan="2"></td></tr>';
echo '<tr>';
echo '<td class="Center">';

$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvElimType=5 and EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 and EvCodeParent='' ORDER BY EvProgr";
$Rs = safe_r_sql($MySql);
if(safe_num_rows($Rs)>0) {
	echo '<form id="PrnParametersInd" action="'.$CFG->ROOT_DIR.'Final/Individual/' . ($_SESSION['ISORIS'] ? 'Oris' : 'Prn') . 'Individual.php" method="get" target="PrintOutWorking">';
	echo '<table class="Tabella w-80 mt-3">';
	echo '<tr>
		<td class="Right w-50">';
			echo '<select id="IndividualEvents" name="Event[]" multiple="multiple" size="10">';
			echo '<option value=".">' . get_text('AllEvents')  . '</option>';
			while($MyRow=safe_fetch($Rs))
				echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
			echo '</select>';
			safe_free_result($Rs);
	echo '</td>
		<td class="Left w-50">';
	echo '<div><input name="IncRankings" type="checkbox" value="1" checked>&nbsp;' . get_text('Rankings') . '</div>';
	echo '<div><input name="IncBrackets" type="checkbox" value="1" checked onclick="CheckIfOrisBrackets(true);">&nbsp;' . get_text('Brackets') . '</div>';
	echo '<div><input name="ShowTargetNo" type="checkbox" value="1" checked>&nbsp;' . get_text('Target') . '</div>';
	echo '<div><input name="ShowSchedule" type="checkbox" value="1" checked>&nbsp;' . get_text('ManFinScheduleInd') . '</div>';
	echo '<div><input name="ShowSetArrows" type="checkbox" value="1">&nbsp;' . get_text('ShowSetEnds', 'Tournament') . '</div>';
	echo '<div><input name="ShowOris" type="checkbox" value="1" '.($_SESSION['ISORIS'] ? ' checked="checked"' : '').'>&nbsp;' . get_text('StdORIS','Tournament') . '</div>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</form>';
	echo '<div class="Center my-3"><div class="Button" onclick="showPrintout(this, 0)">' . get_text('BrakRank') . '</div></div>';
}
echo '</td>';

//Filtri per a Squadre
echo '<td class="Center">';
$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvElimType=5 and EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvFinalFirstPhase!=0 and EvCodeParent='' ORDER BY EvProgr";
$Rs = safe_r_sql($MySql);
if(safe_num_rows($Rs)>0) {
	echo '<form class="Center" id="PrnParametersTeam" action="'.$CFG->ROOT_DIR.'Final/Team/' . ($_SESSION['ISORIS'] ? 'Oris' : 'Prn') . 'Team.php" method="get" target="PrintOutWorking">';
	echo '<table class="Tabella w-80 mt-3">';
	echo '<tr>';
	echo '<td class="Right w-50">';
		echo '<select id="TeamEvents" name="Event[]" multiple="multiple" size="10">';
		echo '<option value=".">' . get_text('AllEvents')  . '</option>';
		while($MyRow=safe_fetch($Rs))
			echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
		echo '</select>';
		safe_free_result($Rs);
	echo '</td><td class="Left w-50">';
	echo '<input name="IncRankings" type="checkbox" value="1" checked>&nbsp;' . get_text('Rankings') . '<br>';
	echo '<input name="IncBrackets" type="checkbox" value="1" checked>&nbsp;' . get_text('Brackets') . '<br>';
	echo '<input name="ShowTargetNo" type="checkbox" value="1" checked>&nbsp;' . get_text('Target') . '<br>';
	echo '<input name="ShowSchedule" type="checkbox" value="1" checked>&nbsp;' . get_text('ManFinScheduleTeam') . '<br>';
	echo '<input name="ShowSetArrows" type="checkbox" value="1">&nbsp;' . get_text('ShowSetEnds', 'Tournament') . '<br>';
	echo '<input name="ShowOris" type="checkbox" value="1" onClick="javascript:CheckIfOris(\'ShowOrisTeam\',\'PrnParametersTeam\',false);"'.($_SESSION['ISORIS'] ? ' checked="checked"' : '').'>&nbsp;' . get_text('StdORIS','Tournament') . '<br>';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</form>';
	echo '<div class="Center my-3"><div class="Button" onclick="showPrintout(this, 1)">' . get_text('BrakRank') . '</div></div>';
}
	echo '</td></tr>';
	echo '</table>';

	include('Common/Templates/tail.php');
?>
