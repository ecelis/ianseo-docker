<?php
$JSON=array('error' => 1, 'phases' => array(), 'msg'=>'');
require_once(dirname(dirname(__FILE__)) . '/config.php');

if(!CheckTourSession()) {
	$JSON['msg']=get_text('CrackError');
	JsonOut($JSON);
}

checkACL(array(AclIndividuals, AclTeams), AclReadWrite);

if(empty($_REQUEST['ev'])) {
	$JSON['msg']=get_text('BadParams', 'Tournament');
	JsonOut($JSON);
}

list($Team, $Event)=explode('-', $_REQUEST['ev'], 2);

if($Team[0]=='R') {
    // Round Robin Stuff
    $Team=substr($Team,1);
    $q=safe_r_SQL("select * from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=".intval($Team)." and EvCode=".StrSafe_DB($Event));
    if($r=safe_fetch($q)) {
        $q=safe_r_sql("select RrLevLevel, RrLevName from RoundRobinLevel where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event));
        while($r=safe_fetch($q)) {
            $JSON['phases'][]=array('cl'=>'ev-norm', 'val'=>$r->RrLevLevel,'text'=>$r->RrLevName);
        }
        $JSON['error']=0;
    }

    JsonOut($JSON);
}

// resume regular events
$q=safe_r_SQL("select * from Events where EvFinalFirstPhase>0 and EvTournament={$_SESSION['TourId']} and EvTeamEvent=".intval($Team)." and EvCode=".StrSafe_DB($Event));
if($r=safe_fetch($q)) {
	require_once('Common/Lib/Fun_Phases.inc.php');
	$JSON['error']=0;
	$CurPhase=valueFirstPhase($r->EvFinalFirstPhase);
	while($CurPhase) {
		$JSON['phases'][]=array('cl'=>'ev-norm', 'val'=>$CurPhase,'text'=>get_text(namePhase($r->EvFinalFirstPhase, $CurPhase).'_Phase'));
		$CurPhase=intval($CurPhase/2);
	}
	$JSON['phases'][]=array('cl'=>'ev-norm', 'val'=>$CurPhase,'text'=>get_text('0_Phase'));
}

JsonOut($JSON);