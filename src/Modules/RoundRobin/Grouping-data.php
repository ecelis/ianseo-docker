<?php

$JSON=array('error'=>1, 'msg'=>'Wrong Data');

require_once(dirname(dirname(__DIR__)) . '/config.php');

if(!CheckTourSession() or !hasACL(AclCompetition, AclReadWrite) or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

require_once('Common/Lib/CommonLib.php');

$Team=intval($_REQUEST['Team'] ?? 0);
$Event=($_REQUEST['Event'] ?? '');
$Level=max(intval($_REQUEST['Level'] ?? 1), 1);
$IsBracket=($_REQUEST['Level']=='B');
$Group=intval($_REQUEST['Group'] ?? 0);
$Item=intval($_REQUEST['Item'] ?? 0);
$TotLevels=0;

if($IsBracket) {
	$Level=0;
}

// Check the event exists
if($Event) {
	$q=safe_r_sql("select EvCodeParent, EvCode, EvEventName, EvElim1, EvFinalFirstPhase, EvNumQualified, coalesce(RrLevGroupArchers,EvNumQualified) as RrLevGroupArchers
		from Events 
		left join RoundRobinLevel on RrLevTournament=EvTournament and RrLevTeam=EvTeamEvent and RrLevEvent=EvCode and RrLevLevel=$Level
		where EvElimType=5 and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event)." and EvTournament={$_SESSION['TourId']}");
	if($EVENT=safe_fetch($q)) {
		$TotLevels = $EVENT->EvElim1;
	} else {
		// no matching event so void it
		$Event='';
	}
}

switch($_REQUEST['act']) {
	case 'getDetails':
		// if no events, load an array of events that fit the "team" and select the first one
		if(!$Event) {
			$JSON['events']=[];
			$q=safe_r_sql("select EvCodeParent, EvCode, EvEventName, EvElim1, EvFinalFirstPhase, EvNumQualified, coalesce(RrLevGroupArchers,EvNumQualified) as RrLevGroupArchers 
				from Events 
				left join RoundRobinLevel on RrLevTournament=EvTournament and RrLevTeam=EvTeamEvent and RrLevEvent=EvCode and RrLevLevel=$Level
				where EvElimType=5 and EvTeamEvent=$Team and EvTournament={$_SESSION['TourId']} order by EvProgr");
			while($r=safe_fetch($q)) {
				$JSON['events'][]=$r;
				if(!$Event) {
					$EVENT=$r;
					$Event=$r->EvCode;
					$TotLevels = $r->EvElim1;
				}
			}
		}

		if(!$Event) {
			JsonOut($JSON);
		}

		$JSON['levels']=[];
		$lvls=array();
		foreach(range(1, $TotLevels) as $l) {
			$lvls[]="select $l as Level";
		}
		$q=safe_r_sql("select Level, RrLevName from (".implode (' UNION ', $lvls).") lvls
    		left join RoundRobinLevel on RrLevLevel=Level and RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event));
		while($r=safe_fetch($q)) {
			$JSON['levels'][]=array(
				'val' => $r->Level,
				'text' => ($r->RrLevName ?? get_text('LevelNum', 'RoundRobin', $r->Level)),
				'disabled' => is_null($r->RrLevName),
			);
		}

		$JSON['headers']=array(
			'grName' => get_text('GroupName', 'RoundRobin'),
			'grSession' => get_text('Session'),
			'grComponents' => get_text('Components', 'RoundRobin'),
			'grAthTgt' => get_text('Ath4Target', 'Tournament'),
			'grMatTgt' => get_text('Match4Target', 'Tournament'),
			'source' => get_text('Source', 'RoundRobin'),
			'srcRank' => get_text('SourceRank', 'RoundRobin'),
			'item' => get_text('Item', 'RoundRobin'),
			'name' => get_text('ItemName', 'RoundRobin'),
		);

		$JSON['grItems']=(int) $EVENT->RrLevGroupArchers;
		$JSON['setAll']=get_text('ToAll');
		$JSON['groups']=array();

		if($IsBracket) {
			require_once('Common/Fun_Sessions.inc.php');
			// bracket Participants have group and level 0
			$RealPhase=valueFirstPhase($EVENT->EvFinalFirstPhase);
			$Position=($RealPhase==$EVENT->EvFinalFirstPhase ? 'GrPosition' : 'GrPosition2');
			$q=safe_r_sql("select RrPartGroup, RrPartSourceLevel, RrPartSourceGroup, RrPartSourceRank, RrPartDestItem, '' as RrGrName, 0 as RrGrSession, 0 as RrGrTargetArchers, 0 as RrGrArcherWaves, coalesce(concat(upper(EnFirstName), ' ', EnName), concat(CoCode, '-', CoName, if(TeSubTeam>0, concat(' (', TeSubTeam, ')'), ''))) as PartName from RoundRobinParticipants
				inner join Events on EvTournament=RrPartTournament and EvTeamEvent=RrPartTeam and EvCode=RrPartEvent
    			inner join Grids on GrPhase=$RealPhase and $Position=RrPartDestItem
    			left join Entries on EnId=RrPartParticipant and RrPartTeam=0
				left join Teams on TeCoId=RrPartParticipant and TeSubTeam=RrPartSubTeam and TeFinEvent=1 and TeEvent=RrPartEvent and RrPartTeam=1
				left join Countries on CoId=TeCoId and CoTournament=RrPartTournament
				where RrPartLevel=0 and RrPartGroup=0 and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and  RrPartTournament={$_SESSION['TourId']}
				order by GrMatchNo");
		} else {
			$q=safe_r_sql("select RrPartGroup, RrPartSourceLevel, RrPartSourceGroup, RrPartSourceRank, RrPartDestItem, RrGrName, RrGrSession, RrGrTargetArchers, RrGrArcherWaves, coalesce(concat(upper(EnFirstName), ' ', EnName), concat(CoCode, '-', CoName, if(TeSubTeam>0, concat(' (', TeSubTeam, ')'), ''))) as PartName from RoundRobinParticipants
	            inner join RoundRobinGroup on RrGrTournament=RrPartTournament and RrGrTeam=RrPartTeam and RrGrEvent=RrPartEvent and RrGrLevel=RrPartLevel and RrGrGroup=RrPartGroup
				left join Entries on EnId=RrPartParticipant and RrPartTeam=0
				left join Teams on TeCoId=RrPartParticipant and TeSubTeam=RrPartSubTeam and TeFinEvent=1 and TeEvent=RrPartEvent and RrPartTeam=1
				left join Countries on CoId=TeCoId and CoTournament=RrPartTournament
				left join Session on SesTournament=RrPartTournament and SesType='R' and SesOrder=RrGrSession
				where RrPartLevel=$Level and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and  RrPartTournament={$_SESSION['TourId']}
				order by RrPartGroup, RrPartDestItem");
		}

		while($r=safe_fetch($q)) {
			if(empty($JSON['groups'][$r->RrPartGroup])) {
				$JSON['groups'][$r->RrPartGroup]=[
					'id'=>$r->RrPartGroup,
					'grName'=>$r->RrGrName,
					'grAthTgt'=>$r->RrGrTargetArchers,
					'grMatTgt'=>$r->RrGrArcherWaves,
					'grSession'=>$r->RrGrSession,
					'grInternal' => get_text('GroupNum', 'RoundRobin', $r->RrPartGroup),
					'grComponents'=>[],
				];

			}
			$JSON['groups'][$r->RrPartGroup]['grComponents'][]=[
				'source' => $r->RrPartSourceLevel.'-'.$r->RrPartSourceGroup,
				'srcRank' => $r->RrPartSourceRank,
				'item' => $r->RrPartDestItem,
				'name' => ($r->PartName ?? ''),
			];
		}

		$JSON['groups']=array_values($JSON['groups']);

		// gets all the source levels+groups of previous level
		// if $Level==1 than previous level is qualification or Parent Event!
		$JSON['srcLevels']=array(
			[
				'val' => '0-0',
				'txt' => $EVENT->EvCodeParent ?: get_text('QualRound')
			],
		);
		$oldLevel='-1';
		$oldLevelName='';
		$q=safe_r_SQL("select distinct RrLevLevel, RrLevName, RrGrGroup, RrGrName 
			from RoundRobinLevel
			inner join RoundRobinGroup on RrGrTournament=RrLevTournament and RrGrTeam=RrLevTeam and RrGrEvent=RrLevEvent and RrGrLevel=RrLevLevel
			where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event)." and RrLevLevel>0 ".($IsBracket ? "" : "and RrLevLevel<$Level")."
			order by RrGrLevel, RrGrGroup");
		while($r=safe_fetch($q)) {
			if($oldLevel!=$r->RrLevLevel and $oldLevelName) {
				$JSON['srcLevels'][]= [
					'val' => "{$oldLevel}-0",
					'txt' => "{$oldLevelName} ".get_text('BestRanked', 'RoundRobin'),
				];
			}
			$JSON['srcLevels'][]= [
					'val' => "{$r->RrLevLevel}-{$r->RrGrGroup}",
					'txt' => "{$r->RrLevName} {$r->RrGrName}",
				];
			$oldLevel=$r->RrLevLevel;
			$oldLevelName=$r->RrLevName;
		}
		if($oldLevelName) {
			$JSON['srcLevels'][]= [
				'val' => "{$oldLevel}-0",
				'txt' => "{$oldLevelName} ".get_text('BestRanked', 'RoundRobin'),
			];
		}

		// adds the Final brackets in the levels!
		$JSON['levels'][]=[
			'val' => 'B',
			'text' => get_text('Brackets'),
		];

		// gets all the named sessions already made for round robins
		$JSON['sessions']=[];
		$q=safe_r_SQL("select SesOrder, SesName from Session where SesTournament={$_SESSION['TourId']} and SesType='R' order by SesOrder");
		while($r=safe_fetch($q)) {
			$JSON['sessions'][]=[
				'val' => $r->SesOrder,
				'text' => $r->SesName,
			];
		}

		break;
	case 'setValue':
		if(!$Event or empty($_REQUEST['which']) or ($IsBracket ? false : (!$Level or !$Group))) {
			JsonOut($JSON);
		}
		switch($_REQUEST['which']) {
			case 'ath':
				safe_w_sql("update RoundRobinGroup set RrGrTargetArchers=".intval($_REQUEST['val'])." 
					where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($Event)." and RrGrLevel=$Level and RrGrGroup=$Group");
				$JSON['error']=0;
				break;
			case 'wave':
				safe_w_sql("update RoundRobinGroup set RrGrArcherWaves=".intval($_REQUEST['val'])." 
					where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($Event)." and RrGrLevel=$Level and RrGrGroup=$Group");
				$JSON['error']=0;
				break;
			case 'name':
				safe_w_sql("update RoundRobinGroup set RrGrName=".StrSafe_DB($_REQUEST['val'])." 
					where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($Event)." and RrGrLevel=$Level and RrGrGroup=$Group");
				$JSON['error']=0;
				break;
			case 'rank':
				if(!$Item) {
					JsonOut($JSON);
				}
				$Rank=intval($_REQUEST['val']);
				safe_w_sql("update RoundRobinParticipants set RrPartSourceRank=$Rank".($Rank ? '' : ', RrPartSourceLevel=0, RrPartSourceGroup=0')." 
					where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level and RrPartGroup=$Group and RrPartDestItem=$Item");
				if(safe_w_affected_rows()) {
					// needs to recalculate the whole thing!
					// TODO: recalculate the things!
				}
				$JSON['error']=0;
				break;
			case 'src':
				if(!$Item) {
					JsonOut($JSON);
				}
				$bits=explode('-',$_REQUEST['val']);
				$srcLevel=intval($bits[0]);
				$srcGroup=intval($bits[1] ?? 0);
				safe_w_sql("update RoundRobinParticipants set RrPartSourceLevel=$srcLevel, RrPartSourceGroup=$srcGroup 
					where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level and RrPartGroup=$Group and RrPartDestItem=$Item");
				if(safe_w_affected_rows()) {
					// needs to recalculate the whole thing!
					// TODO: recalculate the things!
				}
				$JSON['error']=0;
				break;
			default:
				JsonOut($JSON);
		}

		break;
	case 'autoSeed':
		if(!$Event) {
			JsonOut($JSON);
		}
		$q=safe_r_sql("select * from RoundRobinLevel where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event)." and RrLevLevel<=$Level order by RrLevLevel desc");
		$LEVEL=safe_fetch($q);
		$PREV=safe_fetch($q);
		if($Level==1) {
			if(empty($_REQUEST['type'])) {
				// block seed... from qualification!
				$rnk=1;
				for($i=1;$i<=$LEVEL->RrLevGroups;$i++) {
					for($j=1;$j<=$LEVEL->RrLevGroupArchers;$j++) {
						safe_w_sql("update RoundRobinParticipants set RrPartSourceLevel=0, RrPartSourceGroup=0, RrPartSourceRank=$rnk 
							where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level and RrPartGroup=$i and RrPartDestItem=$j");
						$rnk++;
					}
				}
			} else {
				// snake seed
				$rnk=1;
				for($j=1;$j<=$LEVEL->RrLevGroupArchers;$j++) {
					if(intval($j/2)*2==$j) {
						// back so decrement!
						for($i=$LEVEL->RrLevGroups;$i>=1;$i--) {
							safe_w_sql("update RoundRobinParticipants set RrPartSourceLevel=0, RrPartSourceGroup=0, RrPartSourceRank=$rnk 
								where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level and RrPartGroup=$i and RrPartDestItem=$j");
							$rnk++;
						}
					} else {
						for($i=1;$i<=$LEVEL->RrLevGroups;$i++) {
							safe_w_sql("update RoundRobinParticipants set RrPartSourceLevel=0, RrPartSourceGroup=0, RrPartSourceRank=$rnk 
								where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level and RrPartGroup=$i and RrPartDestItem=$j");
							$rnk++;
						}
					}
				}
			}

		} elseif(!$IsBracket) {
			// we only offer the "horizontal seed", all 1st in 1 group, all 2nd in another group and so on...
			// this only works if the number of archers in a group of thius level is at least the number of groups of the previous level!
			// start removing previous setting
			safe_w_sql("update RoundRobinParticipants set RrPartSourceLevel=0, RrPartSourceGroup=0, RrPartSourceRank=0
								where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level");
			if($PREV->RrLevGroups<=$LEVEL->RrLevGroupArchers) {
				for($i=1;$i<=$LEVEL->RrLevGroups;$i++) {
					for($j=1;$j<=$PREV->RrLevGroups;$j++) {
						safe_w_sql("update RoundRobinParticipants set RrPartSourceLevel={$PREV->RrLevLevel}, RrPartSourceGroup=$j, RrPartSourceRank=$i 
								where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level and RrPartGroup=$i and RrPartDestItem=$j");
					}
				}
			}
			$QualifiedPerGroup=floor(($PREV->RrLevGroups*$PREV->RrLevGroupArchers)/($LEVEL->RrLevGroups*$LEVEL->RrLevGroupArchers));

		}
		$JSON['rows']=[];
		$q=safe_r_SQL("select * from RoundRobinParticipants where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartLevel=$Level");
		while($r=safe_fetch($q)) {
			$JSON['rows'][]=[
				'g' => $r->RrPartGroup,
				'i' => $r->RrPartDestItem,
				'k' => ($r->RrPartSourceRank==0 ? 0 : "{$r->RrPartSourceLevel}-{$r->RrPartSourceGroup}"),
				'v' => $r->RrPartSourceRank
			];
		}
		$JSON['error']=0;
		break;
	case 'checkDuplicates':
		// no same sourcelevel+sourcegroup+sourcerank can exist (apart rank=0)
		$q=safe_r_sql("select group_concat(concat_ws('-', RrPartLevel,RrPartGroup,RrPartDestItem) separator '|') as RrItems, count(*) as RrNumber from RoundRobinParticipants
			where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event)." and RrPartSourceRank>0
			group by RrPartSourceLevel, RrPartSourceGroup, RrPartSourceRank
			having RrNumber>1");
		$JSON['list']=[];
		while($r=safe_fetch($q)) {
			foreach(explode('|', $r->RrItems) as $Item) {
				list($l, $g, $i)=explode('-', $Item);
				if($l==$Level) {
					$JSON['list'][]='[ref="'.$g.'"][item="'.$i.'"] [name="srcRank"]';
				}
			}
		}
		break;
	default:
		JsonOut($JSON);
}

$JSON['error']=0;
$JSON['team']=$Team;
$JSON['event']=$Event;
$JSON['level']=($IsBracket ? 'B' : max($Level,1));
$JSON['cmdSave']=get_text('CmdSave');

JsonOut($JSON);
