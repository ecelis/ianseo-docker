<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
checkACL(AclCompetition, AclReadWrite,false);
CheckTourSession(true);

require_once('./LibScheduler.php');

if(empty($_REQUEST['Fld'])) jsonout(array('error'=>1));

$Field=key($_REQUEST['Fld']);

switch($Field) {
	case 'Q':
	case 'E':
	case 'I':
	case 'T':
	case 'Z':
	case 'RA':
		$function="Do{$Field}Schedule";
		$function(current($_REQUEST['Fld']));
}

// always outputs error...
jsonout(array('error'=>1));

function DoRASchedule($Item) {
	$ret=['error'=>1];
	foreach($Item as $Type => $k1) {
		foreach($k1 as $Events => $k2) {
			$EvCodes=[];
			foreach(explode(',', $Events) as $e) {
				list($Team, $Ev)=explode('-', $e,2);
				$EvCodes[]="($Team, ".StrSafe_DB($Ev).")";
			}
			$q=safe_r_SQL("select EvCode, EvElimType from Events where EvTournament={$_SESSION['TourId']} and (EvTeamEvent, EvCode) in (".implode(',', $EvCodes).")");
			if(!safe_num_rows($q)) {
				JsonOut($ret);
			}
			foreach($k2 as $Startlist => $Value) {
				if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $Startlist)) {
					JsonOut($ret);
				}
				switch($Type) {
					case 'Duration':
						$Field='RarDuration';
						$Val=intval($Value);
						break;
					case 'Shift':
						$Field='RarShift';
						$Val=intval($Value);
						break;
					case 'WarmtimeDuration':
						$Field='RarWarmupDuration';
						$Val=intval($Value);
						break;
					case 'Calltime':
						$Time=0;
						if($Value){
							if($Value[0]=='-') {
								// minutes before startlist!
								$Time=substr($Startlist,-8,2)*3600+substr($Startlist,-5,2)*60+substr($Startlist,-2);
								$Time+=($Value*60);
							} else {
								if(preg_match('/^[0-9]{1,2}:[0-9]{1,2}$/', $Value)) {
									list($h, $m)=explode(':', $Value);
									$Time=$h*3600+$m*60;
								} else {
									$Time=0;
								}
							}
						}
						$Field='RarCallTime';
						$Val=StrSafe_DB(sprintf("%02d:%02d:%02d", intval($Time/3600), intval(($Time%3600)/60), intval($Time%60)));
						break;
					case 'Warmtime':
						$Time=0;
						if($Value){
							if($Value[0]=='-') {
								// minutes before startlist!
								$Time=substr($Startlist,-8,2)*3600+substr($Startlist,-5,2)*60+substr($Startlist,-2);
								$Time+=($Value*60);
							} else {
								if(preg_match('/^[0-9]{1,2}:[0-9]{1,2}$/', $Value)) {
									list($h, $m)=explode(':', $Value);
									$Time=$h*3600+$m*60;
								} else {
									$Time=0;
								}
							}
						}
						$Field='RarWarmup';
						$Val=StrSafe_DB(sprintf("%02d:%02d:%02d", intval($Time/3600), intval(($Time%3600)/60), intval($Time%60)));
						break;
					case 'Options':
						$Field='RarNotes';
						$Val=StrSafe_DB($Value);
						break;
					default:
						$ret['error']=1;
						JsonOut($ret);
				}
				$q=safe_r_SQL("select RarTeam, RarEvent, RarPhase, RarPool, RarGroup, EvElimType
					from RunArcheryRank
					inner join Events on EvTournament=RarTournament and EvTeamEvent=RarTeam and EvCode=RarEvent
					where RarTournament={$_SESSION['TourId']} and RarStartlist='$Startlist' and (RarTeam, RarEvent) in (".implode(',', $EvCodes).")");
				while($RAR=safe_fetch($q)) {
					$Filter="RarTournament={$_SESSION['TourId']} and RarTeam=$RAR->RarTeam and RarEvent='$RAR->RarEvent' and RarGroup=$RAR->RarGroup and RarPhase=$RAR->RarPhase and RarPool=$RAR->RarPool";
					if($RAR->RarPhase>0 or $RAR->EvElimType==0) {
						$Filter.=" and RarStartlist='$Startlist'";
					}
					safe_w_sql("update RunArcheryRank set $Field=$Val where $Filter");
				}
				$SQL="select
					date(RarStartlist) DiDay, time(min(RarStartlist)) DiStart,
					RarDuration DiDuration,
					RarCallTime DiCallStart,
					RarWarmup DiWarmStart,
					RarWarmupDuration DiWarmDuration,
					RarNotes DiOptions,
					RarShift as  DiShift
				from RunArcheryRank
				inner join Events on EvTournament=RarTournament and EvTeamEvent=RarTeam and EvCode=RarEvent
				where RarTournament={$_SESSION['TourId']} and RarStartlist='$Startlist'
				group by if(EvElimType=0 or RarPhase>0, RarStartlist, EvCode), RarPhase, RarPool, RarGroup
					";
				$q=safe_r_sql($SQL);
				$ret=DistanceInfoData(safe_fetch($q));
			}
		}
	}
	jsonout($ret);
}

function DoESchedule($Item) {
	DoQSchedule($Item, $Type='E');
}

function DoQSchedule($Item, $Type='Q') {
	$Field=key($Item);
	switch($Field) {
		case 'Day':
			$ret=InsertSchedDate(current($Item), $Type);
			break;
		case 'WarmTime':
			$ret=InsertSchedTime(current($Item), 'Warm', $Type);
			break;
		case 'WarmDuration':
			$ret=InsertSchedDuration(current($Item), 'Warm', $Type);
			break;
		case 'Start':
			$ret=InsertSchedTime(current($Item), '', $Type);
			break;
		case 'Duration':
			$ret=InsertSchedDuration(current($Item), '', $Type);
			break;
		case 'Shift':
			$ret=InsertSchedShift(current($Item), $Type);
			break;
		case 'Options':
		case 'Targets':
			$ret=InsertSchedComment(current($Item), $Type, $Field);
			break;
		default:
// 			debug_svela($Field);
	}
	jsonout($ret);
}

function DoTSchedule($Item) {
	DoISchedule($Item, '1');
}

function DoISchedule($Item, $Team='0') {
	$Field=key($Item);
	switch($Field) {
		case 'Day':
			$ret=ChangeFinSchedDate(current($Item), $Team);
			break;
		case 'Start':
			$ret=ChangeFinSchedTime(current($Item), $Team);
			break;
		case 'Duration':
			$ret=ChangeFinSchedDuration(current($Item), $Team);
			break;
		case 'Shift':
			$ret=ChangeFinShift(current($Item), $Team);
			break;
		case 'WarmTime':
			$ret=ChangeFinSchedWarmTime(current($Item), $Team);
			break;
		case 'WarmDuration':
			$ret=ChangeFinSchedWarmDuration(current($Item), $Team);
			break;
		case 'Options':
			$ret=ChangeFinComment(current($Item), $Team);
			break;
		default:
// 			debug_svela($Field);
	}
	jsonout($ret);
}

function DoZSchedule($Item) {
	$Field=key($Item);
	switch($Field) {
		case 'Day':
			$ret=InsertTextDate(current($Item));
			break;
		case 'Start':
			$ret=InsertTextTime(current($Item));
			break;
		case 'Order':
			$ret=InsertTextDuration(current($Item), true);
			break;
		case 'Duration':
			$ret=InsertTextDuration(current($Item));
			break;
		case 'Shift':
			$ret=InsertTextShift(current($Item));
			break;
		case 'Title':
		case 'SubTitle':
		case 'Text':
		case 'Targets':
		case 'Location':
			$ret=InsertText(current($Item), $Field);
			break;
		default:
// 			debug_svela($Field);
	}
	JsonOut($ret);
}
