<?php
$JSON=['error'=>1, 'msg'=>''];
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

if(!CheckTourSession() or !hasACL(AclRobin, AclReadOnly)) {
	JsonOut($JSON);
}

$Act=($_REQUEST['act']??'');
$Team=intval($_REQUEST['team'] ?? -1);
$Events=($_REQUEST['events']??[]);
$Levels=($_REQUEST['levels']??[]);
$Groups=($_REQUEST['groups']??[]);
$Rounds=($_REQUEST['rounds']??[]);

if(!$Act or $Team==-1) {
	JsonOut($JSON);
}

switch($Act) {
	case 'getEvents':
		$JSON['events']=[['v'=>'','t'=>get_text('AllEvents')]];
		$q=safe_w_sql("select EvCode as v, concat_ws('-',EvCode,EvEventName) as t from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvElimType=5 order by EvProgr");
		while($r=safe_fetch($q)) {
			$JSON['events'][]=$r;
		}
		$JSON['error']=0;
		break;
	case 'getLevels':
		$JSON['levels']=[['v'=>'','t'=>get_text('AllEvents')]];
		$f=array();
		foreach($Events as $e) {
			if(!$e) {
				// all events!
				$f=[];
				break;
			}
			$f[]=$e;
		}
		$filter=($f ? ' AND RrLevEvent in ('.implode(',', StrSafe_DB($f)).')' : '');
		$q=safe_w_sql("select distinct RrLevLevel as v, group_concat(distinct RrLevName) as t from RoundRobinLevel where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team $filter group by RrLevLevel order by RrLevLevel");
		while($r=safe_fetch($q)) {
			$JSON['levels'][]=$r;
		}
		$JSON['error']=0;
		break;
	case 'getGroups':
		$JSON['groups']=[['v'=>'','t'=>get_text('AllEvents')]];
		$f2=[];
		$f=array();
		foreach($Events as $e) {
			if(!$e) {
				// all events!
				$f=[];
				break;
			}
			$f[]=$e;
		}
		if($f) {
			$f2[]=($f ? ' AND RrGrEvent in ('.implode(',', StrSafe_DB($f)).')' : '');
		}
		$f=array();
		foreach($Levels as $e) {
			if(!$e) {
				// all levels!
				$f=[];
				break;
			}
			$f[]=$e;
		}
		if($f) {
			$f2[]=($f ? ' AND RrGrLevel in ('.implode(',', StrSafe_DB($f)).')' : '');
		}
		$filter=($f2 ? implode($f2) : '');
		$q=safe_w_sql("select distinct RrGrGroup as v, group_concat(distinct RrGrName) as t from RoundRobinGroup where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team $filter group by RrGrGroup order by RrGrGroup");
		while($r=safe_fetch($q)) {
			$JSON['groups'][]=$r;
		}
		$JSON['error']=0;
		break;
	case 'getRounds':
		$JSON['rounds']=[['v'=>'','t'=>get_text('AllEvents')]];
		$f2=[];
		$f=array();
		foreach($Events as $e) {
			if(!$e) {
				// all events!
				$f=[];
				break;
			}
			$f[]=$e;
		}
		if($f) {
			$f2[]=($f ? ' AND RrMatchEvent in ('.implode(',', StrSafe_DB($f)).')' : '');
		}
		$f=array();
		foreach($Levels as $e) {
			if(!$e) {
				// all levels!
				$f=[];
				break;
			}
			$f[]=$e;
		}
		if($f) {
			$f2[]=($f ? ' AND RrMatchLevel in ('.implode(',', StrSafe_DB($f)).')' : '');
		}
		$f=array();
		foreach($Groups as $e) {
			if(!$e) {
				// all levels!
				$f=[];
				break;
			}
			$f[]=$e;
		}
		if($f) {
			$f2[]=($f ? ' AND RrMatchGroup in ('.implode(',', StrSafe_DB($f)).')' : '');
		}
		$filter=($f2 ? implode($f2) : '');
		$q=safe_w_sql("select distinct RrMatchRound as v from RoundRobinMatches where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team $filter group by RrMatchRound order by RrMatchRound");
		while($r=safe_fetch($q)) {
			$r->t=get_text('RoundNum', 'RoundRobin', $r->v);
			$JSON['rounds'][]=$r;
		}
		$JSON['error']=0;
		break;
	default:
		JsonOut($JSON);
}

JsonOut($JSON);


// require_once('Common/Globals.inc.php');
// require_once('Common/Fun_DB.inc.php');
require_once('Common/Lib/CommonLib.php');
// require_once('Common/Lib/Fun_Phases.inc.php');
// require_once('Common/Lib/Fun_FormatText.inc.php');
//require_once('HHT/Fun_HHT.local.inc.php');

// calcola la massima fase
$MyQuery = "SELECT MAX(GrPhase) as MaxPhase FROM Grids left join Finals on GrMatchNo=FinMatchNo where FinTournament={$_SESSION['TourId']}";
$Rs = safe_r_sql($MyQuery);
$TmpCnt=32;
if(safe_num_rows($Rs)>0) {
	$r=safe_fetch($Rs);
	$TmpCnt=$r->MaxPhase;
}

// calcola gli eventi esistenti
$MyQuery = "SELECT EvCode, EvEventName, EvElim1 as NumLevels
	FROM Events
	INNER JOIN RoundRobinLevel ON RrLevEvent=EvCode AND RrLevTournament=EvTournament
	WHERE EvTournament={$_SESSION['TourId']} AND EvTeamEvent=0 AND EvElimType=5
    ORDER BY EvTeamEvent, EvProgr";
$Rs = safe_r_sql($MyQuery);

$Events=array();
while($MyRow=safe_fetch($Rs) ) {
	$Events[$MyRow->EvCode] = $MyRow->EvEventName;
}

$IncludeJquery = true;
$JS_SCRIPT=array(
	phpVars2js(array("WebDir" => $CFG->ROOT_DIR, "AllEvents" => get_text('AllEvents'))),
	'<script type="text/javascript" src="./PrintScore.js"></script>',
	);

include('Common/Templates/head.php');

echo '<table class="Tabella">';

echo '<tr>
	<th class="Title" colspan="4">' . get_text('PrintScore','Tournament')  . '</th>
	</tr>';

echo '<tr>
	<th class="SubTitle" colspan="4"><select id="TeamSelector" onchange="getEvents()">
		<option value="-1">---</option>
		<option value="0">'.get_text('Individual').'</option>
		<option value="1">'.get_text('Team').'</option>
		</select></th>
	</tr>';

echo '<tbody id="mainTdBody">';

/**********************************
 *
 * Manual Selection
 *
 *********************************/
echo '<tr>';
echo '<td class="Center"><select id="EventSelector" multiple="multiple" size="10" onchange="getLevels()"></select></td>';
echo '<td class="Center"><select id="LevelSelector" multiple="multiple" size="10" onchange="getGroups()"></select></td>';
echo '<td class="Center"><select id="GroupSelector" multiple="multiple" size="10"></select></td>';
echo '<td class="Center"><select id="RoundSelector" multiple="multiple" size="10"></select></td>';
echo '</tr>';

/**********************************
 *
 * Scheduler Selection
 *
 *********************************/
echo '<tr>';
echo '<td colspan="4" class="Center">' . ApiComboSession(['R']) . '</td>';
echo '</tr>';

/**********************************
 *
 * Options
 *
 *********************************/
echo '<tr>';
echo '<td colspan="4" class="Center">';
echo '<div class="Left" style="display: inline-block">';
echo '<input name="ScoreFilled" type="checkbox" value="1">&nbsp;' . get_text('ScoreFilled') . '<br>';
echo '<input name="IncEmpty" type="checkbox" value="1">&nbsp;' . get_text('ScoreIncEmpty') . '<br>';
echo '<input name="ScoreFlags" type="checkbox" value="1">&nbsp;' . get_text('ScoreFlags','Tournament') . '<br>';
if(module_exists("Barcodes")) {
    echo '<input name="Barcode" type="checkbox" checked value="1">&nbsp;' . get_text('ScoreBarcode', 'Tournament') . '<br>';
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
echo '</div>';
echo '</td>';
echo '</tr>';

echo '<tr>
	<td colspan="4" class="Center"><div class="my-3"><div class="Button" onclick="createScorecards()">' . get_text('PrintScore','Tournament') . '</div></td>
	</tr>';

echo '</tbody>';
echo '</table>';

// echo '<br>&nbsp;<br><br>&nbsp;';
// echo '</form>';
// echo '</td>';
// echo '</tr>';
// //Score in bianco
// echo '<tr><th class="SubTitle" colspan="2">' . get_text('ScoreDrawing')  . '</th></tr>';
// echo '<tr>';
// //Scores Personali
// echo '<td width="50%" class="Center"><br>';
// // recupera per questo torneo quanti formati ci sono...
// $query="SELECT EvCode, EvMatchMode, EvFinalFirstPhase, EvMatchArrowsNo, EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
// 	FROM Events
// 	INNER JOIN Phases on PhId=EvFinalFirstPhase	and (PhIndTeam & 1)=1
// 	WHERE EvTournament = '{$_SESSION['TourId']}'
// 		AND EvTeamEvent =0
// 		AND EvFinalFirstPhase !=0
// 	GROUP BY
// 		EvMatchMode, EvFinalFirstPhase, (EvMatchArrowsNo & (POW(2,1+LOG(2,IF(EvFinalFirstPhase>0, 2*greatest(PhId, PhLevel), 1)))-1)), EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
// ";
// //print $query;
// $q=safe_r_sql($query);
// echo '<table width="100%" cellspacing="0" cellpadding="1">';
// echo '<tr>';
// while($r=safe_fetch($q)) {
// 	echo '<td><a href="'.$CFG->ROOT_DIR.'Final/Individual/PDFScore.php?Blank=1&Model='.$r->EvCode.'" class="Link" target="PrintOut">';
// 		echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('Score1Page1Athlete') . '" border="0"><br>';
// 		echo get_text('Score1Page1Athlete');
// 		$dif=($r->EvElimEnds!=$r->EvFinEnds or $r->EvElimArrows!=$r->EvFinArrows or $r->EvElimSO!=$r->EvFinSO);
//
// 		$txt='<b>'. ($r->EvMatchMode?'<br/>'.get_text('MatchMode_1').':</b> ':'');
//
// 		$tmp=array();
// 		list($hasElim,$hasFin)=eventHasScoreTypes($r->EvCode,0);
// 		if ($hasElim)
// 		{
// 			$tmp[]=array(get_text('EliminationShort', 'Tournament'),get_text('EventDetails', 'Tournament', array($r->EvElimEnds, $r->EvElimArrows, $r->EvElimSO)));
// 		}
//
// 		if ($hasFin)
// 		{
// 			$tmp[]=array(get_text('FinalShort', 'Tournament'),get_text('EventDetails', 'Tournament', array($r->EvFinEnds, $r->EvFinArrows, $r->EvFinSO)));
// 		}
//
// 		//$txt.='<b>'. ($r->EvMatchMode?'<br/>'.get_text('MatchMode_1').':</b> ':'');
//
// 		foreach ($tmp as $t)
// 		{
// 			$txt.='<br>'.(count($tmp)>1 && $dif ? $t[0] . ' ' : '') . $t[1];
// 		}
//
// 		//$txt=substr($txt,0,-5);
//
// 		echo $txt;
// 		echo '<br/>'. get_text('FirstPhase').': 1/'. namePhase($r->EvFinalFirstPhase,$r->EvFinalFirstPhase);
// 	echo '</a></td>';
// }
// echo '</tr>';
// echo '</table>';
// echo '</td>';
// //Scores per singolo match
// echo '<td width="50%" class="Center">';
// // recupera per questo torneo quanti formati ci sono...
// $query="
// 	SELECT
// 		EvCode,EvFinalFirstPhase,EvMatchArrowsNo,
// 		EvElimEnds, EvElimArrows, EvElimSO,
// 		EvFinEnds, EvFinArrows, EvFinSO
// 	FROM
// 		Events
// 	WHERE
// 		EvTournament={$_SESSION['TourId']} AND EvTeamEvent=0
// ";
//
// /*
// * Per ogni evento scopro se le sue fasi prevedono o no l'uso dei parametri elim e fin.
// * Se almeno una fase usa un tipo di parametri, memorizzo la terna in $list (purchè non l'abbia già messa prima).
// * Poi per tutte le terne (che saranno diverse) preparo i link
// */
// $q=safe_r_sql($query);
//
// echo '<br><table width="100%" cellspacing="0" cellpadding="1">';
// echo '<tr>';
// $list=array();
// while($r=safe_fetch($q)) {
// 	$elimFin=elimFinFromMatchArrowsNo($r->EvFinalFirstPhase,$r->EvMatchArrowsNo);
//
// 	$arr=array($r->EvElimEnds,$r->EvElimArrows,$r->EvElimSO);
// 	if ($elimFin[0] && !in_array($arr,$list))
// 	{
// 		$list[]=$arr;
// 	}
//
// 	$arr=array($r->EvFinEnds,$r->EvFinArrows,$r->EvFinSO);
// 	if ($elimFin[1] && !in_array($arr,$list))
// 	{
// 		$list[]=$arr;
// 	}
// }
//
// if (count($list)>0)
// {
// 	foreach ($list as $l)
// 	{
// 		echo '<td><a href="PDFScoreMatch.php?Blank=1&Rows=' . $l[0] . '&Cols='.$l[1].'&SO='.$l[2].'" class="Link" target="PrintOut">';
// 		echo '<img src="../../Common/Images/pdf.gif" alt="' . get_text('Score1Page1Match') . '" border="0"><br>';
// 		echo get_text('Score1Page1Match');
// 		echo '<br/>'. get_text('EventDetails', 'Tournament', array($l[0], $l[1], $l[2])) ;
// 		echo '</a></td>';
// 	}
// }
// echo '</tr>';
// echo '</table>';
//
// echo '</td>';
// echo '</tr>';
// // Nomi Ferrari
// echo '<tr>' . "\n";
// echo '<th colspan="2" class="SubTitle">' . get_text('Partecipants') . '</th>';
// echo '</tr>' . "\n";
// echo '<tr>' . "\n";
// echo '<td colspan="2" class="Center"><br><a href="'.$CFG->ROOT_DIR.'Final/Individual/PrnName.php" class="Link" target="PrintOut"><img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('Partecipants') . '" border="0"><br>' . get_text('Partecipants') . '</a></td>';
// echo '</tr>' . "\n";
// //Selezione evento per nomi ferrari
// echo '<tr>' . "\n";
// echo '<td align="Center" colspan="2"><br>';
// echo '<form id="PrnParametersNames" action="'.$CFG->ROOT_DIR.'Final/Individual/PrnName.php" method="get" target="PrintOut">';
// echo '<table class="Tabella" style="width:60%">';
// echo '<tr>';
//
// //Eventi
// echo '<td class="Center" width="25%">';
// echo get_text('Event') . '<br><select name="Event[]" multiple="multiple" id="p_Event" onChange="ChangeEvent(0,\'p\',null,true);" size="10">';
// foreach($Events as $Event => $EventName) {
// 	echo '<option value="' . $Event . '">' . $Event . ' - ' . get_text($EventName,'','',true)  . '</option>';
// }
// echo '</select>';
// echo '</td><td width="25%" class="Center">';
// echo get_text('Phase') . '<br><select name="Phase" id="p_Phase" size="8">';
// echo '<option value="">' . get_text('AllEvents')  . '</option>';
// echo '</select>';
// echo '</td>';
// echo '<td class="left" width="25%" >';
// echo '<input name="BigNames" type="checkbox" checked="checked" />' . get_text('BigNames','Tournament') ;
// echo '<br/><input name="IncludeLogo" type="checkbox" checked="checked" />' . get_text('IncludeLogo','BackNumbers') ;
// echo '<br/><input name="TargetAssign" type="checkbox" checked="checked" />' . get_text('TargetAssignment','Tournament') ;
// echo '<br/><input name="ColouredPhases" type="checkbox" />' . get_text('ColouredPhases','Tournament') ;
// echo '</td>';
// echo '<td class="Center" width="25%" >';
// echo '<input name="Submit" type="submit" value="' . get_text('Print','Tournament') . '">';
// echo '</td>';
// echo '</tr>';
// echo '</table>';
// echo '</form>';
// echo '</td>';
// echo '</tr>';
// echo '</table>';

include('Common/Templates/tail.php');

