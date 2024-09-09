<?php

require_once(dirname(__FILE__) . '/config.php');

$JSON=array(
	'error'=>1,
	'msg'=>get_text('ErrGenericError', 'Errors'),
	'matches'=>[],
	);

$Event=($_REQUEST['event']??'');
$Day=($_REQUEST['day']??'');
$TourDates=getModuleParameter('FFTA', 'D1TourDates');

if(!$Event or !$Day or !$TourDates) {
	JsonOut($JSON);
}

switch($_REQUEST['action']??'') {
	case 'setTeam2023':
		$SQL="select concat_ws('|', RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) as id, coalesce(CoName,'') as club, RrMatchRound as round, RrMatchMatchNo as matchno, RrMatchTarget as target, RrMatchScheduledDate as date, date_format(RrMatchScheduledTime, '%H:%i') as time, RrMatchScheduledLength as length, RrLevName as level 
			from RoundRobinMatches
			inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel
			inner join RoundRobinGrids on RrGridTournament=RrMatchTournament and RrGridTeam=RrMatchTeam and RrGridEvent=RrMatchEvent and RrGridLevel=RrMatchLevel and RrGridGroup=RrMatchGroup and RrGridRound=RrMatchRound and RrGridMatchno=RrMatchMatchNo
			inner join RoundRobinParticipants on RrPartTournament=RrMatchTournament and RrPartTeam=RrMatchTeam and RrPartEvent=RrMatchEvent and RrPartLevel=RrMatchLevel and RrPartGroup=RrMatchGroup and RrPartDestItem=RrGridItem
			left join (
			    select TeEvent, TeCoId, TeSubTeam, CoName
			    from Teams
			    inner join Countries on CoId=TeCoId and CoTournament=TeTournament
			    where TeEvent=".StrSafe_DB($Event)." and TeTournament={$_SESSION['TourId']} and TeFinEvent=1
			) Teams on TeSubTeam=RrMatchSubTeam and TeCoId=RrMatchAthlete
        	where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent=".StrSafe_DB($Event)."
			";
		switch($Day) {
			case 'D1':
				$SQL.=" and RrMatchRound<=7 and RrMatchLevel=1";
				break;
			case 'D2':
				if($Event=='FCO') {
					$SQL.=" and RrMatchLevel=2";
				} else {
					$SQL.=" and RrMatchRound>7 and RrMatchLevel=1";
				}
				break;
			case 'D3':
				if($Event=='FCO') {
					$SQL.=" and RrMatchLevel>2";
				} else {
					$SQL.=" and RrMatchLevel=2";
				}
				break;
		}
		$SQL.=" order by RrMatchLevel, RrMatchRound, RrMatchMatchno";
		$q=safe_r_sql($SQL);
		$Round=0;
		$Matches=[];
		while($r=safe_fetch($q)) {
			if($r->round!=$Round) {
				if($Matches) {
					$JSON['matches'][]=['round'=>get_text('RoundNum', 'RoundRobin', $Round), 'matches'=>$Matches];
				}
				$Round=$r->round;
				$Matches=[];
				$Match=[];
			}
			$r->target=ltrim($r->target,'0');
			$Match[]=$r;
			if($r->matchno%2) {
				$Matches[]=$Match;
				$Match=[];
			}
		}
		if($Matches) {
			$JSON['matches'][]=['round'=>get_text('RoundNum', 'RoundRobin', $Round), 'matches'=>$Matches];
		}
		$JSON['cols']=$Round;
		$JSON['rows']=count($Matches);
		break;
	case 'setItem':
		$Key=($_REQUEST['key']??'');
		$Fld=($_REQUEST['fld']??'');
		$Val=($_REQUEST['val']??'');
		list($Lev, $Grp, $Rnd, $MatchNo)=explode('|', $Key);
		$Lev=intval($Lev);
		$Grp=intval($Grp);
		$Rnd=intval($Rnd);
		$MatchNo=intval($MatchNo);
		if(!$Lev or !$Grp or !$Rnd) {
			JsonOut($JSON);
		}

		switch($Fld) {
			case 'tgt':
				// the target is set for all the subsequent matchnos in all rounds happening in the same day
				$JSON['targets']=[];
				$q=safe_r_sql("select RrMatchScheduledDate, RrLevGroupArchers
    				from RoundRobinMatches
    				inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel
    				where RrMatchTournament={$_SESSION['TourId']}
						and RrMatchTeam=1
						and RrMatchEvent=".StrSafe_DB($Event)."
						and RrMatchLevel=$Lev
						and RrMatchGroup=$Grp
						and RrMatchRound=$Rnd
						and RrMatchMatchNo=$MatchNo");
				$r=safe_fetch($q);
				$MatchNos=[$MatchNo];
				if(substr($Val, -1)=='+') {
					// set the targets from Matchno to RrLevGroupArchers!
					$MatchNos=range($MatchNo, $r->RrLevGroupArchers);
				}
				$DateScheduled=$r->RrMatchScheduledDate;
				if($Target=intval($Val)) {
					// get the rounds scheduled for that date
					$q=safe_r_sql("select group_concat(distinct RrMatchRound separator '|') as Rounds
	                    from RoundRobinMatches
	                    where RrMatchTournament={$_SESSION['TourId']}
							and RrMatchTeam=1
							and RrMatchEvent=".StrSafe_DB($Event)."
							and RrMatchLevel=$Lev
							and RrMatchGroup=$Grp
							and RrMatchScheduledDate='$DateScheduled'");
					$r=safe_fetch($q);
					$Rounds=explode('|', $r->Rounds);

					foreach($MatchNos as $m) {
						$tuplets=[];
						foreach($Rounds as $r) {
							$tuplets[]="($r,$m)";
						}
						safe_w_sql("update RoundRobinMatches
							set RrMatchTarget='".str_pad($Target,3,'0',STR_PAD_LEFT)."'
							where RrMatchTournament={$_SESSION['TourId']}
								and RrMatchTeam=1
								and RrMatchEvent=".StrSafe_DB($Event)."
								and RrMatchLevel=$Lev
								and RrMatchGroup=$Grp
								and RRMatchScheduledDate='$DateScheduled'
								and (RrMatchRound,RrMatchMatchNo) in (".implode(',',$tuplets).")
								");
						if(safe_w_affected_rows()) {
							foreach($Rounds as $r) {
								$JSON['targets'][]=[
									'id'=>'[key$="'.$Lev.'|'.$Grp.'|'.$r.'|'.$m.'"] [ref="tgt"]',
									'val'=>$Target
								];
							}
						}
						$Target++;
					}
					if(!$JSON['targets']) {
						$JSON['targets'][]=[
							'id'=>'[key$="'.$Lev.'|'.$Grp.'|'.$Rnd.'|'.$MatchNo.'"] [ref="tgt"]',
							'val'=>intval($Val)
						];
					}
					$JSON['error']=0;
					unset($JSON['msg']);
					JsonOut($JSON);
				}
				$Field="RrMatchTarget=''";
				if(!$JSON['targets']) {
					$JSON['targets'][]=[
						'id'=>'[key$="'.$Lev.'|'.$Grp.'|'.$Rnd.'|'.$MatchNo.'"] [ref="tgt"]',
						'val'=>''
					];
				}
				break;
			case 'date':
				require_once('Common/Lib/Fun_DateTime.inc.php');
				$Val=CleanDate($Val);
				$Field='RrMatchScheduledDate='.StrSafe_DB($Val);
				break;
			case 'time':
				$t=explode(':', $Val);
				while(count($t)<3) {
					$t[]='00';
				}
				$NewTime=implode(':', $t);
				$Field='RrMatchScheduledTime='.StrSafe_DB($NewTime);
				// the time changes for all the matches with the old date, so first recover what the date was for that match
				$q=safe_r_sql("select RrMatchScheduledTime, RrMatchScheduledDate 
					from RoundRobinMatches
					where RrMatchTournament={$_SESSION['TourId']}
						and RrMatchTeam=1
						and RrMatchEvent=".StrSafe_DB($Event)."
						and RrMatchLevel=$Lev
						and RrMatchGroup=$Grp
						and RrMatchRound=$Rnd
						and RrMatchMatchNo=$MatchNo");
				if($r=safe_fetch($q)) {
					safe_w_sql("update RoundRobinMatches
						set $Field
						where RrMatchTournament={$_SESSION['TourId']}
							and RrMatchTeam=1
							and RrMatchEvent=".StrSafe_DB($Event)."
							and RrMatchLevel=$Lev
							and RrMatchGroup=$Grp
							and RrMatchRound=$Rnd
							and RrMatchScheduledTime='$r->RrMatchScheduledTime' 
						  	and RrMatchScheduledDate='$r->RrMatchScheduledDate'");
				}
				// gets all the matches with changed time
				$q=safe_r_sql("select RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo
					from RoundRobinMatches
					where RrMatchTournament={$_SESSION['TourId']}
						and RrMatchTeam=1
						and RrMatchEvent=".StrSafe_DB($Event)."
						and RrMatchLevel=$Lev
						and RrMatchGroup=$Grp
						and RrMatchRound=$Rnd
					    and RrMatchScheduledDate='$r->RrMatchScheduledDate'
						and RrMatchScheduledTime=".StrSafe_DB($NewTime));
				while($r=safe_fetch($q)) {
					$JSON['targets'][]=[
						'id'=>'[key$="'.$r->RrMatchLevel.'|'.$r->RrMatchGroup.'|'.$r->RrMatchRound.'|'.$r->RrMatchMatchNo.'"] [ref="time"]',
						'val'=>$NewTime
					];
				}
				break;
			default:
				JsonOut($JSON);
		}

		$SQL="update RoundRobinMatches
			set $Field
			where RrMatchTournament={$_SESSION['TourId']}
				and RrMatchTeam=1
				and RrMatchEvent=".StrSafe_DB($Event)."
				and RrMatchLevel=$Lev
				and RrMatchGroup=$Grp
				and RrMatchRound=$Rnd
				and RrMatchMatchNo=$MatchNo";
		safe_w_sql($SQL);
		$JSON['error']=0;
		unset($JSON['msg']);
		break;
	default:
		JsonOut($JSON);
}

$JSON['error']=0;

JsonOut($JSON);

$Event=$_REQUEST['event'];

$q=safe_r_sql("select EvNumQualified, EvCode from Events where EvTeamEvent=1 and EvCode='$Event' and EvTournament={$_SESSION['TourId']}");
if(!($r=safe_fetch($q))) {
	JsonOut($JSON);
}
$AllInOne=getModuleParameter('FFTA', 'D1AllInOne', 0);
$Matches=array();
if($r->EvNumQualified==8) {
	switch($_REQUEST['day']) {
		case 1:
			$Matches=array(
				'1' => array(8,7,6,5,4),
				'2' => array(7,6,5,8,3),
				'3' => array(6,5,8,7),
				'4' => array(5,8,7,6),
				'5' => array(0,0,0,0,7),
				'6' => array(0,0,0,0,8),
			);
			break;
		case 2:
			$Matches=array(
				'1' => array(3,2,8,7,6),
				'2' => array(4,0,7,6,5),
				'3' => array(0,4,6,5,8),
				'4' => array(0,0,5,8,7),
				'5' => array(8,6),
				'6' => array(7),
				'7' => array(0,8),
			);
			break;
		case 3:
			$Matches=array(
				'1' => array(5,4,3,2),
				'2' => array(8,3,4),
				'3' => array(7,0,0,4),
				'4' => array(6),
				'5' => array(0,7,8,6),
				'6' => array(0,8,7),
				'7' => array(0,0,0,8),
			);
			$JSON['games']=4;
			safe_w_SQL("update FinSchedule set FSScheduledDate=0, FSScheduledTime=0, FSScheduledLen=0 where FSMatchNo>=192 and FSEvent like '$r->EvCode%' and FSTournament={$_SESSION['TourId']}");
			break;
	}
} elseif($r->EvNumQualified==6) {
	switch($_REQUEST['day']) {
		case 1:
			$Matches=array(
				'1' => array(6,5,4,3,2),
				'2' => array(3,6,5,4,0),
				'3' => array(0,4,6,0,5),
				'4' => array(5,0,0,0,6),
				'5' => array(0,0,0,6,0),
				'6' => array(0,0,0,0,0),
			);
			break;
		case 2:
			$Matches=array(
				'1' => array(5,6,4,3,2),
				'2' => array(6,3,5,4,0),
				'3' => array(4,0,6,0,5),
				'4' => array(0,5,0,0,6),
				'5' => array(0,0,0,6,0),
				'6' => array(0,0,0,0,0),
			);
			break;
		case 3:
			$Matches=array(
				'1' => array(4,5,6,3,2),
				'2' => array(5,6,3,4,0),
				'3' => array(6,4,0,0,5),
				'4' => array(0,0,5,0,6),
				'5' => array(0,0,0,6,0),
				'6' => array(0,0,0,0,0),
			);
			break;
	}
} else {
	switch($_REQUEST['day']) {
		case 1:
			$Matches=array(
				'1'  => array(16,15,14,13,12),
				'2'  => array(12,16,15,14,13),
				'3'  => array(13,12,16,15,14),
				'4'  => array(14,13,12,16,15),
				'5'  => array(15,14,13,12,16),
				'6'  => array(11,10, 9, 8, 7),
				'7'  => array( 8,11,10, 9),
				'8'  => array( 0, 9,11, 0,10),
				'9'  => array(10, 0, 0, 0,11),
				'10' => array( 0, 0, 0,11),
			);
			break;
		case 2:
			$Matches=array(
				'1'  => array(11,10, 9, 8, 7),
				'2'  => array( 7,11,10, 9, 8),
				'3'  => array( 8, 7,11,10, 9),
				'4'  => array( 9, 8, 7,11,10),
				'5'  => array(10, 9, 8, 7,11),
				'6'  => array(16,15,14,13,12),
				'12' => array(13,16,15,14),
				'13' => array( 0,14,16, 0,15),
				'14' => array(15, 0, 0, 0,16),
				'15' => array( 0, 0, 0,16),
			);
			break;
		case 3:
			$Matches=array(
				'1'  => array( 6, 5, 4, 3, 2),
				'2'  => array( 3, 6, 5, 4),
				'3'  => array( 0, 4, 6, 0, 5),
				'4'  => array( 5, 0, 0, 0, 6),
				'5'  => array( 0, 0, 0, 6),
				'7'  => array(16,15,14,13,12),
				'8'  => array(12,16,15,14,13),
				'9'  => array(13,12,16,15,14),
				'10' => array(14,13,12,16,15),
				'11' => array(15,14,13,12,16),
			);
			break;
	}
}

// get the team positions from previous year
$Winners=getModuleParameter('FFTA', 'D1Winners');

if(!$Winners or empty($Winners[$Event])) {
	JsonOut($JSON);
}

// get the teams
$Teams=array();
$q=safe_r_sql("select CoId, CoCode from Countries where CoCode in (".implode(',', StrSafe_DB($Winners[$Event])).") and CoTournament={$_SESSION['TourId']}");
while($r=safe_fetch($q)) {
	$Teams[$r->CoCode]=$r->CoId;
}

// resets the grids
safe_w_sql("update TeamFinals set TfTeam=0, TfSubTeam=0 where TfEvent='$Event' and TfTournament={$_SESSION['TourId']}");
safe_w_sql("update Finals set FinAthlete=0 where FinEvent like ".StrSafe_DB($Event.'%')." and FinTournament={$_SESSION['TourId']}");

$Matchnos=array(128, 144, 160, 176, 192);
$TeamMatches=array();
$TeamMatchnos=array();
foreach($Matches as $pos => $Opponents) {
	foreach($Opponents as $m => $Opp) {
		if(!$Opp) {
			continue;
		}
		$Team1=$Teams[$Winners[$Event][$pos]];
		$Team2=$Teams[$Winners[$Event][$Opp]];
		$Matchno1=$Matchnos[$m];
		$Matchno2=$Matchnos[$m]+1;

		$TeamMatches[$Matchno1]=$Team1;
		$TeamMatches[$Matchno2]=$Team2;


		$TeamMatchnos[$Team1][]=$Matchno1;
		$TeamMatchnos[$Team2][]=$Matchno2;

		$Matchnos[$m]+=2;
	}
}

foreach($TeamMatches as $MatchNo => $Team) {
	safe_w_sql("insert into TeamFinals set TfTeam=$Team, TfSubTeam=0, TfEvent='$Event', TfMatchNo=$MatchNo, TfTournament={$_SESSION['TourId']} 
		on duplicate key update TfTeam=$Team, TfSubTeam=0");
}

$SOEvent=array();
if(!$AllInOne) {
	$SQL="select EnId, EnCountry, IndRank
		from Individuals
	    inner join Entries on EnId=IndId and EnTournament=IndTournament and EnTeamFEvent=1
	    where IndEvent=".StrSafe_DB($_REQUEST['event'])." and IndTournament={$_SESSION['TourId']}
	    order by EnCountry, IndRank";
	$OldCountry='';
	$q=safe_r_sql($SQL);
	while($r=safe_fetch($q)) {
		if($OldCountry!=$r->EnCountry) {
			$i=1;
			$OldCountry=$r->EnCountry;
			$OldRank=$r->IndRank;
		}
		foreach($TeamMatchnos[$r->EnCountry] as $MatchNo) {
			safe_w_sql("update Finals set FinAthlete=$r->EnId where FinEvent=".StrSafe_DB($Event.$i)." and FinMatchNo=$MatchNo and FinTournament={$_SESSION['TourId']}");
		}
		$i++;
	}

	// check the SO of all the involved events
	$q=safe_r_sql("select IndEvent 
		from Individuals 
		where IndEvent like '{$Event}_' and IndTournament={$_SESSION['TourId']}
		group by IndEvent, IndRank
		having count(*)>1");
	while($r=safe_fetch($q)) {
		$SOEvent[]=$r->IndEvent;
	}
}

if($SOEvent) {
	$JSON['msg']=get_text('NotAllShootoffResolved', 'Tournament', implode(', ', $SOEvent));
} else {
	safe_w_sql("update Events set EvShootOff=1 where EvCode like '$Event%' and EvTournament={$_SESSION['TourId']}");
	$JSON['msg']='OK';
}

// resets the TeamDavis table
safe_w_sql("delete from TeamDavis where TeDaEvent='$Event' and TeDaTournament={$_SESSION['TourId']}");
if($Bonus=getModuleParameter('FFTA', 'D1Bonus') and !empty($Bonus[$Event])) {
	$q=safe_r_sql("select CoCode, TeRank from Teams inner join Countries on CoId=TeCoId and CoTournament=TeTournament where TeTournament={$_SESSION['TourId']} and TeEvent='$Event' and TeFinEvent=1 order by TeRank");
	$Now=date('Y-m-d H:i:s');
	while($r=safe_fetch($q)) {
		if($AllInOne or isset($Bonus[$Event][$r->TeRank])) {
			$BonusPoints=($AllInOne ? 0 : $Bonus[$Event][$r->TeRank]);
			safe_w_SQL("insert into TeamDavis set TeDaEvent='$Event', TeDaTeam='$r->CoCode', TeDaSubTeam=0, TeDaBonusPoints=".intval($BonusPoints).", TeDaDateTime='$Now', TeDaTournament={$_SESSION['TourId']}");
		}
	}
}

// AllInOne in day 3, creates the final event!
if($AllInOne and $_REQUEST['day']==3) {
	// creates the events, identical BUT with SO set to 0, firstphase=2, qualified=4
	$q=safe_r_sql("select * from Events where EvTeamEvent=1 and EvTournament={$_SESSION['TourId']} AND EvCode=".StrSafe_DB($Event));
	while($r=safe_fetch($q)) {
		$r->EvFinalFirstPhase=2;
		$r->EvCode='F'.$r->EvCode;
		$r->EvNumQualified=4;
		$r->EvShootOff=0;
		$r->EvProgr+=4;
		$SQL2=array();
		foreach($r as $k=>$v) {
			$SQL2[]="$k=".StrSafe_DB($v);
		}
		safe_w_sql("insert ignore into Events set ".implode(',', $SQL2));
		safe_w_sql("delete from TeamFinals where TfTournament={$_SESSION['TourId']} and TfEvent=". StrSafe_DB($r->EvCode));
		$Insert = "INSERT INTO TeamFinals (TfEvent,TfMatchNo,TfTournament,TfDateTime) 
	        SELECT EvCode,GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . "," . StrSafe_DB(date('Y-m-d H:i:s')) . " 
	        FROM Events 
	        INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0
	        INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
	        WHERE EvCode=" . StrSafe_DB($r->EvCode) . " ";
		$RsIns = safe_w_sql($Insert);

	}

}

set_qual_session_flags();

$JSON['error']=0;
$JSON['teams']=$TeamMatchnos;

JsonOut($JSON);
