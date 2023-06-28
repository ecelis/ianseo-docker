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

if(!CheckTourSession() or !hasACL(AclRobin, AclReadWrite) or empty($_REQUEST['act']) or $Team==-1) {
	JsonOut($JSON);
}


switch($_REQUEST['act']) {
	case 'selEvent':
		$q=safe_r_sql("select EvCode as val, concat(EvCode, ' - ', EvEventName) as txt from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvElimType=5 and EvE1ShootOff=1 order by EvProgr");
		$JSON['events']=[];
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
		if(!$Event or $Level==-1) {
			JsonOut($JSON);
		}
		require_once('Common/Lib/ArrTargets.inc.php');
		$q=safe_r_sql("select 
       			RrMatchGroup as val, RrGrName as txt, RrMatchRound as roundId,
	            RrMatchMatchNo as matchno, RrMatchTarget as target, 
	            coalesce(concat(upper(EnFirstName),' ', EnName), if(RrMatchSubTeam>0, concat(CoCode, '-', RrMatchSubTeam), CoCode), '') as athlete, 
	            coalesce(EnCoCode, CoName, '') as country, if(RrLevMatchMode=1, RrMatchSetScore, RrMatchScore) as matchScore, RrMatchSetPoints as endPoints, 
	            RrMatchTie as tie, RrLevSO as soArrows, RrLevEnds as numEnds, RrLevArrows as numArrows, EvFinalTargetType, 
       			RrLevGroupArchers-1 as rounds, RrMatchTbClosest as closest, RrMatchTiebreak as tieArrows,
       			concat(RrPartPoints,'/',RrPartTieBreaker) as gPoints, concat(RrMatchRoundPoints,'/',RrMatchTieBreaker) as mPoints, RrPartGroupRank as gRank
			from RoundRobinMatches 
		    inner join RoundRobinGrids on RrGridTournament=RrMatchTournament and RrGridTeam=RrMatchTeam and RrGridEvent=RrMatchEvent and RrGridLevel=RrMatchLevel and RrGridGroup=RrMatchGroup and RrGridRound=RrMatchRound and RrGridMatchno=RrMatchMatchNo
			inner join RoundRobinParticipants on RrPartTournament=RrMatchTournament and RrPartTeam=RrMatchTeam and RrPartEvent=RrMatchEvent and RrPartLevel=RrMatchLevel and RrPartGroup=RrMatchGroup and RrPartParticipant=RrMatchAthlete and RrPartSubTeam=RrMatchSubTeam and RrPartDestItem=RrGridItem
		    inner join Events on EvTournament=RrMatchTournament and EvTeamEvent=$Team and EvCode=RrMatchEvent
			inner join RoundRobinGroup on RrGrTournament=RrMatchTournament and RrGrTeam=RrMatchTeam and RrGrEvent=RrMatchEvent and RrGrLevel=RrMatchLevel and RrGrGroup=RrMatchGroup
			inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel
			left join (select EnTournament, EnId, EnFirstName, EnName, CoCode as EnCoCode, CoName as EnCoName from Entries inner join Countries on CoId=EnCountry and CoTournament=EnTournament where EnTournament={$_SESSION['TourId']}) Entries on EnTournament=RrMatchTournament and EnId=RrMatchAthlete and RrMatchTeam=0
			left join Countries on CoTournament=RrMatchTournament and CoId=RrMatchAthlete and RrMatchTeam=1
			where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level 
			".($Group ? "and RrMatchGroup=$Group" : "")."
			".($Round ? "and RrMatchRound=$Round" : "")."
			order by RrMatchGroup, RrMatchRound, RrMatchMatchNo");
		$JSON['groups']=[];
		$JSON['numGroups']=0;
		while($r=safe_fetch($q)) {
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
					'name'=>get_text('RoundNum', 'RoundRobin', $r->roundId),
					'rows'=>[],
				];
			}
			$r->tieArrows=array_chunk(array_pad(str_split($r->tieArrows),3*$r->soArrows,''), $r->soArrows);
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
		$Matchno=($_REQUEST['matchno'] ?? -1);
		$Field=($_REQUEST['field'] ?? '');
		$Val=($_REQUEST['val'] ?? '');
		if($Matchno==-1 or !$Field or $Group==-1 or $Round==-1) {
			JsonOut($JSON);
		}

		// check if set or cumulative, number of arrows etc
		$q=safe_r_sql("select EvCode as Event, RrLevMatchMode as MatchMode, RrLevArrows NumArrows, RrLevEnds NumEnds, RrLevSO NumSO, RrLevTieAllowed TieAllowed, 
       		RrLevWinPoints as WinPoints, RrLevTiePoints as TiePoints, RrLevTieBreakSystem TieSystem, $Team as Team, $Level as Level, $Group as `Group`, $Round as `Round`,
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
				break;
			case 'sco':
				safe_w_sql("update RoundRobinMatches set $ScoreField='$Val', RrMatchDateTime=now()
					where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno");
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
				break;
			default:
				JsonOut($JSON);
		}

		// gets the 2 opponents
		if(safe_w_affected_rows()) {
			require_once('./Lib.php');

			// resets the SO solved status!!!
			RobinResetSO($Team, $Event, $Level, $Group);

			setWinner($m1=(int) floor($Matchno/2)*2, $EV, $ScoreField, $OnlyTotals);
			$m2=$m1+1;

			// returns updated situation of the 2 opponents
			$JSON['rows']=[];
			$JSON['gPoints']=[];
			$JSON['gRank']=[];
			$RetSQL="select 
				RrMatchSetPoints as endPoints,
				$ScoreField as score,
				RrMatchTie as tie, 
       			RrMatchTbClosest as closest,
                RrMatchTiebreak as tieArrows,
       			RrMatchMatchNo as matchno,
       			RrMatchEvent as idEvent,
       			RrMatchLevel as idLevel,
       			RrMatchGroup as idGroup,
       			RrMatchRound as idRound,
       			concat(RrPartPoints,'/',RrPartTieBreaker) as gPoints, 
       			concat(RrMatchRoundPoints,'/',RrMatchTieBreaker) as mPoints, 
       			RrPartGroupRank as gRank
				from RoundRobinMatches
				inner join RoundRobinParticipants on RrPartTournament=RrMatchTournament and RrPartTeam=RrMatchTeam and RrPartEvent=RrMatchEvent and RrPartLevel=RrMatchLevel and RrPartGroup=RrMatchGroup and RrPartParticipant=RrMatchAthlete and RrPartSubTeam=RrMatchSubTeam
				where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam={$EV->Team} and RrMatchEvent=".StrSafe_DB($EV->Event)." and RrMatchLevel={$EV->Level} and RrMatchGroup={$EV->Group}
				";
			$q=safe_r_sql($RetSQL);
			while($r=safe_fetch($q)) {
				$r->endPoints=explode('|', $r->endPoints);
				$r->tieArrows=str_split($r->tieArrows);
				if($r->RrMatchRound=$EV->Round) {
					$JSON['rows'][]=$r;
				}
				$JSON['gPoints'][]=['g' => $r->idGroup, 'r'=>$r->idRound,'m'=>$r->matchno,'v'=>$r->gPoints];
				$JSON['gRank'][]=['g' => $r->idGroup, 'r'=>$r->idRound,'m'=>$r->matchno,'v'=>$r->gRank];
			}
		}
		$JSON['error']=0;
		JsonOut($JSON);
		break;
}

JsonOut($JSON);
