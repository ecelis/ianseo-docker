<?php
require_once(dirname(dirname(__DIR__)) . '/config.php');
require_once('Common/Lib/CommonLib.php');

$JSON=[
	'error'=>1,
	'msg'=>get_text('ErrGenericError', 'Errors'),
	];

$Team=intval($_REQUEST['team'] ?? -1);
$Event=($_REQUEST['event'] ?? '');
$Level=intval($_REQUEST['level'] ?? -1);
$Group=intval($_REQUEST['group'] ?? -1);
$Round=intval($_REQUEST['round'] ?? -1);
$Schedule=($_REQUEST['sched'] ?? '');

if(!CheckTourSession() or !hasACL(AclRobin, AclReadWrite) or empty($_REQUEST['act']) or ($Team==-1 and !$Schedule)) {
	JsonOut($JSON);
}


switch($_REQUEST['act']) {
	case 'selEvent':
		$JSON['events']=[];
		$q=safe_r_sql("select EvCode as val, concat(EvCode, ' - ', EvEventName) as txt from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvElimType=5 and EvE1ShootOff=1 order by EvProgr");
		while($r=safe_fetch($q)) {
			$JSON['events'][]=$r;
		}

		$JSON['error']=0;
		JsonOut($JSON);
		break;
	case 'selLevel':
		if(!$Event) {
			JsonOut($JSON);
		}
		$q=safe_r_sql("select RrLevLevel as val, RrLevName as txt from RoundRobinLevel where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event)." order by RrLevLevel");
		$JSON['levels']=[];
		while($r=safe_fetch($q)) {
			$JSON['levels'][]=$r;
		}
		$JSON['error']=0;
		JsonOut($JSON);
		break;
	case 'selGroup':
		if(!$Level) {
			JsonOut($JSON);
		}
		$q=safe_r_sql("select RrGrName, RrGrGroup, RrLevGroupArchers, RrLevTieAllowed
			from RoundRobinLevel 
			inner join RoundRobinGroup on RrGrTournament=RrLevTournament and RrGrTeam=RrLevTeam and RrGrEvent=RrLevEvent and RrGrLevel=RrLevLevel
		    where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event)." and RrLevLevel=$Level
		    order by RrGrGroup");
		$JSON['groups']=[];
		$JSON['rounds']=[];
		while($r=safe_fetch($q)) {
			$JSON['tiesAllowed']=$r->RrLevTieAllowed;
			$JSON['groups'][]=['val'=>$r->RrGrGroup, 'txt'=>$r->RrGrName];
			if(!$JSON['rounds']) {
				for($n=1;$n<$r->RrLevGroupArchers;$n++) {
					$JSON['rounds'][]=['val'=>$n, 'txt'=>get_text('RoundNum', 'RoundRobin', $n)];
				}
			}
		}
		$JSON['error']=0;
		JsonOut($JSON);
		break;
	case 'getMain':
		if(!$Schedule and (!$Event or $Level==-1)) {
			JsonOut($JSON);
		}
		require_once('Common/Lib/ArrTargets.inc.php');
		$f=["RrMatchTournament={$_SESSION['TourId']}"];
		if($Schedule) {
			$f[]="concat_ws(' ', RrMatchScheduledDate, RrMatchScheduledTime)=".StrSafe_DB($Schedule);
		} else {
			if($Team>=0) {
				$f[]="RrMatchTeam=$Team";
			}
			if($Event) {
				$f[]="RrMatchEvent=".StrSafe_DB($Event);
			}
			if($Level) {
				$f[]="RrMatchLevel=$Level";
			}
			if($Group) {
				$f[]="RrMatchGroup=$Group";
			}
			if($Round) {
				$f[]="RrMatchRound=$Round";
			}
		}
		$q=safe_r_sql("select EvCode as code, EvEventName as name,
       			concat_ws('|',EvTeamEvent,EvCode,RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) as `key`,
       			RrMatchGroup as val, RrLevName as levName, RrGrName as txt, RrMatchRound as roundId,
	            RrMatchMatchNo as matchno, RrMatchTarget as target, 
	            coalesce(concat(upper(EnFirstName),' ', EnName), if(RrMatchSubTeam>0, concat(CoCode, '-', RrMatchSubTeam), CoCode), '') as athlete, 
	            coalesce(EnCoCode, CoName, '') as country, if(RrLevMatchMode=1, RrMatchSetScore, RrMatchScore) as matchScore, RrMatchSetPoints as endPoints, 
	            RrMatchTie as tie, RrLevSO as soArrows, RrLevEnds as numEnds, RrLevArrows as numArrows, EvFinalTargetType, 
       			RrLevGroupArchers-1 as rounds, RrMatchTbClosest as closest, RrMatchTiebreak as tieArrows, RrMatchWinLose as winner, RrMatchWinLose as finished,
				if(RrLevTieBreakSystem2>0, concat_ws('/', RrPartPoints,RrPartTieBreaker,RrPartTieBreaker2), concat_ws('/', RrPartPoints,RrPartTieBreaker)) as gPoints, 
				if(RrLevTieBreakSystem2>0, concat_ws('/',RrMatchRoundPoints,RrMatchTieBreaker,RrMatchTieBreaker2), concat_ws('/',RrMatchRoundPoints,RrMatchTieBreaker)) as mPoints, RrPartGroupRank as gRank
			from RoundRobinMatches 
		    inner join RoundRobinGrids on RrGridTournament=RrMatchTournament and RrGridTeam=RrMatchTeam and RrGridEvent=RrMatchEvent and RrGridLevel=RrMatchLevel and RrGridGroup=RrMatchGroup and RrGridRound=RrMatchRound and RrGridMatchno=RrMatchMatchNo
			inner join RoundRobinParticipants on RrPartTournament=RrMatchTournament and RrPartTeam=RrMatchTeam and RrPartEvent=RrMatchEvent and RrPartLevel=RrMatchLevel and RrPartGroup=RrMatchGroup and RrPartParticipant=RrMatchAthlete and RrPartSubTeam=RrMatchSubTeam and RrPartDestItem=RrGridItem
		    inner join Events on EvTournament=RrMatchTournament and EvTeamEvent=RrMatchTeam and EvCode=RrMatchEvent
			inner join RoundRobinGroup on RrGrTournament=RrMatchTournament and RrGrTeam=RrMatchTeam and RrGrEvent=RrMatchEvent and RrGrLevel=RrMatchLevel and RrGrGroup=RrMatchGroup
			inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel
			left join (select EnTournament, EnId, EnFirstName, EnName, CoCode as EnCoCode, CoName as EnCoName from Entries inner join Countries on CoId=EnCountry and CoTournament=EnTournament where EnTournament={$_SESSION['TourId']}) Entries on EnTournament=RrMatchTournament and EnId=RrMatchAthlete and RrMatchTeam=0
			left join Countries on CoTournament=RrMatchTournament and CoId=RrMatchAthlete and RrMatchTeam=1
			where ".implode(' and ', $f)."
			order by EvTeamEvent, EvProgr, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo");
		$JSON['groups']=[];
		$JSON['numGroups']=0;
		$Finished=false;
		while($r=safe_fetch($q)) {
			$r->finished=(bool) $r->finished;
			$r->winner=(bool) $r->winner;
			if($r->winner) {
				if($r->matchno%2==0) {
					$Finished = true;
				} else {
					// need to adjust the previous match
					$tmp=array_pop($JSON['groups'][$r->val]['rounds'][$r->roundId]['rows']);
					$tmp->finished=true;
					$JSON['groups'][$r->val]['rounds'][$r->roundId]['rows'][]=$tmp;
					$Finished=false;
				}
			} elseif($Finished) {
				$r->finished=true;
				$Finished=false;
			}
			if(empty($JSON['maxValue'])) {
				// gets the max values of the target used in this event
				$Target=GetGoodLettersFromTgtId($r->EvFinalTargetType);
				$JSON['maxValue']=GetMaxTargetValue($Target)*$r->numArrows;
			}
			if(empty($JSON['groups'][$r->val])) {
				$JSON['groups'][$r->val]=[
					'id'=>$r->val,
					'name'=>$r->txt,
					'rounds'=>[],
				];
				// $JSON['roundWidth']=ceil(20/$r->rounds)*5;
				$JSON['numGroups']++;
			}
			if(empty($JSON['groups'][$r->val]['rounds'][$r->roundId])) {
				$JSON['groups'][$r->val]['rounds'][$r->roundId]=[
					'id'=>$r->roundId,
					'name'=>$r->levName . ' - ' . get_text('RoundNum', 'RoundRobin', $r->roundId),
					'rows'=>[],
				];
			}
			$r->tieArrows=array_chunk(array_pad(DecodeFromString($r->tieArrows, false, true),3*$r->soArrows,''), $r->soArrows);
			$r->endPoints=array_pad(explode('|', $r->endPoints), $r->numEnds, '');
			$r->target=ltrim($r->target,'0');
			$JSON['groups'][$r->val]['rounds'][$r->roundId]['rows'][]=$r;
		}
		$JSON['colRound']=count($JSON['groups'][1]['rounds'] ?? []);
		$JSON['error']=0;
		$JSON['ties']=[
			['val'=>0,'txt'=>get_text('NoTie', 'Tournament')],
			['val'=>1,'txt'=>get_text('TieWinner', 'Tournament')],
			['val'=>2,'txt'=>get_text('Bye')],
		];
		$JSON['error']=0;
		$JSON['headers']=[
			'target' => [get_text('Target'), 'w-3ch'],
			'athlete' => [get_text('Athlete'), $Team ? 'w-3ch' : ''],
			'country' => [get_text('Country'), $Team ? '' : 'w-3ch'],
			'points' => [get_text('Score', 'Tournament'), 'w-25'],
			'tie' => [get_text('Tie'), 'w-5'],
			'bye' => [get_text('Bye'), 'w-5'],
			'arrows' => [get_text('TieArrows'), 'w-30'],
			'closest' => [get_text('ClosestShort', 'Tournament'), 'w-3ch'],
			'total' => [get_text('Total'), 'w-3ch'],
			'mPoints' => [get_text('MatchPoints', 'Tournament'), 'w-3ch'],
			'gPoints' => [get_text('GroupPoints', 'RoundRobin'), 'w-3ch'],
			'gRank' => [get_text('Rank'), 'w-3ch'],
		];
		JsonOut($JSON);
		break;
	case 'updateScore':
		$tmp=explode('|', $_REQUEST['key']??'');
		$Team=intval($tmp[0]??-1);
		$Event=($tmp[1]??'');
		$Level=intval($tmp[2]??-1);
		$Group=intval($tmp[3]??-1);
		$Round=intval($tmp[4]??-1);
		$Matchno=intval($tmp[5]??-1);
		$Field=($_REQUEST['field'] ?? '');
		$Val=($_REQUEST['val'] ?? '');
		if($Matchno==-1 or !$Field or $Group==-1 or $Round==-1) {
			JsonOut($JSON);
		}

		// check if set or cumulative, number of arrows etc
		$q=safe_r_sql("select EvCode as Event, RrLevMatchMode as MatchMode, RrLevArrows NumArrows, RrLevEnds NumEnds, RrLevSO NumSO, RrLevTieAllowed TieAllowed, 
       		RrLevWinPoints as WinPoints, RrLevTiePoints as TiePoints, RrLevTieBreakSystem TieSystem, RrLevTieBreakSystem2 TieSystem2, $Team as Team, $Level as Level, $Group as `Group`, $Round as `Round`,
       		RrMatchSetPoints as EndPoints, RrMatchTiebreak as TieArrowString
			from Events 
		    inner join RoundRobinLevel on RrLevTournament=EvTournament and RrLevTeam=EvTeamEvent and RrLevEvent=EvCode and RrLevLevel=$Level
			inner join RoundRobinMatches on RrMatchTournament=EvTournament and RrMatchTeam=EvTeamEvent and RrMatchEvent=EvCode and RrMatchLevel=RrLevLevel and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno
	        where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
		if(!($EV=safe_fetch($q))) {
			JsonOut($JSON);
		}
		$ScoreField=($EV->MatchMode ? 'RrMatchSetScore' : 'RrMatchScore');
		$OnlyTotals=true;
		$Recalculate=false;

		require_once('Common/Lib/ArrTargets.inc.php');
		switch(substr($Field,0,3)) {
			case 'end':
				$index=(int) substr($Field, 4);
				$Val=($Val==='' ? '' : intval($Val));
				$EndPoints=explode('|', $EV->EndPoints);
				$EndPoints[$index]=($Val ?: '');
				$Total=(int) array_sum($EndPoints);
				// while(count($EndPoints) and end($EndPoints)==='') {
				// 	array_pop($EndPoints);
				// }
				$EndPoints=implode('|', $EndPoints);
				$EndPoints=rtrim($EndPoints,'|');
				$OnlyTotals=false;
				safe_w_sql("update RoundRobinMatches set RrMatchSetPoints='$EndPoints', RrMatchScore=$Total, RrMatchDateTime=now()
					where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno");
				$Recalculate=safe_w_affected_rows();
				break;
			case 'sco':
				safe_w_sql("update RoundRobinMatches set $ScoreField='$Val', RrMatchDateTime=now()
					where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno");
				$Recalculate=safe_w_affected_rows();
				break;
			case 'bye':
				$Val=intval($Val);
				if($Val) {
					$Val=2; // force to be 2 (bye)
					$m2=($Matchno%2 ? $Matchno-1 : $Matchno+1);
					// setting a bye automatically removes the bye to the opponent
					safe_w_sql("update RoundRobinMatches set RrMatchTie=0, RrMatchDateTime=now()
						where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$m2");
				}
				safe_w_sql("update RoundRobinMatches set RrMatchTie=$Val, RrMatchDateTime=now()
					where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno");
				$Recalculate=safe_w_affected_rows();
				break;
			case 'tb[':
				$idx=substr($Field,3,-1);
				$t=safe_r_sql("select RrMatchTiebreak, RrMatchTbClosest, EvFinalTargetType, RrLevSO
					from RoundRobinMatches
					inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevEvent=RrMatchEvent and RrLevTeam=RrMatchTeam and RrLevLevel=RrMatchLevel
					inner join Events on EvTournament=RrMatchTournament and EvCode=RrMatchEvent and EvTeamEvent=RrMatchTeam
					where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno");
				$u=safe_fetch($t);
				$u->RrMatchTiebreak=str_pad($u->RrMatchTiebreak, $idx+1, ' ', STR_PAD_RIGHT);
				$Ar=GetLetterFromPrint($Val, 'T', $u->EvFinalTargetType);
				$u->RrMatchTiebreak[$idx]=$Ar;
				$TbEnds=[];
				foreach(str_split(rtrim($u->RrMatchTiebreak), $u->RrLevSO) as $chunk) {
					$TbEnds[]=ValutaArrowString($chunk);
				}
				$TbDecoded=implode(',', $TbEnds);
				safe_w_sql("update RoundRobinMatches set RrMatchTiebreak='$u->RrMatchTiebreak', RrMatchTbDecoded='$TbDecoded', RrMatchTbClosest=0, RrMatchDateTime=RrMatchDateTime
					where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno");
				$Recalculate=safe_w_affected_rows();
				$OnlyTotals=false;
				if($Recalculate) {
					$m2=($Matchno%2 ? $Matchno-1 : $Matchno+1);
					safe_w_sql("update RoundRobinMatches set RrMatchTbClosest=0, RrMatchDateTime=now()
						where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo in ($Matchno, $m2)");
				}
				break;
			case 'tie':
				$Val=intval($Val);
				if($Val) {
					$m2=($Matchno%2 ? $Matchno-1 : $Matchno+1);
					// setting a tie removes it from the opponent
					safe_w_sql("update RoundRobinMatches set RrMatchTie=0, RrMatchDateTime=RrMatchDateTime
						where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$m2");
				}
				safe_w_sql("update RoundRobinMatches set RrMatchTie=$Val, RrMatchDateTime=RrMatchDateTime
					where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno");
				$Recalculate=safe_w_affected_rows();
				if($Recalculate) {
					safe_w_sql("update RoundRobinMatches set RrMatchDateTime=now()
						where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo in ($Matchno, $m2)");
				}
				$OnlyTotals=false;
				break;
			case 'c':
				// closest to center, is the winner
				// we need to remove the "+" from the other match and recalculate things
				$Val=(intval($Val) ? 1 : 0);
				safe_w_sql("update RoundRobinMatches set RrMatchTbClosest=$Val, RrMatchDateTime=RrMatchDateTime
					where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno");
				$Recalculate=safe_w_affected_rows();

				if($Recalculate) {
					if($Val) {
						// need to remove the flag in the other match anyway
						$m2=($Matchno%2 ? $Matchno-1 : $Matchno+1);
						safe_w_sql("update RoundRobinMatches set RrMatchTbClosest=0, RrMatchDateTime=now()
							where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo = $m2");
					}
					$OnlyTotals=false;
				}
				break;
			default:
				JsonOut($JSON);
		}

		// gets the 2 opponents
		if($Recalculate) {
			require_once('./Lib.php');

			// resets the SO solved status!!!
			RobinResetSO($Team, $Event, $Level, $Group);

			setWinner($m1=(int) floor($Matchno/2)*2, $EV, $ScoreField, $OnlyTotals);
			$m2=$m1+1;

			// returns updated situation of the 2 opponents
			$f=["RrMatchTournament={$_SESSION['TourId']}"];
			if($Schedule) {
				$f[]="concat_ws(' ', RrMatchScheduledDate, RrMatchScheduledTime)=".StrSafe_DB($Schedule);
			} else {
				if($Team>=0) {
					$f[]="RrMatchTeam=$Team";
				}
				if($Event) {
					$f[]="RrMatchEvent=".StrSafe_DB($Event);
				}
				if($Level) {
					$f[]="RrMatchLevel=$Level";
				}
				if($Group) {
					$f[]="RrMatchGroup=$Group";
				}
				if($Round) {
					$f[]="RrMatchRound=$Round";
				}
			}
			$JSON['rows']=[];
			$JSON['gPoints']=[];
			$JSON['gRank']=[];
			$RetSQL="select 
       			concat_ws('|',RrMatchTeam,RrMatchEvent,RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) as `key`,
				RrMatchSetPoints as endPoints,
				if(RrLevMatchMode=1, RrMatchSetScore, RrMatchScore) as score,
				RrMatchTie as tie, 
       			RrMatchTbClosest as closest,
                RrMatchTiebreak as tieArrows,
       			RrMatchMatchNo as matchno,
       			RrMatchEvent as idEvent,
       			RrMatchLevel as idLevel,
       			RrMatchGroup as idGroup,
       			RrMatchRound as idRound,
       			RrMatchWinLose as winner,
       			RrMatchWinLose as finished,
       			if(RrLevTieBreakSystem2>0, concat_ws('/',RrPartPoints,RrPartTieBreaker,RrPartTieBreaker2), concat_ws('/',RrPartPoints,RrPartTieBreaker)) as gPoints, 
       			if(RrLevTieBreakSystem2>0, concat_ws('/',RrMatchRoundPoints,RrMatchTieBreaker,RrMatchTieBreaker2), concat_ws('/',RrMatchRoundPoints,RrMatchTieBreaker)) as mPoints, 
       			RrPartGroupRank as gRank
				from RoundRobinMatches
				inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel
				inner join RoundRobinParticipants on RrPartTournament=RrMatchTournament and RrPartTeam=RrMatchTeam and RrPartEvent=RrMatchEvent and RrPartLevel=RrMatchLevel and RrPartGroup=RrMatchGroup and RrPartParticipant=RrMatchAthlete and RrPartSubTeam=RrMatchSubTeam
				where ".implode(' AND ', $f)."
				order by RrMatchTeam, RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo";
			$q=safe_r_sql($RetSQL);
			$Finished=false;
			while($r=safe_fetch($q)) {
				$r->finished=(bool) $r->finished;
				$r->winner=(bool) $r->winner;
				if($r->winner) {
					if($r->matchno%2==0) {
						$Finished = true;
					} else {
						// need to adjust the previous match
						$tmp=array_pop($JSON['rows']);
						$tmp->finished=true;
						$JSON['rows'][]=$tmp;
						$Finished=false;
					}
				} elseif($Finished) {
					$r->finished=true;
					$Finished=false;
				}
				$r->endPoints=explode('|', $r->endPoints);
				$r->tieArrows=str_split($r->tieArrows);
				if($r->RrMatchRound=$EV->Round) {
					$JSON['rows'][]=$r;
				}
				$JSON['gPoints'][]=['key'=>$r->key,'v'=>$r->gPoints];
				$JSON['gRank'][]=['key'=>$r->key,'v'=>$r->gRank];
			}
		}
		$JSON['error']=0;
		JsonOut($JSON);
		break;
}

JsonOut($JSON);
