<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
// require_once('Common/Lib/CommonLib.php');
// require_once('Common/Fun_Phases.inc.php');
// require_once('Common/Fun_FormatText.inc.php');
// require_once('Common/Lib/ArrTargets.inc.php');
// require_once('Common/Fun_Sessions.inc.php');

$JSON=array('error'=>1, 'msg'=>get_text('ErrGenericError', 'Errors'));

if(!CheckTourSession(true) or !hasACL(AclRobin, AclReadWrite) or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

$Team=($_REQUEST['team'] ?? -1);

if($Team==-1) {
	$JSON['error']=0;
	JsonOut($JSON);
}

require_once('./Lib.php');

switch($_REQUEST['act']) {
	case 'getMain':
		// get all the events that need to be solved
		$JSON['cols']=12;
		$JSON['error']=0;
		$JSON['headers']=[
			['class'=>'w-20', 'tit' => get_text('Event'), 'cols' => 3],
			['class'=>'w-30', 'tit' => get_text('Phase').'/'.get_text('Levels', 'RoundRobin'), 'cols' => 3],
			['class'=>'w-5', 'tit' => get_text('ShotOff', 'Tournament')],
			['class'=>'w-5', 'tit' => get_text('CoinToss', 'Tournament')],
			['class'=>'w-30', 'tit' => get_text('Groups', 'RoundRobin'), 'cols' => 2],
			['class'=>'w-5', 'tit' => get_text('ShotOff', 'Tournament')],
			['class'=>'w-5', 'tit' => get_text('CoinToss', 'Tournament')],
		];
		$SQL=[];
		// TODO: Phase 0 is missing the saved people...
		if($Team) {
			// get the Qual => Level 1 events
			$SQL[]= "SELECT 0 as EvPhase, 0 as EvGroup, EvProgr, EvCode, EvEventName, RrLevGroups*RrLevGroupArchers as EvNumQualified, 0 as EvExtraQualified, 0 as GroupSoSolved, EvE1ShootOff as LevelSoSolved, EvShootOff as FinalSoSolved, GROUP_CONCAT(DISTINCT itemNoT) as SoCt, '' as SoExtraCt
	        FROM Events
	        inner join RoundRobinLevel on RrLevTournament=EvTournament and RrLevTeam=EvTeamEvent and RrLevEvent=EvCode and RrLevLevel=1 and EvCodeParentWinnerBranch=0
	        LEFT JOIN (
			SELECT TeEvent, CONCAT_WS('|', COUNT(*), TeSO) as itemNoT
	            FROM `Teams` 
	            WHERE `TeTournament` = " . StrSafe_DB($_SESSION['TourId']) . " and TeFinEvent=1 AND TeSO!=0
	            GROUP BY TeEvent, TeSO
	            HAVING  COUNT(*)>1
	            ORDER BY TeEvent, TeSO DESC
	        ) as sqy2 ON EvCode=TeEvent
	        WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCodeParent='' and EvElimType=5 and EvTeamEvent=1
	        GROUP BY EvCode";
		} else {
			// get the Qual => Level 1 events
			$SQL[]= "SELECT 0 as EvPhase, 0 as EvGroup, EvProgr, EvCode, EvEventName, RrLevGroups*RrLevGroupArchers as EvNumQualified, 0 as EvExtraQualified, 0 as GroupSoSolved, EvE1ShootOff as LevelSoSolved, EvShootOff as FinalSoSolved, GROUP_CONCAT(DISTINCT itemNoI) as SoCt, '' as SoExtraCt
	        FROM Events
	        inner join RoundRobinLevel on RrLevTournament=EvTournament and RrLevTeam=EvTeamEvent and RrLevEvent=EvCode and RrLevLevel=1 and EvCodeParentWinnerBranch=0
	        LEFT JOIN (
	            SELECT IndEvent, CONCAT_WS('|', COUNT(*), IndSO) as itemNoI
	            FROM `Individuals` 
	            WHERE `IndTournament` = " . StrSafe_DB($_SESSION['TourId']) . " and IndSO!=0
	            GROUP BY IndEvent, IndSO
	            HAVING  COUNT(*)>1
	            ORDER BY IndEvent, IndSO DESC
	        ) as sqy1 ON EvCode=IndEvent
	        WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCodeParent='' and EvElimType=5 and EvTeamEvent=0
	        GROUP BY EvCode";
		}

		// get each level
		$SQL[]= "SELECT RrLevLevel as EvPhase, RrGrGroup as EvGroup, EvProgr, EvCode, EvEventName, Lev2Qual as EvNumQualified, lev3Qual as EvExtraQualified, 
       		RrGrSoSolved GroupSoSolved, RrLevSoSolved LevelSoSolved, 0 FinalSoSolved, 
       		concat_ws(',', CtNoGroup, SoNoGroup) as SoCt, concat_ws(',', CtNoLevel, SoNoLevel) as SoExtraCt
        FROM Events
		inner join RoundRobinLevel on RrLevTournament=EvTournament and RrLevTeam=$Team and RrLevEvent=EvCode and EvCodeParentWinnerBranch=0
        inner join RoundRobinGroup on RrGrTournament=RrLevTournament and RrGrTeam=RrLevTeam and RrGrEvent=RrLevEvent and RrGrLevel=RrLevLevel
        left join (
            select RrPartEvent Lev2Event, max(RrPartSourceRank) Lev2Qual, RrPartSourceLevel as Lev2SourceLevel, RrPartSourceGroup as Lev2SourceGroup
            from RoundRobinParticipants
            where RrPartTournament=" . StrSafe_DB($_SESSION['TourId']) . " and RrPartTeam=$Team and RrPartParticipant!=0
            group by RrPartEvent, RrPartSourceLevel, RrPartSourceGroup
            ) Lev2 on Lev2Event=RrLevEvent and Lev2SourceLevel=RrLevLevel and Lev2SourceGroup=RrGrGroup
        left join (
            select RrPartEvent Lev3Event, max(RrPartSourceRank) Lev3Qual, RrPartSourceLevel as Lev3SourceLevel, RrPartSourceGroup as Lev3SourceGroup
            from RoundRobinParticipants
            where RrPartTournament=" . StrSafe_DB($_SESSION['TourId']) . " and RrPartTeam=$Team and RrPartSourceGroup=0 and RrPartParticipant!=0
            group by RrPartEvent, RrPartLevel, RrPartSourceLevel, RrPartSourceGroup
            ) Lev3 on Lev3Event=RrLevEvent and Lev3SourceLevel=RrLevLevel
        LEFT JOIN (
            SELECT RrPartEvent GrEvent, RrPartLevel GrLevel, RrPartGroup GrGroup, group_concat(distinct if(RrPartGroupTiesForSO>0, CONCAT_WS('|', 'SO', RrPartGroupTiesForSO, RrPartGroupRankBefSO), null)) as SoNoGroup, group_concat(distinct if(RrPartGroupTiesForCT>0, CONCAT_WS('|', 'CT', RrPartGroupTiesForCT, RrPartGroupRankBefSO), null)) as CtNoGroup
            FROM RoundRobinParticipants
            WHERE RrPartTournament = " . StrSafe_DB($_SESSION['TourId']) . " and RrPartGroupRank!=0 and RrPartTeam=$Team and RrPartParticipant!=0
            GROUP BY RrPartEvent, RrPartLevel, RrPartGroup
        ) as sqyGroup ON EvCode=GrEvent and RrGrLevel=GrLevel and RrGrGroup=GrGroup
        LEFT JOIN (
            SELECT RrPartEvent LevEvent, RrPartLevel LevLevel, group_concat(distinct if(RrPartLevelTiesForSO>0, CONCAT_WS('|', 'SO', RrPartLevelTiesForSO, RrPartLevelRankBefSO), null)) as SoNoLevel, group_concat(distinct if(RrPartLevelTiesForCT>0, CONCAT_WS('|', 'CT', RrPartLevelTiesForCT, RrPartLevelRankBefSO), null)) as CtNoLevel
            FROM RoundRobinParticipants
            WHERE RrPartTournament = " . StrSafe_DB($_SESSION['TourId']) . " and RrPartLevelRank!=0 and RrPartTeam=$Team and RrPartParticipant!=0
            GROUP BY RrPartEvent, RrPartLevel
        ) as sqyLevel ON EvCode=LevEvent and RrGrLevel=LevLevel
        WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " and EvTeamEvent=$Team and EvElimType=5
        GROUP BY EvCode, RrLevLevel, RrGrGroup ";

		$q=safe_r_sql("(".implode(') UNION (', $SQL).") order by EvProgr, EvCode, EvPhase, EvGroup");
		$Events=array();
		$SoDisabled=array();
		while($r=safe_fetch($q)) {
			if(empty($Events[$r->EvCode])) {
				$disabled=0;
				$Events[$r->EvCode]['phases']=[];
				$Events[$r->EvCode]['code']=$r->EvCode;
				$Events[$r->EvCode]['name']=$r->EvEventName;
				$Events[$r->EvCode]['completed']=$r->FinalSoSolved;
				$Events[$r->EvCode]['rowspan']=0;
				$SoDisabled[$r->EvCode]=0; // first SO is always possible
			}
			if(empty($Events[$r->EvCode]['phases'][(int) $r->EvPhase])) {
				$Events[$r->EvCode]['phases'][(int) $r->EvPhase]=[
					'phase' => ($r->EvPhase ? get_text('LevelNum', 'RoundRobin', $r->EvPhase) : get_text('Q-Session', 'Tournament')),
					'groups' => [],
					'completed'=>(int) $r->LevelSoSolved,
					'disabled'=>$SoDisabled[$r->EvCode],
					'rowspan'=>0,
					'hasSoCt'=>false,
					'BestToSelect'=>'',
					'ct'=>[],
					'so'=>[],
				];
				$SoDisabled[$r->EvCode]=1-$r->LevelSoSolved;
			}
			$so=array();
			$ct=array();
			if($r->SoCt) {
				if($r->EvPhase) {
					// robins
					foreach (explode(',',$r->SoCt) as $ctsoItem) {
						list($type,$tmpNo,$tmpPos) = explode('|',$ctsoItem);
						if($type=='SO') {
							$so[] = $tmpNo . '&nbsp;@&nbsp;' . intval($tmpPos) . ((intval($tmpPos)+intval($tmpNo)-1<=$r->EvNumQualified) ? '':' (' . get_text(($r->EvNumQualified == intval($tmpPos) ? 'OnePlace':'PlacesNo'), 'Tournament', (1+$r->EvNumQualified-intval($tmpPos))) . ')');
						} elseif($type=='CT') {
							$ct[] = get_text('NumTieAtPosition', 'Tournament', array($tmpNo,$tmpPos));
						}
					}
				} else {
					// qualifications
					foreach (explode(',',$r->SoCt) as $ctsoItem) {
						list($tmpNo,$tmpPos) = explode('|',$ctsoItem);
						if($tmpPos<0) {
							$ct[] = get_text('NumTieAtPosition', 'Tournament', array($tmpNo,abs(intval($tmpPos))));
						} else {
							$so[] = $tmpNo . '&nbsp;@&nbsp;' . intval($tmpPos) . ((intval($tmpPos)+intval($tmpNo)<=$r->EvNumQualified) ? '':' (' . get_text(($r->EvNumQualified == intval($tmpPos) ? 'OnePlace':'PlacesNo'), 'Tournament', (1+$r->EvNumQualified-intval($tmpPos))) . ')');
						}
					}
				}
			}
			$levso=array();
			$levct=array();
			if($r->SoExtraCt) {
				foreach (explode(',',$r->SoExtraCt) as $ctsoItem) {
					list($type,$tmpNo,$tmpPos) = explode('|',$ctsoItem);
					if($type=='SO') {
						$levso[] = $tmpNo . '&nbsp;@&nbsp;' . intval($tmpPos) . ((intval($tmpPos)+intval($tmpNo)-1<=$r->EvExtraQualified) ? '':' (' . get_text(($r->EvExtraQualified == intval($tmpPos) ? 'OnePlace':'PlacesNo'), 'Tournament', (1+$r->EvExtraQualified-intval($tmpPos))) . ')');
					} elseif($type=='CT') {
						$levct[] = get_text('NumTieAtPosition', 'Tournament', array($tmpNo,$tmpPos));
					}
				}
				$Events[$r->EvCode]['phases'][(int) $r->EvPhase]['so']=$levso;
				$Events[$r->EvCode]['phases'][(int) $r->EvPhase]['ct']=$levct;
			}
			if(count($so)+count($ct)+count($levso)+count($levct)>0) {
				$Events[$r->EvCode]['phases'][(int) $r->EvPhase]['hasSoCt']=true;
			}
			$Events[$r->EvCode]['phases'][(int) $r->EvPhase]['groups'][(int) $r->EvGroup]=[
				'group' => ($r->EvGroup ? get_text('GroupNum', 'RoundRobin', $r->EvGroup) : ''),
				'completed'=>(int) $r->GroupSoSolved,
				'ct' => $ct,
				'so' => $so,
			];
			$Events[$r->EvCode]['phases'][(int) $r->EvPhase]['rowspan']++;
			$Events[$r->EvCode]['rowspan']++;
		}
		$JSON['events']=array_values($Events);
		break;
	case 'getSoStatus':
		$Event=($_REQUEST['event'] ?? '');
		$Level=($_REQUEST['level'] ?? -1);
		$Group=($_REQUEST['group'] ?? -1);
		if(!$Event or $Level==-1 or $Group==-1) {
			JsonOut($JSON);
		}

		if($Level==0) {
			// from qualification to Level 1 status, check EvElim1ShootOff
			$q=safe_r_sql("select EvE1ShootOff from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=".StrSafe_DB($Event));
			if($r=safe_fetch($q)) {
				$JSON['solved']=(int) $r->EvE1ShootOff;
				$JSON['error']=0;
			}
			JsonOut($JSON);
		}

		// Level>0 need to check group also to see if the whole level is done
		if($Group==0) {
			$q=safe_r_sql("select RrLevSoSolved from RoundRobinLevel where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevLevel=$Level and RrLevEvent=".StrSafe_DB($Event));
			if($r=safe_fetch($q)) {
				$JSON['solved']=(int) $r->RrLevSoSolved;
				$JSON['error']=0;
			}
			JsonOut($JSON);
		}

		$q=safe_r_sql("select RrGrSoSolved from RoundRobinGroup where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrLevel=$Level and RrGrGroup=$Group and RrGrEvent=".StrSafe_DB($Event));
		if($r=safe_fetch($q)) {
			$JSON['solved']=(int) $r->RrGrSoSolved;
			$JSON['error']=0;
		}
		JsonOut($JSON);
		break;
	case 'buildSoTable':
		$Event=($_REQUEST['event'] ?? '');
		$Level=($_REQUEST['level'] ?? -1);
		$Group=($_REQUEST['group'] ?? -1);
		$AdvancedMode = intval($_REQUEST["advanced"] ?? 0);
		if(!$Event or $Level==-1 or $Group==-1) {
			JsonOut($JSON);
		}

		$R = ($_REQUEST['R'] ?? []);
		$P = ($_REQUEST['P'] ?? []);
		$T = ($_REQUEST['T'] ?? []);
		$C = ($_REQUEST['C'] ?? []);
		$bSO = ($_REQUEST['bSO'] ?? []);

		require_once('Common/Lib/Obj_RankFactory.php');
		require_once('Final/Fun_ChangePhase.inc.php');

		$JSON['event']=$Event;
		$JSON['level']=$Level;
		$JSON['group']=$Group;


		// only Teams
		// if($Team) {
		// 	JsonOut($JSON);
		// }

		// only individual
		$JSON['message']='';
		$JSON['back']=get_text('Close');
		$JSON['advanced']=$AdvancedMode;
		$JSON['advancedText']=get_text($AdvancedMode ? 'DefaultMode' : 'AdvancedMode');
		$JSON['ResetBeforeSO']=get_text('ResetBeforeSO','Tournament');
		$JSON['colspan']=9;
		$JSON['tables']=[];

		// one event/level/group at a time, so...
		$soEvent=($_REQUEST['soEvent']??'');
		$soLevel=($_REQUEST['soLevel']??'-1');
		$soGroup=($_REQUEST['soGroup']??'-1');

		if ($R AND !IsBlocked(BIT_BLOCK_ROBIN)) {
			$Grids2Handle = array();
			if(!$soEvent or $soLevel==-1 or $soGroup==-1) {
				JsonOut($JSON);
			}

			// select the last qualification rank for the level
			$q = safe_r_sql("select least(max(RrPartSourceRank), ActParts) as LastQualified, EvFinalTargetType
				from RoundRobinParticipants 
                inner join Events on EvTournament=RrPartTournament and EvCode=RrPartEvent and EvTeamEvent=RrPartTeam
				inner join (
				    select count(*) as ActParts, RrPartLevel as ActLevel, ".($soGroup ? "RrPartGroup" : "0")." as ActGroup
				    from RoundRobinParticipants 
				    where RrPartParticipant>0 and RrPartLevel=$soLevel and ".($soGroup ? "RrPartGroup=$soGroup" : "RrPartLevelRank=0")." and RrPartEvent=" . StrSafe_DB($soEvent) . " and RrPartTeam=$Team and RrPartTournament='{$_SESSION['TourId']}'
				    ) actualPart on ActLevel=RrPartSourceLevel and ActGroup=RrPartSourceGroup
				where RrPartSourceLevel=$soLevel and RrPartSourceGroup=$soGroup and RrPartEvent=" . StrSafe_DB($soEvent) . " and RrPartTeam=$Team and RrPartTournament='{$_SESSION['TourId']}'
				");
			$r = safe_fetch($q);
			if(!$r) {
				JsonOut($JSON);
			}

			// Check CT and SO have been done - need to check that in the range of allowed vales, none is present double
			$existingRanks = array();
			$cantResolve = false;
			foreach ($R as $EnId => $AssignedRank) {
				if ($AssignedRank <= $r->LastQualified) {
					if (!array_key_exists($AssignedRank, $existingRanks)) {
						$existingRanks[$AssignedRank] = 0;
					}
					if (++$existingRanks[$AssignedRank] != 1) {
						$cantResolve = true;
					}
				}
			}
			if (!$cantResolve AND (count($existingRanks) < min(count($R),$r->LastQualified))) {
				if ($bSO) {
					foreach ($bSO as $irmPos) {
						if (!array_key_exists($irmPos, $existingRanks)) {
							$existingRanks[$irmPos] = 0;
						}
						$existingRanks[$irmPos]++;
					}
					if (array_sum($existingRanks) < min(count($R),$r->LastQualified)) {
						$cantResolve = true;
					}
				} else {
					$cantResolve = true;
				}
			}

			if($cantResolve) {
				if($soLevel==0) {
					require_once('Common/Fun_Sessions.inc.php');
					ResetShootoff($soEvent, $Team, 0);
				} else {
					RobinResetSO($Team, $Event, $soLevel, $soGroup);
				}
			} else {
				if($soLevel==0) {
					$rank = Obj_RankFactory::create('Abs'.($Team?'Team':''), array('events' => $soEvent, 'dist' => 0));
					$Grids2Handle[] = $soEvent;
					$obj=getEventArrowsParams($soEvent,64, $Team);
					foreach ($R as $IdSubTeam => $AssignedRank) {
						list($EnId, $SubTeam)=explode('-', $IdSubTeam);
						$tmpValue = array('ath' => $EnId, 'event' => $soEvent, 'dist' => 0, 'rank' => $AssignedRank);
						if($Team) {
							$tmpValue['subteam']=$SubTeam;
						}
						if (isset($T[$IdSubTeam]) and is_array($T[$IdSubTeam])) {
							$tmpValue['tiebreak'] = '';
							$tmpValue['closest'] = 0;
							foreach ($T[$IdSubTeam] as $k => $v) {
								$tmpValue['tiebreak'] .= GetLetterFromPrint(str_replace('*','', $v), 'T', $r->EvFinalTargetType);
							}
							$tmpValue['tiebreak'] = trim($tmpValue['tiebreak']);
							if (isset($C[$IdSubTeam])) {
								$tmpValue['closest'] = intval($C[$IdSubTeam]);
							}

							$Decoded=array();
							$idx=0;
							while($TbString=substr($tmpValue['tiebreak'], $idx, $obj->so)) {
								if($obj->so==1) {
									$Decoded[]=DecodeFromLetter($TbString);
								} else {
									$Decoded[]=ValutaArrowString($TbString);
								}
								$idx+=$obj->so;
							}
							$tmpValue['decoded'] = implode(',',$Decoded).($tmpValue['closest'] ? '+' : '');
						}
						$rank->setRow(array($tmpValue));
					}

					// resets all the levels/groups assignments for this event...
					safe_w_sql("update RoundRobinParticipants set RrPartGroupRank=0, RrPartGroupRankBefSO=0, RrPartLevelRank=0, RrPartLevelRankBefSO=0, RrPartPoints=0, RrPartTieBreaker=0, RrPartTieBreaker2=0, RrPartParticipant=0, RrPartSubTeam=0,
						RrPartGroupTieBreak='', RrPartGroupTbClosest=0, RrPartGroupTbDecoded='', RrPartLevelTieBreak='', RrPartLevelTbClosest=0, RrPartLevelTbDecoded='',
						RrPartIrmType=0, RrPartGroupTiesForSO=0, RrPartGroupTiesForCT=0, RrPartDateTime=now(), RrPartLevelTiesForSO=0, RrPartLevelTiesForCT=0
						where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));
					safe_w_sql("update RoundRobinMatches set RrMatchAthlete=0, RrMatchSubTeam=0, RrMatchRank=0, RrMatchScore=0, RrMatchSetScore=0, RrMatchSetPoints='', RrMatchSetPointsByEnd='',
							RrMatchWinnerSet=0, RrMatchTie=0, RrMatchArrowstring='', RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchArrowPosition='', RrMatchTiePosition='', RrMatchWinLose=0,
							RrMatchFinalRank=0, RrMatchDateTime=0, RrMatchSyncro=0, RrMatchLive=0, RrMatchStatus=0, RrMatchShootFirst=0, RrMatchVxF=0, RrMatchConfirmed=0, RrMatchNotes='',
							RrMatchRecordBitmap=0, RrMatchIrmType=0, RrMatchCoach=0, RrMatchRoundPoints=0, RrMatchTieBreaker=0, RrMatchTieBreaker2=0
						where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($soEvent));
					safe_w_sql("update RoundRobinGroup set RrGrSoSolved=0 
						where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($soEvent));
					safe_w_sql("update RoundRobinLevel set RrLevSoSolved=0 
						where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($soEvent));

					// sets the first level
					if($Team) {
						safe_w_sql("update RoundRobinParticipants 
							inner join Teams on TeTournament=RrPartTournament and TeEvent=RrPartEvent and TeRank=RrPartSourceRank and TeFinEvent=1
							inner join RoundRobinGrids on RrGridTournament=RrPartTournament and RrGridEvent=RrPartEvent and RrGridTeam=RrPartTeam and RrGridLevel=RrPartLevel and RrGridGroup=RrPartGroup and RrGridItem=RrPartDestItem
							inner join RoundRobinMatches on RrMatchTournament=RrGridTournament and RrMatchTeam=RrGridTeam and RrMatchEvent=RrGridEvent and RrMatchLevel=RrGridLevel and RrMatchGroup=RrGridGroup and RrMatchRound=RrGridRound and RrMatchMatchNo=RrGridMatchno
							set RrPartParticipant=TeCoId, RrMatchAthlete=TeCoId, RrPartSubTeam=TeSubTeam, RrMatchSubTeam=TeSubTeam
							where RrPartSourceLevel=0 and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=1 and RrPartEvent=".StrSafe_DB($soEvent));
					} else {
						safe_w_sql("update RoundRobinParticipants 
							inner join Individuals on IndTournament=RrPartTournament and IndEvent=RrPartEvent and IndRank=RrPartSourceRank
							inner join RoundRobinGrids on RrGridTournament=RrPartTournament and RrGridEvent=RrPartEvent and RrGridTeam=RrPartTeam and RrGridLevel=RrPartLevel and RrGridGroup=RrPartGroup and RrGridItem=RrPartDestItem
							inner join RoundRobinMatches on RrMatchTournament=RrGridTournament and RrMatchTeam=RrGridTeam and RrMatchEvent=RrGridEvent and RrMatchLevel=RrGridLevel and RrMatchGroup=RrGridGroup and RrMatchRound=RrGridRound and RrMatchMatchNo=RrGridMatchno
							set RrPartParticipant=IndId, RrMatchAthlete=IndId
							where RrPartSourceLevel=0 and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=0 and RrPartEvent=".StrSafe_DB($soEvent));
					}

					// updates the SO status of the event
					safe_w_sql("UPDATE Events SET EvE1ShootOff=1 WHERE EvTeamEvent=$Team AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvCode = " . StrSafe_DB($soEvent));
					set_qual_session_flags();

					// Updates the BYE status of the matches of 1st level
					$q=safe_r_sql("select r1.RrMatchGroup as MatchGroup, r1.RrMatchRound as MatchRound, r1.RrMatchMatchNo as M1, r1.RrMatchAthlete as A1, r2.RrMatchMatchNo as M2, r2.RrMatchAthlete as A2
							from RoundRobinMatches r1
							inner join RoundRobinMatches r2 on r2.RrMatchTournament=r1.RrMatchTournament and r2.RrMatchTeam=r1.RrMatchTeam and r2.RrMatchEvent=r1.RrMatchEvent and r2.RrMatchLevel=r1.RrMatchLevel and r2.RrMatchGroup=r1.RrMatchGroup and r2.RrMatchRound=r1.RrMatchRound and r2.RrMatchMatchNo=r1.RrMatchMatchNo+1
							where r1.RrMatchMatchno%2=0 and (r1.RrMatchAthlete=0 or r2.RrMatchAthlete=0) and r1.RrMatchTournament={$_SESSION['TourId']} and r1.RrMatchTeam=$Team and r1.RrMatchLevel=1 and r1.RrMatchEvent=".StrSafe_DB($soEvent));
					while($r=safe_fetch($q)) {
						if($r->A1) {
							safe_w_sql("update RoundRobinMatches set RrMatchTie=2, RrMatchWinLose=1 where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchLevel=1 and RrMatchGroup=$r->MatchGroup and RrMatchRound=$r->MatchRound and RrMatchMatchNo=$r->M1 and RrMatchEvent=".StrSafe_DB($soEvent));
						} elseif($r->A2) {
							safe_w_sql("update RoundRobinMatches set RrMatchTie=2, RrMatchWinLose=1 where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchLevel=1 and RrMatchGroup=$r->MatchGroup and RrMatchRound=$r->MatchRound and RrMatchMatchNo=$r->M2 and RrMatchEvent=".StrSafe_DB($soEvent));
						}
					}

					// Calculate Final rank of the ones out of the grids
					calculateFinalRank($Team,$soEvent,$soLevel);
				} else {
					// get the SO arrows for that level
					$q=safe_r_sql("select RrLevArrows, RrLevEnds, RrLevSO, EvElim1 as MaxLevels, EvFinalFirstPhase from RoundRobinLevel inner join Events on EvTeamEvent=RrLevTeam and EvTournament=RrLevTournament and EvCode=RrLevEvent where  RrLevTeam=$Team and RrLevLevel=$soLevel and RrLevTournament={$_SESSION['TourId']} and RrLevEvent=".StrSafe_DB($soEvent));
					$ARROWS=safe_fetch($q);
					$Grids2Handle[] = $soEvent;
					$Field=($soGroup ? 'Group' : 'Level');
					foreach ($R as $IdSubTeam => $AssignedRank) {
						list($EnId, $SubTeam)=explode('-', $IdSubTeam);
						$SQL="RrPart{$Field}Rank=$AssignedRank";
						if (isset($T[$IdSubTeam]) and is_array($T[$IdSubTeam])) {
							$tiebreak='';
							$closest=0;
							$decoded='';
							foreach ($T[$IdSubTeam] as $k => $v) {
								$tiebreak .= GetLetterFromPrint(str_replace('*','', $v), 'T', $r->EvFinalTargetType);
							}
							$tiebreak = trim($tiebreak);
							if (isset($C[$IdSubTeam])) {
								$closest = 1;
							}

							$Decoded=array();
							$idx=0;
							while($TbString=substr($tiebreak, $idx, $ARROWS->RrLevSO)) {
								if($ARROWS->RrLevSO==1) {
									$Decoded[]=DecodeFromLetter($TbString);
								} else {
									$Decoded[]=ValutaArrowString($TbString);
								}
								$idx+=$ARROWS->RrLevSO;
							}
							$decoded = implode(',',$Decoded).($closest ? '+' : '');
							$SQL.=", RrPart{$Field}TieBreak='$tiebreak', RrPart{$Field}TbClosest=$closest, RrPart{$Field}TbDecoded='$decoded'";
						}

						safe_w_sql("update RoundRobinParticipants 
							set $SQL
							where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartLevel=$soLevel and RrPartParticipant=$EnId and RrPartSubTeam=$SubTeam and RrPartEvent=".StrSafe_DB($soEvent));
					}

					// resets all the levels/groups assignments for this event and up...
					// get all the fields
					$q=safe_r_sql("show columns from RoundRobinParticipants");
					$PartSql=array();
					while($r=safe_fetch($q)) {
						switch($r->Field) {
							case 'RrPartTournament':
							case 'RrPartTeam':
							case 'RrPartEvent':
							case 'RrPartLevel':
							case 'RrPartGroup':
							case 'RrPartDestItem':
							case 'RrPartSourceLevel':
							case 'RrPartSourceGroup':
							case 'RrPartSourceRank':
								break;
							default:
								$PartSql[]=$r->Field."=".($r->Type[0]=='v' ? "''" : "0");
						}
					}

					$q=safe_r_sql("show columns from RoundRobinMatches");
					$MatchSql=array();
					while($r=safe_fetch($q)) {
						switch($r->Field) {
							case 'RrMatchTournament':
							case 'RrMatchTeam':
							case 'RrMatchEvent':
							case 'RrMatchLevel':
							case 'RrMatchGroup':
							case 'RrMatchRound':
							case 'RrMatchMatchNo':
							case 'RrMatchTarget':
							case 'RrMatchScheduledDate':
							case 'RrMatchScheduledTime':
							case 'RrMatchScheduledLength':
								break;
							default:
								$MatchSql[]=$r->Field."=".($r->Type[0]=='v' ? "''" : "0");
						}
					}

					// this level voids only this group
					safe_w_sql("update RoundRobinParticipants set ".implode(',', $PartSql)."
							where RrPartTournament={$_SESSION['TourId']} and RrPartSourceLevel=$soLevel and RrPartSourceGroup=$soGroup and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));
					safe_w_sql("update RoundRobinMatches
						    inner join RoundRobinGrids on RrGridTournament=RrMatchTournament and RrGridTeam=RrMatchTeam and RrGridEvent=RrMatchEvent and RrGridLevel=RrMatchLevel and RrGridGroup=RrMatchGroup and RrGridRound=RrMatchRound and RrGridMatchno=RrMatchMatchNo
							inner join RoundRobinParticipants on RrPartTournament=RrMatchTournament and RrPartTeam=RrMatchTeam and RrPartEvent=RrMatchEvent and RrPartLevel=RrMatchLevel and RrPartGroup=RrMatchGroup and RrPartDestItem=RrGridItem
							set ".implode(',', $MatchSql)."
							where RrPartTournament={$_SESSION['TourId']} and RrPartSourceLevel=$soLevel and RrPartSourceGroup=$soGroup and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));

					// next level voids all groups
					safe_w_sql("update RoundRobinParticipants set ".implode(',', $PartSql)."
							where RrPartTournament={$_SESSION['TourId']} and RrPartSourceLevel>$soLevel and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));
					safe_w_sql("update RoundRobinMatches
							inner join RoundRobinParticipants on RrPartTournament=RrMatchTournament and RrPartTeam=RrMatchTeam and RrPartEvent=RrMatchEvent and RrPartLevel=RrMatchLevel and RrPartGroup=RrMatchGroup
							set ".implode(',', $MatchSql)."
							where RrPartTournament={$_SESSION['TourId']} and RrPartSourceLevel>$soLevel and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));

					if($soLevel==$ARROWS->MaxLevels) {
						// this is the SO from Robins to regular brackets
						$Offset=$ARROWS->EvFinalFirstPhase*2 - 1;
						if($soGroup) {
							if($Team) {
								safe_w_sql("update RoundRobinParticipants 
									inner join TeamFinals on TfTournament=RrPartTournament and TfEvent=RrPartEvent and TfSubTeam=RrPartSubTeam
                                    inner join Grids on GrMatchno=TfMatchNo and GrPosition=RrPartDestItem
                                    inner join Events on EvFinalFirstPhase=GrPhase and EvTournament=RrPartTournament and EvTeamEvent=RrPartTeam and EvCode=RrPartEvent
									inner join (
									    select RrPartGroupRank as SrcRank, RrPartParticipant as SrcPart, RrPartSubTeam as SrcSubteam, RrPartLevel srcLevel, RrPartGroup srcGroup
									    from RoundRobinParticipants 
									    where RrPartLevel=$soLevel and RrPartGroup=$soGroup and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent)."
									    ) sqy on SrcRank=RrPartSourceRank and SrcLevel=RrPartSourceLevel and SrcGroup=RrPartSourceGroup
									set RrPartParticipant=SrcPart, RrPartSubTeam=SrcSubteam, TfTeam=SrcPart, TfSubTeam=SrcSubTeam
									where RrPartLevel=0 and RrPartGroup=0 and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));
							} else {
								safe_w_sql("update RoundRobinParticipants 
									inner join Finals on FinTournament=RrPartTournament and FinEvent=RrPartEvent
                                    inner join Grids on GrMatchno=FinMatchNo and GrPosition=RrPartDestItem
                                    inner join Events on EvFinalFirstPhase=GrPhase and EvTournament=RrPartTournament and EvTeamEvent=RrPartTeam and EvCode=RrPartEvent
									inner join (
									    select RrPartGroupRank as SrcRank, RrPartParticipant as SrcPart, RrPartSubTeam as SrcSubteam, RrPartLevel srcLevel, RrPartGroup srcGroup
									    from RoundRobinParticipants 
									    where RrPartLevel=$soLevel and RrPartGroup=$soGroup and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent)."
									    ) sqy on SrcRank=RrPartSourceRank and SrcLevel=RrPartSourceLevel and SrcGroup=RrPartSourceGroup
									set RrPartParticipant=SrcPart, RrPartSubTeam=SrcSubteam, FinAthlete=SrcPart
									where RrPartLevel=0 and RrPartGroup=0 and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));
							}
							// updates the SO status of the group
							safe_w_sql("UPDATE RoundRobinGroup SET RrGrSoSolved=1 WHERE RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrLevel=$soLevel and RrGrGroup=$soGroup and RrGrEvent=" . StrSafe_DB($soEvent));
						} else {
							if($Team) {
								safe_w_sql("update RoundRobinParticipants 
									inner join TeamFinals on TfTournament=RrPartTournament and TfEvent=RrPartEvent and TfSubTeam=RrPartSubTeam
                                    inner join Grids on GrMatchno=TfMatchNo and GrPosition=RrPartDestItem
                                    inner join Events on EvFinalFirstPhase=GrPhase and EvTournament=RrPartTournament and EvTeamEvent=RrPartTeam and EvCode=RrPartEvent
									inner join (
									    select RrPartLevelRank as SrcRank, RrPartParticipant as SrcPart, RrPartSubTeam as SrcSubteam, RrPartLevel srcLevel, 0 srcGroup
									    from RoundRobinParticipants 
									    where RrPartLevel=$soLevel and RrPartLevelRank>0 and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent)."
									    ) sqy on SrcRank=RrPartSourceRank and SrcLevel=RrPartSourceLevel and SrcGroup=RrPartSourceGroup
									set RrPartParticipant=SrcPart, RrPartSubTeam=SrcSubteam, TfTeam=SrcPart, TfSubTeam=SrcSubTeam
									where RrPartLevel=0 and RrPartGroup=0 and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));
							} else {
								safe_w_sql("update RoundRobinParticipants 
									inner join Finals on FinTournament=RrPartTournament and FinEvent=RrPartEvent
                                    inner join Grids on GrMatchno=FinMatchNo and GrPosition=RrPartDestItem
                                    inner join Events on EvFinalFirstPhase=GrPhase and EvTournament=RrPartTournament and EvTeamEvent=RrPartTeam and EvCode=RrPartEvent
									inner join (
									    select RrPartLevelRank as SrcRank, RrPartParticipant as SrcPart, RrPartSubTeam as SrcSubteam, RrPartLevel srcLevel, 0 srcGroup
									    from RoundRobinParticipants 
									    where RrPartLevel=$soLevel and RrPartLevelRank>0 and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent)."
									    ) sqy on SrcRank=RrPartSourceRank and SrcLevel=RrPartSourceLevel and SrcGroup=RrPartSourceGroup
									set RrPartParticipant=SrcPart, RrPartSubTeam=SrcSubteam, FinAthlete=SrcPart
									where RrPartLevel=0 and RrPartGroup=0 and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));
							}
							// updates the SO status of the level
							calculateFinalRank($Team,$soEvent,$soLevel);
							safe_w_sql("UPDATE RoundRobinLevel SET RrLevSoSolved=1 WHERE RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevLevel=$soLevel and RrLevEvent=" . StrSafe_DB($soEvent));
						}
						// this is the last level, check if the LevelSO has already been set (could be done already if no "best excluded" need to be selected, see end of procedure!)
						$q=safe_r_sql("select RrLevSoSolved from RoundRobinLevel WHERE RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevLevel=$soLevel and RrLevEvent=" . StrSafe_DB($soEvent));
						if($r=safe_fetch($q) and $r->RrLevSoSolved) {
							// updates the finals SO as well
							safe_w_sql("UPDATE Events SET EvE1ShootOff=1, EvShootOff=1 WHERE EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=" . StrSafe_DB($soEvent));
							set_qual_session_flags();
						}
					} else {
						if($soGroup) {
							safe_w_sql("update RoundRobinParticipants 
								inner join RoundRobinGrids on RrGridTournament=RrPartTournament and RrGridEvent=RrPartEvent and RrGridTeam=RrPartTeam and RrGridLevel=RrPartLevel and RrGridGroup=RrPartGroup and RrGridItem=RrPartDestItem
								inner join RoundRobinMatches on RrMatchTournament=RrGridTournament and RrMatchTeam=RrGridTeam and RrMatchEvent=RrGridEvent and RrMatchLevel=RrGridLevel and RrMatchGroup=RrGridGroup and RrMatchRound=RrGridRound and RrMatchMatchNo=RrGridMatchno
								inner join (
								    select RrPartGroupRank as SrcRank, RrPartParticipant as SrcPart, RrPartSubTeam as SrcSubteam
								    from RoundRobinParticipants 
								    where RrPartLevel=$soLevel and RrPartGroup=$soGroup and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent)."
								    ) sqy on SrcRank=RrPartSourceRank
								set RrPartParticipant=SrcPart, RrPartSubTeam=SrcSubteam, RrMatchAthlete=SrcPart, RrMatchSubTeam=SrcSubteam
								where RrPartSourceLevel=$soLevel and RrPartSourceGroup=$soGroup and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));

							// updates the SO status of the group
							safe_w_sql("UPDATE RoundRobinGroup SET RrGrSoSolved=1 WHERE RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrLevel=$soLevel and RrGrGroup=$soGroup and RrGrEvent=" . StrSafe_DB($soEvent));
						} else {
							safe_w_sql("update RoundRobinParticipants 
								inner join RoundRobinGrids on RrGridTournament=RrPartTournament and RrGridEvent=RrPartEvent and RrGridTeam=RrPartTeam and RrGridLevel=RrPartLevel and RrGridGroup=RrPartGroup and RrGridItem=RrPartDestItem
								inner join RoundRobinMatches on RrMatchTournament=RrGridTournament and RrMatchTeam=RrGridTeam and RrMatchEvent=RrGridEvent and RrMatchLevel=RrGridLevel and RrMatchGroup=RrGridGroup and RrMatchRound=RrGridRound and RrMatchMatchNo=RrGridMatchno
								inner join (
								    select RrPartLevelRank as SrcRank, RrPartParticipant as SrcPart, RrPartSubTeam as SrcSubteam
								    from RoundRobinParticipants 
								    where RrPartLevel=$soLevel and RrPartLevelRank>0 and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent)."
								    ) sqy on SrcRank=RrPartSourceRank
								set RrPartParticipant=SrcPart, RrPartSubTeam=SrcSubteam, RrMatchAthlete=SrcPart, RrMatchSubTeam=SrcSubteam
								where RrPartSourceLevel=$soLevel and RrPartSourceGroup=$soGroup and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($soEvent));

							// select the "losers" and updates the "final rank" status

							// updates the SO status of the level
							calculateFinalRank($Team,$soEvent,$soLevel);
							safe_w_sql("UPDATE RoundRobinLevel SET RrLevSoSolved=1 WHERE RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevLevel=$soLevel and RrLevEvent=" . StrSafe_DB($soEvent));
						}
					}

					set_qual_session_flags();
				}
			}
		}

		if($Level==0) {
			$q = safe_r_sql("select EvShootOff, EvE1ShootOff from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$Team and EvCode=" . StrSafe_DB($Event));
			if ($r = safe_fetch($q) and !$r->EvShootOff and !$r->EvE1ShootOff) {
				Obj_RankFactory::create('Abs'.($Team?'Team':''), array('tournament' => $_SESSION['TourId'], 'events' => $Event, 'dist' => 0))->calculate();
			}

			$rank = Obj_RankFactory::create('Abs'.($Team?'Team':''), array('tournament' => $_SESSION['TourId'], 'events' => array($Event), 'dist' => 0));

			$rank->read();
			$data = $rank->getData();

			foreach ($data['sections'] as $section) {
				$EvRows=[];
				$EvRows['headers']=[
					'rank' => $section['meta']['fields']['rank'],
					'item' => $section['meta']['fields'][$Team?'countryCode':'athlete'],
					'country' => $section['meta']['fields']['countryName'],
					'points' => $section['meta']['fields']['score'],
					'untie1' => $section['meta']['fields']['gold'],
					'untie2' => $section['meta']['fields']['xnine'],
					'arrows' => $section['meta']['fields']['tiebreak'],
					'closest' => $section['meta']['fields']['tiebreakClosest'],
				];
				$EvRows['code']=$section['meta']['event'];
				$EvRows['level']=0;
				$EvRows['group']=0;
				$EvRows['soSolved']=$r->EvE1ShootOff;
				$EvRows['name']=$section['meta']['descr'] . ' (' . $section['meta']['event'] . ')';

				$EvRows['rows']=[];

				$rnkBeforeSO = 1;
				$wasCTSO = false;
				$endRank = 1;
				$obj=getEventArrowsParams($Event,64, $Team);
				foreach ($section['items'] as $item) {
					if (($item['rankBeforeSO'] + $item['ct']) >= $section['meta']['firstQualified']) {
						//Stop if Rank >QualifiedNo and no SO
						if ($item['rank'] > ($section['meta']['qualifiedNo'] + $section['meta']['firstQualified'] - 1) AND $item['so'] == 0) {
							continue;
						} else if ($item['irm'] >= 10) {
							$EvRows['rows'][]=['div'];
							// echo '<tr class="Divider"><td colspan="9"></td></tr>';
						}
						if ($rnkBeforeSO != $item['rankBeforeSO'] AND ($item['so'] != 0 OR $item['ct'] != 1) OR ($item['ct'] == 1 AND $wasCTSO)) {
							$EvRows['rows'][]=['div'];
							// echo '<tr class="Divider"><td colspan="9"></td></tr>';
							$wasCTSO = false;
						}
						$row=[
							'id' => $item['id'],
							'subteam' => ($item['subteam']??0),
							'rank' => $item['rank'],
							'irm' => ($item['irm'] != 0 ? '&nbsp;'.$item['irmText'] : ''),
							'class' => ($item['so'] != 0 ? 'error' : ($item['ct'] != 1 ? 'warning' : '')),
						];

						$endRank = $item['rankBeforeSO'] + $item['ct'] - 1;
						if($AdvancedMode) {
							$row['field']=[
								'type' => 'i',
								'name' => 'R',
								'value' => ($R[$section['meta']['event']][$item['id']] ?? $item['rank']),
							];
						} else if ($item['irm'] < 10) {
							//This part for DNF
							if ($item['rankBeforeSO'] != $endRank) {
								$row['field']=[
									'type' => 's',
									'name' => 'R',
									'value' => [],
								];
								$wasCTSO = true;
								for ($i = $item['rankBeforeSO']; $i <= $endRank; ++$i) {
									$row['field']['value'][]=['k'=>$i, 's'=>(($R[$section['meta']['event']][$item['id']] ?? $item['rank']) == $i)];
								}
							} else {
								$row['field']=[
									'type' => 'h',
									'name' => 'R',
									'value' => $item['rankBeforeSO'],
								];
							}
						} else {
							$row['field']=[
								'type' => 'h',
								'name' => 'bSO',
								'value' => $item['rankBeforeSO'],
							];
						}
						$row['item']=$item[$Team?'countryCode':'athlete'];
						$row['country']=$item['countryCode'].($item['countryName'] ? '-'.$item['countryName'] : '');
						$row['points']=$item['score'];
						$row['untie1']=$item['gold'];
						$row['untie2']=$item['xnine'];
						$row['arrows']=[];
						$row['closest']='';

						if ($item['so'] != 0) {
							for ($j = 0; $j < 3; ++$j) {
								$ar['txt']=get_text('ShotOffShort','Tournament').' '.($j+1);
								$ar['arrows']=[];

								for ($i = 0; $i < $obj->so; ++$i) {
									$idx=($j*$obj->so)+$i;
									$ar['arrows'][]=[
										'type'=>'i',
										'name'=>'T',
										'index'=>$idx,
										'value'=>(strlen($item['tiebreak']) > $idx ? DecodeFromLetter($item['tiebreak'][$idx]) : ($T[$section['meta']['event']][$item['id']][$idx] ?? '')),
									];
								}

								$row['arrows'][]=$ar;
							}
							$row['closest']=[
								'type'=>'c',
								'name'=>'C',
								'sel'=>(($C[$section['meta']['event']][$item['id']] ?? $item['tiebreakClosest']) ? 'checked="checked"' : ''),
							];
						}
						$rnkBeforeSO = $item['rankBeforeSO'];


						$EvRows['rows'][]=$row;
					}
				}
				// echo '<tr><td class="Center" colspan="9"><input type="submit" value="' . get_text('CmdOk') . '"></td></tr>';
				// echo '<tr><td colspan="9"><input type="button" value="' . get_text(($advMode ? 'DefaultMode' : 'AdvancedMode')) . '" onclick="goToAdvancedMode()" ></td></tr>';
				// if($advMode) {
				// 	echo '<tr><td colspan="9" class="Right"><input type="button" value="' . get_text('ResetBeforeSO','Tournament') . '" onclick="ResetDataToQR()" ></td></tr>';
				// }
				// echo '</table>';
				$JSON['tables'][]=$EvRows;
			}
			$JSON['error']=0;
			JsonOut($JSON);
		}

		// other levels...
		// count the number of people OK for the group selection
		$SQL="select RrLevGroups, RrGrSoSolved, RrPartGroupRank, RrPartGroupRankBefSO, RrPartParticipant, RrPartSubTeam, RrPartGroup, RrPartPoints, RrPartTieBreaker, RrPartTieBreaker2, RrPartGroupTieBreak, RrPartGroupTbClosest,
       		RrPartGroupTiesForSO, RrPartGroupTiesForCT, RrPartLevelTiesForSO, RrPartLevelTiesForCT, EvEventName, RrLevEnds, RrLevArrows, RrLevSO, RrLevTieBreakSystem, RrLevTieBreakSystem2,
			coalesce(EnId, CoId, 0)  as id, coalesce(concat(upper(EnFirstName),' ', EnName), concat(CoCode, '/',RrPartSubTeam), '') as athlete, coalesce(EnCoCode, CoName,'') as country,
       		IrmType, IrmId, RrLevSoSolved
			from RoundRobinLevel
			inner join RoundRobinGroup on RrGrTournament=RrLevTournament and RrGrTeam=RrLevTeam and RrGrEvent=RrLevEvent and RrGrLevel=RrLevLevel
			inner join RoundRobinParticipants on RrPartTournament=RrGrTournament and RrPartTeam=RrGrTeam and RrPartEvent=RrGrEvent and RrPartLevel=RrGrLevel and RrPartGroup=RrGrGroup and RrPartParticipant!=0
			inner join Events on EvTournament=RrLevTournament and EvTeamEvent=RrLevTeam and EvCode=RrLevEvent
		    inner join IrmTypes on IrmId=RrPartIrmType
			inner join (
		        select max(RrPartSourceRank) as MaxQualified, RrPartTournament r3To, RrPartTeam r3Te, RrPartEvent r3Ev, RrPartSourceLevel r3Le, RrPartSourceGroup r3Gr
		        from RoundRobinParticipants
		        where RrPartTournament = {$_SESSION['TourId']}
			    group by RrPartTournament, RrPartTeam, RrPartEvent, RrPartSourceLevel, RrPartSourceGroup
			    ) as r3 on r3To=RrPartTournament and r3Te=RrPartTeam and r3Ev=RrPartEvent and r3Le=RrPartLevel and r3Gr=RrPartGroup and RrPartGroupRankBefSO<=MaxQualified
		    left join (select EnTournament, EnId, EnFirstName, EnName, CoCode as EnCoCode, CoName as EnCoName from Entries inner join Countries on CoId=EnCountry and CoTournament=EnTournament where EnTournament={$_SESSION['TourId']}) Entries on EnTournament=RrPartTournament and EnId=RrPartParticipant and RrPartTeam=$Team
			left join Countries on CoTournament=RrLevTournament and CoId=RrPartParticipant and RrPartTeam=1
			where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event)." and RrLevLevel=$Level
			order by RrGrGroup, RrPartGroupRankBefSO, RrPartGroupRank, RrPartLevelRankBefSO, RrPartLevelRank";
		$q=safe_r_sql($SQL);
		$EvRows=[];
		$OldGroup=0;
		$NumRow=-1;
		$GroupSoSolved=true;
		$LevelSoSolved=false;
		$NumGroups=0;
		$NumSO=[];
		while($r=safe_fetch($q)) {
			$LevelSoSolved=$r->RrLevSoSolved;
			$NumGroups=$r->RrLevGroups;
			$NumRow++;
			$key=$r->id.'-'.$r->RrPartSubTeam;
			if(!$r->RrGrSoSolved) {
				$GroupSoSolved=false;
			} else {
				$NumSO[$r->RrPartGroup]=1;
			}
			if($OldGroup!=$r->RrPartGroup) {
				if($EvRows) {
					$JSON['tables'][]=$EvRows;
				}
				$EvRows=[];
				$OldGroup=$r->RrPartGroup;
			}
			if(empty($EvRows['headers'])) {
				$EvRows['headers']=[
					'rank' => get_text('PositionShort'),
					'item' => get_text('Athlete'),
					'country' => get_text('Country'),
					'points' => get_text('TotalShort','Tournament'),
					'untie1' => get_text('TiebreakSystem-'.$r->RrLevTieBreakSystem,'RoundRobin'),
					'untie2' => '',
					'arrows' => get_text('TieArrows'),
					'closest' => get_text('Close2Center', 'Tournament'),
				];
				$EvRows['code']=$Event;
				$EvRows['level']=$Level;
				$EvRows['soSolved']=$r->RrGrSoSolved;
				$EvRows['group']=$r->RrPartGroup;
				$EvRows['name']=$r->EvEventName . ' (' . $Event . ')';

				$EvRows['rows']=[];

				$IsCtSo = false;
			}

			if($r->RrPartGroupTiesForSO+$r->RrPartGroupTiesForCT==0) {
				if($IsCtSo) {
					$EvRows['rows'][]=['div'];
				}
				$IsCtSo=false;
			} else {
				// It is a SO/CT
				if(!$IsCtSo) {
					$EvRows['rows'][]=['div'];
				}
				$IsCtSo=true;
			}

			$row=[
				'id' => $r->id,
				'subteam' => $r->RrPartSubTeam,
				'rank' => $r->RrPartGroupRank,
				'irm' => ($r->IrmId != 0 ? '&nbsp;'.$r->IrmType : ''),
				'class' => ($IsCtSo ? 'error' : ''),
			];

			if($AdvancedMode) {
				$row['field']=[
					'type' => 'i',
					'name' => 'R',
					'value' => ($R[$Event][$key] ?? $r->RrPartGroupRank),
				];
			} else if ($r->IrmId < 10) {
				//This part for DNF
				if ($r->RrPartGroupTiesForSO) {
					$row['field']=[
						'type' => 's',
						'name' => 'R',
						'value' => [],
					];
					$IsCtSo = true;
					for ($i = $r->RrPartGroupRankBefSO; $i < $r->RrPartGroupRankBefSO+$r->RrPartGroupTiesForSO; ++$i) {
						$row['field']['value'][]=['k'=>$i, 's'=>(($R[$Event][$key] ?? $r->RrPartGroupRank) == $i)];
					}
				} elseif ($r->RrPartGroupTiesForCT) {
					$row['field']=[
						'type' => 's',
						'name' => 'R',
						'value' => [],
					];
					$IsCtSo = true;
					for ($i = $r->RrPartGroupRankBefSO; $i < $r->RrPartGroupRankBefSO+$r->RrPartGroupTiesForCT; ++$i) {
						$row['field']['value'][]=['k'=>$i, 's'=>(($R[$Event][$key] ?? $r->RrPartGroupRank) == $i)];
					}
				} else {
					$row['field']=[
						'type' => 'h',
						'name' => 'R',
						'value' => $r->RrPartGroupRankBefSO,
					];
				}
			} else {
				$row['field']=[
					'type' => 'h',
					'name' => 'bSO',
					'value' => $r->RrPartGroupRankBefSO,
				];
			}
			$row['item']=$r->athlete;
			$row['country']=$r->country;
			$row['points']=$r->RrPartPoints;
			$row['untie1']=$r->RrPartTieBreaker;
			$row['untie2']=$r->RrPartTieBreaker2;
			$row['arrows']=[];
			$row['closest']='';

			if ($IsCtSo) {
				for ($j = 0; $j < 3; ++$j) {
					$ar['txt']=get_text('ShotOffShort','Tournament').' '.($j+1);
					$ar['arrows']=[];

					for ($i = 0; $i < $r->RrLevSO; ++$i) {
						$idx=($j*$r->RrLevSO)+$i;
						$ar['arrows'][]=[
							'type'=>'i',
							'name'=>'T',
							'index'=>$idx,
							'value'=>(strlen($r->RrPartGroupTieBreak) > $idx ? DecodeFromLetter($r->RrPartGroupTieBreak[$idx]) : ($T[$Event][$r->id][$idx] ?? '')),
						];
					}

					$row['arrows'][]=$ar;
				}
				$row['closest']=[
					'type'=>'c',
					'name'=>'C',
					'sel'=>(($C[$Event][$r->id] ?? $r->RrPartGroupTbClosest) ? 'checked="checked"' : ''),
				];
			}
			$EvRows['rows'][]=$row;
		}
		$JSON['tables'][]=$EvRows;

		if($GroupSoSolved and $NumGroups and $NumGroups==count($NumSO)) {
			// check if there are "best of level" selection
			$q=safe_r_SQL("select EvElim1 as MaxLevels
				from RoundRobinParticipants
				inner join Events on EvTeamEvent=RrPartTeam and EvTournament=RrPartTournament and EvCode=RrPartEvent
				where RrPartSourceLevel=$Level and RrPartSourceGroup=0 and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event));
			if($LEVELS=safe_fetch($q)) {
				if(!$LevelSoSolved) {
					calculateLevelRank($Team, $Event, $Level);
				}

				// gets the best ranked according to selection
				$SQL="select RrLevSoSolved, RrPartLevelRank, RrPartLevelRankBefSO, RrPartParticipant, RrPartSubTeam, 0 as RrPartGroup, RrPartPoints, RrPartTieBreaker, RrPartTieBreaker2, RrPartLevelTieBreak, RrPartLevelTbClosest, 
		            GroupTies, EvEventName, RrLevEnds, RrLevArrows, RrLevSO, MaxQualified, RrLevTieBreakSystem, RrLevTieBreakSystem2,
					coalesce(EnId, CoId, 0)  as id, coalesce(concat(upper(EnFirstName),' ', EnName), concat(CoCode, '/',RrPartSubTeam), '') as athlete, coalesce(EnCoCode, CoName,'') as country,
		            IrmType, IrmId
					from RoundRobinLevel
					inner join RoundRobinGroup on RrGrTournament=RrLevTournament and RrGrTeam=RrLevTeam and RrGrEvent=RrLevEvent and RrGrLevel=RrLevLevel
					inner join RoundRobinParticipants on RrPartTournament=RrGrTournament and RrPartTeam=RrGrTeam and RrPartEvent=RrGrEvent and RrPartLevel=RrGrLevel and RrPartGroup=RrGrGroup
					inner join Events on EvTournament=RrLevTournament and EvTeamEvent=RrLevTeam and EvCode=RrLevEvent
				    inner join IrmTypes on IrmId=RrPartIrmType
					inner join (
					    SELECT RrPartEvent sqyEvent, Count(*) as GroupTies, RrPartLevelRankBefSO as sqyRank, RrPartLevel as sqyLevel, MaxQualified
						FROM RoundRobinParticipants
					    inner join (
					        select RrPartTournament r2To, RrPartTeam r2Te, RrPartEvent r2Ev, RrPartSourceLevel r2Le, RrPartSourceRank r2Ra
					        from RoundRobinParticipants
					        where RrPartTournament = {$_SESSION['TourId']} and RrPartSourceGroup=0
					        ) as r2 on r2To=RrPartTournament and r2Te=RrPartTeam and r2Ev=RrPartEvent and r2Le=RrPartLevel and r2Ra=RrPartLevelRankBefSO
						inner join (
					        select max(RrPartSourceRank) as MaxQualified, RrPartTournament r3To, RrPartTeam r3Te, RrPartEvent r3Ev, RrPartSourceLevel r3Le
					        from RoundRobinParticipants
					        where RrPartTournament = {$_SESSION['TourId']} and RrPartSourceGroup=0
						    group by RrPartTournament, RrPartTeam, RrPartEvent, RrPartSourceLevel) as r3 on r3To=RrPartTournament and r3Te=RrPartTeam and r3Ev=RrPartEvent and r3Le=RrPartLevel
						
					    inner join IrmTypes on IrmId=RrPartIrmType
						WHERE RrPartTournament = {$_SESSION['TourId']}
						GROUP BY RrPartTournament, RrPartEvent, RrPartTeam, RrPartLevel, RrPartLevelRankBefSO
					) AS sqy ON sqyRank=RrPartLevelRankBefSO AND sqyEvent=RrPartEvent AND sqyLevel=RrPartLevel
				    left join (select EnTournament, EnId, EnFirstName, EnName, CoCode as EnCoCode, CoName as EnCoName from Entries inner join Countries on CoId=EnCountry and CoTournament=EnTournament where EnTournament={$_SESSION['TourId']}) Entries on EnTournament=RrPartTournament and EnId=RrPartParticipant and RrPartTeam=$Team
					left join Countries on CoTournament=RrLevTournament and CoId=RrPartParticipant and RrPartTeam=1
					where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event)." and RrLevLevel=$Level
					order by RrPartLevelRankBefSO, RrPartLevelRank";
				$q=safe_r_sql($SQL);
				$EvRows=[];
				$OldGroup=0;
				$NumRow=-1;
				$GroupSoSolved=true;
				while($r=safe_fetch($q)) {
					$NumRow++;
					$key=$r->id.'-'.$r->RrPartSubTeam;
					if(!$r->RrLevSoSolved) {
						$GroupSoSolved=false;
					}
					if($OldGroup!=$r->RrPartGroup) {
						if($EvRows) {
							$JSON['tables'][]=$EvRows;
						}
						$EvRows=[];
						$OldGroup=$r->RrPartGroup;
					}
					if(empty($EvRows['headers'])) {
						$EvRows['headers']=[
							'rank' => get_text('PositionShort'),
							'item' => get_text('Athlete'),
							'country' => get_text('Country'),
							'points' => get_text('TotalShort','Tournament'),
							'untie1' => get_text('TiebreakSystem-'.$r->RrLevTieBreakSystem,'RoundRobin'),
							'untie2' => '',
							'arrows' => get_text('TieArrows'),
							'closest' => get_text('Close2Center', 'Tournament'),
						];
						$EvRows['code']=$Event;
						$EvRows['level']=$Level;
						$EvRows['soSolved']=$r->RrLevSoSolved;
						$EvRows['group']=0;
						$EvRows['name']=$r->EvEventName . ' (' . $Event . ')';

						$EvRows['rows']=[];

						$IsCtSo = false;
					}

					if(!$r->GroupTies) {
						// no selection in next level for this rank
						continue;
					}
					if($r->GroupTies==1) {
						if($IsCtSo) {
							$EvRows['rows'][]=['div'];
						}
						$IsCtSo=false;
					} else {
						// check if this is a CT or SO
						// $s=safe_fetch($q);
						// repositions Recordset to the current row
						// safe_data_seek($q, $NumRow);

						// but for now all are treated as SO
						if(!$IsCtSo) {
							$EvRows['rows'][]=['div'];
						}
						$IsCtSo=true;
					}

					$row=[
						'id' => $r->id,
						'subteam' => $r->RrPartSubTeam,
						'rank' => $r->RrPartLevelRank,
						'irm' => ($r->IrmId != 0 ? '&nbsp;'.$r->IrmType : ''),
						'class' => ($IsCtSo ? 'error' : ''),
					];

					if($AdvancedMode) {
						$row['field']=[
							'type' => 'i',
							'name' => 'R',
							'value' => ($R[$Event][$key] ?? $r->RrPartLevelRank),
						];
					} else if ($r->IrmId < 10) {
						//This part for DNF
						if ($r->GroupTies>1 and $r->RrPartLevelRankBefSO+$r->GroupTies >= $r->MaxQualified) {
							$row['field']=[
								'type' => 's',
								'name' => 'R',
								'value' => [],
							];
							$IsCtSo = true;
							for ($i = $r->RrPartLevelRankBefSO; $i < $r->RrPartLevelRankBefSO+$r->GroupTies; ++$i) {
								$row['field']['value'][]=['k'=>$i, 's'=>(($R[$Event][$key] ?? $r->RrPartLevelRank) == $i)];
							}
						} else {
							$row['field']=[
								'type' => 'h',
								'name' => 'R',
								'value' => $r->RrPartLevelRankBefSO,
							];
						}
					} else {
						$row['field']=[
							'type' => 'h',
							'name' => 'bSO',
							'value' => $r->RrPartLevelRankBefSO,
						];
					}
					$row['item']=$r->athlete;
					$row['country']=$r->country;
					$row['points']=$r->RrPartPoints;
					$row['untie1']=$r->RrPartTieBreaker;
					$row['untie2']=$r->RrPartTieBreaker2;
					$row['arrows']=[];
					$row['closest']='';

					if ($IsCtSo) {
						for ($j = 0; $j < 3; ++$j) {
							$ar['txt']=get_text('ShotOffShort','Tournament').' '.($j+1);
							$ar['arrows']=[];

							for ($i = 0; $i < $r->RrLevSO; ++$i) {
								$idx=($j*$r->RrLevSO)+$i;
								$ar['arrows'][]=[
									'type'=>'i',
									'name'=>'T',
									'index'=>$idx,
									'value'=>(strlen($r->RrPartLevelTieBreak) > $idx ? DecodeFromLetter($r->RrPartLevelTieBreak[$idx]) : ($T[$Event][$r->id][$idx] ?? '')),
								];
							}

							$row['arrows'][]=$ar;
						}
						$row['closest']=[
							'type'=>'c',
							'name'=>'C',
							'sel'=>(($C[$Event][$r->id] ?? $r->RrPartLevelTbClosest) ? 'checked="checked"' : ''),
						];
					}
					$EvRows['rows'][]=$row;
				}
				$JSON['tables'][]=$EvRows;
				if($GroupSoSolved) {
					calculateFinalRank($Team,$Event,$Level);
					safe_w_sql("UPDATE RoundRobinLevel SET RrLevSoSolved=1 WHERE RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevLevel=$Level and RrLevEvent=" . StrSafe_DB($Event));
				}
			} else {
				// no other selection needs to be done, so updates the level SO also
				calculateFinalRank($Team,$Event,$Level);
				safe_w_sql("UPDATE RoundRobinLevel SET RrLevSoSolved=1 WHERE RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevLevel=$Level and RrLevEvent=" . StrSafe_DB($Event));
			}
		}
		break;
	case 'resetSO':
		$Event=($_REQUEST['event'] ?? '');
		$Level=($_REQUEST['level'] ?? -1);
		$Group=($_REQUEST['group'] ?? -1);

		if(!$Event or $Level==-1 or $Group==-1) {
			JsonOut($JSON);
		}

		if($Level==0) {
			require_once('Common/Fun_Sessions.inc.php');
			require_once('Common/Lib/Obj_RankFactory.php');
			require_once('Final/Fun_ChangePhase.inc.php');

			ResetShootoff($Event, $Team, 0);
			Obj_RankFactory::create('Abs'.($Team ? 'Team' : ''), array('tournament' => $_SESSION['TourId'], 'events' => $Event, 'dist' => 0))->calculate();
			if($Team) {
				// destroys the grid of all the events that need "handling"
				safe_w_sql("DELETE FROM TeamFinals WHERE TfTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfEvent = " . StrSafe_DB($Event));

				// Recreate Empty Grids
				safe_w_SQL("INSERT INTO TeamFinals (TfEvent, TfMatchNo, TfTournament, TfDateTime) " .
					"SELECT EvCode, GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . ", NOW() " .
					"FROM Events " .
					"INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0 " .
					"INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent=1 AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
	            WHERE EvCode=" . StrSafe_DB($Event));
			} else {
				// destroys the grid of all the events that need "handling"
				safe_w_sql("DELETE FROM Finals WHERE FinTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND FinEvent = " . StrSafe_DB($Event));

				// Recreate Empty Grids
				safe_w_SQL("INSERT INTO Finals (FinEvent, FinMatchNo, FinTournament, FinDateTime) " .
					"SELECT EvCode, GrMatchNo," . StrSafe_DB($_SESSION['TourId']) . ", NOW() " .
					"FROM Events " .
					"INNER JOIN Phases on PhId=EvFinalFirstPhase and (PhIndTeam & pow(2,EvTeamEvent))>0 " .
					"INNER JOIN Grids ON GrPhase<=greatest(PhId, PhLevel) AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " 
	            WHERE EvCode=" . StrSafe_DB($Event));
			}

			// reset all the following levels!
			// resets all the levels/groups assignments for this event...
			safe_w_sql("update RoundRobinParticipants set RrPartGroupRank=0, RrPartGroupRankBefSO=0, RrPartLevelRank=0, RrPartLevelRankBefSO=0, RrPartPoints=0, RrPartTieBreaker=0, RrPartTieBreaker2=0, RrPartParticipant=0, RrPartSubTeam=0,
				RrPartGroupTieBreak='', RrPartGroupTbClosest=0, RrPartGroupTbDecoded='', RrPartLevelTieBreak='', RrPartLevelTbClosest=0, RrPartLevelTbDecoded='',
				RrPartIrmType=0, RrPartGroupTiesForSO=0, RrPartGroupTiesForCT=0, RrPartDateTime=now(), RrPartLevelTiesForSO=0, RrPartLevelTiesForCT=0
				where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=$Team and RrPartEvent=".StrSafe_DB($Event));
			safe_w_sql("update RoundRobinMatches set RrMatchAthlete=0, RrMatchSubTeam=0, RrMatchRank=0, RrMatchScore=0, RrMatchSetScore=0, RrMatchSetPoints='', RrMatchSetPointsByEnd='',
				RrMatchWinnerSet=0, RrMatchTie=0, RrMatchArrowstring='', RrMatchTiebreak='', RrMatchTbClosest=0, RrMatchTbDecoded='', RrMatchArrowPosition='', RrMatchTiePosition='', RrMatchWinLose=0,
				RrMatchFinalRank=0, RrMatchDateTime=0, RrMatchSyncro=0, RrMatchLive=0, RrMatchStatus=0, RrMatchShootFirst=0, RrMatchVxF=0, RrMatchConfirmed=0, RrMatchNotes='',
				RrMatchRecordBitmap=0, RrMatchIrmType=0, RrMatchCoach=0, RrMatchRoundPoints=0
				where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event));
			safe_w_sql("update RoundRobinGroup set RrGrSoSolved=0 
						where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=".StrSafe_DB($Event));
			safe_w_sql("update RoundRobinLevel set RrLevSoSolved=0 
						where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=".StrSafe_DB($Event));

			$JSON['error']=0;
			JsonOut($JSON);
		}

		// reset all groups and levels after this
		RobinResetSO($Team, $Event, $Level, $Group);
		$JSON['error']=0;
		JsonOut($JSON);

		break;
	default:
		JsonOut($JSON);
}

JsonOut($JSON);