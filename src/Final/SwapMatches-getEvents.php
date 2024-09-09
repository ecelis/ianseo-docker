<?php
$JSON=array('error' => 1, 'events' => array(), 'msg'=>'');
require_once(dirname(dirname(__FILE__)) . '/config.php');

if(!CheckTourSession()) {
	$JSON['msg']=get_text('CrackError');
	JsonOut($JSON);
}

checkACL(array(AclIndividuals, AclTeams), AclReadWrite);

$SQL="(" . "SELECT 0 as RoundRobin, EvCode, EvEventName, EvTeamEvent, EvProgr, EvFinalFirstPhase 
        FROM Events 
        WHERE EvTournament={$_SESSION['TourId']} AND EvFinalFirstPhase!=0 
    ) UNION (
        SELECT 1 as RoundRobin, EvCode, EvEventName, EvTeamEvent, EvProgr, EvFinalFirstPhase
        FROM Events 
        WHERE EvTournament={$_SESSION['TourId']} AND EvFinalFirstPhase=0 and EvElimType=5
        group by EvCode, EvTeamEvent
    )
    order by EvTeamEvent, EvProgr
    ";

$q=safe_r_SQL($SQL);

$options=array();
while($r=safe_fetch($q)) {
	$options[$r->RoundRobin][$r->EvTeamEvent][$r->EvCode]=$r->EvEventName;
}

$JSON['error']=0;
foreach($options as $IsRobin => $AllEvents) {
    $OldTeam='--';
    if(count($options)>=1 and $IsRobin) {
        $JSON['events'][]=array('cl'=>'ev-bold', 'val'=>'','text'=>get_text('R-Session', 'Tournament'));
    }
    foreach($AllEvents as $Team => $events) {
        if(count($events)>=1 and $OldTeam!==$Team) {
            $JSON['events'][]=array('cl'=>'ev-bold', 'val'=>'','text'=>get_text(($Team ? 'T' : 'I').'-Session', 'Tournament'));
        }
        foreach($events as $k => $v) {
            $JSON['events'][]=array('cl'=>'ev-norm', 'val'=>($IsRobin?'R':'').$Team.'-'.$k,'text'=>'&nbsp;&nbsp;&nbsp;'.$v);
        }
        $OldTeam=$Team;
    }
}

JsonOut($JSON);
