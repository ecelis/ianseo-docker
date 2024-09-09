<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_Phases.inc.php');
require_once('Common/Lib/Fun_Modules.php');

CheckTourSession(true);

$JSON=array('error'=>0, 'winner'=>'');

if(!isset($_REQUEST['match']) or !isset($_REQUEST['team']) or !isset($_REQUEST['event']) ) {
	JsonOut($JSON);
}

$Error=1;
$Out='';
$Team=intval($_REQUEST['team']);
$Event=$_REQUEST['event'];
$Matchno=intval($_REQUEST['match']);

checkACL(($Team ? AclTeams : AclIndividuals), AclReadWrite, false);

$m=array($Matchno, ($Matchno%2) ? $Matchno-1 : $Matchno+1);
$TabPrefix=($Team ? 'Tf' : 'Fin');
$Table=($Team ? 'Team' : '');

// updates the confirmation of the match
safe_w_sql("update {$Table}Finals set {$TabPrefix}Status=1, {$TabPrefix}Confirmed=1, {$TabPrefix}DateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . " where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo in ($m[0],$m[1])");

$q=safe_r_sql("select {$TabPrefix}WinLose as Winner, {$TabPrefix}TbClosest as Closest, {$TabPrefix}TbDecoded as Decoded, {$TabPrefix}MatchNo as MatchNo from {$Table}Finals where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo in ($m[0],$m[1]) order by {$TabPrefix}MatchNo");



$Winner='';
$Loser='';
$JSON['finished']=false;
if($r1=safe_fetch($q) and $r2=safe_fetch($q) and $r1->Winner+$r2->Winner) {
	// check if there is a "tie" tiebreak
	if($r1->Decoded and $r1->Decoded==$r2->Decoded and $r1->Closest==$r2->Closest) {
		if($r1->Winner) {
			// the closest is on the $r1
			safe_w_sql("update {$Table}Finals set {$TabPrefix}TbClosest=1, {$TabPrefix}TbDecoded=".StrSafe_DB($r1->Decoded.'+')." where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo=$r1->MatchNo");
		} else {
			safe_w_sql("update {$Table}Finals set {$TabPrefix}TbClosest=1, {$TabPrefix}TbDecoded=".StrSafe_DB($r2->Decoded.'+')." where {$TabPrefix}Tournament={$_SESSION['TourId']} and {$TabPrefix}Event='$Event' and {$TabPrefix}MatchNo=$r2->MatchNo");
		}
	}
	$JSON['winner']=$r1->Winner ? 'L' : 'R';
	$JSON['finished']=true;
}

$JSON['error']=0;

//runJack("FinConfirmEnd", $_SESSION['TourId'], array("Event"=>$Event, "Team"=>$Team,"MatchNo"=>min($m), "Side"=>0, "TourId"=>$_SESSION['TourId']));
//runJack("FinConfirmEnd", $_SESSION['TourId'], array("Event"=>$Event, "Team"=>$Team,"MatchNo"=>min($m), "Side"=>1, "TourId"=>$_SESSION['TourId']));
runJack("MatchFinished", $_SESSION['TourId'], array("Event"=>$Event, "Team"=>$Team, "MatchNo"=>min($m), "TourId"=>$_SESSION['TourId']));

JsonOut($JSON);

