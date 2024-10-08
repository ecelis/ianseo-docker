<?php
require_once(dirname(dirname(dirname(__DIR__))) . '/config.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Modules.php');
CheckTourSession(true);
checkACL(AclQualification, AclReadOnly);

$RowTour=NULL;

$Select
	= "SELECT ToCategory, ToId,ToNumDist AS TtNumDist, ToCategory&12 as IsTeam3D "
	. "FROM Tournament  "
	. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']) . " ";

$RsTour=safe_r_sql($Select);
if (safe_num_rows($RsTour)==1){
	$RowTour=safe_fetch($RsTour);
	safe_free_result($RsTour);
}

$IncludeJquery = true;
$JS_SCRIPT=array(
	phpVars2js(array('MsgAreYouSure' => get_text('MsgAreYouSure'), "nDist"=> $RowTour->TtNumDist)),
//	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
//	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
//	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_AJAX_index.js"></script>',
//	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_JS.js"></script>',
//	'<script type="text/javascript">
//		function DisableChkOther(NoDist, NumDist) {
//			if(NoDist) {
//				if(document.getElementById(\'ChkDist0\').checked) {
//					for(i=1; i<=NumDist; i++)
//						document.getElementById(\'ChkDist\'+i).checked=false;
//					document.getElementById(\'ScoreFilled\').checked=false;
//					document.getElementById(\'ScoreFilled\').disabled=true;
//				}
//			} else {
//				for(i=1; i<=NumDist; i++) {
//					if(document.getElementById(\'ChkDist\'+i).checked)
//						document.getElementById(\'ChkDist0\').checked=false;
//					document.getElementById(\'ScoreFilled\').disabled=false;
//				}
//			}
//		}
//
//		function CheckAction() {
//			var action="PDFScore.php";
//			if(document.getElementById("ScoreCollector") && document.getElementById("ScoreCollector").checked) {
//				action="PDFScoreCollect.php";
//				if(document.getElementById("ScoreCollector6").checked) action="PDFScoreCollect.php?arr=6";
//			}
//			if(document.getElementById("ScoreLabels").checked) action="PrnLabels.php";
//			if(document.getElementById("BigNames").checked) action="PDFBigNames.php";
//			document.getElementById("PrnParameters").action=action;
//			return true;
//		}
//
//		function manageDistances(doEnable) {
//			for(i=1; i<=nDist; i++) {
//				document.getElementById("ChkDist"+i).disabled = (!doEnable);
//				if(!doEnable && document.getElementById("ChkDist"+i).checked) {
//					document.getElementById("ChkDist"+i).checked = false;
//				}
//			}
//			if(!doEnable) {
//				document.getElementById("ChkDist0").checked = true;
//			}
//		}
//	</script>',
	'<style>#x_Coalesce_div {display:inline-block;margin-left:2em;vertical-align:middle;text-align: left;}#x_Coalesce_div div {font-size:0.8em}</style>'
	);

$PAGE_TITLE=get_text('PrintScore', 'Tournament');

include('Common/Templates/head.php');

echo '<form id="PrnParameters" action="./PDFScore.php" method="post" target="PrintOut">';
echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="2">' . get_text('PrintScore','Tournament')  . '</th></tr>';
echo '<tr><th class="SubTitle" colspan="2">' . get_text('ScorePrintMode','Tournament')  . '</th></tr>';
//Parametri
echo '<tr>';
//Tipo di Score
echo '<td width="50%"><br>';
echo '<input name="ScoreDraw" type="radio" value="Complete" checked onClick="manageDistances(true);">&nbsp;' . get_text('ScoreComplete','Tournament') . '<br>';
//echo '<input name="ScoreDraw" type="radio" value="CompleteTotals" onClick="manageDistances(true);">&nbsp;' . get_text('ScoreCompleteTotals','Tournament') . '<br>';
//echo '<input name="ScoreDraw" type="radio" value="Data" onClick="manageDistances(true);">&nbsp;' . get_text('ScoreData','Tournament') . '<br>';
//echo '<input name="ScoreDraw" type="radio" value="TargetNo" onClick="manageDistances(true);">&nbsp;' . get_text('ScoreTargetNo','Tournament') . '<br>';
echo '<input name="ScoreDraw" type="radio" value="Draw" onClick="manageDistances(true);">&nbsp;' . get_text('ScoreDrawing') . '<br>';
//echo '<input name="ScoreDraw" type="radio" value="FourScoresNFAA" onClick="manageDistances(true);">&nbsp;' . get_text('FourScoresNFAA','Tournament') . '<br>';
//echo '<input name="ScoreDraw" type="radio" value="HorScore" onClick="manageDistances(true);">&nbsp;' . get_text('HorScore','Tournament') . '<br>';
//echo '<input name="ScoreDraw" type="radio" value="HorScoreAllDist" onClick="manageDistances(true);">&nbsp;' . get_text('HorScoreAllDist','Tournament') . '<br>';
////echo '<input name="ScoreDraw" type="radio" value="VertScoreAllDist" onClick="manageDistances(true);">&nbsp;' . get_text('VertScoreAllDist','Tournament') . '<br>';
//echo '<input name="TourField3D" type="checkbox" value="'.($RowTour->IsTeam3D==0 ? '': ($RowTour->IsTeam3D==4 ? 'FIELD'  : '3D')).'" onclick="$(\'#ScoreCollectorDiv\').toggleClass(\'d-none\', this.checked)" '.($RowTour->IsTeam3D==0?'':' checked="checked"').'>&nbsp;'.get_text('FieldScorecard', 'Tournament').'<br/>';
//echo '<div id="ScoreCollectorDiv" class="'.($RowTour->IsTeam3D==0 ? '' : 'd-none').'">';
//echo '<input name="ScoreCollector" id="ScoreCollector" type="checkbox" value="Collector">&nbsp;' . get_text('ScoreCollector', 'Tournament') ;
//echo '<input name="ScoreCollectorArrows" id="ScoreCollector6" type="radio" value="6" checked="checked">6 - ';
//echo '<input name="ScoreCollectorArrows" id="ScoreCollector3" type="radio" value="3">3<br>';
echo '</div>';
echo '</td>';
//Header e Immagini
echo '<td class="w-50"><br>';
echo '<input name="ScoreHeader" type="checkbox" value="1" checked>&nbsp;' . get_text('ScoreTournament','Tournament') . '<br>';
echo '<input name="ScoreLogos" type="checkbox" value="1" checked>&nbsp;' . get_text('ScoreLogos','Tournament') . '<br>';
echo '<input name="ScoreFlags" type="checkbox" value="1" checked>&nbsp;' . get_text('ScoreFlags','Tournament') . '<br>';
//echo '<input name="GetArcInfo" type="checkbox" value="1" >&nbsp;' . get_text('GetArcInfo','Tournament') . '<br>';
if(module_exists("Barcodes")) {
	echo '<input name="ScoreBarcode" type="checkbox" checked value="1" >&nbsp;' . get_text('ScoreBarcode','Tournament') . '<br>';
}
if(getModuleParameter('ISK-NG', 'UsePersonalDevices', '')) {
	echo '<input name="ScoreQrPersonal" type="checkbox" checked value="1" >&nbsp;' . get_text('UsePersonalDevices-Print','Api') . '<br>';
}

foreach(AvailableApis() as $Api) {
	if(!($tmp=getModuleParameter($Api, 'Mode')) || strpos($tmp,'live') !== false) {
		continue;
	}
	if(strpos($tmp,'ng-') === 0) {
		$Api.= '-NG';
	}
	echo '<input name="QRCode[]" type="checkbox" '.(strpos($tmp,'pro')!== false ? '' : 'checked="checked"').' value="'.$Api.'" >&nbsp;' . get_text($Api.'-QRCode','Api') . '<br>';
}
echo '</td>';
echo '</tr>';


$ComboSes='';
$TxtFrom='';
$TxtTo='';
$ComboDist='';
$ChkG='';
$ChkX='';
if($RowTour != NULL)
{
//Sessioni
	$sessions=GetSessions('Q');
	echo '<tr><th class="SubTitle" colspan="2">' . get_text('Session')  . '</th></tr>';
	echo '<tr>';
	echo '<td colspan="2" align="Center"><br>';
	echo '<input type="hidden" name="chk_BlockAutoSave" id="chk_BlockAutoSave" value="1">';
	echo get_text('Session') . '&nbsp;<select name="x_Session" id="x_Session" onChange="SelectSession_JSON(this)">';
	echo '<option value="-1">---</option>';
	foreach ($sessions as $s)
	{
		echo '<option value="' . $s->SesOrder . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$s->SesOrder ? ' selected' : '') . '>' . $s->Descr . '</option>';
	}
	echo '</select>';

	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('From','Tournament') . '&nbsp;<input type="text" name="x_From" id="x_From" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_From']) ? $_REQUEST['x_From'] : '') . '">';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo  get_text('To','Tournament') . '&nbsp;<input type="text" name="x_To" id="x_To" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_To']) ? $_REQUEST['x_To'] : '') . '">';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<input id="x_noEmpty" name="noEmpty" type="checkbox" value="1">' . get_text('StartlistSessionNoEmpty', 'Tournament');
	echo '<div id="x_Coalesce_div"></div>';
// 		if($RowTour->ToCategory==8) {
// 			// 3D => 2 Arrows score!;
// 			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
// 			echo '<input id="TwoArrows" name="TwoArrows" type="checkbox">2 ' . get_text('Arrows', 'Tournament');
// 		}
	echo '</td>';
	echo '</tr>';
//Distanze
//	echo '<tr><th class="SubTitle" colspan="2">' . get_text('Distance','Tournament')  . '</th></tr>';
//	echo '<tr>';
//	echo '<td colspan="2" align="Center"><br>';
////	echo '<input name="ScoreDist[]" type="checkbox" value="0" checked id="ChkDist0" onClick="javascript: DisableChkOther(true, ' . $RowTour->TtNumDist . ')">&nbsp;' . get_text('NoDistance','Tournament') . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
//	for($i=1; $i<=$RowTour->TtNumDist; $i++)
//		echo '<input name="ScoreDist[]" type="checkbox" value="' . $i . '" checked id="ChkDist' . $i . '"  onClick="javascript: DisableChkOther(false, ' . $RowTour->TtNumDist . ')">&nbsp;' . $i . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
//	echo '</td>';
//	echo '</tr>';
	echo '<tr>';
	echo '<td colspan="2" align="Center">';
	echo '<input id="ScoreFilled" name="ScoreFilled" type="checkbox" value="1">' . get_text('ScoreFilled');
	echo '</td>';
	echo '</tr>';
}
//echo '<tr>';
//echo '<td colspan="2" align="Center">';
//echo '<input type="checkbox" id="ScoreLabels" name="ScoreLabels">' . get_text('PrintLabels','Tournament') . '';
//echo '&nbsp;&nbsp;<input type="checkbox" id="BigNames">' . get_text('PrintNames','Tournament') . '';
//echo '</td>';
//echo '</tr>';

echo '<tr>';
echo '<td colspan="2" align="Center"><br>';
echo '<input type="submit" value="' . get_text('PrintScore','Tournament') . '"><br/>&nbsp;';
if($_SESSION['TourLocSubRule']=='SetFrBouquet') {
    echo '<br/><input type="submit" name="Marmot" value="Marmot">';
}
echo '</td>';
echo '</tr>';
echo '</table>';
echo '</form>';
print '<br/>';

//Bigliettini
//print '<form name="frmTick" method="post" action="PrnGetScore.php" target="PrintOut">';
//	print '<input type="hidden" name ="x_Session" id="xx_Session" value="">';
//	print '<input type="hidden" name="x_From" id="xx_From" value="">';
//	print '<input type="hidden" name="x_To" id="xx_To" value="">';
//	print '<input type="hidden" name="noEmpty" id="xx_noEmpty" value="">';
//	print '<table class="Tabella">';
//		echo '<tr><th class="SubTitle" colspan="2">' . get_text('TicketGetScore', 'Tournament')  . '</th></tr>';
//		echo '<tr>';
//			echo '<td colspan="2" align="Center"><br>';
//				//echo '<a href="PrnGetScore.php" target="PrintOut" class="Link">' .  get_text('Print', 'Tournament') . '</a>&nbsp;';
//				print '<input type="button" onclick="submitTicket();" value="' . get_text('Print', 'Tournament') .'">';
//			echo '</td>';
//		echo '</tr>';
//	print '</table>';
//print '</form>';


include('Common/Templates/tail.php');
