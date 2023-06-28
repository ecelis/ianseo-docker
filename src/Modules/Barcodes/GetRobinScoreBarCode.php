<?php
define('IN_PHP', true);

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_Number.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Final/Fun_Final.local.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');

CheckTourSession(true);
checkACL(array(AclIndividuals,AclTeams),AclReadWrite);
$Match='';

// Check the correct separator (as barcode reader may interpret «-» as a «'» !)
//
if(empty($_SESSION['BarCodeSeparator'])) {
	require_once('./GetBarCodeSeparator.php');
	die();
}

$ShowMiss=(!empty($_GET['ShowMiss']));
$T=0;
$Turno='';

if($_GET) {
	if(!empty($_GET['BARCODESEPARATOR'])) {
		unset($_SESSION['BarCodeSeparator']);
		CD_redirect($_SERVER['PHP_SELF']);
	}
/*

Aggiunto il campo FinConfirmed e TfConfirmed (int(4)) nelle rispettive tabelle per confermare i match!

*/
	if(!empty($_GET['T'])) $Turno='&T='.($T=$_GET['T']);

	// sets the autoedit feature
	if(!empty($_GET['AutoEdit']) and empty($_GET['return']) and empty($_GET['C'])) $_GET['C']='EDIT';
	unset($_GET['return']);

	if(!empty($_GET['B'])) {
		// get the match
		$Match=getScore($_GET['B']);
		if(!empty($Match->FsDate1) and !empty($Match->FsTime1)) $_GET['T']=$Match->FsDate1.'|'.$Match->FsTime1;

		// if we have a "C" input (beware of autoedit!) then do the action
		if(!empty($_GET['C'])) {
			$C=$_GET['C'];
			unset($_GET['C']);
			if($Match and !IsBlocked(BIT_BLOCK_ROBIN)) {
				switch(strtoupper($C)) {
					case 'EDIT':
						$GoBack=$_SERVER['SCRIPT_NAME'].go_get().'&return=1';

						// edit the scorecard
						$_REQUEST['team']=$Match->EvTeamEvent;
						$_REQUEST['event']=$Match->EvCode;
						$_REQUEST['level']=$Match->M1Level;
						$_REQUEST['group']=$Match->M1Group;
						$_REQUEST['round']=$Match->M1Round;
						$_REQUEST['match']=$Match->M1MatchNo;
						//require_once('Final/WriteScoreCard.php');
						require_once('Modules/RoundRobin/Spotting.php');
						die();
						break;
// 					case 'EDIT2':
// 						$GoBack=$_SERVER['SCRIPT_NAME'].go_get().'&return=1';
// 						TODO: go to the edit page;

// 						// edit the scorecard
// 						$_REQUEST['Command']='OK';
// 						$_REQUEST['x_Session']=$archer->QuTargetNo[0];
// 						$_REQUEST['x_Dist']=$D;
// 						$_REQUEST['x_From']=substr($archer->QuTargetNo, 1, -1);
// 						$_REQUEST['x_To']=substr($archer->QuTargetNo, 1, -1);
// 						if(count($archers)==1) $_REQUEST['x_Target']=$archer->QuTargetNo;
// 						$_REQUEST['x_Gold']=1;

// 						require_once('Qualification/index.php');
// 						die();
// 						break;
					case strtoupper($_GET['B']):
						ConfirmMatch($Match);
						unset($_GET['B']);
						cd_redirect(basename(__FILE__).go_get());
						break;
					default:
						// reads another barcode
						$_GET['B']=$C;
						cd_redirect(basename(__FILE__).go_get());
				}
			} elseif(getScore($C)) {
				// reads another barcode
				$_GET['B']=$C;
				cd_redirect(basename(__FILE__).go_get());
			}
		}
	}
}

$ONLOAD=' onLoad="javascript:document.Frm.bib.focus()"';
$JS_SCRIPT=array('<style>');
if($ShowMiss) {
	$JS_SCRIPT[]='
		form.ShowMiss {position:absolute;left:0;right:200px;}
		div.ShowMiss {position:absolute;width:190px;top:0;right:0;bottom:0;overflow:hide;}
		';
}
$JS_SCRIPT[]='
    .winner {border: 5px solid green;}
    .equals {border: 15px solid blue;}
    .tie {border: 15px solid red;}
    .th {background-color:#BFDDFF; text-align:center; font-weight:bold; color: #004488;margin:1px;white-space:nowrap;display:flex;align-items:center;}
    .th div {flex:1 0 5rem; padding:0.5rem;}
    div.td {flex:1 0 6rem; background-color:white; text-align:center; color: black; }
	.selected td {background-color:#d0d0d0;font-weight:bold}
	.txtGreen {color:green;}
	.txtGray {color:gray;}
	';
$JS_SCRIPT[]='</style>';
$JS_SCRIPT[] = '<link href="'.$CFG->ROOT_DIR.'Common/css/font-awesome.css" rel="stylesheet" type="text/css">';

include('Common/Templates/head.php');

?>
<table class="Tabella">
    <tr style="vertical-align: top">
        <td class="w-100">

<form name="Frm" method="get" action="">
<table class="Tabella2 half">
	<tr>
		<th class="Title" colspan="4"><?php print get_text('CheckScorecards','Tournament');?></th>
	</tr>
	<?php
		echo '<tr>';
		echo '<th colspan="3">' . get_text('BarcodeSeparator','BackNumbers') . ': <span style="font-size:150%">' . $_SESSION['BarCodeSeparator'] . '</span>' . '</th>';
		echo '<th colspan="1"><a href="' . $_SERVER["PHP_SELF"]. '?BARCODESEPARATOR=1">' . get_text('ResetBarcodeSeparator','BackNumbers') . '</a></th>';
		echo '</tr>';
	?>
	<tr>
		<th><?php print get_text('AutoEdits','Tournament');?></th>
		<th><?php print get_text('ShowMissing','Tournament');?></th>
		<th><?php print get_text('Barcode','BackNumbers');?></th>
		<th><?php print get_text('Session');?></th>
	</tr>
	<tr>
		<td class="Center"><input type="checkbox" onclick="document.Frm.bib.focus()" name="AutoEdit"  <?php echo (!empty($_GET['AutoEdit']) ? ' checked="checked"' : ''); ?>></td>
		<td class="Center"><input type="checkbox" onclick="document.Frm.bib.focus()" name="ShowMiss"  <?php echo ((empty($_GET) or !empty($_GET['ShowMiss'])) ? ' checked="checked"' : ''); ?>></td>
		<td class="Center"><?php
if(!empty($_GET['B'])) {
	echo '<input type="hidden" name="B" value="'.$_GET['B'].'">';
	echo '<input type="text" name="C" id="bib" tabindex="1">';
} else {
	echo '<input type="text" name="B" id="bib" tabindex="1">';
}


?></td>
		<td class="Center"><select id="Session" name="T"  onchange="document.Frm.bib.focus()"><?php

$tmp=array(1);
$Combo=ComboSession('Robin', '', $tmp);

if(!empty($_REQUEST['T'])) {
    $s='value="'.$_REQUEST['T'].'"';
    echo str_replace($s,$s.' selected="selected"', $Combo);
} else {
    echo $Combo;
}

?></select></td>
</tr>
	<tr>
		<td class="Center" colspan="2"><input type="submit" value="<?php print get_text('CmdGo','Tournament');?>" id="Vai" onClick="javascript:SendBib();"></td>
		<td class="Center" colspan="2"><input type="button" value="<?php print get_text('BarcodeMissing','Tournament');?>" onClick="window.open('./GetScoreBarCodeMissing.php?S=R&T='+document.getElementById('Session').value);"></td>
	</tr>
	<?php
	if(!$Match){
		echo '<tr class="divider"><td colspan="4"></td></tr>
		<tr><th colspan="4"><img src="beiter.png" width="80" hspace="10" alt="Beiter Logo" border="0"/><br>' . get_text('Credits-BeiterCredits', 'Install') . '</th></tr>';
	}
	?>
</table>
<?php

if($Match) {
    // check who is winner...
    $Win1='';
    $Win2='';
    $Score1=($Match->RrLevMatchMode ? $Match->M1SetScore:$Match->M1Score);
    $Score2=($Match->RrLevMatchMode ? $Match->M2SetScore:$Match->M2Score);
    $TB1=ValutaArrowStringSO($Match->M1Tiebreak);
    $TB2=ValutaArrowStringSO($Match->M2Tiebreak);
    $Closest1=($Match->M1Tiebreak!=strtoupper($Match->M1Tiebreak) or $Match->M1TbClosest);
    $Closest2=($Match->M2Tiebreak!=strtoupper($Match->M2Tiebreak) or $Match->M2TbClosest);

	if($Match->M1WinLose) {
		$Win1=' winner';
	} elseif($Match->M2WinLose) {
		$Win2=' winner';
	} else {
        $A1String=strlen(str_replace(' ','', $Match->M1Arrowstring));
        $A2String=strlen(str_replace(' ','', $Match->M2Arrowstring));
        $TieAllowedCumulative=($Match->RrLevMatchMode==0 and $A1String==$A2String and $A1String==$Match->RrLevArrows*$Match->RrLevEnds);
        $TieAllowedSetSystem=($Match->RrLevMatchMode==1 and $Match->M1SetScore==$Match->M2SetScore and $Match->M1SetScore==$Match->RrLevEnds);
        if($Match->RrLevTieAllowed and ($TieAllowedSetSystem or $TieAllowedCumulative)) {
	        $Win1=' equals';
	        $Win2=' equals';
        } else {
            $Win1=' tie';
            $Win2=' tie';
        }
	}

	echo '<table class="Tabella2 half" style="font-size:150%">';
	echo '<tr><th class="Title" colspan="5">'.get_text('Archer').'</th></tr>';
	echo '<tr><th class="Title" colspan="5">'.get_text('Target'). ' ' . ltrim($Match->M1Target, '0') . ($Match->M1Target!=$Match->M2Target ? ' - ' . ltrim($Match->M2Target,'0') : '') . '</th></tr>';

	echo '<tr>';

	// Opponent 1
	echo '<td colspan="2" class="'.$Win1.'">';
	echo '<div class="th"><div>'.$Match->Athlete1.'</div></div>';
	echo '<div class="th"><div>'.get_text('Score', 'Tournament').'</div><div class="LetteraGrande td"> '.$Score1.'</div></div>';
	if($Match->RrLevMatchMode) {
		echo '<div>';
		echo '<div class="LetteraGrande td">'.str_replace("|",",&nbsp;",$Match->M1SetPoints).'</div>';
		echo '</div>';
	}
	echo '<div class="th"><div>'.get_text('ShotOffShort', 'Tournament').'</div><div class="LetteraGrande td">'.(!empty($Match->M1Tiebreak) ? (strlen($Match->M1Tiebreak)>1 ? implode(DecodeFromString($Match->M1Tiebreak, false),',') : DecodeFromString($Match->M1Tiebreak, false)):'&nbsp;').'</div></div>';

	if($Closest1 or $Closest2) {
        echo '<div class="th"><div>'.get_text('ClosestShort', 'Tournament').'</div><div class="LetteraGrande td">'.($Closest1 ? '<i class="fa fa-check-circle txtGreen"></i>' :'&nbsp;').'</div></div>';
    }

	echo '</td>';

	echo '<td>&nbsp;</td>';

	// Opponent 2
	echo '<td colspan="2" class="'.$Win2.'">';
	echo '<div class="th"><div>'.$Match->Athlete2.'</div></div>';
	echo '<div class="th"><div>'.get_text('Score', 'Tournament').'</div><div class="LetteraGrande td"> '.$Score2.'</div></div>';
	if($Match->RrLevMatchMode) {
		echo '<div>';
		echo '<div class="LetteraGrande td">'.str_replace("|",",&nbsp;",$Match->M2SetPoints).'</div>';
		echo '</div>';
	}
	echo '<div class="th"><div>'.get_text('ShotOffShort', 'Tournament').'</div><div class="LetteraGrande td">'.(!empty($Match->M2Tiebreak) ? (strlen($Match->M2Tiebreak)>1 ? implode(DecodeFromString($Match->M2Tiebreak, false),',') : DecodeFromString($Match->M2Tiebreak, false)):'&nbsp;').'</div></div>';

	if($Closest1 or $Closest2) {
		echo '<div class="th"><div>'.get_text('ClosestShort', 'Tournament').'</div><div class="LetteraGrande td">'.($Closest2 ? '<i class="fa fa-check-circle txtGreen"></i>' :'&nbsp;').'</div></div>';
	}
    echo '</td>';
	echo '</tr>';

	//echo '<tr>';
	//echo '<th class="'.$Win1.'">'.get_text('Score', 'Tournament').'</th>';
	//echo '<td class="LetteraGrande'.$Win1.'" align="right">'.$Score1.'</td>';
	//echo '<td>&nbsp;</td>';
	//echo '<th class="'.$Win2.'">'.get_text('Score', 'Tournament').'</th>';
	//echo '<td class="LetteraGrande'.$Win2.'" align="right">'.$Score2.'</td>';
	//echo '</tr>';
	//
	//if($Match->matchMode) {
	//	echo '<tr>';
	//	echo '<td colspan="2" class="LetteraGrande'.$Win1.'" align="right">'.str_replace("|",",&nbsp;",$Match->setPoints1).'</td>';
	//	echo '<td>&nbsp;</td>';
	//	echo '<td colspan="2" class="LetteraGrande'.$Win2.'" align="right">'.str_replace("|",",&nbsp;",$Match->setPoints2).'</td>';
	//	echo '</tr>';
	//}
	//
	//echo '<tr>';
	//echo '<th>'.get_text('ShotOffShort', 'Tournament').'</th>';
	//echo '<td class="LetteraGrande" align="right">'.(!empty($Match->M1Tiebreak) ? (strlen($Match->M1Tiebreak)>1 ? implode(DecodeFromString($Match->M1Tiebreak, false),',') : DecodeFromString($Match->M1Tiebreak, false)):'&nbsp;').'</td>';
	//echo '<td>&nbsp;</td>';
	//echo '<th>'.get_text('ShotOffShort', 'Tournament').'</th>';
	//echo '<td class="LetteraGrande" align="right">'.(!empty($Match->M2Tiebreak) ? (strlen($Match->M2Tiebreak)>1 ? implode(DecodeFromString($Match->M2Tiebreak, false),',') : DecodeFromString($Match->M2Tiebreak, false)):'&nbsp;').'</td>';
	//echo '</tr>';

	echo '<tr>';
		echo '<td colspan="2" align="center" style="font-size:80%"><b><a href="'.go_get(array('C'=>$_REQUEST['B'])).'">CONFIRM</a></b></td>';
		echo '<td>&nbsp;</td>';
		echo '<td colspan="2" align="center" style="font-size:80%"><b><a href="'.go_get(array('C'=> 'EDIT')).'">Edit arrows</a>';
// 		echo '<br/><a href="'.go_get(array('C' => 'EDIT2')).'">Edit totals</a></b>';
		echo '</td>';
		echo '</tr>';
	echo '</table>';
}


?>
</form>

        </td>
        <td>

<?php
if($ShowMiss and !empty($_GET['T'])) {
	list($Date, $Time)=explode('|', $_GET['T']);
	echo '<table class="Missing">';
	$cnt = 0;
	$tmpRow = '';

	$options=[
		'date'=>$Date,
		'time'=>$Time,
		'confirmed'=>0,
		'order'=>'T',
	];

	$rank=Obj_RankFactory::create('Robin',$options);
	$MyQuery = $rank->getQuery();

	$Q=safe_r_sql($MyQuery);
	while($r=safe_fetch($Q)) {
		if(!$r->AthleteShort1 and !$r->AthleteShort2) continue;
	    $lnk=' onclick="location.href=\''.go_get('B', implode($_SESSION['BarCodeSeparator'], [$r->M1MatchNo, $r->M1Round, $r->M1Group, $r->M1Level, $r->EvTeamEvent, $r->EvCode])).'\'"';
		if($r->M1WinLose or $r->M2WinLose) {
			$lnk.=' style="font-weight:bold;"';
        }
		$tmpRow .= '<tr'.$lnk.'><td nowrap="nowrap">'.ltrim($r->M1Target,'0').($r->M1Target!=$r->M2Target ? '-'.ltrim($r->M2Target,'0') : '').'</td><td nowrap="nowrap">'.$r->AthleteShort1.'</td><td nowrap="nowrap">'.$r->AthleteShort2.'</td></tr>';
		$cnt++;
	}
	echo '<tr><th colspan="3" class="Title">' . get_text('TotalMissingScorecars','Tournament',$cnt) . '</th></tr>';
	echo $tmpRow;
	echo '</table>';
}
?>

        </td>
    </tr>
</table>
<div id="idOutput"></div>
<?php
include('Common/Templates/tail.php');


function getScore($barcode, $strict=false) {
	@list($matchno, $round, $group, $level, $team, $event) = @explode($_SESSION['BarCodeSeparator'], $barcode, 6);
	$event=str_replace($_SESSION['BarCodeSeparator'], "-", $event);

	$options=[
		'team'=>$team,
		'events'=>[$event],
		'levels'=>[$level],
		'groups'=>[$group],
		'rounds'=>[$round],
		'matchno'=>$matchno,
	];

	$rank=Obj_RankFactory::create('Robin',$options);
	$MyQuery = $rank->getQuery();

	$q=safe_r_sql($MyQuery);
	$r= safe_fetch($q);
	return $r;
}

function ConfirmMatch($Match) {
	$SQL= "update RoundRobinMatches
		set RrMatchConfirmed=1,
		RrMatchStatus=1
		where RrMatchTournament={$_SESSION['TourId']}
			and RrMatchEvent='$Match->EvCode'
			and RrMatchTeam=$Match->EvTeamEvent
			and RrMatchLevel=$Match->M1Level
			and RrMatchGroup=$Match->M1Group
			and RrMatchRound=$Match->M1Round
			and RrMatchMatchNo in ($Match->M1MatchNo, $Match->M2MatchNo) ";
	safe_w_sql($SQL);

	// sends the events for the confirmation of the match
	// runJack("MatchFinished", $_SESSION['TourId'], array("Event"=>$Match->event ,"Team"=>$Match->teamEvent,"MatchNo"=>min($Match->match1, $Match->match2) ,"TourId"=>$_SESSION['TourId']));

	// Calculate Rank
    $Event=(object) [
        'Team'=>$Match->EvTeamEvent,
        'Event'=>$Match->M1Event,
        'Level'=>$Match->M1Level,
        'Group'=>$Match->M1Group,
    ];
    require_once('../RoundRobin/Lib.php');
	calculateGroupRank($Event);

	// runJack("FinConfirmEnd", $_SESSION['TourId'], array("Event"=>$Match->event ,"Team"=>$Match->teamEvent,"MatchNo"=>min($Match->match1, $Match->match2) ,"TourId"=>$_SESSION['TourId']));
	//runJack("MatchConfirmed", $_SESSION['TourId'], array("Event"=>$Match->event ,"Team"=>$Match->teamEvent,"MatchNo"=>min($Match->match1, $Match->match2) ,"TourId"=>$_SESSION['TourId']));
}
