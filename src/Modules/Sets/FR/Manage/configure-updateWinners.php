<?php

require_once(dirname(__FILE__) . '/config.php');

$JSON=array('error'=>1, 'reload'=> 0);

if(($_REQUEST['item']??'')!='ALLCLUBS' and (!isset($_REQUEST['pos']) or !isset($_REQUEST['cat']) or empty($_REQUEST['item']))) {
	JsonOut($JSON);
}

switch($_REQUEST['item']) {
	case 'ALLONE':
		setModuleParameter('FFTA', 'D1AllInOne', intval($_REQUEST['club']));
		if($_REQUEST['club']) {
			// all matches are made only with teams, so no bonus and no individual, FCO=6 teams...
			setModuleParameter('FFTA', 'DefaultMatchIndividual', 0);
			$Winners=getModuleParameter('FFTA', 'D1Winners');
			$ClubsToRemove=array();
			// removes from the table
			if(!empty($Winners['FCO']['7'])) {
				$ClubsToRemove[]=$Winners['FCO']['7'];
			}
			if(!empty($Winners['FCO']['8'])) {
				$ClubsToRemove[]=$Winners['FCO']['8'];
			}
			$Modules=array();
			foreach(array('DefaultMatchIndividual', 'DefaultMatchTeam', 'D1Bonus', 'D1Winners', 'D1AllInOne') as $type) {
				$tmp=getModuleParameter('FFTA', $type);
				// reset bonus...
				if($type=='D1Bonus') {
					foreach($tmp as $k1 => &$v1) {
						foreach($v1 as $k2 => &$v2) {
							$v2=0;
						}
					}
				}
				if(isset($tmp['FCO']) and is_array($tmp['FCO'])) {
					$tmp['FCO']=array_slice($tmp['FCO'],0,6, true);
				}
				$Modules[$type]=$tmp;
			}

			require_once('Qualification/Fun_Qualification.local.inc.php');
			if($Connected=getModuleParameter('FFTA', 'ConnectedCompetitions')) {
				foreach($Connected as $tmp) {
					if($CompId=getIdFromCode($tmp)) {
						// reset to 6 the numbers of FCO
						safe_w_sql("update Events set EvNumQualified=6 where EvTournament=$CompId and EvTeamEvent=1 and EvCode='FCO'");
						// removes all the individual events
						safe_w_sql("delete from Finals where FinTournament=$CompId");
						safe_w_sql("delete from Events where EvTeamEvent=0 and EvTournament=$CompId");
						safe_w_sql("delete from FinSchedule where FsTeamEvent=0 and FsTournament=$CompId");
						safe_w_sql("update Qualifications inner join Entries on EnId=QuId and EnTournament=$CompId set QuHits=1");
						if($ClubsToRemove) {
							safe_w_sql("delete from TeamDavis where TeDaTournament=$CompId and TeDaEvent='FCO' and TeDaTeam in ('".implode("','", $ClubsToRemove)."')");
						}
						foreach(array('DefaultMatchIndividual', 'DefaultMatchTeam', 'D1Bonus', 'D1Winners', 'D1AllInOne') as $type) {
							setModuleParameter('FFTA', $type, $Modules[$type], $CompId);
						}
						// recreates the teams
						MakeTeams(NULL, NULL, $CompId);
						MakeTeamsAbs(null,null,null, $CompId);
					}
				}
			} else {
				// reset to 6 the numbers of FCO
				safe_w_sql("update Events set EvNumQualified=6 where EvTournament={$_SESSION['TourId']} and EvTeamEvent=1 and EvCode='FCO'");
				// removes all the individual events
				safe_w_sql("delete from Finals where FinTournament={$_SESSION['TourId']}");
				safe_w_sql("delete from Events where EvTeamEvent=0 and EvTournament={$_SESSION['TourId']}");
				safe_w_sql("delete from FinSchedule where FsTeamEvent=0 and FsTournament={$_SESSION['TourId']}");
				if($ClubsToRemove) {
					safe_w_sql("delete from TeamDavis where TeDaTournament={$_SESSION['TourId']} and TeDaEvent='FCO' and TeDaTeam in ('".implode("','", $ClubsToRemove)."')");
				}
				// recreates the teams
				MakeTeams();
				MakeTeamsAbs();
			}
			// sets all qual arrows to be shot

			set_qual_session_flags();
		}
		$JSON['reload']=1;
		$JSON['error']=0;
		break;
	case 'DEFIND':
		setModuleParameter('FFTA', 'DefaultMatchIndividual', intval($_REQUEST['club']));
		$JSON['error']=0;
		break;
	case 'DEFTEAM':
		setModuleParameter('FFTA', 'DefaultMatchTeam', intval($_REQUEST['club']));
		$JSON['error']=0;
		break;
	case 'BONUS':
		$Bonus=getModuleParameter('FFTA', 'D1Bonus');

		if(isset($Bonus[$_REQUEST['cat']][$_REQUEST['pos']])) {
			$Bonus[$_REQUEST['cat']][$_REQUEST['pos']]=intval($_REQUEST['club']);
			setModuleParameter('FFTA', 'D1Bonus', $Bonus);

			$JSON['error']=0;
		}
		break;
	case 'ALLCLUBS':
		$teams=preg_split('/[^0-9a-z]+/im', $_REQUEST['club']??'');
		$cat=($_REQUEST['cat']??'');
		if(!$cat or !$teams) {
			JsonOut($JSON);
		}
		$pos=1;
		foreach($teams as $team) {
			if(!$team) {
				continue;
			}

			if($JSON['msg']=assignFrTeam($team, $cat, $pos)) {
				JsonOut($JSON);
			}
			$JSON['ret'][]=['cat'=>$cat,'pos'=>$pos,'team'=>$team];
			$pos++;
		}
		$JSON['error']=0;
		break;
	case 'CLUB':
		if($JSON['msg']=assignFrTeam($_REQUEST['club'], $_REQUEST['cat'], intval($_REQUEST['pos']??0))) {
			JsonOut($JSON);
		}
		$JSON['error']=0;
		break;
	case 'CONNECTED':
		$Comps=array();
		foreach(preg_split('/[ ,;+]+/', $_REQUEST['club']) as $c) {
			if($c=trim($c)) {
				$Comps[]=$c;
			}
		}

		setModuleParameter('FFTA', 'ConnectedCompetitions', $Comps);
		$JSON['error']=0;

		// check if we have some more defaults already set!
		if($Comps) {
			// check if this parameter is already present in the first competition set
			$CompId=getIdFromCode($Comps[0]);
			foreach(array('DefaultMatchIndividual', 'DefaultMatchTeam', 'D1Bonus', 'D1Winners', 'D1AllInOne') as $type) {
				if($valOld=getModuleParameter('FFTA', $type, '', $CompId)) {
					setModuleParameter('FFTA', $type, $valOld);
					$JSON['reload']=1;
				}
			}
		}

		break;
	case 'TOURDATE':
		$pos=($_REQUEST['pos']??'');
		$cat=($_REQUEST['cat']??'');
		$val=($_REQUEST['club']??'');
		if(!$pos or !$cat) {
			JsonOut($JSON);
		}
		switch($cat) {
			case 'date':
				require_once('Common/Lib/Fun_DateTime.inc.php');
				$val=CleanDate($val);
				break;
			case 'time':
				if($val) {
					$i=explode(':',$val);
					if(count($i)<2) {
						$val='';
					} else {
						$val=sprintf("%02d:%02d", intval($i[0]), intval($i[1]));
					}
				}
				break;
		}
		$TourDates=getModuleParameter('FFTA', 'D1TourDates', ['D1' => [], 'D2' => [], 'D3' => []]);
		$TourDates['D'.$pos][$cat]=$val;
		setModuleParameter('FFTA', 'D1TourDates', $TourDates);
		// update the dates of the matches
		if(($TourDates['D'.$pos]['date']??'') and ($TourDates['D'.$pos]['time']??'')) {
			$StartingDatetime=$TourDates['D'.$pos]['date'].' '.$TourDates['D'.$pos]['time'];
			$Duration=getModuleParameter('FFTA', 'DefaultMatchTeam', 30);
			switch($pos) {
				case '1': // rounds 1-7 for everybody
					for($i=1;$i<=7;$i++) {
						$items=explode(' ', $StartingDatetime);
						safe_w_sql("update RoundRobinMatches set RrMatchScheduledDate='{$items[0]}', RrMatchScheduledTime='$items[1]', RrMatchScheduledLength=$Duration
                         	where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=1 and RrMatchLevel=1 and RrMatchGroup=1 and RrMatchRound=$i");
						$StartingDatetime=date('Y-m-d H:i:s', strtotime($StartingDatetime." +{$Duration} minutes"));
					}
					break;
				case '2': // rounds 8-15 for FCL, HCL, HCO; rounds 1-7 of level 2 for FCO
					for($i=8;$i<=15;$i++) {
						$items=explode(' ', $StartingDatetime);
						safe_w_sql("update RoundRobinMatches set RrMatchScheduledDate='{$items[0]}', RrMatchScheduledTime='$items[1]', RrMatchScheduledLength=$Duration
                         	where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent!='FCO' and RrMatchTeam=1 and RrMatchLevel=1 and RrMatchGroup=1 and RrMatchRound=$i");
						$StartingDatetime=date('Y-m-d H:i:s', strtotime($StartingDatetime." +{$Duration} minutes"));
					}
					$StartingDatetime=$TourDates['D'.$pos]['date'].' '.$TourDates['D'.$pos]['time'];
					for($i=1;$i<=7;$i++) {
						$items=explode(' ', $StartingDatetime);
						safe_w_sql("update RoundRobinMatches set RrMatchScheduledDate='{$items[0]}', RrMatchScheduledTime='$items[1]', RrMatchScheduledLength=$Duration
                         	where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='FCO' and RrMatchTeam=1 and RrMatchLevel=2 and RrMatchGroup=1 and RrMatchRound=$i");
						$StartingDatetime=date('Y-m-d H:i:s', strtotime($StartingDatetime." +{$Duration} minutes"));
					}
					break;
				case '3': // rounds 1-15 for FCL, HCL, HCO; rounds 1-7 of level 3 and 4 for FCO
					for($i=1;$i<=12;$i++) {
						$items=explode(' ', $StartingDatetime);
						safe_w_sql("update RoundRobinMatches set RrMatchScheduledDate='{$items[0]}', RrMatchScheduledTime='$items[1]', RrMatchScheduledLength=$Duration
                         	where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent!='FCO' and RrMatchTeam=1 and RrMatchLevel=2 and RrMatchGroup=1 and RrMatchRound=$i");
						$StartingDatetime=date('Y-m-d H:i:s', strtotime($StartingDatetime." +{$Duration} minutes"));
					}
					$StartingDatetime=$TourDates['D'.$pos]['date'].' '.$TourDates['D'.$pos]['time'];
					for($i=1;$i<=7;$i++) {
						$items=explode(' ', $StartingDatetime);
						safe_w_sql("update RoundRobinMatches set RrMatchScheduledDate='{$items[0]}', RrMatchScheduledTime='$items[1]', RrMatchScheduledLength=$Duration
                         	where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='FCO' and RrMatchTeam=1 and RrMatchLevel=3 and RrMatchGroup=1 and RrMatchRound=$i");
						$StartingDatetime=date('Y-m-d H:i:s', strtotime($StartingDatetime." +{$Duration} minutes"));
					}
					for($i=1;$i<=5;$i++) {
						$items=explode(' ', $StartingDatetime);
						safe_w_sql("update RoundRobinMatches set RrMatchScheduledDate='{$items[0]}', RrMatchScheduledTime='$items[1]', RrMatchScheduledLength=$Duration
                         	where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='FCO' and RrMatchTeam=1 and RrMatchLevel=4 and RrMatchGroup=1 and RrMatchRound=$i");
						$StartingDatetime=date('Y-m-d H:i:s', strtotime($StartingDatetime." +{$Duration} minutes"));
					}
					$StartingDatetime=date('Y-m-d H:i:s', strtotime($TourDates['D'.$pos]['date'].' '.$TourDates['D'.$pos]['time']." +23 hours +30 minutes"));
					for($i=13;$i<=15;$i++) {
						$items=explode(' ', $StartingDatetime);
						safe_w_sql("update RoundRobinMatches set RrMatchScheduledDate='{$items[0]}', RrMatchScheduledTime='$items[1]', RrMatchScheduledLength=$Duration
                         	where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent!='FCO' and RrMatchTeam=1 and RrMatchLevel=2 and RrMatchGroup=1 and RrMatchRound=$i");
						$StartingDatetime=date('Y-m-d H:i:s', strtotime($StartingDatetime." +{$Duration} minutes"));
					}
					$StartingDatetime=date('Y-m-d H:i:s', strtotime($TourDates['D'.$pos]['date'].' '.$TourDates['D'.$pos]['time']." +23 hours +30 minutes"));
					for($i=6;$i<=7;$i++) {
						$items=explode(' ', $StartingDatetime);
						safe_w_sql("update RoundRobinMatches set RrMatchScheduledDate='{$items[0]}', RrMatchScheduledTime='$items[1]', RrMatchScheduledLength=$Duration
                         	where RrMatchTournament={$_SESSION['TourId']} and RrMatchEvent='FCO' and RrMatchTeam=1 and RrMatchLevel=4 and RrMatchGroup=1 and RrMatchRound=$i");
						$StartingDatetime=date('Y-m-d H:i:s', strtotime($StartingDatetime." +{$Duration} minutes"));
					}
					break;
			}
		}
		$JSON['error']=0;
		break;
}

JsonOut($JSON);

function assignFrTeam($team, $cat, $pos) {
	$Winners=getModuleParameter('FFTA', 'D1Winners');
	if(isset($Winners[$cat]["$pos"])) {
		$Winners[$cat]["$pos"]=$team;
		setModuleParameter('FFTA', 'D1Winners', $Winners);

		$q=safe_r_sql("select distinct CoId, LueCountry, LueCoDescr, LueCoShort from LookUpEntries left join Countries on CoCode=LueCountry and CoTournament={$_SESSION['TourId']} where LueIocCode like 'FRA%' and LueCountry=".StrSafe_DB($team));
		if($r=safe_fetch($q)) {
			if(!$r->CoId) {
				safe_w_sql("insert into Countries set CoCode=".StrSafe_DB($r->LueCountry).", CoName=".StrSafe_DB($r->LueCoShort).", CoNameComplete=".StrSafe_DB($r->LueCoDescr).", CoTournament={$_SESSION['TourId']}");
				$r->CoId=safe_w_last_id();
			}
			safe_w_sql("delete from Teams 
       				where TeTournament={$_SESSION['TourId']}
					and TeFinEvent=1
					and TeRank=$pos
					and TeEvent=".StrSafe_DB($cat));
			safe_w_sql("insert into Teams set
					TeCoId=$r->CoId,
					TeTournament={$_SESSION['TourId']},
					TeFinEvent=1,
					TeHits=1,
					TeRank=$pos,
					TeEvent=".StrSafe_DB($cat)."
					on duplicate key update TeCoId=$r->CoId,
					TeTournament={$_SESSION['TourId']},
					TeFinEvent=1,
					TeHits=1,
					TeRank=$pos,
					TeEvent=".StrSafe_DB($cat)."");

			if($_SESSION['TourLocSubRule']=='SetFRD12023') {
				// insert this club in level 1!
				safe_w_sql("update RoundRobinParticipants set RrPartParticipant=$r->CoId 
                		where RrPartTournament={$_SESSION['TourId']} 
                			and RrPartTeam=1 
                		  	and RrPartEvent=".StrSafe_DB($cat)."
                		  	and RrPartLevel=1
                		  	and RrPartGroup=1
                		  	and RrPartSourceRank=$pos");
				// insert into the matches of level 1!
				safe_w_sql("Update RoundRobinMatches
						inner join RoundRobinGrids on RrGridTournament=RrMatchTournament
							and RrGridTeam=RrMatchTeam
							and RrGridEvent=RrMatchEvent
							and RrGridLevel=RrMatchLevel
							and RrGridGroup=RrMatchGroup
							and RrGridRound=RrMatchRound
							and RrGridMatchno=RrMatchMatchno
							and RrGridItem=$pos
						set RrMatchAthlete=$r->CoId, RrMatchSubTeam=0
						where RrMatchTournament={$_SESSION['TourId']} 
							and RrMatchTeam=1 
							and RrMatchEvent=".StrSafe_DB($cat)."
						    and RrMatchLevel=1
							and RrMatchGroup=1
							 ");
			}

			// check if all teams are there
			$q=safe_r_sql("select * from RoundRobinParticipants 
                    where RrPartTournament={$_SESSION['TourId']} 
                        and RrPartParticipant=0
                        and RrPartTeam=1 
                        and RrPartEvent=".StrSafe_DB($cat)."
                        and RrPartLevel=1
                        and RrPartGroup=1");
			if(safe_num_rows($q)==0) {
				safe_w_sql("update Events set EvE1ShootOff=1 where EvCode=".StrSafe_DB($cat)." and EvTournament={$_SESSION['TourId']} and EvTeamEvent=1");
				set_qual_session_flags();
			}
			return 0;
		}
	}
	return get_text('ClubNotFound', 'Errors');
}