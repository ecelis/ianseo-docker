<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
CheckTourSession(true);
checkACL(AclIndividuals, AclReadOnly);
require_once('Common/Globals.inc.php');
require_once('Common/Fun_DB.inc.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_FormatText.inc.php');

$avalQR = array();
$ApiMode='';
$Api='';
foreach(AvailableApis() as $Api) {
    if(!($ApiMode=getModuleParameter($Api, 'Mode')) || strpos($ApiMode,'live') !== false ) {
        continue;
    }
    if(strpos($ApiMode,'ng-') === 0) {
        $Api.= '-NG';
    }
    $avalQR[] = $Api;
    break;
}

// calcola la massima fase
$MyQuery = "SELECT MAX(GrPhase) as MaxPhase FROM Grids left join Finals on GrMatchNo=FinMatchNo where FinTournament={$_SESSION['TourId']}";
$Rs = safe_r_sql($MyQuery);
$TmpCnt=32;
if(safe_num_rows($Rs)>0) {
    $r=safe_fetch($Rs);
    $TmpCnt=$r->MaxPhase;
}

// calcola gli eventi esistenti
$MyQuery = 'SELECT '
    . ' EvCode, EvEventName, GrPhase, MAX(IF(FinAthlete=0,0,1)) as Printable'
    . ' FROM Events '
    . ' INNER JOIN Phases ON PhId=EvFinalFirstPhase and (PhIndTeam & 1)=1 '
    . ' INNER JOIN Finals ON EvCode=FinEvent AND EvTournament=FinTournament '
    . ' INNER JOIN Grids ON FinMatchNo=GrMatchNo AND if(EvElimType=3, true, GrPhase<=greatest(PhId, PhLevel)) '
    . ' WHERE EvTournament=' . StrSafe_DB($_SESSION['TourId']) . ' AND EvTeamEvent=0 AND EvFinalFirstPhase!=0 '
    . ' GROUP BY EvCode, EvEventName, EvFinalFirstPhase, GrPhase'
    . ' ORDER BY EvCode, GrPhase DESC';
$Rs = safe_r_sql($MyQuery);

$Rows=array();
$Events=array();
$EventsFirstPhase=array();
$Printable=false;
$OldCode='';
$numPhasesMax=10;
while( $MyRow=safe_fetch($Rs) ) {
    if(empty($Rows[$MyRow->EvCode])) {
        if($OldCode and !$Printable) {
            unset($Rows[$OldCode]);
            unset($Events[$OldCode]);
            unset($EventsFirstPhase[$OldCode]);
        }
        $Printable = false;
        $OldCode = $MyRow->EvCode;
        $Rows[$MyRow->EvCode]='';
        $Events[$MyRow->EvCode] = $MyRow->EvEventName;
        $EventsFirstPhase[$MyRow->EvCode] = $MyRow->GrPhase;
        for($i=$TmpCnt; $i>$MyRow->GrPhase; $i = floor($i/2)) $Rows[$MyRow->EvCode] .= '<td>&nbsp;</td>';
    }
    $Rows[$MyRow->EvCode] .=  '<td class="Center"><a href="PDFScoreMatch.php?Event=' .  $MyRow->EvCode . '&amp;Phase=' . $MyRow->GrPhase . '&Barcode=1'.(count($avalQR) ? '&QRCode[]=' . implode('&QRCode[]=',$avalQR):'').'" class="Link" target="PrintOut">';
    $Rows[$MyRow->EvCode] .=  '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf' . ($MyRow->Printable==1 ? '' : "_small") . '.gif" alt="' . $MyRow->EvCode . '" border="0"><br>';
    $Rows[$MyRow->EvCode] .=  $MyRow->EvCode;
    $Rows[$MyRow->EvCode] .=  '</a></td>';
    $Printable = ($Printable or $MyRow->Printable);
}
$numPhasesMax = max(count($Events),numPhases(max($EventsFirstPhase))+1);

if($OldCode and !$Printable) {
    unset($Rows[$OldCode]);
    unset($Events[$OldCode]);
    unset($EventsFirstPhase[$OldCode]);
}

$JS_SCRIPT=array(
    phpVars2js(array("WebDir" => $CFG->ROOT_DIR, "AllEvents" => get_text('AllEvents'))),
    '<script type="text/javascript" src="../../Common/js/Fun_JS.inc.js"></script>',
    '<script type="text/javascript" src="../../Common/ajax/ObjXMLHttpRequest.js"></script>',
    '<script type="text/javascript" src="../Fun_AJAX.js"></script>'
);

$ONLOAD=' onload="updateSchedule(\'I\')"';
$PAGE_TITLE=get_text('PrintScoreInd','Tournament');
$IncludeJquery=true;
include('Common/Templates/head.php');

echo '<table class="Tabella">';
echo '<tr><th class="Title" colspan="2">' . get_text('PrintScoreInd','Tournament')  . '</th></tr>';

/*** SELECTORS ***/
echo '<tr>';
echo '<th class="SubTitle w-50">' . get_text('Score1Page1Athlete')  . '</th>';
echo '<th class="SubTitle w-50">' . get_text('Score1Page1Match')  . '</th>';
echo '</tr>';
echo '<tr>';
echo '<td class="Center Top w-50"><div class="Center">';
    //multiple  event table selection
    echo '<form id="PrnParameters" action="PDFScore.php" method="get" target="PrintOut">';
    echo '<table class="Tabella" style="width:98%; margin: 1%;">';
        echo '<tr>';
        //events
        echo '<td class="Center w-50">';
        echo get_text('IndEventList') . '<br><select name="Event[]" multiple="multiple" style="width: 90%" size="'.(count($Events)+1).'">';
        foreach($Events as $Event => $EventName) {
            echo '<option value="' . $Event . '">' . $Event . ' - ' . get_text($EventName,'','',true)  . '</option>';
        }
        echo '</select>';
        echo '</td><td class="Left w-50">';
        echo '<input name="IncEmpty" type="checkbox" value="1">&nbsp;' . get_text('ScoreIncEmpty') . '<br>';
        if(module_exists("Barcodes"))
            echo '<input name="Barcode" type="checkbox" checked value="1">&nbsp;' . get_text('ScoreBarcode','Tournament') . '<br>';
        foreach($avalQR as $Api) {
            echo '<input name="QRCode[]" type="checkbox" '.(($ApiMode=='pro' or $ApiMode=='ng-pro') ? '' : 'checked="checked"').' value="'.$Api.'" >&nbsp;' . get_text($Api.'-QRCode','Api') . '<br>';
        }
        echo '</tr>';
    echo '</table>';
    echo '<br><input name="Submit" type="submit" onclick="this.form.action=\'PDFScore.php\'" value="' . get_text('PrintScore','Tournament') . '">';
    echo '<br><br><input name="Submit" type="submit" onclick="this.form.action=\'PrnLabels.php\'" value="' . get_text('FinalIndividualLabels','Tournament') . '">';
    echo '</form>';
echo '</div></td>';

echo '<td class="Center w-50"><div class="Center">';
echo '<form id="PrnParametersMatch" action="PDFScoreMatch.php" method="get" target="PrintOut">';
echo '<table class="Tabella" style="width:98%; margin: 1%">';
echo '<tr>';
//Events
echo '<td class="Center Top w-75">';
echo get_text('IndEventList') . '<br><select name="Event[]" id="d_Event" onChange="ChangeEvent(0);" multiple="multiple" style="width: 90%" size="'.(count($Events)+1).'">';
echo '<option value="">' . get_text('AllEvents')  . '</option>';
foreach($Events as $Event => $EventName) {
    echo '<option value="' . $Event . '">' . $Event . ' - ' . get_text($EventName,'','',true)  . '</option>';
}
echo '</select>';
echo '</td><td class="Center Top w-25">';
echo get_text('Phase') . '<br><select name="Phase[]" id="d_Phase" multiple="multiple" style="width: 90%" size="'.($numPhasesMax+1).'">';
echo '<option value="">' . get_text('AllEvents')  . '</option>';
echo '</select>';
echo '</td>';
echo '</tr>';
$comboSesArray = null;
echo '<tr><td colspan="2" class="Left">'.
    '<div class="mt-4 mb-2">'.
    '<span class="mr-2"><input type="checkbox" id="OnlyToday" title="'.get_text('ScheduleToday', 'Tournament').'" checked="checked" onclick="updateSchedule(\'I\')">'.get_text('ScheduleToday', 'Tournament').'</span>' .
    '<span class="mr-2"><input type="checkbox" id="Unfinished" title="'.get_text('ScheduleUnfinished', 'Tournament').'" checked="checked" onclick="updateSchedule(\'I\')">'.get_text('ScheduleUnfinished', 'Tournament').'</span>'.
    '<span class="mr-2"><input type="button" onclick="updateSchedule(\'I\')" value="'.get_text('CmdRefresh').'"></span>'.
    '</div><div class="mb-4">'. ApiComboSession(['I'],'x_Session', $comboSesArray, 'style="width: 100%"') . '</div></td></tr>';
echo '<tr>';
echo '<td colspan="2" class="Left">';
echo '<input name="ScoreFilled" type="checkbox" value="1">&nbsp;' . get_text('ScoreFilled') . '<br>';
echo '<input name="IncEmpty" type="checkbox" value="1">&nbsp;' . get_text('ScoreIncEmpty') . '<br>';
echo '<input name="IncAllNames" type="checkbox" value="1">&nbsp;' . get_text('ScoreIncAllNames') . '<br>';
echo '<input name="ScoreFlags" type="checkbox" value="1">&nbsp;' . get_text('ScoreFlags','Tournament') . '<br>';
if(module_exists("Barcodes"))
    echo '<input name="Barcode" type="checkbox" checked value="1">&nbsp;' . get_text('ScoreBarcode','Tournament') . '<br>';
foreach($avalQR as $Api) {
    echo '<input name="QRCode[]" type="checkbox" '.(($ApiMode=='pro' or $ApiMode=='ng-pro') ? '' : 'checked="checked"').' value="'.$Api.'" >&nbsp;' . get_text($Api.'-QRCode','Api') . '<br>';
}
if(getModuleParameter('ISK-NG', 'UsePersonalDevices', '')) {
    echo '<input name="ScoreQrPersonal" type="checkbox" checked value="1" >&nbsp;' . get_text('UsePersonalDevices-Print','Api') . '<br>';
}
if($_SESSION['TourLocRule']=='LANC') {
    // specific fro lancaster
    echo '<input name="Margins" type="checkbox" checked value="1" >&nbsp;' . get_text('LancasterScorecard','Tournament') . '<br>';
    echo '<input name="TopMargin" type="number" value="165" >&nbsp;' . get_text('IdMarginT','BackNumbers') . '<br>';
    echo '<input name="LeftMargin" type="number" value="210" >&nbsp;' . get_text('IdMarginL','BackNumbers') . '<br>';
}
echo '</td>';
echo '</tr>';
echo '</table>';
echo '<div class="m-3"><input name="Submit" type="submit" value="' . get_text('PrintScore','Tournament') . '"></div>';
echo '</form>';
echo '</td></tr>';

/*** PDF Direct Links ***/
echo '<tr><th class="Title" colspan="2">' . get_text('PrintScoreInd','Tournament')  . '</th></tr>';
echo '<tr>';
echo '<th class="SubTitle w-50">' . get_text('Score1Page1Athlete')  . '</th>';
echo '<th class="SubTitle w-50">' . get_text('Score1Page1Match')  . '</th>';
echo '</tr>';
echo '<tr style="vertical-align: top;">';
echo '<td class="Center w-50"><div class="Center">';
    //Personal Score on single page
    echo '<div style="display: inline-block;"><a href="PDFScore.php?Barcode=1'.(count($avalQR) ? '&QRCode[]=' . implode('&QRCode[]=',$avalQR):'').'" class="Link" target="PrintOut">';
    echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('IndFinal') . '" border="0"><br>';
    echo get_text('IndFinal');
    echo '</a></div>';

    if(intval($TmpCnt)>32) {
        $tmp=array();
        foreach ($EventsFirstPhase  as $Event => $EventFirstPhase) {
            if(intval($EventFirstPhase)>32) {
                $tmp[] = 'Event[]=' . $Event;
            }
        }
        echo '<div style="display: inline-block; margin-left: 5%"><a href="PDFScoreMatch.php?'.implode('&',$tmp).'&Phase[]=1&Phase[]=0&Barcode=1' . (count($avalQR) ? '&QRCode[]=' . implode('&QRCode[]=', $avalQR) : '') . '" class="Link" target="PrintOut">';
        echo '<img src="' . $CFG->ROOT_DIR . 'Common/Images/pdf.gif" alt="' . get_text('ScoreFinalMatch','Tournament') . '" border="0"><br>';
        echo get_text('ScoreFinalMatch','Tournament');
        echo '</a></div>';
    }
    //Left table with all events list
    echo '<table class="Tabella" style="width:98%; margin: 1%;">';
    $i=-1;
    echo '<tr>';
    foreach($Events as $Event => $EventName) {
        if($i>0) {
            echo '</tr><tr>';
        }
        echo '<td class="Center w-50">'.
            '<div style="display: inline-block;"><a href="PDFScore.php?Event=' .  $Event . '&Barcode=1'.(count($avalQR) ? '&QRCode[]=' . implode('&QRCode[]=',$avalQR):'').'" class="Link" target="PrintOut">'.
            '<img src="../../Common/Images/pdf_small.gif" alt="' . $Event . '" border="0"><br>'.
            $Event . ' - ' . get_text($EventName,'','',true).
            '</a></div>';
        if(intval($EventsFirstPhase[$Event])>32) {
            echo '<div style="display: inline-block;margin-left: 5%"><a href="PDFScoreMatch.php?Event[]=' .  $Event . '&Phase[]=1&Phase[]=0&Barcode=1'.(count($avalQR) ? '&QRCode[]=' . implode('&QRCode[]=',$avalQR):'').'" class="Link" target="PrintOut">'.
            '<img src="../../Common/Images/pdf_small.gif" alt="' . get_text('ScoreFinalMatch','Tournament'). '" border="0"><br>'.
            $Event . ' - ' . get_text('ScoreFinalMatch','Tournament').
            '</a></div>';
        }
        echo '</td>';
        $i = 1-abs($i);
    }
    if(!$i) echo '<td>&nbsp;</td>';
    echo '</tr>';
    echo '</table>';

echo '</div></td>';

echo '<td class="Center w-50"><div class="Center">';

	//Right table, score by match
	$ColWidth=intval(100/round(log($TmpCnt, 2)+2));

	echo '<table class="Tabella" style="width:98%; margin: 1%">';
	echo '<tr>';
	for($i=$TmpCnt; $i>0; $i=floor($i/2)) {
		if($i==24)
			$i=32;
		elseif ($i==48)
			$i=64;
		echo '<th class="SubTitle" style="width:'.$ColWidth.'%">' . get_text($i . '_Phase') . ($i==32 ?  " - " . get_text('24_Phase') :($i==64 ?  " - " . get_text('48_Phase') :'')) . '</th>';
	}
	echo '<th class="SubTitle">' . get_text('0_Phase') . '</th>';
	echo '</tr>';

	echo '<tr>'.implode('</tr><tr>', $Rows).'</tr>';
	echo '</table>';


	echo '</td>';
	echo '</tr>';
//Empty Scorecards
	echo '<tr><th class="SubTitle" colspan="2">' . get_text('ScoreDrawing')  . '</th></tr>';
	echo '<tr>';
//Personal Scoreards
	echo '<td class="Center w-50"><br>';
	// recupera per questo torneo quanti formati ci sono...
	$query="SELECT EvCode, EvMatchMode, EvFinalFirstPhase, EvMatchArrowsNo, EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
		FROM Events
		INNER JOIN Phases on PhId=EvFinalFirstPhase	and (PhIndTeam & 1)=1
		WHERE EvTournament = '{$_SESSION['TourId']}'
			AND EvTeamEvent =0
			AND EvFinalFirstPhase !=0
		GROUP BY
			EvMatchMode, EvFinalFirstPhase, (EvMatchArrowsNo & (POW(2,1+LOG(2,IF(EvFinalFirstPhase>0, 2*greatest(PhId, PhLevel), 1)))-1)), EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
	";
	//print $query;
	$q=safe_r_sql($query);
	echo '<table width="100%" cellspacing="0" cellpadding="1">';
	echo '<tr>';
	while($r=safe_fetch($q)) {
		echo '<td><a href="PDFScore.php?Blank=1&Model='.$r->EvCode.'" class="Link" target="PrintOut">';
			echo '<img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('Score1Page1Athlete') . '" border="0"><br>';
			echo get_text('Score1Page1Athlete');
			$dif=($r->EvElimEnds!=$r->EvFinEnds or $r->EvElimArrows!=$r->EvFinArrows or $r->EvElimSO!=$r->EvFinSO);

			$txt='<b>'. ($r->EvMatchMode?'<br/>'.get_text('MatchMode_1').':</b> ':'');

			$tmp=array();
			list($hasElim,$hasFin)=eventHasScoreTypes($r->EvCode,0);
			if ($hasElim){
				$tmp[]=array(get_text('EliminationShort', 'Tournament'),get_text('EventDetails', 'Tournament', array($r->EvElimEnds, $r->EvElimArrows, $r->EvElimSO)));
			}

			if ($hasFin) {
				$tmp[]=array(get_text('FinalShort', 'Tournament'),get_text('EventDetails', 'Tournament', array($r->EvFinEnds, $r->EvFinArrows, $r->EvFinSO)));
			}

			//$txt.='<b>'. ($r->EvMatchMode?'<br/>'.get_text('MatchMode_1').':</b> ':'');

			foreach ($tmp as $t) {
				$txt.='<br>'.(count($tmp)>1 && $dif ? $t[0] . ' ' : '') . $t[1];
			}

			//$txt=substr($txt,0,-5);

			echo $txt;
			echo '<br/>'. get_text('FirstPhase').': 1/'. namePhase($r->EvFinalFirstPhase,$r->EvFinalFirstPhase);
		echo '</a></td>';
	}
	echo '</tr>';
	echo '</table>';
	echo '</td>';
//Scores per singolo match
	echo '<td width="50%" class="Center">';
	// recupera per questo torneo quanti formati ci sono...
	$query=" SELECT EvCode, EvFinalFirstPhase, EvMatchArrowsNo, EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
		FROM Events WHERE EvTournament={$_SESSION['TourId']} AND EvTeamEvent=0";

/*
 * Per ogni evento scopro se le sue fasi prevedono o no l'uso dei parametri elim e fin.
 * Se almeno una fase usa un tipo di parametri, memorizzo la terna in $list (purchè non l'abbia già messa prima).
 * Poi per tutte le terne (che saranno diverse) preparo i link
 */
	$q=safe_r_sql($query);

	echo '<br><table width="100%" cellspacing="0" cellpadding="1">';
	echo '<tr>';
	$list=array();
	while($r=safe_fetch($q)) {
		$elimFin=elimFinFromMatchArrowsNo($r->EvFinalFirstPhase,$r->EvMatchArrowsNo);

		$arr=array($r->EvElimEnds,$r->EvElimArrows,$r->EvElimSO);
		if ($elimFin[0] && !in_array($arr,$list)) {
			$list[]=$arr;
		}

		$arr=array($r->EvFinEnds,$r->EvFinArrows,$r->EvFinSO);
		if ($elimFin[1] && !in_array($arr,$list)) {
			$list[]=$arr;
		}
	}

	if (count($list)>0) {
		foreach ($list as $l) {
			echo '<td><a href="PDFScoreMatch.php?Blank=1&Rows=' . $l[0] . '&Cols='.$l[1].'&SO='.$l[2].'" class="Link" target="PrintOut">';
			echo '<img src="../../Common/Images/pdf.gif" alt="' . get_text('Score1Page1Match') . '" border="0"><br>';
			echo get_text('Score1Page1Match');
			echo '<br/>'. get_text('EventDetails', 'Tournament', array($l[0], $l[1], $l[2])) ;
			echo '</a></td>';
		}
	}
	echo '</tr>';
	echo '</table>';

	echo '</td>';
	echo '</tr>';
// Nomi Ferrari
	echo '<tr>';
	echo '<th colspan="2" class="Title">' . get_text('Nameplates','Tournament') . '</th>';
	echo '</tr>';
//Selezione evento per nomi ferrari
	echo '<tr>';
	echo '<td class="Center" colspan="2"><br>';
	echo '<form id="PrnParametersNames" action="'.$CFG->ROOT_DIR.'Final/Individual/PrnName.php" method="get" target="PrintOut">';
	echo '<table class="Tabella w-100">';
	echo '<tr>';
//Eventi
	echo '<td class="Center Top w-25">';
	echo get_text('IndEventList') . '<br><select name="Event[]" multiple="multiple" id="p_Event" onChange="ChangeEvent(0,\'p\',null,true);" style="width: 90%" size="'.(count($Events)).'">';
	foreach($Events as $Event => $EventName) {
		echo '<option value="' . $Event . '">' . $Event . ' - ' . get_text($EventName,'','',true)  . '</option>';
	}
	echo '</select>';
	echo '</td><td class="Center Top w-25">';
	echo get_text('Phase') . '<br><select name="Phase" id="p_Phase" style="width: 90%" size="'.($numPhasesMax).'">';
	echo '<option value="">' . get_text('AllEvents')  . '</option>';
	echo '</select>';
	echo '</td>';
	echo '<td class="Left w-25" >';
	echo '<input name="BigNames" type="checkbox" checked="checked" />' . get_text('BigNames','Tournament') ;
	echo '<br/><input name="IncludeLogo" type="checkbox" checked="checked" />' . get_text('IncludeLogo','BackNumbers') ;
	echo '<br/><input name="TargetAssign" type="checkbox" checked="checked" />' . get_text('TargetAssignment','Tournament') ;
	echo '<br/><input name="ColouredPhases" type="checkbox" />' . get_text('ColouredPhases','Tournament') ;
	echo '</td>';
	echo '<td class="Center w-25">';
	echo '<input name="Submit" type="submit" value="' . get_text('Print','Tournament') . '">';
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	echo '</form>';
	echo '</td>';
	echo '</tr>';
    echo '<tr>';
    echo '<td colspan="2" class="Center"><br><a href="PrnName.php" class="Link" target="PrintOut"><img src="'.$CFG->ROOT_DIR.'Common/Images/pdf.gif" alt="' . get_text('Nameplates','Tournament') . '"><br>' . get_text('Nameplates','Tournament') . '</a></td>';
    echo '</tr>';
	echo '</table>';

	include('Common/Templates/tail.php');
