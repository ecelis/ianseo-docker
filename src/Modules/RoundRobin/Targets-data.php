<?php

$JSON=array('error'=>1, 'msg'=>'Wrong Data');

require_once(dirname(dirname(__DIR__)) . '/config.php');

if(!CheckTourSession() or !hasACL(AclCompetition, AclReadWrite) or empty($_REQUEST['act']) or IsBlocked(BIT_BLOCK_ROBIN)) {
	JsonOut($JSON);
}

require_once('Common/Lib/CommonLib.php');
require_once('./Lib.php');

$Team=intval($_REQUEST['Team'] ?? 0);
$Event=($_REQUEST['Event'] ?? '');
if(($_REQUEST['Level'] ?? 1)==='') {
    $Level=1;
} else {
    $Level=intval($_REQUEST['Level']);
}
$Group=intval($_REQUEST['Group'] ?? 0);
$Round=intval($_REQUEST['Round'] ?? 0);
$Matchno=intval($_REQUEST['Matchno'] ?? -1);
$TotLevels=0;
// filters!
$EvGroup=intval($_REQUEST['EvGroup'] ?? 0);
$EvRound=intval($_REQUEST['EvRound'] ?? 0);


// Check the event exists
if($Event) {
	$q=safe_r_sql("select EvCode, EvEventName, EvElim1, EvFinalFirstPhase, EvNumQualified from Events
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
		// if no events, dies
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
        $JSON['levels'][]=array(
            'val' => 0,
            'text' => 'all',
            'disabled' => 0,
        );

		$JSON['dateTimes']=getRrMatchDateTimes($Team);

		$JSON['headers']=array(
			'mItem' => get_text('Item', 'RoundRobin'),
			'mTarget' => get_text('Target'),
			'mSchedule' => get_text('MatchDateTime', 'Tournament'),
			'mDate' => get_text('Date', 'Tournament'),
			'mTime' => get_text('TimeAt', 'Tournament'),
			'mLength' => get_text('Length', 'Tournament'),
            'mMatTgt' => get_text('Match4Target', 'Tournament'),
		);

		$JSON['setAll']=get_text('ToAll');
		$JSON['groups']=array();

		$q=safe_r_sql("select RrMatchGroup, RrMatchRound, RrMatchMatchNo, RrMatchTarget, RrGridItem, RrGrTargetArchers, RrGrArcherWaves, 
            RrLevGroups, RrLevGroupArchers, RrMatchLevel, RrLevName, RrMatchScheduledDate as MatchDate, date_format(RrMatchScheduledTime, '%H:%i') as MatchTime, RrMatchScheduledLength as MatchLength, RrGrName, 
       		coalesce(concat(upper(EnFirstName), ' ', EnName), concat(CoCode, '-', CoName, if(TeSubTeam>0, concat(' (', TeSubTeam, ')'), '')), '') as PartName 
			from RoundRobinMatches
            inner join RoundRobinGroup on RrGrTournament=RrMatchTournament and RrGrTeam=RrMatchTeam and RrGrEvent=RrMatchEvent and RrGrLevel=RrMatchLevel and RrGrGroup=RrMatchGroup
            inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel
            inner join RoundRobinGrids on RrGridTournament=RrMatchTournament and RrGridTeam=RrMatchTeam and RrGridEvent=RrMatchEvent and RrGridLevel=RrMatchLevel and RrGridGroup=RrMatchGroup and RrGridRound=RrMatchRound and RrGridMatchno=RrMatchMatchNo
			left join Entries on EnId=RrMatchAthlete and RrMatchTeam=0
			left join Teams on TeCoId=RrMatchAthlete and RrMatchTeam=1 and TeSubTeam=RrMatchSubTeam and TeFinEvent=1 and TeEvent=RrMatchEvent and TeTournament=RrMatchTournament
			left join Countries on CoId=TeCoId and CoTournament=RrMatchTournament
			where ".($Level=='0' ? '': "RrMatchLevel=$Level and")." RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and  RrMatchTournament={$_SESSION['TourId']} ".($EvGroup ? "and RrMatchGroup=$EvGroup" : "")." ".($EvRound ? "and RrMatchRound=$EvRound" : "")."
			order by RrMatchGroup, RrMatchRound, RrMatchMatchNo");

		$JSON['items']=0;
		$JSON['rounds']=[];

		while($r=safe_fetch($q)) {
			if(!$JSON['items']) {
				$JSON['items']=(int) $r->RrLevGroupArchers;
				for($n=1;$n<=ceil($r->RrLevGroupArchers/2)*2 - 1;$n++) {
					$JSON['rounds'][]=['val' => $n, 'txt' => get_text('Round#', 'Tournament', $n)];
				}
			}

            $lv='l'.str_pad($r->RrMatchLevel,3, '0', STR_PAD_LEFT);
			$gr='g'.str_pad($r->RrMatchGroup,3, '0', STR_PAD_LEFT);
			$ro='r'.str_pad($r->RrMatchRound,3, '0', STR_PAD_LEFT);
			if(empty($JSON['groups'][$lv])) {
				$JSON['groups'][$lv]=[
					'lId'=>$r->RrMatchLevel,
					'lName'=>($r->RrLevName ?: get_text('LevelNum', 'RoundRobin', $r->RrMatchLevel)),
					'lA4T'=>$r->RrGrTargetArchers,
					'lM4T'=>$r->RrGrArcherWaves,
					'groups'=>[],
				];

			}
			if(empty($JSON['groups'][$lv]['groups'][$gr])) {
				$JSON['groups'][$lv]['groups'][$gr]=[
					'gId'=>$r->RrMatchGroup,
					'gName'=>($r->RrGrName ?: get_text('GroupNum', 'RoundRobin', $r->RrMatchGroup)),
					'gA4T'=>$r->RrGrTargetArchers,
					'gM4T'=>$r->RrGrArcherWaves,
					'gRounds'=>[],
				];

			}
			if(empty($JSON['groups'][$lv]['groups'][$gr]['gRounds'][$ro])) {
				$JSON['groups'][$lv]['groups'][$gr]['gRounds'][$ro]=[
					'rId'=>$r->RrMatchRound,
					'rName'=>get_text('Round#', 'Tournament', $r->RrMatchRound),
					'rComponents'=>[],
				];

			}
			$JSON['groups'][$lv]['groups'][$gr]['gRounds'][$ro]['rComponents'][]=[
				'mMatchno' => $r->RrMatchMatchNo,
				'mItem' => $r->RrGridItem,
				'mDate' => ($r->MatchDate=='0000-00-00' ? '' : $r->MatchDate),
				'mTime' => ($r->MatchTime=='00:00' ? '' : $r->MatchTime),
				'mLength' => ($r->MatchLength== 0 ? '' : $r->MatchLength),
				'mTarget' => $r->RrMatchTarget,
				'mName' => $r->PartName,
				'mIsBye' => $r->RrGridItem>$r->RrLevGroupArchers,
			];
		}

		// $JSON['groups']=array_values($JSON['groups']);
		break;
	case 'update':
		if(!$Event or !$Level or !$Group or !$Round or $Matchno==-1) {
			JsonOut($JSON);
		}
		if(isset($_REQUEST['tgt'])) {
			// first check the waves/archers on target
			$q=safe_r_sql("select RrGrTargetArchers, RrGrArcherWaves from RoundRobinGroup where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($Event)." and RrGrLevel=$Level and RrGrGroup=$Group");
			$r=safe_fetch($q);
			if(!$q) {
				JsonOut($JSON);
			}

			require_once('./Lib.php');

			if($JSON['targets']=SetRoundRobinTarget($Event, $Team, $Level, $Group, $Round, $Matchno, $_REQUEST['tgt'], ($_REQUEST['multi']??''))) {
				$JSON['error']=0;
			}

		} elseif(isset($_REQUEST['date']) and isset($_REQUEST['time']) and isset($_REQUEST['length'])) {
			require_once('Common/Lib/Fun_DateTime.inc.php');
			$Matchno=intval($Matchno/2)*2;
			$Matchnos=$Matchno.','.($Matchno+1);

			$Date='0';
			if($tmp=strtolower($_REQUEST['date'])) {
				if($tmp[0]=='d') {
					$Date=date('Y-m-d', strtotime(sprintf('%+d days', substr($tmp, 1) -1), $_SESSION['ToWhenFromUTS']));
				} else {
					$Date=CleanDate($tmp);
				}
			}

			$Time='0';
			$tmp=strtolower($_REQUEST['time']);
            $Length=intval($_REQUEST['length']);

            if($tmp) {
                if(($tmp[0]=='+' or $tmp[0]=='-')) {
                    // it is relative to the time from the previous, so round must be > 1
                    $Minutes=intval(substr($tmp,1));
                    if($Date and $Date!='0000-00-00') {
                        $SQL=($tmp[0]=='+' ? 'date_add' : 'date_sub')."(concat('$Date ', RrMatchScheduledTime), INTERVAL $Minutes MINUTE)";
                    } else {
                        $SQL=($tmp[0]=='+' ? 'date_add' : 'date_sub')."(concat(RrMatchScheduledDate, ' ', RrMatchScheduledTime), INTERVAL $Minutes MINUTE)";
                    }

                    if($Round==1 and $Level>1) {
                        // only 1 round so it travels throught the levels!!!
                        $q=safe_r_sql("select date_format($SQL, '%Y-%m-%d %H:%i:%s') as NewDateTime, RrMatchScheduledLength from RoundRobinMatches 
					        where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=".($Level-1)." and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno");
                    } elseif($Round>1) {
                        $q=safe_r_sql("select date_format($SQL, '%Y-%m-%d %H:%i:%s') as NewDateTime, RrMatchScheduledLength from RoundRobinMatches 
					        where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=".($Round-1)." and RrMatchMatchNo=$Matchno");
                    } else {
                        JsonOut($JSON);
                    }
                    if($r=safe_fetch($q)) {
                        $Date=substr($r->NewDateTime, 0, 10);
                        $Time=substr($r->NewDateTime, -8, 5);
                    }
                } elseif(($tmp[0]=='*') and $Level>1) {
                    // it is relative to the time from the previous level (same round and group)
                    $Minutes=intval(substr($tmp,1));
                    if($Date and $Date!='0000-00-00') {
                        $SQL="date_add(concat('$Date ', RrMatchScheduledTime), INTERVAL $Minutes MINUTE)";
                    } else {
                        $SQL="date_add(concat(RrMatchScheduledDate, ' ', RrMatchScheduledTime), INTERVAL $Minutes MINUTE)";
                    }

                    $q=safe_r_sql("select date_format($SQL, '%Y-%m-%d %H:%i:%s') as NewDateTime, RrMatchScheduledLength from RoundRobinMatches 
					where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=".($Level-1)." and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo=$Matchno");
                    if($r=safe_fetch($q)) {
                        $Date=substr($r->NewDateTime, 0, 10);
                        $Time=substr($r->NewDateTime, -8, 5);
                        $Length=$r->RrMatchScheduledLength;
                    }
                } else {
                    if(!preg_match('#^[0-9]{1,2}[^0-9][0-9]{1,2}$#', $tmp)) {
                        JsonOut($JSON);
                    }
                    $Items=preg_split('#[^0-9]+#',$tmp);
                    if(count($Items)!=2) {
                        JsonOut($JSON);
                    }
                    $Items[]='00';
                    $Time=implode(':', $Items);
                }

                safe_w_sql("update RoundRobinMatches set RrMatchScheduledDate='$Date', RrMatchScheduledTime='$Time', RrMatchScheduledLength='$Length'
				where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo in ($Matchnos)");

                $q=safe_r_sql("select RrMatchMatchNo, RrMatchScheduledDate, left(RrMatchScheduledTime,5) as MatchTime, RrMatchScheduledLength from RoundRobinMatches
				where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round and RrMatchMatchNo in ($Matchnos)");
                $JSON['dates']=[];
                while($r=safe_fetch($q)) {
                    $JSON['dates'][]=array('lv' => $Level, 'g'=>$Group, 'r'=>$Round, 'm'=>$r->RrMatchMatchNo, 'd' => $r->RrMatchScheduledDate, 't' => $r->MatchTime, 'l' => $r->RrMatchScheduledLength);
                }
            }
			$JSON['error']=0;
		} else {
			JsonOut($JSON);
		}

		$JSON['dateTimes']=getRrMatchDateTimes($Team);
		break;
	case 'checkDuplicates':
		// no same sourcelevel+sourcegroup+sourcerank can exist (apart rank=0)
		$q=safe_r_sql("select group_concat(concat_ws('-', RrMatchLevel,RrMatchGroup,RrMatchRound,RrMatchMatchNo,RrGrEvent) separator '|') as RrItems, count(*) as RrNumber 
			from RoundRobinMatches
			inner join RoundRobinGroup on RrGrTournament=RrMatchTournament and RrGrTeam=RrMatchTeam and RrgrEvent=RrMatchEvent and RrGrLevel=RrMatchLevel and RrGrGroup=RrMatchGroup
			where RrMatchTournament={$_SESSION['TourId']} and RrMatchScheduledDate!=0 and RrMatchTarget!=''
			group by RrMatchTarget, RrMatchScheduledDate, RrMatchScheduledTime
			having RrNumber>1");
		$JSON['list']=[];
		while($r=safe_fetch($q)) {
			foreach(explode('|', $r->RrItems) as $Item) {
				list($l, $g, $r, $m, $e)=explode('-', $Item);
				if($l==$Level and $e==$Event) {
					// target
					$JSON['list'][]='.Tabella[level="'.$l.'"][group="'.$g.'"][round="'.$r.'"] [matchno="'.$m.'"] [name="tgt"]';
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
$JSON['level']=$Level;
$JSON['evGroup']=$EvGroup;
$JSON['evRound']=$EvRound;
$JSON['cmdSave']=get_text('CmdSave');

JsonOut($JSON);
