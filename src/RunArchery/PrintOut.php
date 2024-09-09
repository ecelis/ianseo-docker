<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
CheckTourSession(true);
require_once('Common/Fun_FormatText.inc.php');
checkACL(array(AclIndividuals, AclTeams), AclReadOnly);

$IncludeJquery = true;
$JS_SCRIPT=array(
	'<script src="PrintOut.js"></script>',
);

$PAGE_TITLE=get_text('PrintList','Tournament');

include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="4">' . get_text('PrintList','Tournament')  . '</th></tr>';
echo '<tr><th class="SubTitle">' . get_text('ResultsIndividual', 'RunArchery')  . '</th>';
// echo '<th class="SubTitle" colspan="2">' . get_text('CompleteResultBook')  . '</th>';
echo '<th class="SubTitle">' . get_text('ResultsTeam', 'RunArchery')  . '</th></tr>';

echo '<tr>';

// Individual Results
echo '<td class="Center" rowspan="2">';
echo '<div class="mt-2">';
$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ORDER BY EvProgr";
$RsInd = safe_r_sql($MySql);
$MySql = "SELECT EvCode, EvEventName FROM Events WHERE EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " and EvCodeParent='' ORDER BY EvProgr";
$RsTeam = safe_r_sql($MySql);

$Size=min(14, max(safe_num_rows($RsInd), safe_num_rows($RsTeam)))+1;

if(safe_num_rows($RsInd)>0) {
	echo '<select id="IndividualEvents" multiple="multiple" size="'.$Size.'">';
	echo '<option value=".">' . get_text('AllEvents')  . '</option>';
	while($MyRow=safe_fetch($RsInd))
		echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
	echo '</select>';
	safe_free_result($RsInd);
}
echo '</div>';
// echo '<div class="my-3"><input id="ShowOrisInd" type="checkbox" '.($_SESSION['ISORIS'] ? 'checked="checked"' : '').'>&nbsp;' . get_text('StdORIS','Tournament') . '</div>';
echo '<div class="my-2"><input type="checkbox" id="DetailedInd" class="mx-2">' . get_text('DetailedRank', 'RunArchery') . '</div></div>';
echo '<div class="my-2"><div class="Button" onclick="printResultInd()">' . get_text('ResultsIndividual', 'RunArchery') . '</div></div>';
echo '</td>';

// Complete Book
// echo '<td class="Center"  colspan="2">';
// echo '<a href="'.$CFG->ROOT_DIR.'Final/OrisCompleteBook.php" class="Link" target="OrisPrintOut">';
// echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris.gif" alt="' . get_text('CompleteResultBook') . '" border="0"></a><br>';
// echo '<a href="'.$CFG->ROOT_DIR.'Final/OrisCompleteBook.php" class="Link" target="OrisPrintOut">' . get_text('CompleteResultBook') . '</a>';
// echo '</br><a href="'.$CFG->ROOT_DIR.'Final/OrisCompleteBookChoose.php" class="Link">' . get_text('CompleteResultBookChoose') . '</a>';
// echo '</td>';

// Team Results
echo '<td class="Center" rowspan="2">';
echo '<div class="mt-2">';
if(safe_num_rows($RsTeam)>0) {
	echo '<select id="TeamEvents" multiple="multiple" size="'.$Size.'">';
	echo '<option value=".">' . get_text('AllEvents')  . '</option>';
	while($MyRow=safe_fetch($RsTeam))
		echo '<option value="' . $MyRow->EvCode . '">' . $MyRow->EvCode . ' - ' . get_text($MyRow->EvEventName,'','',true)  . '</option>';
	echo '</select>';
	safe_free_result($RsTeam);
}
echo '</div>';
// echo '<div class="my-3"><input id="ShowOrisTeam" type="checkbox" '.($_SESSION['ISORIS'] ? 'checked="checked"' : '').'>&nbsp;' . get_text('StdORIS','Tournament') . '</div>';
// echo '<div class="my-2"><input type="checkbox" id="DetailedTeam" class="mx-2">' . get_text('DetailedRank', 'RunArchery') . '</div></div>';
echo '<div class="my-2"><div class="Button" onclick="printResultTeam()">' . get_text('ResultsTeam', 'RunArchery') . '</div></div>';
echo '</td>';
echo '</tr>';

// echo '<tr>';
// // Medals List
// echo '<td class="Center">';
// echo '<a href="PDFMedalStanding.php" class="Link" target="PrintOut">';
// echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf_small.gif" alt="Medal Standing" border="0"></a>&nbsp;&nbsp;&nbsp;';
// echo '<a href="OrisMedalStanding.php" class="Link" target="OrisPrintOut">';
// echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
// echo '<a href="'.($_SESSION['ISORIS'] ? 'OrisMedalStanding.php' : 'PDFMedalStanding.php').'" class="Link" target="OrisPrintOut">' . get_text('MedalStanding') . '</a>';
// echo '</td>';
// // Medal Standing
// echo '<td class="Center">';
// echo '<a href="PDFMedalList.php" class="Link" target="PrintOut">';
// echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf_small.gif" alt="Medal Standing" border="0"></a>&nbsp;&nbsp;&nbsp;';
// echo '<a href="'.$CFG->ROOT_DIR.'Final/OrisMedalList.php" class="Link" target="OrisPrintOut">';
// echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdfOris_small.gif" title="' . get_text('StdORIS','Tournament') . '"  alt="' . get_text('StdORIS','Tournament') . '" border="0"></a><br>';
// echo '<a href="'.$CFG->ROOT_DIR.'Final/'.($_SESSION['ISORIS'] ? 'OrisMedalList.php' : 'PDFMedalList.php').'" class="Link" target="OrisPrintOutvvv">' . get_text('MedalList') . '</a>';
// echo '</td>';
// echo '</tr>';
echo '</table>';

include('Common/Templates/tail.php');
