<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_DateTime.inc.php');

$JSON=array('error' => 1, 'time' => '', 'force' => false);

if(!CheckTourSession()) {
	JsonOut($JSON);
}

$TourId=$_SESSION['TourId'];
if (!isset($_REQUEST['time']) or checkACL(array((empty($_REQUEST['Team']) ? AclIndividuals:AclTeams), AclOutput),AclNoAccess, false, $TourId)==AclNoAccess) {
	JsonOut($JSON);
}

$JSON['error']=0;
if(empty($_REQUEST['time']) or !empty($_REQUEST['SelectedEnd'])) {
    $_REQUEST['time'] = '0000-00-00 00:00:00';
}

// start checking if there is a modification
$q=safe_r_sql("("."Select FinDateTime LastUpdate from Finals where FinTournament={$TourId} and FinDateTime>'{$_REQUEST['time']}'
    ) UNION (
    Select TfDateTime LastUpdate from TeamFinals where TfTournament={$TourId} and TfDateTime>'{$_REQUEST['time']}'
    ) UNION (
    Select RrMatchDateTime LastUpdate from RoundRobinMatches where RrMatchTournament={$TourId} and RrMatchDateTime>'{$_REQUEST['time']}'
    )
    Order by LastUpdate desc
    limit 1");
if(!($r=safe_fetch($q)) or $r->LastUpdate<=$_REQUEST['time']) {
	// no new things
	JsonOut($JSON);
}

$JSON['time']=$r->LastUpdate;
$JSON['Event']='';
$JSON['OppLeft']='';
$JSON['OppRight']='';
$JSON['TgtLeft']='';
$JSON['TgtRight']='';
$JSON['ScoreLeft']='';
$JSON['ScoreRight']='';
$JSON['UpdateL']=0;
$JSON['UpdateR']=0;
$JSON['IdL']=0;
$JSON['IdR']=0;
$JSON['WinnerL']=false;
$JSON['WinnerR']=false;
$JSON['TgtSize']=0;
$JSON['AthL'] = [];
$JSON['AthR'] = [];

// get what needs to be checked
$MatchNo=((isset($_REQUEST['MatchNo']) AND intval($_REQUEST['MatchNo'])>=0) ? intval($_REQUEST['MatchNo']/2)*2 : -1);
$Event=(empty($_REQUEST['Event']) ? '' : $_REQUEST['Event']);
$Team=(isset($_REQUEST['Team']) ? intval($_REQUEST['Team']) : -1);
$Lock=(isset($_REQUEST['Lock']) ? intval($_REQUEST['Lock']) : 0);
$TourId=(isset($_REQUEST['TourId']) ? intval($_REQUEST['TourId']) : $_SESSION['TourId']);
$LiveExists=false;
$Live=false;

if ($x=getMatchLive($TourId)) {
	if(!$Lock) {
		$Event=$x->Event;
		$MatchNo=$x->MatchNo;
		$Team=$x->Team;
    }
	$Live=($Event==$x->Event and $MatchNo==$x->MatchNo and $Team==$x->Team);
	$LiveExists=(!$Live and $Lock);
}

if(!$Event or $MatchNo<0 or $Team<0) {
	$JSON['Event']=get_text('NoLiveEvent');
	JsonOut($JSON);
}

$JSON['EvCode']=$Event;
$JSON['MatchNo']=intval($MatchNo);
$JSON['EvTeam']=intval($Team);
$JSON['LiveExists']=boolval($LiveExists);

$options = array(
    'tournament' => $TourId,
    'matchno' => $MatchNo,
    'events' => $Event,
    'records' => true,
    'extended' => true,
);

if($MatchNo>256) {
    $options['team']=$Team;
    $rank = Obj_RankFactory::create('Robin', $options);
    $rank->OnlyMatch=true;
} else {
    if($Team) {
        $rank = Obj_RankFactory::create('GridTeam', $options);
    } else {
        $rank = Obj_RankFactory::create('GridInd', $options);
    }
}

$rank->read();
$rankData=$rank->getData();

if(!$rankData['sections']) {
    $JSON['Event']=get_text('NoLiveEvent');
    JsonOut($JSON);
}

$JSON['time']=$rankData['meta']['lastUpdate'];

if($rankData['meta']['lastUpdate'] <= $_REQUEST['time']) {
    //nothing changed so stay put
	$JSON['error']==0;
	JsonOut($JSON);
}

if($MatchNo>256) {
    $Section=end($rankData['sections']);
    $Level=end($Section['levels']);
    $Group=end($Level['matches']);
    $Round=end($Group['rounds']);
    $Match=end($Round['items']);
    $NumEnds= $Level['ends'];
    $NumArrowsMatch= $Level['arrows'];
    $NumArrowsSO= $Level['soNumArrows'];
    $MatchName="{$Level['name']} - {$Group['name']} - {$Round['name']}";
    
    $Sx=($Match['swapped'] ? 'R' : 'L');
    $Rx=($Match['swapped'] ? 'L' : 'R');
    $Left=($Match['swapped'] ? 'Right' : 'Left');
    $Right=($Match['swapped'] ? 'Left' : 'Right');
} else {
    $Section=end($rankData['sections']);
    $Phase=end($Section['phases']);
    $Match=end($Phase['items']);
    $NumEnds=$Phase['meta']['FinElimChooser'] ? $Section['meta']['elimEnds'] : $Section['meta']['finEnds'];
    $NumArrowsMatch=$Phase['meta']['FinElimChooser'] ? $Section['meta']['elimArrows'] : $Section['meta']['finArrows'];
    $NumArrowsSO=$Phase['meta']['FinElimChooser'] ? $Section['meta']['elimSO'] : $Section['meta']['finSO'];
    $MatchName=$Phase['meta']['phaseName'];

    $JSON['Phase']=intval(key($Section['phases']));
    
    $Sx='L';
    $Rx='R';
    $Left='Left';
    $Right='Right';
}

if($Match['lastUpdated']>$_REQUEST['time']) {
    $JSON['Update'.$Sx]=1;
}
if($Match['oppLastUpdated']>$_REQUEST['time']) {
    $JSON['Update'.$Rx] = 1;
}

$JSON['Id'.$Sx]=$Match[($Team ? 'countryCode':'bib')];
$JSON['Id'.$Rx]=$Match[($Team ? 'oppCountryCode':'oppBib')];

// check if we are running SO or normal match
$IsSO=false;
$NumArrows=$NumArrowsMatch;
$EndNo=ceil(max(strlen(rtrim($Match['arrowstring'])), strlen(rtrim($Match['oppArrowstring'])))/$NumArrows);
if(trim($Match['tiebreak'])!='' or trim($Match['oppTiebreak'])!='') {
    $IsSO = true;
    $NumArrows=$NumArrowsSO;
    $EndNo = ceil(max(strlen(rtrim($Match['tiebreak'])), strlen(rtrim($Match['oppTiebreak']))) / $NumArrows);
}

if($Match['status']==3 and $Match['oppStatus']==3 and $Match['winner']==0 and $Match['oppWinner']==0) {
    $EndNo++;
    if(!$IsSO AND $EndNo>$NumEnds) {
        $IsSO=true;
        $NumArrows=$NumArrowsSO;
        $EndNo=1;
    }
    $JSON['UpdateL'] = 1;
    $JSON['UpdateR'] = 1;
}

if($IsSO) {
    ${'Obj'.$Sx}='tiebreak';
    ${'Obj'.$Rx}='oppTiebreak';
    ${'Pos'.$Sx}='tiePosition';
    ${'Pos'.$Rx}='oppTiePosition';
} else {
    ${'Obj'.$Sx}='arrowstring';
    ${'Obj'.$Rx}='oppArrowstring';
    ${'Pos'.$Sx}='arrowPosition';
    ${'Pos'.$Rx}='oppArrowPosition';
}
$JSON['CurEnd'] = $EndNo + ($IsSO ? $NumEnds : 0);
$JSON['NumEnds'] = intval($NumEnds);
$JSON['NumSO'] = ($IsSO ? $EndNo : 0);

if($Team) {
	$JSON['Opp'.$Left]=$Match['countryName'].get_flag_ianseo($Match['countryCode'], 0, '', $_SESSION['TourCode']);
	$JSON['Opp'.$Right]=$Match['oppCountryName'].get_flag_ianseo($Match['oppCountryCode'], 0, '', $_SESSION['TourCode']);
} else {
	$JSON['Opp'.$Left]= $Match['fullName'] . ' - '.$Match['countryName'].get_flag_ianseo($Match['countryCode'], 0, '', $_SESSION['TourCode']);
	$JSON['Opp'.$Right]=$Match['oppFullName'] . ' - '.$Match['oppCountryName'].get_flag_ianseo($Match['oppCountryCode'], 0, '', $_SESSION['TourCode']);
}
$JSON['Event']=$Event.'<br/>'.$MatchName;

$ArrL=substr($Match[${'Obj'.$Sx}], $IndexL=$NumArrows*($EndNo-1), $NumArrows);
$ArrR=substr($Match[${'Obj'.$Rx}], $IndexR=$NumArrows*($EndNo-1), $NumArrows);

$JSON['Score'.$Left]='<div class="badge badge-danger" id="scoreLabelL" numArr="'.$NumArrows.'">'.($IsSO ? 'SO ' : 'End '). $EndNo .'</div>';
$JSON['Score'.$Right]='<div class="badge badge-danger" id="scoreLabelR" numArr="'.$NumArrows.'">'.($IsSO ? 'SO ' : 'End '). $EndNo .'</div>';

$TotL=ValutaArrowString($ArrL);
$TotR=ValutaArrowString($ArrR);

foreach(DecodeFromString(str_pad($ArrL, $NumArrows, ' ', STR_PAD_RIGHT), false, true) as $k => $Point) {
    $JSON['Score'.$Left].='<div class="badge badge-primary" id="'.$k.'-L" onclick="showSight(this)" onmouseover="showSight(this)" onmouseout="hideSight(this)">'.
        $Point.
        ((!empty($Match[${'Pos'.$Sx}][$IndexL+$k]) and isset($Match[${'Pos'.$Sx}][$IndexL+$k]['D'])) ? '<span class="arrowDist ml-2'.($IsSO ? '': ' hidden d-grid').'">'.($Match[${'Pos'.$Sx}][$IndexL+$k]['D']??'').'</span>' : '').
        '</div>';
}
$JSON['Score'.$Left].='<div class="badge badge-info">'.$TotL.(($IsSO AND $Match['closest']!=0 AND $k==($NumArrows-1)) ? '+':'').'</div>';
if(!$IsSO) {
    $JSON['Score'.$Left].='<div class="badge badge-secondary total">'.($Section['meta']['matchMode'] ? $Match['setScore'] : $Match['score']).'</div>';
}

foreach(DecodeFromString(str_pad($ArrR, $NumArrows, ' ', STR_PAD_RIGHT), false, true) as $k => $Point) {
    $JSON['Score'.$Right].='<div class="badge badge-primary" id="'.$k.'-R" onclick="showSight(this)" onmouseover="showSight(this)" onmouseout="hideSight(this)">'.
        $Point.
        ((!empty($Match[${'Pos'.$Rx}][$IndexR+$k]) and isset($Match[${'Pos'.$Rx}][$IndexR+$k]['D'])) ? '<span class="arrowDist ml-2'.($IsSO ? '': ' hidden').'">'.$Match[${'Pos'.$Rx}][$IndexR+$k]['D'].'</span>' : '').
        '</div>';
}
$JSON['Score'.$Right].='<div class="badge badge-info">'.$TotR.(($IsSO AND $Match['oppClosest']!=0 AND $k==($NumArrows-1)) ? '+':'').'</div>';
if(!$IsSO) {
    $JSON['Score'.$Right] .= '<div class="badge badge-secondary total">' . ($Section['meta']['matchMode'] ? $Match['oppSetScore'] : $Match['oppScore']) . '</div>';
}

$JSON['Winner'.$Sx] = ($Match['winner']==1);
$JSON['Winner'.$Rx] = ($Match['oppWinner']==1);

if($Team) {
    foreach ($Section['athletes'][$Match['teamId']][$Match['subTeam']] as $ath) {
        $JSON['Ath'.$Sx][] = array("Id"=>$ath['code'], "Ath"=>$ath['fullName']);
    }
    foreach ($Section['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']] as $ath) {
        $JSON['Ath'.$Rx][] = array("Id"=>$ath['code'], "Ath"=>$ath['fullName']);
    }
} else {
    $JSON['Ath'.$Sx][] = array("Id"=>$Match['bib'], "Ath"=>$Match['fullName']);
    $JSON['Ath'.$Rx][] = array("Id"=>$Match['oppBib'], "Ath"=>$Match['oppFullName']);
}

switch ($_REQUEST["View"]) {
    case 'Scorecard':
        $cols= $NumArrowsMatch;
        $rows  = $NumEnds;
        $so = $NumArrowsSO;
        $matchMode = $Section['meta']['matchMode'];

        $JSON['Tgt'.$Left] = '<table class="table table-bordered table-sm mt-2"><thead class="table-dark"><tr><th scope="col"></th>';
        for ($i=1; $i<=$cols; $i++) {
            $JSON['Tgt'.$Left] .= '<th scope="col" class="text-center">'.$i.'</th>';
        }
        $JSON['Tgt'.$Left] .= '<th scope="col" class="text-center">'.get_text('EndScore').'</th><th scope="col" class="text-center">'.($matchMode ? get_text('SetPoints', 'Tournament') : get_text('Total')).'</th></tr></thead>';
        $JSON['Tgt'.$Right] = $JSON['Tgt'.$Left];

        $arrString = str_pad($Match['arrowstring'],$rows*$cols," ",STR_PAD_RIGHT);
        $oppString = str_pad($Match['oppArrowstring'],$rows*$cols," ",STR_PAD_RIGHT);
        $lenSo = max(strlen(trim($Match['tiebreak'])),strlen(trim($Match['oppTiebreak'])));
        $arrSo = str_pad($Match['tiebreak'],$lenSo," ",STR_PAD_RIGHT);
        $oppSo = str_pad($Match['oppTiebreak'],$lenSo," ",STR_PAD_RIGHT);

        $athEnds = explode('|', $Match['setPoints']);
        $oppEnds = explode('|', $Match['oppSetPoints']);
        $athSets = explode('|', $Match['setPointsByEnd']);
        $oppSets = explode('|', $Match['oppSetPointsByEnd']);
        $athRunning=0;
        $oppRunning=0;
        for($r=0; $r<$rows; $r++) {
            $JSON['Tgt'.$Left] .= '<tr><th scope="row" class="table-dark text-center">'.($r+1).'</th>';
            $JSON['Tgt'.$Right] .= '<tr><th scope="row" class="table-dark text-center">'.($r+1).'</th>';
            for($c=0; $c<$cols; $c++) {
                $JSON['Tgt'.$Left] .= '<td class="text-center whiteBg">'.DecodeFromLetter($arrString[($r*$cols)+$c]).'</td>';
                $JSON['Tgt'.$Right] .= '<td class="text-center whiteBg">'.DecodeFromLetter($oppString[($r*$cols)+$c]).'</td>';

            }
            $athRunning += ($matchMode ? (empty($athSets[$r]) ? 0 : $athSets[$r]) : (empty($athEnds[$r]) ? 0 : $athEnds[$r]));
            $oppRunning += ($matchMode ? (empty($oppSets[$r]) ? 0 : $oppSets[$r]) : (empty($oppEnds[$r]) ? 0 : $oppEnds[$r]));
            $JSON['Tgt'.$Left] .= '<td class="text-right table-warning">'. (empty($athEnds[$r]) ? '' : $athEnds[$r]).'</td><td class="text-right font-weight-bold table-info">'.$athRunning.'</td></tr>';
            $JSON['Tgt'.$Right] .= '<td class="text-right table-warning">'.(empty($oppEnds[$r]) ? '' : $oppEnds[$r]).'</td><td class="text-right font-weight-bold table-info">'.$oppRunning.'</td></tr>';
        }
        for($r=0; $r<max(ceil($lenSo/$so),1); $r++) {
            $JSON['Tgt'.$Left] .= '<tr><th scope="row" class="table-dark text-center">'.($lenSo ? get_text('ShotOffShort', 'Tournament') . ' ' . ($r+1) : '&nbsp;').'</th>';
            $JSON['Tgt'.$Right] .= '<tr><th scope="row" class="table-dark text-center">'.($lenSo ? get_text('ShotOffShort', 'Tournament') . ' ' . ($r+1) : '&nbsp;').'</th>';
            if($lenSo) {
                for ($c = 0; $c < $so; $c++) {
                    $JSON['Tgt'.$Left] .= '<td class="text-center whiteBg">' . DecodeFromLetter($arrSo[($r * $so) + $c]??'') . '</td>';
                    $JSON['Tgt'.$Right] .= '<td class="text-center whiteBg">' . DecodeFromLetter($oppSo[($r * $so) + $c]??'') . '</td>';
                }
                if ($so < $cols) {
                    $JSON['Tgt'.$Left] .= '<td class="text-center whiteBg closestText" colspan="' . ($cols - $so) . '">' . ($Match['closest']!=0 ? '+':'&nbsp;') . '</td>';
                    $JSON['Tgt'.$Right] .= '<td class="text-center whiteBg closestText" colspan="' . ($cols - $so) . '">' . ($Match['oppClosest']!=0 ? '+':'&nbsp;') . '</td>';
                }
                $JSON['Tgt'.$Left] .= '<td class="text-right table-warning">' . ValutaArrowString(substr($arrSo, ($r * $so), $so)) . '</td>';
                $JSON['Tgt'.$Right] .= '<td class="text-right table-warning">' . ValutaArrowString(substr($oppSo, ($r * $so), $so)) . '</td>';
            } else {
                $JSON['Tgt'.$Left] .= '<td class="text-center whiteBg" colspan="' . ($cols+1) . '">&nbsp;</td>';
                $JSON['Tgt'.$Right] .= '<td class="text-center whiteBg" colspan="' . ($cols+1) . '">&nbsp;</td>';
            }
            if($r==0) {
                $JSON['Tgt'.$Left] .= '<td class="text-right font-weight-bold table-info align-middle" rowspan="'.max(ceil($lenSo/$so),1).'">' . ($matchMode ? $Match['setScore'] : $Match['score']) . '</td>';
                $JSON['Tgt'.$Right] .= '<td class="text-right font-weight-bold table-info align-middle" rowspan="'.max(ceil($lenSo/$so),1).'">' . ($matchMode ? $Match['oppSetScore'] : $Match['oppScore']) . '</td>';
            }
            $JSON['Tgt'.$Left] .= '</tr>';
            $JSON['Tgt'.$Right] .= '</tr>';

        }
        $JSON['Tgt'.$Left] .= '</table>';
        $JSON['Tgt'.$Right] .= '</table>';


        break;
    case 'Presentation':
        $options['extended'] = false;
        unset($options['matchno']);
        $J=array();
        if(!empty($Match['lineJudge'])) {
            $J[]='<div class="judges">'.get_text('LineJudge','Tournament').': <span class="judge">'.$Match['lineJudge'].'</span></div>';
        }
        if(!empty($Match['targetJudge'])) {
            $J[]='<div class="judges">'.get_text('TargetJudge','Tournament').': <span class="judge">'.$Match['targetJudge'].'</span></div>';
        }
        $Records='';
        if(count($Section['records']??[])) {
            $Records = '<br>';
            foreach ($Section['records'] as $record) {
                $Records .= '<div class="records"><b>' . $record->TrHeader . '</b> ' . $record->RtRecDistance . ': <b>' . $record->RtRecTotal.($record->RtRecXNine ? '/'.$record->RtRecXNine : '') . '</b>';
                foreach($record->RtRecExtra as $recDet) {
                    $arc = array();
                    foreach($recDet->Archers as $t => $Archer) {
                        $arc[]=$Archer['Archer'];
                    }
                    if($Team) {
                        $Records .= '<div class="oldrecord">' . $recDet->NocName . ' (' . implode(", ", $arc) . ') - ' . $recDet->EventNOC . ' ' . formatTextDate($record->RtRecDate) . '</div>';
                    } else {
                        $Records .= '<div class="oldrecord">' . implode(", ", $arc) . ' (' . $recDet->NOC . ') - ' . $recDet->EventNOC . ' ' . formatTextDate($record->RtRecDate) . '</div>';
                    }

                }
                $Records .= '</div>';
            }
        }
        if($Team) {
            //Left Team
            $options['coid'] = $Match['teamId'];
            $rank = Obj_RankFactory::create('GridTeam', $options);
            $rank->read();
            $rankData=$rank->getData();
            $JSON['Tgt'.$Left] = '<div id="picsL" class="d-flex justify-content-center">';
            foreach ($rankData['sections'][$options['events']]['athletes'][$Match['teamId']][$Match['subTeam']] as $ath) {
                $JSON['Tgt'.$Left] .= '<figure class="figure m-2">' .
                    get_photo_ianseo($ath['id'], '', '', 'class="figure-img rounded" style="width: 8vw;"', true, $_SESSION['TourCode']) .
                    '<figcaption class="figure-caption text-center"  style="width: 10vw; overflow-x: fragments">'.$ath['fullName'].'</figcaption>' .
                    '</figure>';
            }
            $JSON['Tgt'.$Left] .= '</div>';
            $JSON['Tgt'.$Left] .= '<div class="text-left">' .
                $Section['meta']['eventName'].
                '<ul><li>' . get_text('QualRound') . ': ' . $Match['qualScore'] . ' - #&nbsp;' . $Match['qualRank'] . '</li>';
            foreach ($rankData['sections'][$options['events']]['phases'] as $kPh => $vPh) {
                if($MatchNo >= $vPh['items'][0]['matchNo']) {
                    continue;
                }
                $JSON['Tgt'.$Left] .= '<li>'.$vPh['meta']['phaseName'].': ';
                if(($vPh['items'][0]['saved'] OR $vPh['items'][0]['oppSaved']) AND ($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) AND ($vPh['items'][0]['teamId']==0 OR $vPh['items'][0]['oppTeamId']==0)) {
                    $JSON['Tgt'.$Left] .= '<span class="small font-italic">'.$rankData['meta']['saved'].'</span>';
                } else if($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) {
                    if($vPh['items'][0]['teamId']==0 OR $vPh['items'][0]['oppTeamId']==0) {
                        $JSON['Tgt'.$Left] .= '<span class="small font-italic">' . get_text('Bye') . '</span>';
                    } else {
                        $JSON['Tgt'.$Left] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                            $vPh['items'][0]['countryName'] . '</span> (<span class="font-italic">' .
                            ' <span class="small">' . (($vPh['items'][0]['tie']==2) ? get_text('Bye'):$vPh['items'][0]['notes']).'</span>'.
                            ' - ' .
                            ' <span class="small">' . (($vPh['items'][0]['oppTie']==2) ? get_text('Bye'):$vPh['items'][0]['oppNotes']).'</span>'.
                            '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppCountryName'] .
                            '</span>';
                    }
                } else {
                    $JSON['Tgt'.$Left] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                        $vPh['items'][0]['countryName'] . '</span> (<span class="font-italic">' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['setScore'] : $vPh['items'][0]['score']) .
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['tiebreakDecoded'] .'</span>':'').
                        ' - ' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['oppSetScore'] : $vPh['items'][0]['oppScore']).
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['oppTiebreakDecoded'].'</span>':'').
                        '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppCountryName'] .
                        '</span>';
                }
                $JSON['Tgt'.$Left] .= '</li>';
            }
            if($vPh['items'][0]['irm']) {
	            $JSON['Tgt'.$Left] .= '<li><span class="font-weight-bold">'.$vPh['items'][0]['irmText'].'</span></li>';
            }
            $JSON['Tgt'.$Left] .= '</ul>';
            if(!empty($Match['coach'])) {
                $JSON['Tgt'.$Left] .= '<div id="CoachLeft">' . get_text('Coach', 'Tournament') . ' : <span id="CoachL">'.$Match['coach'].'</span></div>';
            }
            if(count($J)) {
                $JSON['Tgt'.$Left] .= '<br>'.implode('',$J);
            }
            $JSON['Tgt'.$Left] .= $Records.'</div>';

            //Right Team
            $options['coid'] = $Match['oppTeamId'];
            $rank = Obj_RankFactory::create('GridTeam', $options);
            $rank->read();
            $rankData=$rank->getData();
            $JSON['Tgt'.$Right] = '<div id="picsR" class="d-flex justify-content-center"">';
            foreach ($rankData['sections'][$options['events']]['athletes'][$Match['oppTeamId']][$Match['oppSubTeam']] as $ath) {
                $JSON['Tgt'.$Right] .= '<figure class="figure m-2">' .
                    get_photo_ianseo($ath['id'], '', '', 'class="figure-img rounded" style="width: 8vw;"', true, $_SESSION['TourCode']) .
                    '<figcaption class="figure-caption text-center" style="width: 10vw; overflow-x: fragments">'.$ath['fullName'].'</figcaption>' .
                    '</figure>';
            }
            $JSON['Tgt'.$Right] .= '</div>';
            $JSON['Tgt'.$Right] .= '<div class="text-left">' .
                $Section['meta']['eventName'].
                '<ul><li>' . get_text('QualRound') . ': ' . $Match['oppQualScore'] . ' - #&nbsp;' . $Match['oppQualRank'] . '</li>';
            foreach ($rankData['sections'][$options['events']]['phases'] as $kPh => $vPh) {
                if($MatchNo >= $vPh['items'][0]['matchNo']) {
                    continue;
                }
                $JSON['Tgt'.$Right] .= '<li>'.$vPh['meta']['phaseName'].': ';
                if(($vPh['items'][0]['saved'] OR $vPh['items'][0]['oppSaved']) AND ($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) AND ($vPh['items'][0]['teamId']==0 OR $vPh['items'][0]['oppTeamId']==0)) {
                    $JSON['Tgt'.$Right] .= '<span class="small font-italic">'.$rankData['meta']['saved'].'</span>';
                } else if($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) {
                    if($vPh['items'][0]['teamId']==0 OR $vPh['items'][0]['oppTeamId']==0) {
                        $JSON['Tgt'.$Right] .= '<span class="small font-italic">' . get_text('Bye') . '</span>';
                    } else {
                        $JSON['Tgt'.$Right] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                            $vPh['items'][0]['countryName'] . '</span> (<span class="font-italic">' .
                            ' <span class="small">' . (($vPh['items'][0]['tie']==2) ? get_text('Bye'):$vPh['items'][0]['notes']).'</span>'.
                            ' - ' .
                            ' <span class="small">' . (($vPh['items'][0]['oppTie']==2) ? get_text('Bye'):$vPh['items'][0]['oppNotes']).'</span>'.
                            '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppCountryName'] .
                            '</span>';
                    }
                } else {
                    $JSON['Tgt'.$Right] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                        $vPh['items'][0]['countryName'] . '</span> (<span class="font-italic">' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['setScore'] : $vPh['items'][0]['score']) .
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['tiebreakDecoded'] .'</span>':'').
                        ' - ' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['oppSetScore'] : $vPh['items'][0]['oppScore']).
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['oppTiebreakDecoded'].'</span>':'').
                        '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppCountryName'] .
                        '</span>';
                }
                $JSON['Tgt'.$Right] .= '</li>';
            }
            $JSON['Tgt'.$Right] .= '</ul>';
            if(!empty($Match['oppCoach'])) {
                $JSON['Tgt'.$Right] .= '<div id="CoachRight">' . get_text('Coach', 'Tournament') . ' : <span id="CoachR">'.$Match['oppCoach'].'</span></div>';
            }
            if(count($J)) {
                $JSON['Tgt'.$Right] .= '<br>'.implode('',$J);
            }
            $JSON['Tgt'.$Right] .= $Records.'</div>';

        } else {
            //Left Archer
            $options['enid'] = ($Match['id']??$Match['itemId']);
            if($MatchNo>256) {
                $options['events']=$Section['meta']['eventParent'];
            }
            $rank = Obj_RankFactory::create('GridInd', $options);
            $rank->read();
            $rankData=$rank->getData();
            $JSON['Tgt'.$Left] = '<div id="picsL" class="d-flex justify-content-center"><figure class="figure m-2">' .
                get_photo_ianseo($Match['id']??$Match['itemId'], 150, '', 'class="figure-img rounded"', true, $_SESSION['TourCode']) .
                '<figcaption class="figure-caption text-center">'.$Match['fullName'].'</figcaption>' .
                '</figure></div>' .
                '<div class="text-left">' .
                ($Section['meta']['eventName']??$Section['meta']['descr']).
                '<ul><li>' . get_text('QualRound') . ': ' . $Match['qualScore'] . ' - #&nbsp;' . $Match['qualRank'] . '</li>';
            foreach ($rankData['sections'][$options['events']]['phases'] as $kPh => $vPh) {
                if(($MatchNo<=256 and $MatchNo >= $vPh['items'][0]['matchNo']) or ($MatchNo>256 and ($Section['meta']['lastPhase']??0) > $kPh)) {
                    continue;
                }
                $JSON['Tgt'.$Left] .= '<li>'.$vPh['meta']['phaseName'].': ';
                if(($vPh['items'][0]['saved'] OR $vPh['items'][0]['oppSaved']) AND ($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) AND ($vPh['items'][0]['id']==0 OR $vPh['items'][0]['oppId']==0)) {
                    $JSON['Tgt'.$Left] .= '<span class="small font-italic">'.$rankData['meta']['saved'].'</span>';
                } else if($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) {
                    if($vPh['items'][0]['id']==0 OR $vPh['items'][0]['oppId']==0) {
                        $JSON['Tgt'.$Left] .= '<span class="small font-italic">' . get_text('Bye') . '</span>';
                    } else {
                        $JSON['Tgt'.$Left] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                            $vPh['items'][0]['countryCode'] . ' - ' . $vPh['items'][0]['fullName'] . '</span> (<span class="font-italic">' .
                            ' <span class="small">' . (($vPh['items'][0]['tie']==2) ? get_text('Bye'):$vPh['items'][0]['notes']).'</span>'.
                            ' - ' .
                            ' <span class="small">' . (($vPh['items'][0]['oppTie']==2) ? get_text('Bye'):$vPh['items'][0]['oppNotes']).'</span>'.
                            '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppFullName'] . ' - '.  $vPh['items'][0]['oppCountryCode'] .
                            '</span>';
                    }
                } else {
                    $JSON['Tgt'.$Left] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                        $vPh['items'][0]['countryCode'] . ' - ' . $vPh['items'][0]['fullName'] . '</span> (<span class="font-italic">' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['setScore'] : $vPh['items'][0]['score']) .
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['tiebreakDecoded'] .'</span>':'').
                        ' - ' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['oppSetScore'] : $vPh['items'][0]['oppScore']).
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['oppTiebreakDecoded'].'</span>':'').
                        '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppFullName'] . ' - '.  $vPh['items'][0]['oppCountryCode'] .
                        '</span>';
                }
                $JSON['Tgt'.$Left] .= '</li>';
            }
            $JSON['Tgt'.$Left] .= '</ul>';

            if($MatchNo>256) {
                $JSON['Tgt'.$Left] .= '<ul>';
                // add also the round robin results
                // Left Archer
                $opts=[
                    'events'=>$Event,
                    'enid'=>$Match['itemId'],
                ];
                $rank = Obj_RankFactory::create('Robin', $opts);
                $rank->OnlyMatch=true;
                $rank->read();
                $rankData=$rank->getData();
                $header=true;
                foreach ($rankData['sections'][$Event]['levels'] as $kLev => $vLev) {
                    foreach($vLev['matches'] as $kGroup => $vGroup) {
                        foreach($vGroup['rounds'] as $kRound=>$vRound) {
                            foreach($vRound['items'] as $vMatch) {
                                if($header) {
                                    $JSON['Tgt'.$Left] .= '</div><div class="text-left">'.get_text('PreSeedNum', 'RoundRobin', ($Match['itemId']==$vMatch['itemId'] ? $vMatch['sourceRank'] : $vMatch['oppSourceRank']));
                                    $JSON['Tgt'.$Left] .= '<ul>';
                                    $header=false;
                                }
                                if($vLev['name'].' - '.$vGroup['name'].' - '.$vRound['name']==$MatchName) {
                                    continue;
                                }
                                $JSON['Tgt'.$Left] .= '<li>'.$vLev['name'].' - '.$vGroup['name'].' - '.$vRound['name'].': ';
                                if($vMatch['tie']==2 OR $vMatch['oppTie']==2) {
                                    if($vMatch['itemId']==0 OR $vMatch['oppItemId']==0) {
                                        $JSON['Tgt'.$Left] .= '<span class="small font-italic">' . get_text('Bye') . '</span>';
                                    } else {
                                        $JSON['Tgt'.$Left] .= '<span class="'.($vMatch['winner'] ? 'font-weight-bold':'').'">' .
                                            $vMatch['countryCode'] . ' - ' . $vMatch['fullName'] . '</span> (<span class="font-italic">' .
                                            ' <span class="small">' . (($vMatch['tie']==2) ? get_text('Bye'):$vMatch['notes']).'</span>'.
                                            ' - ' .
                                            ' <span class="small">' . (($vMatch['oppTie']==2) ? get_text('Bye'):$vMatch['oppNotes']).'</span>'.
                                            '</span>) <span class="'.($vMatch['oppWinner'] ? 'font-weight-bold':'').'">' . $vMatch['oppFullName'] . ' - '.  $vMatch['oppCountryCode'] .
                                            '</span>';
                                    }
                                } else {
                                    $JSON['Tgt'.$Left] .= '<span class="'.($vMatch['winner'] ? 'font-weight-bold':'').'">' .
                                        $vMatch['countryCode'] . ' - ' . $vMatch['fullName'] . '</span> (<span class="font-italic">' .
                                        ($rankData['sections'][$Event]['meta']['matchMode'] ? $vMatch['setScore'] : $vMatch['score']) .
                                        (($vMatch['tie']==1 OR $vMatch['oppTie']==1) ? ' <span class="small">T.'.$vMatch['tiebreakDecoded'] .'</span>':'').
                                        ' - ' .
                                        ($rankData['sections'][$Event]['meta']['matchMode'] ? $vMatch['oppSetScore'] : $vMatch['oppScore']).
                                        (($vMatch['tie']==1 OR $vMatch['oppTie']==1) ? ' <span class="small">T.'.$vMatch['oppTiebreakDecoded'].'</span>':'').
                                        '</span>) <span class="'.($vMatch['oppWinner'] ? 'font-weight-bold':'').'">' . $vMatch['oppFullName'] . ' - '.  $vMatch['oppCountryCode'] .
                                        '</span>';
                                }
                                $JSON['Tgt'.$Left] .= '</li>';
                            }
                        }
                    }
                }
                $JSON['Tgt'.$Left] .= '</ul>';
                if(!empty($Match['coach'])) {
                    $JSON['Tgt'.$Left] .= '<div id="CoachLeft">' . get_text('Coach', 'Tournament') . ' : <span id="CoachL">'.$Match['coach'].'</span></div>';
                }
                if(count($J)) {
                    $JSON['Tgt'.$Left] .= '<br>'.implode('',$J);
                }
                $JSON['Tgt'.$Left] .= '</ul>';
            }

            if(!empty($Match['coach'])) {
                $JSON['Tgt'.$Left] .= '<div id="CoachLeft">' . get_text('Coach', 'Tournament') . ' : <span id="CoachL">'.$Match['coach'].'</span></div>';
            }
            if(count($J)) {
                $JSON['Tgt'.$Left] .= '<br>'.implode('',$J);
            }
            $JSON['Tgt'.$Left] .= $Records.'</div>';

        //Right Archer
            $options['enid'] = ($Match['oppId']??$Match['oppItemId']);
            if($MatchNo>256) {
                $options['events']=$Section['meta']['eventParent'];
            }
            $rank = Obj_RankFactory::create('GridInd', $options);
            $rank->read();
            $rankData=$rank->getData();
            $JSON['Tgt'.$Right] = '<div id="picsR" class="d-flex justify-content-center"><figure class="figure m-2">' .
                get_photo_ianseo($Match['oppId']??$Match['oppItemId'], 150, '', 'class="figure-img rounded"', true, $_SESSION['TourCode']) .
                '<figcaption class="figure-caption text-center">'.$Match['oppFullName'].'</figcaption>' .
                '</figure></div>' .
                '<div class="text-left">' .
                ($Section['meta']['eventName']??$Section['meta']['descr']).
                '<ul><li>' . get_text('QualRound') . ': ' . $Match['oppQualScore'] . ' - #&nbsp;' . $Match['oppQualRank'] . '</li>';
            foreach ($rankData['sections'][$options['events']]['phases'] as $kPh => $vPh) {
                if(($MatchNo<=256 and $MatchNo >= $vPh['items'][0]['matchNo']) or ($MatchNo>256 and ($Section['meta']['lastPhase']??0) > $kPh)) {
                    continue;
                }
                $JSON['Tgt'.$Right] .= '<li>'.$vPh['meta']['phaseName'].': ';
                if(($vPh['items'][0]['saved'] OR $vPh['items'][0]['oppSaved']) AND ($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) AND ($vPh['items'][0]['id']==0 OR $vPh['items'][0]['oppId']==0)) {
                    $JSON['Tgt'.$Right] .= '<span class="small font-italic">' . $rankData['meta']['saved'] . '</span>';
                } else if($vPh['items'][0]['tie']==2 OR $vPh['items'][0]['oppTie']==2) {
                    if($vPh['items'][0]['id']==0 OR $vPh['items'][0]['oppId']==0) {
                        $JSON['Tgt'.$Right] .= '<span class="small font-italic">' . get_text('Bye') . '</span>';
                    } else {
                        $JSON['Tgt'.$Right] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                            $vPh['items'][0]['countryCode'] . ' - ' . $vPh['items'][0]['fullName']  . '</span> (<span class="font-italic">' .
                            ' <span class="small">' . (($vPh['items'][0]['tie']==2) ? get_text('Bye'):$vPh['items'][0]['notes']).'</span>'.
                            ' - ' .
                            ' <span class="small">' . (($vPh['items'][0]['oppTie']==2) ? get_text('Bye'):$vPh['items'][0]['oppNotes']).'</span>'.
                            '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppFullName'] . ' - '.  $vPh['items'][0]['oppCountryCode'] .
                            '</span>';
                    }
                } else {
                    $JSON['Tgt'.$Right] .= '<span class="'.($vPh['items'][0]['winner'] ? 'font-weight-bold':'').'">' .
                        $vPh['items'][0]['countryCode'] . ' - ' . $vPh['items'][0]['fullName'] . '</span> (<span class="font-italic">' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['setScore'] : $vPh['items'][0]['score']) .
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['tiebreakDecoded'] .'</span>':'').
                        ' - ' .
                        ($rankData['sections'][$options['events']]['meta']['matchMode'] ? $vPh['items'][0]['oppSetScore'] : $vPh['items'][0]['oppScore']).
                        (($vPh['items'][0]['tie']==1 OR $vPh['items'][0]['oppTie']==1) ? ' <span class="small">T.'.$vPh['items'][0]['oppTiebreakDecoded'].'</span>':'').
                        '</span>) <span class="'.($vPh['items'][0]['oppWinner'] ? 'font-weight-bold':'').'">' . $vPh['items'][0]['oppFullName'] . ' - '.  $vPh['items'][0]['oppCountryCode'] .
                        '</span>';

                }
                $JSON['Tgt'.$Right] .= '</li>';
            }

            $JSON['Tgt'.$Right] .= '</ul>';

            if($MatchNo>256) {
                // add also the round robin results
                $opts=[
                    'events'=>$Event,
                    'enid'=>$Match['oppItemId'],
                ];
                $rank = Obj_RankFactory::create('Robin', $opts);
                $rank->OnlyMatch=true;
                $rank->read();
                $rankData=$rank->getData();
                $header=true;
                foreach ($rankData['sections'][$Event]['levels'] as $kLev => $vLev) {
                    foreach($vLev['matches'] as $kGroup => $vGroup) {
                        foreach($vGroup['rounds'] as $kRound=>$vRound) {
                            foreach($vRound['items'] as $vMatch) {
                                if($header) {
                                    $JSON['Tgt'.$Right] .= '</div><div class="text-left">'.get_text('PreSeedNum', 'RoundRobin', ($Match['oppItemId']==$vMatch['itemId'] ? $vMatch['sourceRank'] : $vMatch['oppSourceRank']));
                                    $JSON['Tgt'.$Right] .= '<ul>';
                                    $header=false;
                                }
                                if($vLev['name'].' - '.$vGroup['name'].' - '.$vRound['name']==$MatchName) {
                                    continue;
                                }
                                $JSON['Tgt'.$Right] .= '<li>'.$vLev['name'].' - '.$vGroup['name'].' - '.$vRound['name'].': ';
                                if($vMatch['tie']==2 OR $vMatch['oppTie']==2) {
                                    if($vMatch['itemId']==0 OR $vMatch['oppItemId']==0) {
                                        $JSON['Tgt'.$Right] .= '<span class="small font-italic">' . get_text('Bye') . '</span>';
                                    } else {
                                        $JSON['Tgt'.$Right] .= '<span class="'.($vMatch['winner'] ? 'font-weight-bold':'').'">' .
                                            $vMatch['countryCode'] . ' - ' . $vMatch['fullName'] . '</span> (<span class="font-italic">' .
                                            ' <span class="small">' . (($vMatch['tie']==2) ? get_text('Bye'):$vMatch['notes']).'</span>'.
                                            ' - ' .
                                            ' <span class="small">' . (($vMatch['oppTie']==2) ? get_text('Bye'):$vMatch['oppNotes']).'</span>'.
                                            '</span>) <span class="'.($vMatch['oppWinner'] ? 'font-weight-bold':'').'">' . $vMatch['oppFullName'] . ' - '.  $vMatch['oppCountryCode'] .
                                            '</span>';
                                    }
                                } else {
                                    $JSON['Tgt'.$Right] .= '<span class="'.($vMatch['winner'] ? 'font-weight-bold':'').'">' .
                                        $vMatch['countryCode'] . ' - ' . $vMatch['fullName'] . '</span> (<span class="font-italic">' .
                                        ($rankData['sections'][$Event]['meta']['matchMode'] ? $vMatch['setScore'] : $vMatch['score']) .
                                        (($vMatch['tie']==1 OR $vMatch['oppTie']==1) ? ' <span class="small">T.'.$vMatch['tiebreakDecoded'] .'</span>':'').
                                        ' - ' .
                                        ($rankData['sections'][$Event]['meta']['matchMode'] ? $vMatch['oppSetScore'] : $vMatch['oppScore']).
                                        (($vMatch['tie']==1 OR $vMatch['oppTie']==1) ? ' <span class="small">T.'.$vMatch['oppTiebreakDecoded'].'</span>':'').
                                        '</span>) <span class="'.($vMatch['oppWinner'] ? 'font-weight-bold':'').'">' . $vMatch['oppFullName'] . ' - '.  $vMatch['oppCountryCode'] .
                                        '</span>';
                                }
                                $JSON['Tgt'.$Right] .= '</li>';
                            }
                        }
                    }
                }
                if(!$header) {
                    $JSON['Tgt'.$Right] .= '</ul>';
                }
                if(!empty($Match['coach'])) {
                    $JSON['Tgt'.$Right] .= '<div id="CoachLeft">' . get_text('Coach', 'Tournament') . ' : <span id="CoachL">'.$Match['coach'].'</span></div>';
                }
                if(count($J)) {
                    $JSON['Tgt'.$Right] .= '<br>'.implode('',$J);
                }
            }

            if(!empty($Match['oppCoach'])) {
                $JSON['Tgt'.$Right] .= '<div id="CoachRight">' . get_text('Coach', 'Tournament') . ' : <span id="CoachR">'.$Match['oppCoach'].'</span></div>';
            }
            if(count($J)) {
                $JSON['Tgt'.$Right] .= '<br>'.implode('',$J);
            }
            $JSON['Tgt'.$Right] .= $Records . '</div>';
        }
        break;
    case 'Ceremony':
        $Ceremonies=array('','');
        require_once('sub-Ceremonies.php');
        $JSON['Tgt'.$Left] = $Ceremonies[0];
        $JSON['Tgt'.$Right] = $Ceremonies[1];
        break;
    case 'Target':
        require_once("Common/Obj_Target.php");
        $target = new Obj_Target();
        $SelectedEnd=$EndNo;
        $JSON['SelectedEnd'] = intval($_REQUEST["SelectedEnd"]);
        if(!empty($_REQUEST["SelectedEnd"])) {
            $SelectedEnd = intval($_REQUEST["SelectedEnd"]);
            if($SelectedEnd <=  $NumEnds) {
                $IsSO = false;
                ${'Obj'.$Sx}='arrowstring';
                ${'Obj'.$Rx}='oppArrowstring';
                ${'Pos'.$Sx}='arrowPosition';
                ${'Pos'.$Rx}='oppArrowPosition';
                $NumArrows = $NumArrowsMatch;
            } else {
                $IsSO = true;
                ${'Obj'.$Sx}='tiebreak';
                ${'Obj'.$Rx}='oppTiebreak';
                ${'Pos'.$Sx}='tiePosition';
                ${'Pos'.$Rx}='oppTiePosition';
                $NumArrows = $NumArrowsSO;
                $SelectedEnd -= $NumEnds;
            }
        }
        $IndexArrow = (($SelectedEnd - 1) * $NumArrows);

        // we already have most of the data needed for the target!
        $target->initSVG($TourId, $Event, $MatchNo, $Team);
        $target->setSVGHeader('', '');
        $target->setTarget();

        // get the arrow timing, assuming it is the last one
        $lastTime='';
        $LastArrow='';

        $q=safe_r_sql("select date_sub(now(), interval 10 minute) as CurrentDateTime");
        $r=safe_fetch($q);

        if($Match['lastUpdated']>$r->CurrentDateTime) {
            $q=safe_r_sql("select * from FinOdfTiming where FinOdfEvent='$Event' and FinOdfTeamEvent=$Team and FinOdfMatchno in ($MatchNo, ".($MatchNo+1).") and FinOdfTournament={$_SESSION['TourId']} ");
            while($r=safe_fetch($q)) {
                if($r->FinOdfArrows) {
                    $ar=json_decode($r->FinOdfArrows, true);
                    if(!is_array($ar)) {
                        $ar=array($ar);
                    }
                    $ar=end($ar);
                    if($ar['Ts']>$lastTime) {
                        $lastTime=$ar['Ts'];
                        $LastArrow=($r->FinOdfMatchno==$MatchNo ? 'L' : 'R');
                    }

                }
            }
        }

        $JSON['TgtSize'] = $target->Diameter;
        $JSON['TgtZoom'] = round(sqrt($target->TargetRadius) / 7, 1);

        $arrowsL=array();
        $arrowsR=array();
        foreach(range($IndexArrow, $IndexArrow+$NumArrows-1) as $k => $i) {
        	if(isset($Match[${'Pos'.$Sx}][$i])) {
	            $arrowsL[$k]=$Match[${'Pos'.$Sx}][$i];
	        }
        	if(isset($Match[${'Pos'.$Rx}][$i])) {
	            $arrowsR[$k]=$Match[${'Pos'.$Rx}][$i];
	        }
        }

        $target2=clone $target;

        $ar=array('X'=>-1000,'Y'=>-1000);
        $target->drawSVGArrows($arrowsL, true, $LastArrow=='L');
        $target->DrawSVGSighter($ar, false, 'SighterL');
        $JSON['Tgt'.$Left] = $target->OutputStringSVG();

        $target2->drawSVGArrows($arrowsR, true, $LastArrow=='R');
        $target2->DrawSVGSighter($ar, false, 'SighterR');
        $JSON['Tgt'.$Right] = $target2->OutputStringSVG();
        $JSON['LastArrow'] = $LastArrow;

        if($JSON['SelectedEnd']!=0 AND $JSON['SelectedEnd']!=$JSON['CurEnd']) {
            $tmp = array('L'=>'','R'=>'');
            $ArrL=substr($Match[${'Obj'.$Sx}], $IndexL=$NumArrows*($SelectedEnd-1), $NumArrows);
            $ArrR=substr($Match[${'Obj'.$Rx}], $IndexR=$NumArrows*($SelectedEnd-1), $NumArrows);
            $tmp[$Left]='<div class="badge badge-dark" id="scoreLabelL" numArr="'.$NumArrows.'">'.($IsSO ? 'SO ' : 'End '). $SelectedEnd .'</div>';
            $tmp[$Right]='<div class="badge badge-dark" id="scoreLabelR" numArr="'.$NumArrows.'">'.($IsSO ? 'SO ' : 'End '). $SelectedEnd .'</div>';

            $TotL=ValutaArrowString($ArrL);
            $TotR=ValutaArrowString($ArrR);

            foreach(DecodeFromString(str_pad($ArrL, $NumArrows, ' ', STR_PAD_RIGHT), false, true) as $k => $Point) {
                $tmp[$Left].='<div class="badge badge-primary" id="'.$k.'-L" onclick="showSight(this)" onmouseover="showSight(this)" onmouseout="hideSight(this)">'.
                    $Point.
                    ((!empty($Match[${'Pos'.$Sx}][$IndexL+$k]) and isset($Match[${'Pos'.$Sx}][$IndexL+$k]['D'])) ? '<span class="arrowDist ml-2'.($IsSO ? '': ' hidden d-grid').'">'.($Match[${'Pos'.$Sx}][$IndexL+$k]['D']??'').'</span>' : '').
                    '</div>';
            }
            $tmp[$Left].='<div class="badge badge-info">'.$TotL.(($IsSO AND $Match['closest']!=0 AND $k==($NumArrows-1)) ? '+':'').'</div>';

            foreach(DecodeFromString(str_pad($ArrR, $NumArrows, ' ', STR_PAD_RIGHT), false, true) as $k => $Point) {
                $tmp[$Right].='<div class="badge badge-primary" id="'.$k.'-R" onclick="showSight(this)" onmouseover="showSight(this)" onmouseout="hideSight(this)">'.
                    $Point.
                    ((!empty($Match[${'Pos'.$Rx}][$IndexR+$k]) and isset($Match[${'Pos'.$Rx}][$IndexR+$k]['D'])) ? '<span class="arrowDist ml-2'.($IsSO ? '': ' hidden').'">'.$Match[${'Pos'.$Rx}][$IndexR+$k]['D'].'</span>' : '').
                    '</div>';
            }
            $tmp[$Right].='<div class="badge badge-info">'.$TotR.(($IsSO AND $Match['oppClosest']!=0 AND $k==($NumArrows-1)) ? '+':'').'</div>';
            $JSON['Tgt'.$Left] = '<div class="p-0">' . $JSON['Tgt'.$Left] . '</div><div class="previous-end">' . $tmp[$Left] . '</div>';
            $JSON['Tgt'.$Right] = '<div class="p-0">' . $JSON['Tgt'.$Right] . '</div><div class="previous-end">' . $tmp[$Right] . '</div>';
        }

}

JsonOut($JSON);
