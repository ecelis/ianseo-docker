<?php

require_once(dirname(__FILE__, 5) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Final/Fun_ChangePhase.inc.php');

CheckTourSession(true);
checkACL(AclIndividuals, AclReadWrite);

$Error=false;

$advMode = (!empty($_REQUEST["Advanced"]));

$EventCodes=array();
if(isset($_REQUEST['EventCodes'])) {
    $EventCodes=$_REQUEST['EventCodes'];
}

if(!empty($_REQUEST["RESET"]) AND intval($_REQUEST["RESET"])==(count($EventCodes)*42)) {
    foreach ($EventCodes as $evCodeParent) {
        $allEvents = getChildrenEvents($evCodeParent, $Team=0, $_SESSION['TourId']);
        foreach ($allEvents as $evCode) {
            ResetShootoff($evCode, 0, 0);
            Obj_RankFactory::create('Abs', array('tournament' => $_SESSION['TourId'], 'events' => $evCode, 'dist' => 0))->calculate();

            // destroys the grid of all the events that need "handling"
            safe_w_sql("DELETE FROM Finals WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent = " . StrSafe_DB($evCode));

            // Recreate Empty Grids
            safe_w_SQL("INSERT INTO Finals (FinEvent, FinMatchNo, FinTournament, FinDateTime) " .
                "SELECT EvCode, GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . ", NOW() " .
                "FROM Events " .
                "INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0 " .
                "INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
            WHERE EvCode=" . StrSafe_DB($evCode));
        }
    }
    header('location: ' . $_SERVER["PHP_SELF"]);
    die();
}

$JS_SCRIPT = array('<script type="text/javascript" src="./AbsTae.js"></script>',
    '<link href="./AbsTae.css" rel="stylesheet" type="text/css">',
    );
$IncludeJquery = true;
include('Common/Templates/head.php');

$q=safe_r_sql("select ToGolds, ToXNine from Tournament where ToId={$_SESSION['TourId']}");
$TOUR=safe_fetch($q);
$Sql = "select EvCode, IndId, QuScore, QuGold, QuXnine, IndRank, IndTieBreak, IndTbDecoded, IndTbClosest, EnFirstName, EnName, if(TfT2>0, TfT2, TfT1) as TargetId
            from Individuals
            inner join Qualifications on QuId=IndId
            inner join Entries on EnId=IndId
            inner join Events on EvTournament=IndTournament and EvCode=IndEvent and EvTeamEvent=0 and EvFinalFirstPhase=0
            inner join (select EvCode TieCode, count(*) as ExAequo, QuScore as TieScore
                from Individuals
                inner join Events on EvTournament=IndTournament and EvCode=IndEvent and EvTeamEvent=0 and EvFinalFirstPhase=0
                inner join Qualifications on QuId=IndId
                where IndTournament={$_SESSION['TourId']} and IndRank between 1 and 4
                group by EvCode, QuScore
                having ExAequo>1) Ties on TieCode=IndEvent and QuScore=TieScore
            inner join TargetFaces on TfTournament=IndTournament and TfId=EnTargetFace
            where IndTournament={$_SESSION['TourId']}
            order by EvProgr, QuScore desc, IndRank asc";
$q=safe_r_SQL($Sql);
$OldEvent='';
$OldScore=0;
$Cats=[];
while ($r=safe_fetch($q)) {
    if(empty($Cats[$r->EvCode][$r->QuScore])) {
        $Cats[$r->EvCode][$r->QuScore]=[
            'Min'=>$r->IndRank,
            'Max'=>$r->IndRank,
            'Items'=>[],
        ];
        $OldScore=$r->QuScore;
    }
    if($OldEvent==$r->EvCode and $OldScore==$r->QuScore) {
        $Cats[$r->EvCode][$r->QuScore]['Max']=$r->IndRank;
    }
    $Cats[$r->EvCode][$r->QuScore]['Items'][]=$r;

    $OldScore=$r->QuScore;
    $OldEvent=$r->EvCode;
}
echo '<table class="Tabella2">';
echo '<thead><tr><th colspan="11" class="Title">'.(get_text('ShootOff4Rank') . ' - ' . get_text('Individual')).'</th></tr>';
echo '<tr>
    <th>'.get_text('Event').'</th>
    <th colspan="2"></th>
    <th>'.get_text('Score', 'Tournament').'</th>
    <th>'.$TOUR->ToGolds.'</th>
    <th>'.$TOUR->ToXNine.'</th>
    <th colspan="3">'.get_text('ShootOffArrows', 'Tournament').'</th>
    <th>'.get_text('ClosestShort', 'Tournament').'</th>
    <th>'.get_text('Rank').'</th>
    </tr></thead><tbody>';
$First=true;
foreach($Cats as $EvCode => $Items) {

    if(!$First) {
        echo '<tr class="Divider"><th colspan="11" class="Divider">&nbsp;</th></tr>';
    }
    $First=false;

    foreach($Items as $Scores) {
        foreach($Scores['Items'] as $r) {
            echo '<tr class="EventLine" event="'.$r->EvCode.'"  id="'.$r->IndId.'" tgt="'.$r->TargetId.'">'.
                '<th class="w-10ch">'.$r->EvCode.'</th>'.
                '<td>'.$r->EnFirstName.'</td>'.
                '<td>'.$r->EnName.'</td>'.
                '<td class="Right w-7ch">'.$r->QuScore .'</td>'.
                '<td class="Right w-7ch">'.$r->QuGold.'</td>'.
                '<td class="Right w-7ch">'.$r->QuXnine.'</td>'.
                '<td class="Center w-7ch px-2"><input type="text" name="IndTieBreak_0" value="'.DecodeFromLetter($r->IndTieBreak[0]??'').'" onchange="setValue(this)"></td>'.
                '<td class="Center w-7ch px-2"><input type="text" name="IndTieBreak_1" value="'.DecodeFromLetter($r->IndTieBreak[1]??'').'" onchange="setValue(this)"></td>'.
                '<td class="Center w-7ch px-2"><input type="text" name="IndTieBreak_2" value="'.DecodeFromLetter($r->IndTieBreak[2]??'').'" onchange="setValue(this)"></td>'.
                '<td class="Center w-7ch px-2"><input type="checkbox" name="IndTbClosest" '.($r->IndTbClosest ? ' checked="checked"' : '').' onchange="setValue(this)"></td>'.
                '<td class="Center w-7ch px-2"><input type="number" name="IndRank" min="'.$Scores['Min'].'" max="'.$Scores['Max'].'" value="'.$r->IndRank.'" onchange="setValue(this)"></td>'.
                '</tr>';
        }
    }
}

echo '</tbody></table>';

include('Common/Templates/tail.php');
