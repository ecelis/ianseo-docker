<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_Sessions.inc.php');
if (!CheckTourSession() || !isset($_REQUEST['EvCode']) || !isset($_REQUEST['NewPhase'])) {
	print get_text('CrackError');
	exit;
}
checkACL(AclCompetition, AclReadWrite, false);

$JSON=array('error' => 1, 'events' => array());


if (IsBlocked(BIT_BLOCK_TOURDATA)) {
	JsonOut($JSON);
}

$NewPhase=intval($_REQUEST['NewPhase']);
$NumQualified=numQualifiedByPhase($NewPhase);
$GridPhase=valueFirstPhase($NewPhase);

// controllo che esista l'evento...
$q = safe_r_sql("select EvElimType from Events WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) );
$EVENT=safe_fetch($q);
if(!$EVENT) {
	JsonOut($JSON);

}

// aggiorno la fase
$Update = "UPDATE Events SET 
	EvFinalFirstPhase=$NewPhase, EvNumQualified=$NumQualified 
	WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
$Rs=safe_w_sql($Update);

$JSON['error']=0;

if (safe_w_affected_rows()) {
	// Distruggo la griglia
	$Delete = "DELETE FROM Finals WHERE FinEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " AND FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$Rs=safe_w_sql($Delete);

	if($EVENT->EvElimType==5) {
		// Round Robin!!
		// starts removing the exceeding people ;)
		safe_w_sql("delete from RoundRobinParticipants 
						where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=0 and RrPartEvent=".StrSafe_DB($_REQUEST['EvCode'])." and RrPartLevel=0 and RrPartGroup=0 and RrPartDestItem>{$NumQualified}");
		// check if the final level => Brackets is needed!
		if($NewPhase) {
			// inserts all the items
			$sqlPart=array();
			for($n=1;$n<=$NumQualified;$n++) {
				$sqlPart[]="({$_SESSION['TourId']}, 0, ".StrSafe_DB($_REQUEST['EvCode']).", 0, 0, $n)";
			}
			safe_w_SQL("insert ignore into RoundRobinParticipants (RrPartTournament, RrPartTeam, RrPartEvent, RrPartLevel, RrPartGroup, RrPartDestItem) 
							values ".implode(', ', $sqlPart));
			safe_w_sql("update ignore RoundRobinParticipants set RrPartSourceLevel=0, RrPartSourceGroup=0, RrPartSourceRank=0, RrPartDestItem=0, RrPartParticipant=0, RrPartSubTeam=0 
							where (RrPartTournament, RrPartTeam, RrPartEvent, RrPartLevel, RrPartGroup) = ({$_SESSION['TourId']}, 0, ".StrSafe_DB($_REQUEST['EvCode']).", 0, 0)");
		}
	}

	if($GridPhase) {
		// Deletes unused warmups
		$delSchedule = "DELETE FROM FinWarmup USING
	        Events
	        INNER JOIN FinSchedule ON EvCode = FsEvent AND EvTeamEvent = FsTeamEvent AND EvTournament = FsTournament
	        INNER JOIN Grids ON GrMatchNo = FsMatchNo
	        INNER JOIN FinWarmup on FsEvent=FwEvent and FsTeamEvent=FwTeamEvent and FsTournament=FwTournament and FsScheduledDate=FwDay and FsScheduledTime=FwMatchTime
	        WHERE GrPhase > $GridPhase
	        AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' AND EvCode=" . StrSafe_DB($_REQUEST['EvCode']);
		$RsDel=safe_w_sql($delSchedule);

		// deletes schedule
		$delSchedule = "DELETE FROM FinSchedule USING
	        Events
	        INNER JOIN FinSchedule ON EvCode = FsEvent AND EvTeamEvent = FsTeamEvent AND EvTournament = FsTournament
	        INNER JOIN Grids ON GrMatchNo = FsMatchNo
	        WHERE GrPhase > $GridPhase
	        AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='0' AND EvCode=" . StrSafe_DB($_REQUEST['EvCode']);
		$RsDel=safe_w_sql($delSchedule);

		// Re-create the brackets
		$Insert = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) 
	        SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i:s')) . " 
	        FROM Events 
	        INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0
	        INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
	        WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " ";
		$RsDel=safe_w_sql($Insert);
	} else {
		// deletes warmups
		$delSchedule = "DELETE FROM FinWarmup WHERE FwTournament={$_SESSION['TourId']} AND FwTeamEvent=0 AND FwEvent=" . StrSafe_DB($_REQUEST['EvCode']);
		$RsDel=safe_w_sql($delSchedule);

		// deletes schedule
		$delSchedule = "DELETE FROM FinSchedule WHERE FsTournament={$_SESSION['TourId']} AND FsTeamEvent=0 AND FsEvent=" . StrSafe_DB($_REQUEST['EvCode']);
		$RsDel=safe_w_sql($delSchedule);
	}

    // Azzero il flag di spareggio
    ResetShootoff($_REQUEST['EvCode'],0,3);


	// TODO: needs to check the descendent events!
	$q=safe_r_sql("select * from Events where EvFinalFirstPhase>" . StrSafe_DB($_REQUEST['NewPhase']/2) . " and EvTeamEvent='0' AND EvCodeParent=" . StrSafe_DB($_REQUEST['EvCode']) . " and EvTournament=" . StrSafe_DB($_SESSION['TourId']));
	while($r=safe_fetch($q)) {
		$JSON['events']=array_merge(deleteEvent($r->EvCode), $JSON['events']);
	}
}

JsonOut($JSON);
