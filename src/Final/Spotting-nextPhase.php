<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Fun_ChangePhase.inc.php');
require_once('Common/Lib/CommonLib.php');
//require_once('Common/Fun_FormatText.inc.php');
//require_once('Fun_Final.local.inc.php');

CheckTourSession(true);

$JSON=array('error'=>1, 'msg'=>get_text('Error'));

if(empty($_REQUEST['event']) or !isset($_REQUEST['team']) or !isset($_REQUEST['matchno'])) {
	JsonOut($JSON);
}

$event=$_REQUEST['event'];
$team=intval($_REQUEST['team']);
$match=intval($_REQUEST['matchno']);
$pool=empty($_REQUEST['pool']) ? '' : $_REQUEST['pool'];

if($team==0 ? IsBlocked(BIT_BLOCK_IND) : IsBlocked(BIT_BLOCK_TEAM)) {
	JsonOut($JSON);
}

checkACL(($team ? AclTeams : AclIndividuals), AclReadWrite);

// normalize the matchno to get the lower 1
if($match%2) {
	$match--;
}

$M=[$match,$match+1];

$prefix=($team ? 'Tf' : 'Fin');
$table =($team ? 'TeamFinals' : 'Finals');

// // check if the match has a winner or a double IRM
// $q=safe_r_SQL("select f1.{$prefix}WinLose or f2.{$prefix}WinLose or (f1.{$prefix}IrmType>0 and f2.{$prefix}IrmType>0) as MatchFinished
// 	from {$table} f1
// 	inner join {$table} f2 on f2.{$prefix}Tournament={$_SESSION['TourId']}
// 			and f2.{$prefix}Event=f1.{$prefix}Event
// 			and f2.{$prefix}MatchNo=f1.{$prefix}MatchNo+1
// 	where f1.{$prefix}Tournament={$_SESSION['TourId']} and f1.{$prefix}Event='$event' and f1.{$prefix}MatchNo=$match");
// if($r=safe_fetch($q) and !$r->MatchFinished) {
// 	$JSON['msg']=get_text('MatchNotFinished', 'Tournament');
// 	JsonOut($JSON);
// }


$SQL= "update {$table}
		set {$prefix}Confirmed=1,
		{$prefix}Status=1
		where {$prefix}Tournament={$_SESSION['TourId']}
			and {$prefix}Event='$event'
			and {$prefix}Matchno in ($match, ".($match+1).") ";
safe_w_sql($SQL);
updateOdfTiming('C', $_SESSION['TourId'], $event, $team, $match);

$ok=false;
if ($team) {
	$ok=move2NextPhaseTeam(null, $event, $match, $_SESSION['TourId'], false, true);
} else {
	$ok=move2NextPhase(null, $event, $match, $_SESSION['TourId'], false, $pool, true);
}

if ($ok) {
	$JSON['error']=0;
	$JSON['msg']=get_text('CmdOk');
} else {
	$JSON['msg']=get_text('MatchNotFinished', 'Tournament');
}

JsonOut($JSON);

