<?php

require_once(dirname(dirname(__DIR__)) . '/config.php');

$JSON=array('error'=>1, 'msg'=>get_text('ErrGenericError', 'Errors'));

if(!CheckTourSession() or !hasACL(AclCompetition, AclReadWrite) or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

require_once('Common/Lib/CommonLib.php');

$Team=intval($_REQUEST['Team'] ?? 0);
$Event=($_REQUEST['Event'] ?? '');
$Level=max(intval($_REQUEST['Level'] ?? 1), 1);

// Check the event exists
if($Event) {
	$q=safe_r_sql("select EvCode, EvEventName, EvElim1, EvFinalFirstPhase, EvNumQualified from Events where EvElimType=5 and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event)." and EvTournament={$_SESSION['TourId']}");
	if(!($EVENT=safe_fetch($q))) {
		// no matching event so void it
		$Event='';
	}
}

switch($_REQUEST['act']) {
	case 'getDetails':
		$JSON['reloadEvents']=false;

		// if no events, load an array of events that fit the "team" and select the first one
		if(!$Event) {
			$JSON['reloadEvents']=true;
			$JSON['events']=[];
			$q=safe_r_sql("select EvCode, EvEventName, EvElim1, EvFinalFirstPhase from Events where EvElimType=5 and EvTeamEvent=$Team and EvTournament={$_SESSION['TourId']} order by EvProgr");
			while($r=safe_fetch($q)) {
				$JSON['events'][]=$r;
				if(!$Event) {
					$EVENT=$r;
					$Event=$r->EvCode;
				}
			}
		}

		if(!$Event) {
			JsonOut($JSON);
		}

		$JSON['error']=0;
		$q=safe_r_sql("select RoundRobinLevel.*, EvElim1, EvMatchMode, EvElimArrows, EvElimEnds, EvElimSO from Events 
		    left join RoundRobinLevel on RrLevTournament=EvTournament and RrLevTeam=EvTeamEvent and RrLevEvent=EvCode and RrLevLevel=$Level
			where EvElimType=5 and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event)." and  EvTournament={$_SESSION['TourId']}");
		$LEVEL=safe_fetch($q);

		$JSON['team']=$Team;
		$JSON['event']=$Event;
		$JSON['level']=min($Level, $EVENT->EvElim1);
		$JSON['cmdSave']=get_text('CmdSave');
		$JSON['showAlert']=(!is_null($LEVEL->RrLevGroups));

		// normalize details of this event...
		$JSON['details']=array(
			'groups'        =>array('text'=>get_text('Groups','RoundRobin'),'val'=>($LEVEL->RrLevGroups ?? 2)),
			'groupArchers'  =>array('text'=>get_text('GroupArchers', 'RoundRobin'),'val'=>($LEVEL->RrLevGroupArchers ?? 4)),
			'name'          =>array('text'=>get_text('LevelName','RoundRobin'),'val'=>($LEVEL->RrLevName ?? get_text('LevelNum', 'RoundRobin', $Level))),
			'mode'          =>array('text'=>get_text('MatchModeScoring'),'val'=>($LEVEL->RrLevMatchMode ?? $LEVEL->EvMatchMode)),
			'bestRanked'    =>array('text'=>get_text('BestRanked','RoundRobin'),'val'=>($LEVEL->RrLevBestRankMode ?? 0)),
			'arrows'        =>array('text'=>get_text('ArrowsPerEnd','Tournament'),'val'=>($LEVEL->RrLevArrows ?? $LEVEL->EvElimArrows)),
			'ends'          =>array('text'=>get_text('Ends','Tournament'),'val'=>($LEVEL->RrLevEnds ?? $LEVEL->EvElimEnds)),
			'so'            =>array('text'=>get_text('TieArrows'),'val'=>($LEVEL->RrLevSO ?? $LEVEL->EvElimSO)),
			'tieAllowed'    =>array('text'=>get_text('TieAllowed', 'RoundRobin'),'val'=>($LEVEL->RrLevTieAllowed ?? 1)),
			'winPoints'     =>array('text'=>get_text('WinPoints', 'RoundRobin'),'val'=>($LEVEL->RrLevWinPoints ?? 2)),
			'tiePoints'     =>array('text'=>get_text('TiePoints', 'RoundRobin'),'val'=>($LEVEL->RrLevTiePoints ?? 1)),
			'tieBreakSystem'=>array('text'=>get_text('TiebreakSystem', 'RoundRobin'),'val'=>($LEVEL->RrLevTieBreakSystem ?? 1)),
			'tieBreakSystem2'=>array('text'=>get_text('TiebreakSystem', 'RoundRobin'),'val'=>($LEVEL->RrLevTieBreakSystem2 ?? 0)),
			'checkGolds'    =>array('text'=>get_text('CheckGoldsInMatch', 'Tournament'),'val'=>($LEVEL->RrLevCheckGolds ?? 0)),
			'checkXNines'   =>array('text'=>get_text('CheckXNinesInMatch', 'Tournament'),'val'=>($LEVEL->RrLevCheckXNines ?? 0)),
		);
		$JSON['tieBreakOptions']=array(
			array('val' => 1, 'text' => get_text('TiebreakSystem-1', 'RoundRobin')),
			array('val' => 2, 'text' => get_text('TiebreakSystem-2', 'RoundRobin')),
			array('val' => 3, 'text' => get_text('TiebreakSystem-3', 'RoundRobin')),
			array('val' => 4, 'text' => get_text('TiebreakSystem-4', 'RoundRobin')),
			array('val' => 5, 'text' => get_text('TiebreakSystem-5', 'RoundRobin')),
		);
		$JSON['modeOptions']=array(
			array('val' => 0, 'text' => get_text('MatchMode_0')),
			array('val' => 1, 'text' => get_text('MatchMode_1')),
		);
		$JSON['bestOptions']=array(
			array('val' => 0, 'text' => get_text('BestRanked_0', 'RoundRobin')),
			array('val' => 1, 'text' => get_text('BestRanked_1', 'RoundRobin')),
		);

		$JSON['levels']=[];
		$lvls=array();
		foreach(range(1, $EVENT->EvElim1) as $l) {
			$lvls[]="select $l as Level";
		}
		$q=safe_r_sql("select Level as Level, RrLevName from (".implode (' UNION ', $lvls).") lvls
    		left join RoundRobinLevel on RrLevLevel=Level and RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event));
		while($r=safe_fetch($q)) {
			$JSON['levels'][]=array(
				'val' => $r->Level,
				'text' => ($r->RrLevName ?? get_text('LevelNum', 'RoundRobin', $r->Level)),
			);
		}

		// get which events are already made to create the select "copy from"
		$JSON['copyfrom']=array();
		$q=safe_r_SQL("select EvCode as `val`, group_concat(EvCode order by EvProgr separator ', ') as `text`, RrLevGroups, RrLevGroupArchers
			from Events 
			inner join RoundRobinLevel on RrLevLevel=$Level and RrLevTournament=EvTournament and RrLevTeam=EvTeamEvent and RrLevEvent=EvCode
			where EvTeamEvent=$Team and EvElimType=5 and EvTournament={$_SESSION['TourId']} and (EvTeamEvent,EvCOde)!=($Team, ".StrSafe_DB($Event).") and EvElim1={$EVENT->EvElim1} and EvFinalFirstPhase={$EVENT->EvFinalFirstPhase}
			group by EvElim1, RrLevArrows, RrLevEnds, RrLevSO, RrLevTieAllowed, RrLevWinPoints, RrLevTiePoints, RrLevTieBreakSystem, RrLevTieBreakSystem2, RrLevGroups, RrLevGroupArchers");
		while($r=safe_fetch($q)) {
			$r->text.=" ({$r->RrLevGroups} ".get_text('Groups', 'RoundRobin')." - {$r->RrLevGroupArchers} ".get_text('GroupArchersShort','RoundRobin').")";
			$JSON['copyfrom'][]=$r;
		}
		if($JSON['copyfrom']) {
			array_unshift($JSON['copyfrom'], array('val'=>'', 'text'=>get_text('CopyFrom', 'RoundRobin')));
		}
		break;
	case 'setDetails':
		if(!$Event) {
			JsonOut($JSON);
		}

		$JSON['error']=0;

		require_once(__DIR__.'/Lib.php');
		$Recreates=false;
		$SQL1=array(
			"RrLevTournament={$_SESSION['TourId']}",
			"RrLevLevel=$Level",
			"RrLevEvent=".StrSafe_DB($Event),
			"RrLevTeam=$Team",
			"RrLevGroups=".intval($_REQUEST['Groups']),
			"RrLevGroupArchers=".intval($_REQUEST['GroupArchers']),
		);
		safe_w_SQL("insert into RoundRobinLevel set ".implode(',',$SQL1)." on duplicate key update ".implode(',',$SQL1));
		if(safe_w_affected_rows()) {
			// this is a destroying event so recreates the groups etc for this level and event
			createRound($Team, $Event, $Level);
			if($EVENT->EvFinalFirstPhase) {
				if($Level==1) {
					CreateFinalLevel($Team, $Event, $EVENT->EvNumQualified);
				}
			} else {
				safe_w_sql("delete from RoundRobinParticipants where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=0 and RrPartGroup=0");
			}
			$Recreates=true;
		}

		$Key="RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event)." and RrLevLevel=$Level";
		$SQL2=array(
			"RrLevMatchMode=".intval($_REQUEST['mode']),
			"RrLevBestRankMode=".intval($_REQUEST['bestRanked']),
			"RrLevArrows=".intval($_REQUEST['Arrows']),
			"RrLevEnds=".intval($_REQUEST['Ends']),
			"RrLevSO=".intval($_REQUEST['SO']),
			"RrLevTieAllowed=".intval($_REQUEST['TieAllowed']),
			"RrLevWinPoints=".intval($_REQUEST['WinPoints']),
			"RrLevTiePoints=".intval($_REQUEST['TiePoints']),
			"RrLevTieBreakSystem=".intval($_REQUEST['TieBreakSystem']),
			"RrLevTieBreakSystem2=".intval($_REQUEST['TieBreakSystem2']),
		);
		safe_w_SQL("update RoundRobinLevel set ".implode(',',$SQL2)." where $Key");
		if(safe_w_affected_rows()) {
			// this changes the rank calculation of the level, so rerank THIS level and voids the following levels
			calculateFinalRank($Team, $Event, $Level);
			$Recreates=true;
		}

		// updates just the name...
		safe_w_SQL("update RoundRobinLevel set RrLevName=".StrSafe_DB($_REQUEST['Name'])." where $Key");
		if(safe_w_affected_rows()) {
			$JSON['levels']=[];
			$lvls=array();
			foreach(range(1, $EVENT->EvElim1) as $l) {
				$lvls[]="select $l as Level";
			}
			$q=safe_r_sql("select Level, RrLevName from (".implode (' UNION ', $lvls).") lvls
    		left join RoundRobinLevel on RrLevLevel=Level and RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event));
			while($r=safe_fetch($q)) {
				$JSON['levels'][]=array(
					'val' => $r->Level,
					'text' => ($r->RrLevName ?? get_text('LevelNum', 'RoundRobin', $r->Level)),
				);
			}
		}

		// in case of a disrupting change, updates the rest
		if($Recreates) {
			for($n=$Level+1; $n<=$EVENT->EvElim1; $n++) {
				// destroys the groups etc for the following events
				createRound($Team, $Event, $n);
			}
		}
		break;
	case 'copyFrom':
		if(empty($_REQUEST['from']) or empty($Level) or empty($Event)) {
			JsonOut($JSON);
		}
		// check the from event exists
		$q=safe_r_sql("select * from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($_REQUEST['from']));
		if(!safe_num_rows($q)) {
			JsonOut($JSON);
		}

		/*
		 * To avoid issues when changing columns, all copies are made one by one :(
		 * */
		// RoundRobinGrids
		safe_w_sql("delete from RoundRobinGrids where RrGridTournament={$_SESSION['TourId']} and RrGridTeam=$Team and RrGridEvent=".StrSafe_DB($Event));
		$q=safe_r_sql("select * from RoundRobinGrids where RrGridTournament={$_SESSION['TourId']} and RrGridTeam=$Team and RrGridEvent=".StrSafe_DB($_REQUEST['from']));
		while($r=safe_fetch($q)) {
			$r->RrGridEvent=$Event;
			$SQL=array();
			foreach($r as $k=>$v) {
				$SQL[]="$k = ".StrSafe_DB($v);
			}
			safe_w_sql("insert into RoundRobinGrids set ".implode(',',$SQL));
		}

		// RoundRobinGroup
		safe_w_sql("delete from RoundRobinGroup where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($Event));
		$q=safe_r_sql("select * from RoundRobinGroup where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($_REQUEST['from']));
		while($r=safe_fetch($q)) {
			$r->RrGrEvent=$Event;
			$SQL=array();
			foreach($r as $k=>$v) {
				$SQL[]="$k = ".StrSafe_DB($v);
			}
			safe_w_sql("insert into RoundRobinGroup set ".implode(',',$SQL));
		}

		// RoundRobinLevel
		safe_w_sql("delete from RoundRobinLevel where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event));
		$q=safe_r_sql("select * from RoundRobinLevel where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($_REQUEST['from']));
		while($r=safe_fetch($q)) {
			$r->RrLevEvent=$Event;
			$SQL=array();
			foreach($r as $k=>$v) {
				$SQL[]="$k = ".StrSafe_DB($v);
			}
			safe_w_sql("insert into RoundRobinLevel set ".implode(',',$SQL));
		}

		// RoundRobinMatches
		safe_w_sql("delete from RoundRobinMatches where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event));
		$q=safe_r_sql("select RrMatchTournament, RrMatchTeam, RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo from RoundRobinMatches where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($_REQUEST['from']));
		while($r=safe_fetch($q)) {
			$r->RrMatchEvent=$Event;
			$SQL=array();
			foreach($r as $k=>$v) {
				$SQL[]="$k = ".StrSafe_DB($v);
			}
			safe_w_sql("insert into RoundRobinMatches set ".implode(',',$SQL));
		}

		// RoundRobinParticipants
		safe_w_sql("delete from RoundRobinParticipants where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event));
		$q=safe_r_sql("select RrPartTournament, RrPartTeam, RrPartEvent, RrPartLevel, RrPartGroup, RrPartDestItem, RrPartSourceLevel, RrPartSourceGroup, RrPartSourceRank from RoundRobinParticipants where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($_REQUEST['from']));
		while($r=safe_fetch($q)) {
			$r->RrPartEvent=$Event;
			$SQL=array();
			foreach($r as $k=>$v) {
				$SQL[]="$k = ".StrSafe_DB($v);
			}
			safe_w_sql("insert into RoundRobinParticipants set ".implode(',',$SQL));
		}

		$JSON['error']=0;
		break;
	default:
		JsonOut($JSON);
}


JsonOut($JSON);
