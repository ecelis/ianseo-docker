<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/ScoreEditor/Score.class.php');
CheckTourSession(true);
checkACL(AclQualification, AclReadWrite);

require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Various.inc.php');

$IncludeJquery = true;
$JS_SCRIPT=array(
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/ajax/ObjXMLHttpRequest.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Common/js/Fun_JS.inc.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/WriteScoreCard.js"></script>',
	'<script type="text/javascript" src="'.$CFG->ROOT_DIR.'Qualification/Fun_AJAX_index.js"></script>',
	phpVars2js(array(
		'CmdPostUpdate'=>get_text('CmdPostUpdate'),
		'PostUpdating'=>get_text('PostUpdating'),
		'PostUpdateEnd'=>get_text('PostUpdateEnd'),
		'RootDir'=>$CFG->ROOT_DIR.'Qualification/',
		'MsgAreYouSure' => get_text('MsgAreYouSure'),
	)),
	'<style>table.Tabella tr.out-of-range {background-color:#cccccc}</style>',
);

$PAGE_TITLE=get_text('QualRound');

$ONLOAD=(isset($_REQUEST['chk_PostUpdate']) && $_REQUEST['chk_PostUpdate']==1 ? ' onLoad="ManagePostUpdateArrow(true)"' : '');

include('Common/Templates/head.php');

$Select
	= "SELECT ToId,ToNumSession,ToGolds AS TtGolds,ToXNine AS TtXNine, ToNumEnds AS TtNumEnds, ToNumDist AS TtNumDist, (ToMaxDistScore/ToGolds) AS MaxArrows "
	. "FROM Tournament "
	. "WHERE ToId=" . StrSafe_DB($_SESSION['TourId']);
$RsTour=safe_r_sql($Select);

$RowTour=NULL;
$ComboSes='';
$ComboDist='';
$TxtTarget='';


if (safe_num_rows($RsTour)==1) {
	$RowTour=safe_fetch($RsTour);

	$ComboSes = '<select name="x_Session" id="x_Session" onChange="SelectSession();">';
	$ComboSes.= '<option value="-1">---</option>';

	$ComboDist = '<select name="x_Dist" id="x_Dist">';
	$ComboDist.= '<option value="-1">---</option>';

	for ($i=1;$i<=$RowTour->ToNumSession;++$i)
		$ComboSes.= '<option value="' . $i . '"' . (isset($_REQUEST['x_Session']) && $_REQUEST['x_Session']==$i ? ' selected' : '') . '>' . $i . '</option>';
	$ComboSes.= '</select>';

	for ($i=1;$i<=$RowTour->TtNumDist;++$i)
		$ComboDist.= '<option value="' . $i . '"' . (isset($_REQUEST['x_Dist']) && $_REQUEST['x_Dist']==$i ? ' selected' : '') . '>' . $i . '</option>';
	$ComboDist.= '</select>';

	$TxtTarget = '<input type="text" name="x_Target" id="x_Target" size="5" maxlength="' . (TargetNoPadding +1) . '" value="' . (isset($_REQUEST['x_Target']) ? $_REQUEST['x_Target'] : '') . '">';
?>
<?php print prepareModalMask('PostUpdateMask','<div align="center" style="font-size: 20px; font-weight: bold;"><br/><br/><br/><br/><br/>'.get_text('PostUpdating').'</div>');?>

<form name="FrmParam" method="POST" action="">
<input type="hidden" name="xxx" id="Command">
<input type="hidden" name="Command" value="OK">
<table class="Tabella">
<tr><th class="Title" colspan="5"><?php print get_text('QualRound');?></th></tr>
<tr><th class="SubTitle" colspan="5"><?php print get_text('SingleArrow','Tournament');?></th></tr>
<tr class="Divider"><td colspan="5"></td></tr>
<tr>
<th width="5%"><?php print get_text('Session');?></th>
<th width="5%"><?php echo get_text('Distance','Tournament') ?></th>
<th width="5%"><?php echo get_text('Target') ?></th>
<th width="5%">&nbsp;</th>
<th>&nbsp;</th>
</tr>
<tr>
<td class="Center"><?php print $ComboSes; ?></td>
<td class="Center"><?php print $ComboDist; ?></td>
<td class="Center"><?php print $TxtTarget; ?></td>
<td><input type="submit" value="<?php print get_text('CmdOk');?>"></td>
<td>
<a class="Link" href="javascript:MakeTeams();"><?php print get_text('MakeTeams','Tournament'); ?></a>&nbsp;-&nbsp;
<a class="Link" href="javascript:CalcRank(true);"><?php print get_text('CalcRankDist','Tournament'); ?></a>&nbsp;-&nbsp;
<a class="Link" href="javascript:CalcRank(false);"><?php print get_text('CalcRank','Tournament'); ?></a>
</td>
</tr>
<tr class="Divider"><td colspan="8"></td></tr>
<tr><td colspan="8" class="Bold">
	<input type="checkbox" name="chk_PostUpdate" id="chk_PostUpdate" value="1"
		<?php print (isset($_REQUEST['chk_PostUpdate']) && $_REQUEST['chk_PostUpdate']==1 ? ' checked' : '');?>
		onclick="ManagePostUpdateArrow();"
	/><?php print get_text('CmdPostUpdate');?>
</td></tr>
<tr class="Divider"><td colspan="8"><span id="idPostUpdateMessage"></span></td></tr>
</table>
</form>
<br>
<?php

if (isset($_REQUEST['Command']) && $_REQUEST['Command']=='OK' && $_REQUEST['x_Session']!=-1 && $_REQUEST['x_Dist']!=-1) {
	if(!preg_match("/^[0-9]{1," . TargetNoPadding . "}[A-Z]{1}$/i",$_REQUEST['x_Target'])) {
		// shows the whole target
		$Select = "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, CoName, (EnStatus <=1) AS EnValid,EnStatus,
			QuTargetNo, SUBSTRING(QuTargetNo,2) AS Target,(QuTarget-1) as TgtOffset,
			QuD" . $_REQUEST['x_Dist'] . "Score AS SelScore,QuD" . $_REQUEST['x_Dist'] . "Hits AS SelHits,QuD" . $_REQUEST['x_Dist'] . "Gold AS SelGold,QuD" . $_REQUEST['x_Dist'] . "Xnine AS SelXNine, ";
		for ($i=1; $i<=$RowTour->TtNumDist; $i++) {
			$Select .= "QuD" . $i . "Score, QuD" . $i . "Hits, QuD" . $i . "Gold, QuD" . $i . "Xnine, ";
		}
		$Select .= "QuScore, QuGold, QuXnine, QuD" . $_REQUEST['x_Dist'] . "ArrowString AS ArrowString, 
			ToId,ToType,ToNumDist AS TtNumDist, ToCategory, IF(TfGolds!='',TfGolds,ToGolds) AS GoldLabel, IF(TfXNine!='',TfXNine,ToXNine) AS XNineLabel
			FROM Entries 
			INNER JOIN Qualifications ON EnId=QuId 
			INNER JOIN Countries ON EnCountry=CoId 
			RIGHT JOIN AvailableTarget ON QuTargetNo=AtTargetNo AND AtTournament=EnTournament
			INNER JOIN Tournament ON EnTournament=ToId
            LEFT JOIN TargetFaces ON TfTournament=EnTournament and EnTargetFace=TfId
			WHERE EnAthlete=1 AND QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " AND ToId=" . StrSafe_DB($_SESSION['TourId']) . "
			AND QuTargetNo LIKE " . StrSafe_DB($_REQUEST['x_Session'] . str_pad($_REQUEST['x_Target'],TargetNoPadding,'0',STR_PAD_LEFT) . "_");
		$Rs=safe_r_sql($Select);
		echo '<table class="Tabella">';
		echo '<tr>';
		echo '<th>' . get_text('Target') . '</th>';
		echo '<th>' . get_text('Code','Tournament') . '</th>';
		echo '<th>' . get_text('Archer') . '</th>';
		echo '<th>' . get_text('Country') . '</th>';
		echo '<th>' . get_text('Div') . '</th>';
		echo '<th>' . get_text('Cl') . '</th>';
		echo '<th>' . get_text('Total') . '</th>';
		echo '<th>' . get_text('DistanceShort', 'Tournament') . '</th>';
		echo '<th>' . $RowTour->TtGolds . '</th>';
		echo '<th>' . $RowTour->TtXNine . '</th>';
		echo '</tr>';
		while ($MyRow=safe_fetch($Rs)) {
			echo '<tr onClick="document.getElementById(\'x_Target\').value=\'' .$MyRow->Target . '\';document.FrmParam.submit();">';
			echo '<td>' . $MyRow->Target . '</td>';
			echo '<td>' . $MyRow->EnCode . '</td>';
			echo '<td>' . $MyRow->EnFirstName . ' ' . $MyRow->EnName . '</td>';
			echo '<td>' . $MyRow->CoCode . '-' . $MyRow->CoName . '</td>';
			echo '<td>' . $MyRow->EnDivision . '</td>';
			echo '<td>' . $MyRow->EnClass . '</td>';
			echo '<td>' . $MyRow->QuScore . '</td>';
			echo '<td>' . $MyRow->SelScore . '</td>';
			echo '<td>' . $MyRow->SelGold . '</td>';
			echo '<td>' . $MyRow->SelXNine . '</td>';
			echo '</tr>';

		}
		echo '</table>';

	} else {
		// show the single scorecard
		$Dist=intval($_REQUEST['x_Dist']);
		$tmpSel='';
		for ($i=1; $i<=$RowTour->TtNumDist; $i++) {
			$tmpSel .= "QuD" . $i . "Score, QuD" . $i . "Hits, QuD" . $i . "Gold, QuD" . $i . "Xnine, ";
		}
		$Select = "SELECT EnId,EnCode,EnName,EnFirstName,EnTournament,EnDivision,EnClass,EnCountry,CoCode, (EnStatus <=1) AS EnValid,EnStatus,
				QuTargetNo, QuTarget, SUBSTRING(QuTargetNo,2) AS Target, (QuTarget-1) as TgtOffset,
				QuD{$Dist}Score AS SelScore,QuD{$Dist}Hits AS SelHits,QuD{$Dist}Gold AS SelGold,QuD{$Dist}Xnine AS SelXNine, 
				$tmpSel
				QuScore, QuGold, QuXnine, QuD{$Dist}ArrowString AS ArrowString, ToId, ToType, ToNumDist AS TtNumDist, 
			    ToCategory, IF(TfGolds!='',TfGolds,ToGolds) AS GoldLabel, IF(TfXNine!='',TfXNine,ToXNine) AS XNineLabel,
			    coalesce(DiEnds, ToNumEnds) as NumEnds, coalesce(DiArrows, ToMaxDistScore/ToGolds) as NumArrows, coalesce(DiScoringEnds, 0) as ScoringEnds,
			    coalesce(DiScoringOffset,0) as ScoringOffset, ToCategory in (4,8) as IsField3D
			FROM Entries 
			INNER JOIN Qualifications ON EnId=QuId 
			INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament 
			RIGHT JOIN AvailableTarget ON QuTargetNo=AtTargetNo AND AtTournament=EnTournament
			INNER JOIN Tournament ON EnTournament=ToId
			left join DistanceInformation on DiTournament=EnTournament and DiSession=QuSession and DiDistance=$Dist and DiType='Q'
            LEFT JOIN TargetFaces ON TfTournament=EnTournament AND EnTargetFace=TfId
			WHERE EnAthlete=1 AND QuSession=" . StrSafe_DB($_REQUEST['x_Session']) . " AND ToId = " . StrSafe_DB($_SESSION['TourId']) . "
			AND QuTargetNo =" . StrSafe_DB($_REQUEST['x_Session'] . str_pad($_REQUEST['x_Target'],TargetNoPadding+1,'0',STR_PAD_LEFT));
		$Rs=safe_r_sql($Select);

		if (safe_num_rows($Rs)==1) {
			$MyRow=safe_fetch($Rs);
?>
<form name="Frm" method="POST" action="">
<input type="hidden" name="ScoreCard" id="ScoreCard">
<table class="Tabella">
<tr>

<td class="w-50 h-0">
<?php
//Dettaglio Arciere
					echo '<table class="Tabella">';
					echo '<tr><th>' . get_text('Target') . '</th><td>' . $MyRow->Target . '</td><th>' . get_text('Code','Tournament') . '</th><td>' . $MyRow->EnCode . '</td></tr>';
					echo '<tr><th>' . get_text('Archer') . '</th><td colspan="3">' . $MyRow->EnFirstName . ' ' . $MyRow->EnName . '</td></tr>';
					echo '<tr><th>' . get_text('Country') . '</th><td colspan="3">' . $MyRow->CoCode . '</td></tr>';
					echo '<tr><th>' . get_text('Div') . '</th><td>' . $MyRow->EnDivision . '</td><th>' . get_text('Cl') . '</th><td>' . $MyRow->EnClass . '</td></tr>';
					echo '<tr><td colspan="4"></td></tr>';
					echo '<tr><th>' . get_text('Distance', 'Tournament') . '</th><th>' . get_text('Score', 'Tournament') . '</th><th>' . $MyRow->GoldLabel . '</th><th>' . $MyRow->XNineLabel . '</th></tr>';
					for ($i=1; $i<=$RowTour->TtNumDist; $i++)
						echo '<tr><th>' . $i . '</th><td class="Bold Right"><div id="idScore_' . $i . '_' . $MyRow->EnId . '">' . $MyRow->{"QuD" . $i . "Score"} . '</div></td><td class="Right"><div id="idGold_' . $i . '_' . $MyRow->EnId . '">' . $MyRow->{"QuD" . $i . "Gold"} . '</div></td><td class="Right"><div id="idXNine_' . $i . '_' . $MyRow->EnId . '">' . $MyRow->{"QuD" . $i . "Xnine"} . '</div></td></tr>';
					echo '<tr><th>' . get_text('Total') . '</th><td class="Bold Right"><div id="idScore_' . $MyRow->EnId . '">' . $MyRow->QuScore . '</div></td><td class="Right"><div id="idGold_' . $MyRow->EnId . '">' . $MyRow->QuGold . '</div></td><td class="Right"><div id="idXNine_' . $MyRow->EnId . '">' . $MyRow->QuXnine . '</div></td></tr>';
					echo '</table>';
?>
</td>
<td class="w-50 h-0">
<?php
//Dettaglio Score
					$NumEnds=$MyRow->NumEnds;

					$NumArrows=$MyRow->NumArrows;
					$ScoreNumArrows=$MyRow->NumArrows;

					$MultiLine=false;

					if($NumArrows>3 and !($NumArrows%3)) {
						$NumEnds=$NumEnds*($NumArrows/3);
						$NumArrows=3;
						$MultiLine=true;
					}
					$MaxArrows=$MyRow->NumEnds*$MyRow->NumArrows;

					echo '<table class="Tabella">';
					echo '<tr>';
					echo '<td>&nbsp;<input type="hidden" name="MaxArrows" id="MaxArrows" value="' . $MaxArrows . '"><input type="hidden" name="NumEnds" id="NumEnds" value="' . $NumEnds . '"></td>';
					for($i=1; $i<=$NumArrows; $i++)
						echo '<th>' . $i . '</th>';
					echo '<th>'. get_text('TotalProg','Tournament') . '</th><th' . ($MultiLine ? ' colspan="2"' : ''). '>'. get_text('TotalShort','Tournament') . '</th></tr>';
					$ArrowString = str_pad($MyRow->ArrowString,$MyRow->NumEnds*$MyRow->NumArrows,' ',STR_PAD_RIGHT);
					$TotRunning=0;
					$TotEndRun=0;
					$OffSet = 0;
					$FirstToScore=0;
					$LastToScore=$NumEnds;
					$OffSetInd=$OffSet*$MyRow->NumArrows;
					if($MyRow->IsField3D) {
						$tgt=((intval($MyRow->QuTarget)-1)%$MyRow->NumEnds)+1;
						$OffSet=($tgt-1+$MyRow->ScoringOffset)%$MyRow->NumEnds;
						$ArrowString=substr($ArrowString.$ArrowString, $OffSetInd, $MaxArrows);
						$FirstToScore=$OffSet;
						$LastToScore=$OffSet+($MyRow->ScoringEnds?:$MyRow->NumEnds)-1;
					}
					for($i=0; $i<$NumEnds; $i++) {
						$Class='';
						if($i+$OffSet<$FirstToScore or $i+$OffSet>$LastToScore) {
							$Class='out-of-range';
						}
						$CurEnd=(($i+$OffSet)%$NumEnds);
						echo "<tr class='{$Class}'>";
                        if(!$MultiLine ) {
                            echo '<th>' . ($CurEnd+1) . '</th>';
                        } else if($i % 2 == 0) {
                            echo '<th rowspan="2">' . ($CurEnd/2+1) . '</th>';
                        }
						$ArrNo = $CurEnd * $NumArrows;
						$TotEnd=0;
						for($j=0; $j<$NumArrows; $j++) {
							$ind=(($ArrNo+$j)%$MaxArrows);
							echo '<td class="Center">'
								. '<input type="text" id="arr_' . $Dist . '_' . $ind . '_' . $MyRow->EnId . '" '
								. 'size="2" maxlength="2" value="' . DecodeFromLetter($ArrowString[$ArrNo+$j]) . '" '
								. 'onBlur="javascript:UpdateArrow(\'arr_' . $Dist . '_' . $ind . '_' . $MyRow->EnId . '\');">'
								. '</td>';
							$TotEnd += ValutaArrowString($ArrowString[$ArrNo+$j]);
							$TotEndRun += ValutaArrowString($ArrowString[$ArrNo+$j]);
							$TotRunning += ValutaArrowString($ArrowString[$ArrNo+$j]);
						}
						echo '<td class="Right"><div id="idEnd_' . $Dist . '_' . $CurEnd . '_' . $MyRow->EnId . '">' . $TotEnd . '</div></td>';
						if($MultiLine && !(($ArrNo+3) % $ScoreNumArrows)) {
							echo '<td class="Right"><div id="idEndRun_' . $Dist . '_' . intval($CurEnd/($ScoreNumArrows/$NumArrows)) . '_' . $MyRow->EnId . '">' . $TotEndRun . '</div></td>';
							echo '<td class="Bold Right"><div id="idScore_' . $Dist . '_' . intval($CurEnd/($ScoreNumArrows/$NumArrows)) . '_' . $MyRow->EnId . '">' . $TotRunning . '</div></td>';
							$TotEndRun=0;
						} elseif($MultiLine) {
							echo '<td colspan="2">&nbsp;</td>';
						} else {
							echo '<td class="Bold Right"><div id="idScore_' . $Dist . '_' . $CurEnd . '_' . $MyRow->EnId . '">' . $TotRunning . '</div></td>';
						}
						echo '</tr>';
					}
					echo '</tr>';
					echo '<tr>';
					echo '<td colspan="' . ($NumArrows+1) . '">&nbsp;</td>';
					echo '<th>'. get_text('Total') . '</th>';
					echo '<td '.($MultiLine ? ' colspan="2"' : '').' class="Bold Right FontMedium"><div id="idTotScore_' . $Dist . '_' . $MyRow->EnId . '">' . $MyRow->SelScore . '</div></td>';
					echo '</tr>';
					echo '</table>';
?>
</td>
</tr>

</table>
</form>
<?php
				}
			}
		}
	}

	if(!empty($GoBack)) {
		echo '<table class="Tabella2" width="50%"><tr><th style="background-color:red"><a href="'.$GoBack.'" style="color:white">'.get_text('BackBarCodeCheck','Tournament').'</a></th></tr></table>';
	}

?>
<div id="idOutput"></div>
<?php
	include('Common/Templates/tail.php');
?>
