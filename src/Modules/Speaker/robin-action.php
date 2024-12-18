<?php
require_once(dirname(__DIR__) . '/config.php');

$JSON=[
	'error'=>1,
	'msg'=>get_text('ErrGenericError', 'Errors'),
];

$act=($_REQUEST['act']??'');
$viewInd = intval($_REQUEST['viewInd']??null);
$viewTeam = intval($_REQUEST['viewTeam']??null);

if(!CheckTourSession() or !hasACL(AclSpeaker, AclReadOnly) or !$act or is_null($viewInd) or is_null($viewTeam)) {
	JsonOut($JSON);
}

switch($act) {
	case 'getSchedule':
        $Today=getToday();
		$OnlyToday='';
		$SelectedEvent="''";
		$JSON['onlytoday']=0;
		$JSON['running']='';
		$JSON['rows']=[];

		if(intval($_REQUEST["onlyToday"]??0)) {
			$OnlyToday="AND RrMatchScheduledDate='$Today'";
			$JSON['onlytoday']=1;
		}

		if($IskSequence=getModuleParameter('ISK', 'Sequence') OR $IskSequence=getModuleParameter('ISK-NG', 'Sequence')) {
			if(!isset($IskSequence['session'])) {
                //  cycle through the sequences and fetches the most recent
                $ToKeep=current($IskSequence);
                $OldSession='';
                foreach($IskSequence as $k => $i) {
                    if($k) {
                        if($i['session']>$OldSession);
                        $ToKeep=$i;
                    }
                    $OldSession=$i['session'];
                }
                $IskSequence=$ToKeep;
            }
			// get the running sequence
			$SelectedEvent="concat(RrMatchScheduledDate,RrMatchScheduledTime) = '{$IskSequence['session']}'";
			$JSON['running']=$IskSequence['session'];
			if(!empty($_REQUEST['reset']) and $OnlyToday and !strstr($IskSequence['session'], $Today)) {
				$JSON['onlytoday']=0;
				$OnlyToday='';
			}
		}

		$Select="select distinct
				RrMatchTeam, 
				concat(RrMatchScheduledDate, ' ',  date_format(RrMatchScheduledTime, '%H:%i')) MyDate,
				group_concat(distinct RrMatchEvent order by RrMatchEvent separator ', ') Events,
				$SelectedEvent as SelectedEvent
			from RoundRobinMatches
		    inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel
			inner join Events on EvCode=RrMatchEvent and EvTeamEvent=RrMatchTeam and EvTournament=RrMatchTournament
			where RrMatchTournament={$_SESSION['TourId']}
				and RrMatchScheduledDate>0
				$OnlyToday
			group by MyDate
			order by MyDate, RrMatchTeam";

		$Schedule=array();
		$Rs=safe_r_sql($Select);
		while ($myRow=safe_fetch($Rs)) {
			$k="{$myRow->RrMatchTeam}{$myRow->MyDate}";
			if(empty($Schedule[$k])) {
				$Schedule[$k]=array(
					'team'=>($myRow->RrMatchTeam ? get_text('Team'):get_text('Individual')),
					'sel'=>($myRow->SelectedEvent ? '1' : '0'),
					'txt'=>array(),
				);
			}
			$Schedule[$k]['txt'][]= $myRow->Events;
		}

		foreach($Schedule as $MyDate => $Items) {
			$JSON['rows'][]=array(
				'val' => $MyDate,
				'txt' => $Items['team'] . ' ' . substr($MyDate,1) . ' - '. implode(' + ', $Items['txt']),
				'sel' => $Items['sel'] ? '1' : '0',
			);
		}

		$JSON['error']=0;
		JsonOut($JSON);
		break;
	case 'getEvents':
		$schedule=(isset($_REQUEST['schedule']) && preg_match('/^[0-1][0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})?$/',$_REQUEST['schedule']) ? $_REQUEST['schedule'] : '');
		if(!$schedule) {
			JsonOut($JSON);
		}

		$JSON['newdata']='';
		$JSON['rows']=[];

		$team=intval($schedule[0]);
		$tmp=explode(' ',substr($schedule,1));
		$date=$tmp[0];
		$time=$tmp[1];

		if($IskSequence=getModuleParameter('ISK', 'Sequence') OR $IskSequence=getModuleParameter('ISK-NG', 'Sequence')) {
            if(!isset($IskSequence['session'])) {
                //  cycle through the sequences and fetches the most recent
                $ToKeep=current($IskSequence);
                $OldSession='';
                foreach($IskSequence as $k => $i) {
                    if($k) {
                        if($i['session']>$OldSession);
                        $ToKeep=$i;
                    }
                    $OldSession=$i['session'];
                }
                $IskSequence=$ToKeep;
            }
			// get the running sequence
			$JSON['newdata']=($IskSequence['session']==$tmp[0].$tmp[1] ? '' : 'newdata');
		}


		$query = "SELECT EvCode AS code, EvEventName as name
			FROM Events
			INNER JOIN RoundRobinMatches ON RrMatchEvent=EvCode 
            	AND RrMatchTeam=EvTeamEvent 
                AND RrMatchTournament=EvTournament
				AND RrMatchScheduledDate=" . StrSafe_DB($date) . " 
				AND RrMatchScheduledTime=" . StrSafe_DB($time) . "
			WHERE EvTournament={$_SESSION['TourId']} 
			  	AND EvTeamEvent=$team
			group by EvCode
			ORDER BY EvProgr";

		$JSON['error']=0;

		$rs=safe_r_sql($query);

		while ($myRow=safe_fetch($rs)) {
			$JSON['rows'][]=array(
				'val' => $myRow->code,
				'txt' => $myRow->name,
				'sel' => '0',
			);
		}
		break;
	case 'getMatches':
		$schedule=(isset($_REQUEST['schedule']) && preg_match('/^[0-1][0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}(:[0-9]{2})?$/',$_REQUEST['schedule']) ? $_REQUEST['schedule'] : '');
		if(!$schedule) {
			JsonOut($JSON);
		}

		$JSON['rows']=[];
		$team=intval($schedule[0]);
		$events=($_REQUEST['events']??[]);
		$serverDate=intval($_REQUEST['serverDate'] ?? 0);
		$parameters=($_REQUEST['parameters'] ?? null);
		$schedule=substr($schedule, 1);
		if(strlen($schedule)<19) $schedule.=':00';
		list($SchDate, $SchTime)= explode(' ', $schedule);

		$query="SELECT UNIX_TIMESTAMP('".date('Y-m-d H:i:s')."') AS serverDate ";
		$rs=safe_r_sql($query);
		$row=safe_fetch($rs);
		$JSON['serverdate']=$row->serverDate;
		if($IskSequence=getModuleParameter('ISK', 'Sequence') OR $IskSequence=getModuleParameter('ISK-NG', 'Sequence')) {
            if(!isset($IskSequence['session'])) {
                //  cycle through the sequences and fetches the most recent
                $ToKeep=current($IskSequence);
                $OldSession='';
                foreach($IskSequence as $k => $i) {
                    if($k) {
                        if($i['session']>$OldSession);
                        $ToKeep=$i;
                    }
                    $OldSession=$i['session'];
                }
                $IskSequence=$ToKeep;
            }
			$tmp=str_replace(' ', '', $schedule);
			// get the running sequence
			$JSON['newdata']=($IskSequence['session']==$tmp ? '' : 'newdata');
		}

		// check if there has been a change from last server time
		$countSql="SELECT RrMatchMatchNo
			FROM RoundRobinMatches
			WHERE RrMatchTournament={$_SESSION['TourId']} 
			  	AND CONCAT(RrMatchScheduledDate,' ',RrMatchScheduledTime)='$schedule'
				AND UNIX_TIMESTAMP(RrMatchDateTime)>$serverDate
				and RrMatchTeam=$team";
		if($events) {
			$countSql.=" and RrMatchEvent in (".implode(StrSafe_DB($events)).")";
		}

		$rs=safe_r_sql($countSql);
		if(safe_num_rows($rs)==0) {
			$JSON['error']=0;
			JsonOut($JSON);
		}

		$otherWhere= " AND f1.RrMatchTeam=$team
			AND (CONCAT(f1.RrMatchScheduledDate,' ',f1.RrMatchScheduledTime)='$schedule' OR CONCAT(f2.RrMatchScheduledDate,' ',f2.RrMatchScheduledTime)='$schedule')";

		if (count($events)>0 && $events[0]!='') {
			//array_walk($events,'safe');
			$otherWhere .= " AND EvCode IN(" . implode(',',StrSafe_DB($events)) . ")";
		}


		$orderBy="order by least(f1.RrMatchTarget,f2.RrMatchTarget) ASC ";


		if (safe_num_rows($rs)>0) {
			// $rs=GetFinMatches_sql($otherWhere,$team,$orderBy,false);
			require_once(__DIR__.'/var.inc.php');
			require_once('Common/Lib/ArrTargets.inc.php');
			$query= " SELECT 
					coalesce(e1.EnFirstName, t1.CoCode, '') familyName1,
					coalesce(e2.EnFirstName, t2.CoCode, '') familyName2,
					'' rank1,
					'' rank2,
					coalesce(
					    if(e1.EnNameOrder, CONCAT(upper(e1.EnFirstName),' ',e1.EnName), CONCAT(e1.EnName,' ',upper(e1.EnFirstName))),
					    CONCAT(t1.CoName, IF(f1.RrMatchSubTeam!='0',CONCAT(' - ',f1.RrMatchSubTeam),''),''),
					    '') AS name1,
					coalesce(
					    if(e2.EnNameOrder, CONCAT(upper(e2.EnFirstName),' ',e2.EnName), CONCAT(e2.EnName,' ',upper(e2.EnFirstName))),
					    CONCAT(t2.CoName, IF(f2.RrMatchSubTeam!='0',CONCAT(' - ',f2.RrMatchSubTeam),''),''),
					    '') AS name2,
     				coalesce(c1.CoCode, t1.CoCode, '') AS countryCode1,
     				coalesce(c2.CoCode, t2.CoCode, '') AS countryCode2,
					coalesce(c1.CoName, CONCAT(t1.CoName, IF(f1.RrMatchSubTeam!='0',CONCAT(' - ',f1.RrMatchSubTeam),''),''), '') AS countryName1,
					coalesce(c2.CoName, CONCAT(t2.CoName, IF(f2.RrMatchSubTeam!='0',CONCAT(' - ',f2.RrMatchSubTeam),''),''), '') AS countryName2,
					RrLevArrows,
					RrLevEnds,
					RrLevSO,
					RrLevMatchMode as matchMode,
					RrLevName,
					RrLevLevel,
					RrGrName,
					RrGrGroup,
					f1.RrMatchRound,
					f1.RrMatchMatchNo AS match1,
					f2.RrMatchMatchNo AS match2,
					f1.RrMatchTarget AS target1,
					f2.RrMatchTarget AS target2,
					f1.RrMatchEvent AS event,
					f1.RrMatchWinLose AS win1,
					f2.RrMatchWinLose AS win2,
					ev1.EvEventName AS eventName,
					ev1.EvTeamEvent AS teamEvent,
					-- ev1.EvMatchArrowsNo AS matchArrowsNo,
					UNIX_TIMESTAMP(greatest(f1.RrMatchDateTime, f2.RrMatchDateTime)) AS lastUpdate, 
					f1.RrMatchScore AS score1,f1.RrMatchSetScore AS setScore1,f1.RrMatchSetPoints AS setPoints1,f1.RrMatchTie AS tie1,f1.RrMatchArrowstring AS arrowString1,f1.RrMatchTiebreak AS tiebreak1,f1.RrMatchTbClosest AS tieclosest1,
					f2.RrMatchScore AS score2,f2.RrMatchSetScore AS setScore2,f2.RrMatchSetPoints AS setPoints2,f2.RrMatchTie AS tie2,f2.RrMatchArrowstring AS arrowString2,f2.RrMatchTiebreak AS tiebreak2,f2.RrMatchTbClosest AS tieclosest2
				FROM RoundRobinMatches AS f1
				INNER JOIN RoundRobinMatches AS f2 ON f2.RrMatchEvent=f1.RrMatchEvent AND f2.RrMatchTeam=f1.RrMatchTeam and f2.RrMatchMatchNo=f1.RrMatchMatchNo+1 AND f2.RrMatchTournament=f1.RrMatchTournament and f2.RrMatchLevel=f1.RrMatchLevel and f2.RrMatchGroup=f1.RrMatchGroup and f2.RrMatchRound=f1.RrMatchRound 
				INNER JOIN RoundRobinGroup ON RrGrEvent=f1.RrMatchEvent AND RrGrTournament=f1.RrMatchTournament and RrGrLevel=f1.RrMatchLevel and RrGrTeam=f1.RrMatchTeam and RrGrGroup=f1.RrMatchGroup
				INNER JOIN RoundRobinLevel ON RrLevEvent=f1.RrMatchEvent AND RrLevTournament=f1.RrMatchTournament and RrLevLevel=f1.RrMatchLevel and RrLevTeam=f1.RrMatchTeam
				INNER JOIN Events AS ev1 ON ev1.EvCode=f1.RrMatchEvent AND ev1.EvTeamEvent=f1.RrMatchTeam AND ev1.EvTournament=f1.RrMatchTournament
				LEFT JOIN Entries AS e1 ON f1.RrMatchAthlete=e1.EnId and f1.RrMatchTeam=0
				LEFT JOIN Countries AS c1 ON e1.EnCountry=c1.CoId and f1.RrMatchTeam=0
				LEFT JOIN Entries AS e2 ON f2.RrMatchAthlete=e2.EnId and f2.RrMatchTeam=0
				LEFT JOIN Countries AS c2 ON e2.EnCountry=c2.CoId and f2.RrMatchTeam=0
				LEFT JOIN Countries AS t1 ON t1.CoId=f1.RrMatchAthlete and f1.RrMatchTeam=1
				LEFT JOIN Countries AS t2 ON t2.CoId=f2.RrMatchAthlete and f2.RrMatchTeam=1
				WHERE
					f1.RrMatchTournament={$_SESSION['TourId']} AND (f1.RrMatchMatchNo % 2)=0
					$otherWhere
				having familyName1!='' or familyName2!=''
				$orderBy
				";

			$rs=safe_r_SQL($query);
			if (safe_num_rows($rs)>0) {
				$points4win=array();
				$arrow4Match=array();
				$max=[];
				$stdArrowShot=[];
				$tieArrowShot=[];

				// get the basic info for this level
				$q=safe_r_sql("select r1.RrMatchEvent, RrLevEnds, RrLevArrows, RrLevSO, RrLevMatchMode, max(r1.RrMatchSetScore+r2.RrMatchSetScore) as MaxSet, 
       					max(greatest(length(trim(r1.RrMatchArrowstring)),length(trim(r2.RrMatchArrowstring)))) as ArShot, 
       					max(greatest(length(trim(r1.RrMatchTiebreak)),length(trim(r2.RrMatchTiebreak)))) as TieShot
					from RoundRobinMatches r1
					inner join RoundRobinMatches r2 on r2.RrMatchTournament=r1.RrMatchTournament and r2.RrMatchTeam=r1.RrMatchTeam and r2.RrMatchEvent=r1.RrMatchEvent and r2.RrMatchLevel=r1.RrMatchLevel and r2.RrMatchGroup=r1.RrMatchGroup and r2.RrMatchRound=r1.RrMatchRound and r2.RrMatchMatchNo=r1.RrMatchMatchNo +1					    
					inner join RoundRobinLevel on RrLevTournament=r1.RrMatchTournament and RrLevTeam=r1.RrMatchTeam and RrLevLevel=r1.RrMatchLevel
					where r1.RrMatchTournament={$_SESSION['TourId']} and r1.RrMatchScheduledDate='$SchDate' and r1.RrMatchScheduledTime='$SchTime' and r1.RrMatchMatchNo%2=0
					group by r1.RrMatchEvent");
				// // primo giro x inizializzare i vettori accessori
				while ($r=safe_fetch($q)) {
					$points4win[$r->RrMatchEvent]=$r->RrLevEnds+1;
					$arrow4Match[$r->RrMatchEvent]=$r->RrLevEnds*$r->RrLevArrows;
					$max[$r->RrMatchEvent]=max($r->MaxSet, $max[$r->RrMatchEvent]??0);

					if($r->RrLevMatchMode==0) {
						$stdArrowShot[$r->RrMatchEvent] = max($r->ArShot, $stdArrowShot[$r->RrMatchEvent]??0);
						$tieArrowShot[$r->RrMatchEvent] = max($r->TieShot, $tieArrowShot[$r->RrMatchEvent]??0);
					}
				}

				$id=0;	// id fittizio
				while ($myRow=safe_fetch($rs)) {
					$target=$myRow->target1;
					$target2='';
					if ($myRow->target2!=$myRow->target1)
						$target2 = $myRow->target2;

					$score1=$myRow->score1;
					$score2=$myRow->score2;

					if ($myRow->matchMode==1) {
						$score1=$myRow->setScore1;
						$score2=$myRow->setScore2;
					}

					$score=$score1 . ' - ' . $score2;
					$setPoints1='';
					$setPoints2='';

					if ($myRow->tie1==2 && $myRow->tie2!=2) {
						$setPoints1=get_text('Bye');
					} elseif ($myRow->tie1!=2 && $myRow->tie2==2) {
						$setPoints2=get_text('Bye');
					} elseif ($myRow->matchMode==1) {
						list($setPoints1,$setPoints2)=purgeSetPoints($myRow->setPoints1,$myRow->setPoints2);
					} else {
						for($cEnd=0; $cEnd<strlen($myRow->arrowString1); $cEnd+=$myRow->RrLevArrows) {
							$setPoints1 = $setPoints1 . ValutaArrowString(substr($myRow->arrowString1,$cEnd,$myRow->RrLevArrows)) . " ";
						}
						for($cEnd=0; $cEnd<strlen($myRow->arrowString2); $cEnd+=$myRow->RrLevArrows) {
							$setPoints2 = $setPoints2 . ValutaArrowString(substr($myRow->arrowString2,$cEnd,$myRow->RrLevArrows)) . " ";
						}
					}

					// le frecce di tiebreak
					for ($index=1;$index<=2;++$index) {
						$arrowstring=$myRow->{'tiebreak'.$index};
						if (trim($arrowstring)!='') {
							//print 'pp';
							$tmp=array();
							for ($i=0;$i<strlen($arrowstring);++$i) {
								$tmp[]=DecodeFromLetter($arrowstring[$i]);
							}
							if($myRow->{'tieclosest'.$index} != 0) {
								$tmp[] = '+';
							}
							${'setPoints'.$index}.=' ' . implode(' ',$tmp);
						}
					}

					/*
					 * 0 => il match no è finito
					 * 1 => il match è finito prima
					 * 2 => il match è finito ora
					 * 3 => shootoff
					 */
					$finished=0;

					/*
					 * <r> stabilisce lo stato di lettura della riga.
					 * Normalmente è zero però il suo valore diventa 1 se:
					 * 1) il match è finito in una volee precedente all'attuale check.
					 * 2) esiste nella request la var corrispondente e vale 1
					 * Questo mi serve per inizializzare la colonna read dello store.
					 *
					 */

					$r=0;
					if ($myRow->matchMode==1) {
						$finished=isFinished($myRow,$myRow->RrLevEnds+1, $max);
					} elseif($myRow->tie1==2 || $myRow->tie2==2) {
						$finished = 1;
					} elseif(strlen(trim($myRow->arrowString1))==$arrow4Match[$myRow->event] && strlen(trim($myRow->arrowString2))==$arrow4Match[$myRow->event]) {
						if($myRow->score1 != $myRow->score2 || ($myRow->tie1==1 || $myRow->tie2==1)) {
							if(strlen(trim($myRow->arrowString1))==$stdArrowShot && strlen(trim($myRow->arrowString2))==$stdArrowShot && strlen(trim($myRow->tiebreak1))==$tieArrowShot && strlen(trim($myRow->tiebreak2))==$tieArrowShot) {
								$finished = 2;
							} else {
								$finished = 1;
							}
						} elseif($myRow->score1 == $myRow->score2) {
							$finished = 3;
						}
					}

					if ($finished==1) {
						$r=1;
					}

					// controllo la request
					if (isset($_REQUEST['r_' . $id]) && preg_match('/^[0-1]{1}$/',$_REQUEST['r_' . $id]) && $myRow->lastUpdate<$serverDate) {
						$r=$_REQUEST['r_' . $id];
					}
					if($target or $target2) {
						$JSON['rows'][]=array(
							'id'	=> $myRow->match1,
							'f'		=> $finished,
							'r'		=> $r,
							'ev'	=> $myRow->event,
							'ph'	=> ($myRow->RrLevName ?: get_text('LevelNum', 'RoundRobin', $myRow->RrLevLevel)).' '.($myRow->RrGrName ?: get_text('GroupNumShort', 'RoundRobin', $myRow->RrGrGroup)).' #'. $myRow->RrMatchRound,
							'evn'	=> $myRow->eventName,
							't'	    => $target,
							't2'	=> $target2,
							'n1'	=> $myRow->name1 . ' (#' . $myRow->rank1 . ')',
							'cn1'	=> $myRow->countryName1,
							'ar1'	=> strlen(str_replace(' ','',$myRow->arrowString1)),
							'sar1'	=> strlen(str_replace(' ','',$myRow->tiebreak1)),
							'n2'	=> $myRow->name2. ' (#' . $myRow->rank2 . ')',
							'cn2'	=> $myRow->countryName2,
							'ar2'	=> strlen(str_replace(' ','',$myRow->arrowString2)),
							'sar2'	=> strlen(str_replace(' ','',$myRow->tiebreak2)),
							'sp1'	=> $setPoints1,
							'sp2'	=> $setPoints2,
							's'		=> $score,
							'lu'	=> $myRow->lastUpdate,
						);
						++$id;
					}
				}
			}
		}

		$JSON['error']=0;
		break;
	default:
		$JSON['error']=1;
}

JsonOut($JSON);
