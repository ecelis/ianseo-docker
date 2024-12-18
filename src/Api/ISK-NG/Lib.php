<?php

function readJsonData($Data) {
	// updates the lastseen!
	safe_w_SQL("update IskDevices set IskDvLastSeen='".date('Y-m-d H:i:s')."' where IskDvDevice=".StrSafe_DB($Data->device));

    if($Data->IskDvAppVersion != ISK_NG_LITE_CODE) {
        $IskGroups = getModuleParameter('ISK-NG', 'Sequence', array(), $Data->ToId);
        // the setup is already stored as a JSON in IskDvSetup!
        if (empty($IskGroups[$Data->IskDvGroup])) {
            // wrong, reset Device in the caller page, arrows eventually sent by initial sendall are lost
            return 'RESET';
        }
        $IskSequence = $IskGroups[$Data->IskDvGroup];

        if ($Data->type != $IskSequence['type']) {
            // wrong, reset Device in the caller page
            return 'RESET';
        }
    }

	$Error=false;
    // sets as true the global check error, it will turn to false if at least one distance is OK
    $GlobalErrorFlag=true;
    $GlobalErrorLast='';
    switch($Data->type) {
		// case 'X':
		// 	return applyScore($Data, $Data->ToId);
		// 	break;
		case 'Q':
            if($Data->IskDvAppVersion != ISK_NG_LITE_CODE) {
                // this is only for the qualification and for the "update" message
                if (empty($Data->archers) and empty($Data->refKey) and !in_array($Data->distance, $IskSequence['distance'])) {
                    // wrong, reset Device in the caller page
                    return 'RESET';
                }
                // NO BREAK AS Q and E are the same
                // case 'E1':
                // case 'E2':
                // from here everything is the same for Q and E1/E2
                if ($Data->session != $IskSequence['session']) {
                    // wrong, reset Device in the caller page
                    return 'RECONFIGURE';
                }
            }

            if(!empty($Data->archers)) {
                // device is sending everything, including all distances! so we need to build the object one by one
                $UpdatedEntries=array();
                $UpdatedEntriesDistances=array();
				foreach($Data->archers as $archer) {
					$item=(object) [
						"device" => $Data->device,
						"type"=> $Data->type,
						"subtype"=>($IskSequence['subtype']??''),
			            "session" => $Data->session,
			            "distance"=> 0,
			            "key" => $archer->refKey,
			            "refKey" => $archer->refKey,
			            "arrowstring" => "",
			            "matchno" => 0,
			            "event" => "",
			            "team" => 0,
						"soClosest"=>'0',
						"ToId" => $Data->ToId,
						"IskDvGroup" => $Data->IskDvGroup,
						"IskDvTarget" => $Data->IskDvTarget,
						"IskDvSchedKey" => $Data->IskDvSchedKey,
                        "IskDvAppVersion" => $Data->IskDvAppVersion
						];

                    foreach($archer->scoring as $distance) {
						$item->distance=$distance->distance;
						$item->arrowstring=$distance->arrowstring;
						if(($Error=applyScore($item, $Data->ToId, 1, $UpdatedEntries))=='OK') {
                            $GlobalErrorFlag=false;
						} else {
                            $GlobalErrorLast=$Error;
                        }
						foreach($UpdatedEntries as $Type => $EnIds) {
							if(empty($UpdatedEntriesDistances[$distance->distance][$Type])) {
								$UpdatedEntriesDistances[$distance->distance][$Type]=[];
							}
							$UpdatedEntriesDistances[$distance->distance][$Type]=$UpdatedEntriesDistances[$distance->distance][$Type]+$EnIds;
						}
					}
				}
                if($GlobalErrorFlag) {
                    return $GlobalErrorLast;
                }
				foreach($UpdatedEntriesDistances as $D => $Types) {
					CalculateRanks($Types, $Data->ToId, $D);
				}
				return 'OK';
			} else {
				$Data->matchno=0;
				$Data->event='';
				$Data->team=0;
                $Data->key=$Data->refKey;
				$Data->subtype=($IskSequence['subtype']??'');
				$Data->soClosest=(string) ($Data->soClosest??'0');
				return applyScore($Data, $Data->ToId, 0);
			}
			break;
		// case 'I':
		// case 'T':
		case 'M':
			if(!empty($Data->archers)) {
				// device is sending everything!
				$item=(object) [
					"device" => $Data->device,
					"type"=> $Data->type,
					"subtype"=>'',
					"session" => $Data->session,
					"distance"=> 1,
					"key" => '',
					"ToId" => $Data->ToId,
					"IskDvGroup" => $Data->IskDvGroup,
					"IskDvTarget" => $Data->IskDvTarget,
					"IskDvSchedKey" => $Data->IskDvSchedKey,
					"IskDvAppVersion" => $Data->IskDvAppVersion,
				];
				foreach($Data->archers as $archer) {
					$bits=explode('|', $archer->refKey);
					$item->subtype=(count($bits)>3 ? 'R' : ($bits[0] ? 'T' : 'I'));
					$item->team=$bits[0];
					$item->event=$bits[1];
					$item->matchno=end($bits);
					$item->refKey=$archer->refKey;
					$item->arrowstring='';

					foreach($archer->scoring as $distance) {
                        $item->soClosest=(string) ($distance->soClosest??'0');
						$item->distance=$distance->distance;
						$item->arrowstring=$distance->arrowstring;
						if(($Error=applyScore($item, $Data->ToId))=='OK') {
                            $GlobalErrorFlag=false;
                        } else {
                            $GlobalErrorLast=$Error;
						}
					}
				}
                if($GlobalErrorFlag) {
                    return $GlobalErrorLast;
                }
                return 'OK';
			} else {
				$bits=explode('|', $Data->refKey);
				$Data->subtype=(count($bits)>3 ? 'R' : ($bits[0] ? 'T' : 'I'));
				$Data->key='';
				$Data->matchno=end($bits);
				$Data->event=$bits[1];
				$Data->team=$bits[0];
				$Data->soClosest=(string) ($Data->soClosest??'0');
				return applyScore($Data, $Data->ToId);
			}
			break;
	}
}

function applyScore($Data, $ToId, $IsSendAll=0, &$UpdatedEntries=[]) {
	// check if the device is in a group and returns the targets the device is aloud to score!

	// first check if this device is allowed to score...
	// based on session, target and state of the device
	// and fetches ends and arrows/end just to be sure...

	$TargetNo = $Data->IskDvTarget;
	$Grouping=getModuleParameter('ISK-NG', 'Grouping', null, $ToId);

	if($Grouping[$Data->IskDvGroup] ?? 0) {
		$TargetNo=getGroupedTargets($Data->IskDvTarget, $ToId, $Data->IskDvGroup??0);
	}

	switch($Data->type) {
		// case 'X':
			// $Grouping=getModuleParameter('ISK-NG', 'Grouping', null, $ToId);
			//
			// if($Grouping[$Data->IskDvGroup] ?? 0) {
			// 	$TargetNo=getTargetsFromGroup(intval(substr($Data->key, 0, -1)), $ToId);
			// } else {
			// 	if(isset($Data->key)) {
			// 		$TargetNo = intval(substr($Data->key, 0, -1));
			// 	} else {
			// 		foreach ($Data->archers as $k=>$v) {
			// 			foreach ( range ( 0, 4) as $end ) {
			// 				$start = $end * 3;
			// 				$arr = 3;
			// 				$IskArrowstring = substr ( $v->arrowstring, $start, $arr );
			// 				if(trim($IskArrowstring)) {
			// 					$SQL = array ();
			// 					$SQL [] = "IskDtTournament=$ToId";
			// 					if(isset($v->matchno)) {
			// 						$SQL [] = "IskDtMatchNo=" . StrSafe_DB($v->matchno);
			// 					}
			// 					if(isset($v->event)) {
			// 						$SQL [] = "IskDtEvent=" . StrSafe_DB($v->event);
			// 					}
			// 					if(isset($v->team)) {
			// 						$SQL [] = "IskDtTeamInd=" . StrSafe_DB($v->team);
			// 					}
			// 					$SQL [] = "IskDtType=" . StrSafe_DB ( $Data->type );
			// 					$SQL [] = "IskDtTargetNo=" . StrSafe_DB ( $v->key );
			// 					$SQL [] = "IskDtDistance=" . ($Data->distance);
			// 					$SQL [] = "IskDtSession=" . StrSafe_DB($Data->session);
			// 					$SQL [] = "IskDtEndNo=" . ($end + 1);
			// 					$SQL [] = "IskDtDevice=" . StrSafe_DB ( $Data->device );
			// 					$SQL [] = "IskDtArrowstring=" . StrSafe_DB ( $IskArrowstring );
			// 					$SQL [] = "IskDtUpdate=" . StrSafe_DB ( date ( 'Y-m-d H:i:s' ) );
			//
			// 					$SQL = "INSERT INTO IskData set " . implode ( ',', $SQL ) . "
			// 				        ON DUPLICATE KEY UPDATE " . implode ( ',', $SQL );
			// 					safe_w_SQL ( $SQL );
			// 				}
			// 			}
			// 		}
			// 		return '';
			// 	}
			// }
			// $SQL="select 4 as DiEnds, 3 as DiArrows, 1 as DiSO, '' as Arrowstring, IskDvGroup, IskDvSchedKey, 0 as StopScore
			// 	from IskDevices
			// 	where IskDvTournament=$ToId and IskDvDevice='{$Data->device}' and IskDvProActive=1";
			// break;
		case 'Q':
			$SQL="select DiEnds, DiArrows, 1 as DiSO, QuD{$Data->distance}Arrowstring as Arrowstring, IskDvGroup, IskDvSchedKey, 
					QuConfirm & ".pow(2, $Data->distance).">0 as StopScore, 0 as IsClosest,
			        concat_ws('|','Q', QuSession, DiDistance) as LockKey
				from Entries
				inner join Qualifications on EnId=QuId and QuTargetNo='{$Data->refKey}'
				INNER JOIN DistanceInformation ON EnTournament=DiTournament and DiSession=QuSession and DiDistance={$Data->distance} and DiType='Q'
				INNER JOIN IskDevices on IskDvTournament=EnTournament and IskDvDevice='{$Data->device}' and IskDvProActive=1 and IskDvTarget in ({$TargetNo})
				where EnTournament=$ToId and QuSession={$Data->session}";
			break;
		// case 'E1':
		// case 'E2':
			// $Phase=$Data->type[1]-1;
			//
			// //$TargetNo=$Data->session.sprintf("%03s", intval($Data->key));
			// $TargetNo=intval($Data->key);
			//
			// $SQL="select if(ElElimPhase=0, EvE1Ends, EvE2Ends) as DiEnds, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) as DiArrows, 1 as DiSO,
			// ElArrowstring as Arrowstring, IskDvGroup, IskDvSchedKey, ElConfirm as StopScore, concat_ws('|','E', ElElimPhase, ElEventCode) as LockKey
			// 	from Eliminations
			// 	inner join Events on EvCode=ElEventCode and EvTournament=ElTournament and EvTeamEvent=0
			// 	INNER JOIN IskDevices on IskDvTournament=ElTournament and IskDvDevice='{$Data->device}' and IskDvProActive=1 and IskDvTarget in ({$TargetNo})
			// 	where ElTournament=$ToId and ElElimPhase=$Phase and ElSession={$Data->session} and ElTargetNo='{$Data->key}'
			// 	";
			// break;
		case 'M':
			$match1=($Data->matchno%2 ? $Data->matchno-1 : $Data->matchno);
			$match2=($Data->matchno%2 ? $Data->matchno : $Data->matchno+1);
			switch($Data->subtype) {
				case 'I':
					$SQL="select @ArBit:=(EvMatchArrowsNo & GrBitPhase), 
						if(@ArBit=0, EvFinArrows, EvElimArrows) DiArrows, if(@ArBit=0, EvFinEnds, EvElimEnds) DiEnds, if(@ArBit=0, EvFinSO, EvElimSO) DiSO, 
       					IskDvGroup, IskDvSchedKey, StopScore, concat_ws('|','I',GrPhase,EvCode) as LockKey,
       					if($Data->matchno=FsMatchNo1, Closest1, Closest2) as IsClosest,
						concat(rpad(if($Data->matchno=FsMatchNo1, Arrowstring1, Arrowstring2), if(@ArBit=0, EvFinEnds, EvElimEnds)*if(@ArBit=0, EvFinArrows, EvElimArrows), ' '), if($Data->matchno=FsMatchNo1, TieBreak1, TieBreak2)) Arrowstring, if($Data->matchno=FsMatchNo1, TieBreak1, TieBreak2) TieBreak
					from (select FinConfirmed as StopScore, FinArrowstring Arrowstring1, FinTieBreak TieBreak1, FsTarget+0 Target1, substr(FsLetter, length(FsTarget)+1, 1) Letter1, FsLetter FsLetter1, FsMatchNo FsMatchNo1, FsEvent FsEvent1, FinWinLose as Win1, FinTbClosest as Closest1
						from FinSchedule
						inner join Finals on FsEvent=FinEvent and FinTournament=$ToId and FsMatchNo=FinMatchNo
						where FSMatchNo%2=0 and FsTournament=$ToId and FsTarget>'' and FsEvent='$Data->event' and FsTeamEvent=0 and FsMatchNo=$match1) tgt1
					inner join (select FinArrowstring Arrowstring2, FinTieBreak TieBreak2, FsTarget+0 Target2, substr(FsLetter, length(FsTarget)+1, 1) Letter2, FsLetter FsLetter2, FsMatchNo FsMatchNo2, FsEvent FsEvent2, FinWinLose as Win2, FinTbClosest as Closest2
						from FinSchedule
						inner join Finals on FsEvent=FinEvent and FsTournament=FinTournament and FsMatchNo=FinMatchNo
						where FsTournament=$ToId and FsTarget>'' and FsEvent='$Data->event' and FsTeamEvent=0 and FsMatchNo=$match2) tgt2
						on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
					inner join Events on FsEvent1=EvCode and EvTeamEvent=0 and EvTournament=$ToId
					inner join Grids on FsMatchNo1=GrMatchNo
					INNER JOIN IskDevices on IskDvTournament=$ToId and IskDvDevice='{$Data->device}'
					where Target1+0 in ($TargetNo)
					";
					break;
				case 'T':
					$SQL="select @ArBit:=(EvMatchArrowsNo & GrBitPhase), 
						if(@ArBit=0, EvFinArrows, EvElimArrows) DiArrows, if(@ArBit=0, EvFinEnds, EvElimEnds) DiEnds, if(@ArBit=0, EvFinSO, EvElimSO) DiSO, 
       					IskDvGroup, IskDvSchedKey, StopScore, concat_ws('|','T',GrPhase,EvCode) as LockKey,
       					if($Data->matchno=FsMatchNo1, Closest1, Closest2) as IsClosest,
						concat(rpad(if($Data->matchno=FsMatchNo1, Arrowstring1, Arrowstring2), if(@ArBit=0, EvFinEnds, EvElimEnds)*if(@ArBit=0, EvFinArrows, EvElimArrows), ' '), if($Data->matchno=FsMatchNo1, TieBreak1, TieBreak2)) Arrowstring, if($Data->matchno=FsMatchNo1, TieBreak1, TieBreak2) TieBreak
						from (select TfConfirmed as StopScore, 
			            		TfArrowstring Arrowstring1, TfTieBreak TieBreak1,
								FsTarget+0 Target1,
								substr(FsLetter, length(FsTarget)+1, 1) Letter1,
								FsLetter FsLetter1, FsMatchNo FsMatchNo1, FsEvent FsEvent1, TfWinLose as Win1, TfTbClosest as Closest1
							from FinSchedule
							inner join TeamFinals on FsEvent=TfEvent and TfTournament=$ToId and FsMatchNo=TfMatchNo
							where FsTournament=$ToId and FsTarget>'' and FsEvent='$Data->event' and FsTeamEvent=1 and FsMatchNo=$match1) tgt1
						inner join (select 
			                	TfArrowstring Arrowstring2, TfTieBreak TieBreak2, 
			                	FsTarget+0 Target2, 
			                	substr(FsLetter, length(FsTarget)+1, 1) Letter2, 
			                	FsLetter FsLetter2, FsMatchNo FsMatchNo2, FsEvent FsEvent2, TfWinLose as Win2, TfTbClosest as Closest2
							from FinSchedule
							inner join TeamFinals on FsEvent=TfEvent and TfTournament=$ToId and FsMatchNo=TfMatchNo
							where FsTournament=$ToId and FsTarget>'' and FsEvent='$Data->event' and FsTeamEvent=1 and FsMatchNo=$match2) tgt2
							on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
						inner join Events on FsEvent1=EvCode and EvTeamEvent=1 and EvTournament=$ToId
						inner join Grids on FsMatchNo1=GrMatchNo
						INNER JOIN IskDevices on IskDvTournament=$ToId and IskDvDevice='{$Data->device}'
						where Target1+0 in ($TargetNo)
						";
					break;
				case 'R':
					list($Team,$Event,$Level,$Group,$Round,$dm)=explode('|', $Data->refKey);
					// need to rewrite the matchno to be cohrerent
					$Data->matchno=$dm+100*$Round+10000*$Group+1000000*$Level;
					$m1=($dm%2 ? $dm-1 : $dm);
					$m2=$m1+1;
					$MainFilter="RrMatchTournament=$ToId and RrMatchEvent='$Data->event' and RrMatchTeam=$Data->team and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round ";
					$SQL="select IskDvGroup, IskDvSchedKey, StopScore, LockKey,
							RrLevArrows as DiArrows, RrLevEnds as DiEnds, RrLevSO as DiSO,
	                        concat(rpad(if($dm=FsMatchNo1, Arrowstring1, Arrowstring2), RrLevEnds*RrLevArrows, ' '), if($dm=FsMatchNo1, TieBreak1, TieBreak2)) Arrowstring, if($dm=FsMatchNo1, TieBreak1, TieBreak2) TieBreak
						from (
						    select RrMatchConfirmed as StopScore, concat_ws('|','R',RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchEvent) as LockKey,
			                    RrMatchArrowstring Arrowstring1, RrMatchTiebreak TieBreak1,
								RrMatchTarget+0 Target1,
								'A' Letter1,
								'A' FsLetter1, RrMatchMatchNo FsMatchNo1, RrMatchEvent FsEvent1, RrMatchWinLose as Win1, RrMatchTbClosest as Closest1
							from RoundRobinMatches
							where $MainFilter and RrMatchMatchNo=$m1
						    ) tgt1
						inner join (
						    select 			                	
						        RrMatchArrowstring Arrowstring2, RrMatchTiebreak TieBreak2, 
			                    RrMatchTarget+0 Target2, 
			                    'B' Letter2, 
			                    'B' FsLetter2, RrMatchMatchNo FsMatchNo2, RrMatchEvent FsEvent2, RrMatchWinLose as Win2, RrMatchTbClosest as Closest2
							from RoundRobinMatches
							where $MainFilter and RrMatchMatchNo=$m2
						    ) tgt2 on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
						inner join Events on FsEvent1=EvCode and EvTeamEvent=$Data->team and EvTournament=$ToId
					    inner join RoundRobinLevel on RrLevTournament=$ToId and RrLevEvent=EvCode and RrLevTeam=$Data->team and RrLevLevel=$Level
						INNER JOIN IskDevices on IskDvTournament=$ToId and IskDvDevice='{$Data->device}'
						where Target1+0 in ($TargetNo) or Target2+0 in ($TargetNo)
						";
					break;
				default:
					return('NOT IMPLEMENTED YET');
			}
			break;
		default:
			return('NOT IMPLEMENTED YET');
	}

    $AutoImport=getModuleParameter('ISK-NG', 'AutoImport', Array(), $ToId);
	$LockedSessions=getModuleParameter('ISK-NG', 'LockedSessions', array(), $ToId);

	$q=safe_r_sql($SQL);
	if($r=safe_fetch( $q )) {
		if($r->StopScore) {
			return 'This scorecard has already been validated';
		}
		if(in_array($r->LockKey, $LockedSessions)) {
			return 'This session is locked!';
		}
		$Options = array(
			'TourId' => $ToId,
			'dist' => $Data->distance,
			'end' => 0,
			'ses' => ($Data->IskDvAppVersion != ISK_NG_LITE_CODE ? $Data->IskDvSchedKey : substr($Data->key, 0,1)),
			'target' => intval(substr($Data->key, -4, 3)),
			'group' => $r->IskDvGroup,
			'type' => $Data->type,
			'subtype' => $Data->subtype,
			'ClDivIndCalc' => 0,
			'ClDivTeamCalc' => 0,
			'FinIndCalc' => 0,
			'FinTeamCalc' => 0,
		);
		if($Data->type=='M') {
			$Options['matchno']=$Data->matchno.','.($Data->matchno%2 ? $Data->matchno-1 : $Data->matchno+1);
			$Options['event']=$Data->event;
			if($Data->IskDvAppVersion == ISK_NG_LITE_CODE) {
				if($Data->subtype!='R') {
					$Options['ses'] = substr($Data->IskDvSchedKey, 1);
				} else {
					$Options['ses'] = $Data->IskDvSchedKey;
				}
			}
		}

		$maxNumEnd = ($r->DiEnds-1);
		if($Data->type =='M') {
			if(($NumSO=ceil((strlen($Data->arrowstring) - ($r->DiEnds * $r->DiArrows)) / $r->DiSO)) > 0) {
				$maxNumEnd += $NumSO;
			}
		}
		$Errors=[];

		foreach ( range ( 0, $maxNumEnd) as $end ) {
			// $Options['end'] = ($Data->type == 'Q' ? 0 : $end + 1);
			$start = $end * $r->DiArrows;
			$arr = $r->DiArrows;
			if($end >= $r->DiEnds){
				$start = ($r->DiEnds*$r->DiArrows)+($end-$r->DiEnds)*$r->DiSO;
				$arr = $r->DiSO;
			}
			$DbArrowstring = substr ( $r->Arrowstring, $start, $arr );
			$IskArrowstring = substr ( $Data->arrowstring, $start, $arr );
			if(trim($IskArrowstring)) {
				$SQL = array ();
				$SQL [] = "IskDtTournament=$ToId";
				$SQL [] = "IskDtIsClosest=".intval($end==$maxNumEnd and $Data->soClosest);
				$SQL [] = "IskDtType=" . StrSafe_DB ( $Data->type );
				$SQL [] = "IskDtTargetNo=" . StrSafe_DB ( $Data->key );
				$SQL [] = "IskDtDistance=" . ($Data->distance);
				$SQL [] = "IskDtSession=" . StrSafe_DB($Data->session);
				$SQL [] = "IskDtEndNo=" . ($end + 1);
				$SQL [] = "IskDtDevice=" . StrSafe_DB ( $Data->device );
				if(isset($Data->matchno)) {
					$SQL [] = "IskDtMatchNo=" . StrSafe_DB($Data->matchno);
				}
				if(isset($Data->event)) {
					$SQL [] = "IskDtEvent=" . StrSafe_DB($Data->event);
				}
				if(isset($Data->team)) {
					$SQL [] = "IskDtTeamInd=" . StrSafe_DB($Data->team);
				}
				if ($DbArrowstring != $IskArrowstring or ($end==$maxNumEnd and $r->IsClosest!=$Data->soClosest)) {
					$SQL [] = "IskDtArrowstring=" . StrSafe_DB ( $IskArrowstring );
					$SQL [] = "IskDtUpdate=" . StrSafe_DB ( date ( 'Y-m-d H:i:s' ) );

					$SQL = "INSERT INTO IskData set " . implode ( ',', $SQL ) . " ON DUPLICATE KEY UPDATE " . implode ( ',', $SQL );
					safe_w_SQL ($SQL);
				} else {
					safe_w_sql("delete from IskData where ".implode(' AND ', $SQL));
				}
			}
		}
        if($Data->IskDvAppVersion == ISK_NG_LITE_CODE OR (!empty($AutoImport) AND !empty($AutoImport[$r->IskDvGroup]))) {
            if($Data->type=='Q') {
                $Options['ClDivIndCalc']=getModuleParameter('ISK-NG','CalcClDivInd',0, $ToId);
                $Options['ClDivTeamCalc']=getModuleParameter('ISK-NG','CalcClDivTeam',0, $ToId);
                $Options['FinIndCalc']=getModuleParameter('ISK-NG','CalcFinInd',0, $ToId);
                $Options['FinTeamCalc']=getModuleParameter('ISK-NG','CalcFinTeam',0, $ToId);
            }
            if($err=DoImportData($Options, $IsSendAll, $UpdatedEntries)) {
                $Errors[]=$err;
            }
        }
		if($Errors) {
			return implode('; ', $Errors);
		}
		return 'OK';
	} else {
		return 'RECONFIGURE';
	}
}

function DoImportData($Options=array(), $IsSendall=0, &$UpdatedEntries=[]) {
	require_once(dirname(dirname(__FILE__)).'/config.php');
	require_once('Qualification/Fun_Qualification.local.inc.php');
	require_once('Final/Fun_MatchTotal.inc.php');

	$Error=1;

	if(empty($Options['TourId'])) {
		if(!CheckTourSession()) {
			return 'no competition!';
		}
		$CompId = $_SESSION["TourId"];
	} else {
		$CompId = $Options['TourId'];
	}

    //Get app mode
    $iskMode = getModuleParameter('ISK', 'Mode', '', $CompId);
    $isLiteMode = ($iskMode == ISK_NG_LITE);

	$Group=(empty($Options['group']) ? 0 : (empty(intval($Options['group'])) ? intval(substr($Options['group'], 6)) : intval($Options['group'])));
	$IskSequence=array('IskKey'=>'', "type"=>'', "subtype"=>'', "session"=>'', "distance"=>array(), "maxdist"=>'', "end"=>'');
	$AllSequences=getModuleParameter('ISK-NG', 'Sequence', [$IskSequence], $CompId);
	if($isLiteMode) {
        $IskSequence['IskKey'] = $Options['ses'];
        $IskSequence['type'] = $Options['type'];
        $IskSequence['subtype'] = $Options['subtype'];
        $IskSequence['session'] = substr($Options['ses'],-18);
        $IskSequence['distance'][] = $Options['dist'];
    } else if(!empty($AllSequences[$Group])) {
		$IskSequence=$AllSequences[$Group];
	}

	if(!$IskSequence['type']) {
		return 'missing sequence type!';
	}

	// sets the operational key of the group
	// $Key=$IskSequence['type'].($IskSequence['subtype']??'').$IskSequence['maxdist'].$IskSequence['session'];
	$Key=$IskSequence['IskKey'];

	//$Sequence=$Options['ses'];
	//$Dist=intval($Options['dist']);
	$End=intval($Options['end']);
	$Filtre='';

	if(!empty($Options['target'])) {
		$Filtre=' AND substr(IskDtTargetNo, -4, 3)+0 = ' . intval($Options['target']);
	}

	switch($IskSequence['type']) {
		case 'Q':
			// check the distance is in the allowed distances
			if(!empty($Options['target'])) {
				$Filtre=' AND QuTarget = ' . intval($Options['target']);
			}

			if(empty($Options['dist']) or (!$isLiteMode and !in_array($Options['dist'], $IskSequence['distance']))) {
				return 'Incorrect Distance Setting';
			}
			$qSes=$IskSequence['session'];
			if(!empty($Options['Category'])) {
                if (is_array($Options['Category'])) {
                    $Filtre = " AND concat(EnDivision,EnClass) in ('".implode("','",$Options['Category'])."')";
                } else if(preg_match('/^[a-z0-9_.-]+$/sim', $Options['Category'])) {
                    $Filtre = " AND concat(EnDivision,EnClass)='{$Options['Category']}'";
                }
			}
			$SQL="SELECT QuId, QuSession, QuTargetNo, QuD{$Options['dist']}Arrowstring as Arrowstring, DIDistance, DIEnds, DIArrows, DiScoringEnds,
                    IF(TfGoldsChars{$Options['dist']}='',IF(TfGoldsChars='',ToGoldsChars,TfGoldsChars),TfGoldsChars{$Options['dist']}) as GoldsChars, 
                    IF(TfXNineChars{$Options['dist']}='',IF(TfXNineChars='',ToXNineChars,TfXNineChars),TfXNineChars{$Options['dist']}) as XNineChars,
                    EnIndClEvent, EnTeamClEvent, EnIndFEvent, EnTeamFEvent+EnTeamMixEvent as EnTeamFinals,
                    group_concat(concat_ws(':', IskDtEndNo, IskDtArrowstring) separator '|') as IskArrowstring,
                    ToElabTeam!=127 as MakeTeams, ToLocRule, QuConfirm & ".pow(2, $Options['dist']).">0 as StopScore
                FROM Qualifications
				INNER JOIN Entries ON QuId=EnId and EnTournament={$CompId}
				INNER JOIN Tournament ON ToId=EnTournament
				INNER JOIN DistanceInformation ON DITournament=EnTournament AND DISession=QuSession AND DIDistance={$Options['dist']} AND DIType='Q'
                INNER JOIN TargetFaces on TfId=EnTargetFace and TfTournament=EnTournament
				INNER JOIN IskData ON iskDtTournament=EnTournament AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='Q' AND IskDtTargetNo=QuTargetNo AND IskDtDistance={$Options['dist']} ".($End ? "AND IskDtEndNo={$End}" : '')."
					$Filtre
				INNER JOIN IskDevices on IskDvTournament=IskDtTournament and IskDvProActive>0 and IskDvDevice=IskDtDevice" . ($isLiteMode ? "" : " and IskDvSchedKey=".StrSafe_DB($Key))." and IskDvGroup=$Group
				WHERE QuSession={$qSes}
				".(isset($Options['filterGroup']) ? ' AND IskDvGroup = ' . intval($Options['filterGroup']) : "")."
				group by IskDtTournament, IskDtMatchNo, IskDtEvent, IskDtTeamInd, IskDtType, IskDtTargetNo, IskDtDistance, IskDtSession";
			$updated=false;
			$ToRank=array(
				'RnkIndCat'=>[],
				'RnkIndFin'=>[],
				'RnkTeamCat'=>[],
				'RnkTeamFin'=>[],
				);
			$q=safe_r_sql($SQL);
			while($r=safe_fetch($q)) {
                if($r->StopScore) {
                    // scorecard validated, do not accept anything: deletes the data and skip to next record
                    $Update = "DELETE FROM IskData
                        WHERE IskDtTournament={$CompId} AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='Q'
                        AND IskDtTargetNo='{$r->QuTargetNo}' AND IskDtDistance={$Options['dist']}";
                    safe_w_SQL($Update);
                    continue;
                }
				$arrowString = str_pad($r->Arrowstring,$r->DIArrows*$r->DIEnds);
				foreach(explode('|', $r->IskArrowstring) as $IskEnds) {
					list($IskEnd,$IskString)=explode(':', $IskEnds);
					for($i=0; $i<$r->DIArrows; $i++){
						if(isset($IskString[$i]) and $IskString[$i]!=' '){
							$arrowString[($IskEnd-1)*$r->DIArrows+$i]=$IskString[$i];
						}
					}
				}
				$Score=0;
				$Gold=0;
				$XNine=0;
				list($Score,$Gold,$XNine)=ValutaArrowStringGX($arrowString,$r->GoldsChars,$r->XNineChars);
				$Hits=strlen(str_replace(' ', '', $arrowString));

				$Update = "UPDATE Qualifications SET
					QuD{$Options['dist']}Score={$Score}, QuD{$Options['dist']}Gold={$Gold}, QuD{$Options['dist']}Xnine={$XNine}, QuD{$Options['dist']}ArrowString='{$arrowString}', QuD{$Options['dist']}Hits={$Hits},
					QuScore=QuD1Score+QuD2Score+QuD3Score+QuD4Score+QuD5Score+QuD6Score+QuD7Score+QuD8Score,
					QuGold=QuD1Gold+QuD2Gold+QuD3Gold+QuD4Gold+QuD5Gold+QuD6Gold+QuD7Gold+QuD8Gold,
					QuXnine=QuD1Xnine+QuD2Xnine+QuD3Xnine+QuD4Xnine+QuD5Xnine+QuD6Xnine+QuD7Xnine+QuD8Xnine,
					QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits,
					QuTimestamp=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
					WHERE QuId={$r->QuId}";
				safe_w_SQL($Update);
				if(safe_w_affected_rows()) {
                    if($r->ToLocRule=='LANC') {
                        CalculateDropWeight($r->QuId, $r->GoldsChars);
                    }
					$FullDistance=(strlen(str_replace(' ','', $arrowString))>=$r->DIArrows*($r->DiScoringEnds?:$r->DIEnds));
					if($r->EnIndClEvent and (empty($Options['ClDivIndCalc']) or ($Options['ClDivIndCalc']==1 and $FullDistance))) {
						$ToRank['RnkIndCat'][]=$r->QuId;
					}
					if($r->EnIndFEvent and (empty($Options['FinIndCalc']) or ($Options['FinIndCalc']==1 and $FullDistance))) {
						$ToRank['RnkIndFin'][]=$r->QuId;
					}
					if($r->MakeTeams and $r->EnTeamClEvent and (empty($Options['ClDivTeamCalc']) or ($Options['ClDivTeamCalc']==1 and $FullDistance))) {
						$ToRank['RnkTeamCat'][]=$r->QuId;
					}
					if($r->MakeTeams and $r->EnTeamFinals and (empty($Options['FinTeamCalc']) or ($Options['FinTeamCalc']==1 and $FullDistance))) {
						$ToRank['RnkTeamFin'][]=$r->QuId;
					}
					$updated = true;
				}
				$Update = "DELETE FROM IskData
					WHERE IskDtTournament={$CompId} AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='Q'
					AND IskDtTargetNo='{$r->QuTargetNo}' AND IskDtDistance={$Options['dist']}".($End ? " AND IskDtEndNo={$End}" : "");
				safe_w_SQL($Update);

				// run Jack Event
				runJack("QualArrUpdate", $CompId, array("Dist"=>$Options['dist'] ,"Index"=>0 ,"Id"=>$r->QuId ,"Point"=>0 ,"TourId"=>$CompId));

				// calculate snapshot if any
				if(getModuleParameter('ISK-NG', 'Snapshot', '', $CompId)) {
					useArrowsSnapshotTarget($Options['dist'], $r->QuTargetNo, strlen(rtrim($arrowString)));
				}
			}
			if($IsSendall) {
				$UpdatedEntries=$ToRank;
			} elseif($updated) {
				CalculateRanks($ToRank, $CompId, $Options['dist']);
			}
			$Error=0;
			break;
		// case 'E':
		// 	$Phase=$IskSequence['maxdist']-1;
		// 	$Session=$IskSequence['session'];
		// 	if(!empty($Options['Category']) and preg_match('/^[a-z0-9_.-]+$/sim', $Options['Category'])) {
		// 		$Filtre=" AND ElEventCode='{$Options['Category']}'";
		// 	}
		// 	$SQL="SELECT ElId, ElTargetNo, ElArrowstring as Arrowstring, IskDtArrowstring, IskDtEndNo, if(ElElimPhase=0, EvE1Ends, EvE2Ends) as DIEnds, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) as DIArrows, IF(EvGoldsChars='',ToGoldsChars,EvGoldsChars) as GoldsChars, IF(EvXNineChars='',ToXNineChars,EvXNineChars) as XNineChars
		// 		from Eliminations
		// 		INNER JOIN Events ON EvTournament=ElTournament and EvTeamEvent=0 and EvCode=ElEventCode
		// 		INNER JOIN Tournament ON ToId=ElTournament
		// 		INNER JOIN IskData ON IskDtTournament=ElTournament AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='E{$IskSequence['maxdist']}' AND IskDtTargetNo=ElTargetNo AND IskDtEndNo={$End}
		// 			$Filtre
		// 		INNER JOIN IskDevices on IskDvTournament=IskDtTournament and IskDvProActive>0 and IskDvDevice=IskDtDevice and IskDvSchedKey=".StrSafe_DB($Key)." and IskDvGroup=$Group
		// 		WHERE ElTournament={$CompId} and ElSession={$Session} and ElElimPhase=$Phase";
		// 	$updated=array();
		// 	$q=safe_r_sql($SQL);
		// 	while($r=safe_fetch($q)) {
		// 		$arrowString = str_pad($r->Arrowstring,$r->DIArrows*$r->DIEnds);
		// 		for($i=0; $i<$r->DIArrows; $i++){
		// 			if($r->IskDtArrowstring[$i]!=' '){
		// 				$arrowString[($r->IskDtEndNo-1)*$r->DIArrows+$i]=$r->IskDtArrowstring[$i];
		// 			}
		// 		}
		// 		$Score=0;
		// 		$Gold=0;
		// 		$XNine=0;
		// 		list($Score,$Gold,$XNine)=ValutaArrowStringGX($arrowString,$r->GoldsChars,$r->XNineChars);
		// 		$Hits=strlen(str_replace(' ', '', $arrowString));
		//
		// 		$Update = "UPDATE Eliminations SET
		// 			ElScore={$Score}, ElGold={$Gold}, ElXnine={$XNine}, ElArrowString='{$arrowString}', ElHits={$Hits},
		// 			ElDateTime=" . StrSafe_DB(date('Y-m-d H:i:s')) . "
		// 			WHERE  ElElimPhase=$Phase and ElId={$r->ElId}";
		// 		safe_w_SQL($Update);
		// 		if(safe_w_affected_rows()) {
		// 			$updated[] = $r->ElId;
		// 		}
		// 		$Update = "DELETE FROM IskData
		// 			WHERE IskDtTournament={$CompId} AND IskDtMatchNo=0 AND IskDtEvent='' AND IskDtTeamInd=0 AND IskDtType='E{$IskSequence['maxdist']}'
		// 			AND IskDtTargetNo='{$r->ElTargetNo}' AND IskDtEndNo={$End} AND IskDtArrowstring='{$r->IskDtArrowstring}'";
		// 		safe_w_SQL($Update);
		// 	}
		// 	if(count($updated)) {
		// 		// needs to recalculate ranks
		// 		$q="SELECT distinct ElEventCode FROM Eliminations WHERE ElId IN (" . implode(",",$updated) . ") AND ElElimPhase={$Phase} and ElEventCode!=''";
		// 		$r=safe_r_sql($q);
		// 		while($row=safe_fetch($r)) {
		// 			if ($Phase==0) {
		// 				ResetElimRows($row->ElEventCode,2, $CompId);
		// 			}
		//
		// 			Obj_RankFactory::create('ElimInd',array('tournament'=>$CompId,'eventsC'=>array($row->ElEventCode.'@'.($Phase+1))))->calculate();
		//
		// 			ResetShootoff ( $row->ElEventCode, 0, $Phase+1, $CompId);
		// 		}
		// 	}
		// 	$Error=0;
		// 	break;
		case 'M':
		// case 'I':
		// case 'T':
            $importAllSession = !empty($Options['allSessions']);
			if(isset($Options['team'])) {
				$IndTeam=intval($Options['team']);
			} elseif(($Options['subtype']??'')=='R') {
				$IndTeam = intval($IskSequence['IskKey'][0]=='T');
			} else {
				$IndTeam = ($IskSequence['subtype']=='I' ? 0:1);
			}
            if(isset($Options['event']) or isset($Options['matchno'])) {
                $f = array();
                if (is_array($Options['event']) or is_array($Options['matchno'])) {
                    if(count($Options['event']) == count($Options['matchno'])) {
                        for($evCnt=0; $evCnt<count($Options['event']); $evCnt++) {
                            list($m1,$m2) = explode(',', $Options['matchno'][$evCnt]);
                            $f[] = "('{$Options['event'][$evCnt]}', $m1)";
                            $f[] = "('{$Options['event'][$evCnt]}', $m2)";
                        }
                    }
                } else {
                    foreach (explode(',', $Options['matchno']) as $m) {
                        $f[] = "('{$Options['event']}', $m)";
                    }
                    if (isset($Options['event2']) or isset($Options['matchno2'])) {
                        foreach (explode(',', $Options['matchno2']) as $m) {
                            $f[] = "('{$Options['event2']}', $m)";
                        }
                    }
                }
                $Filtre = " and IskDtTeamInd=$IndTeam AND (IskDtEvent, IskDtMatchNo) in (" . implode(',', $f) . ") ";
			}
			if($IskSequence['subtype']=='R') {
				require_once('Modules/RoundRobin/Lib.php');
				$fSes=$IskSequence['session'];
				$SQL="SELECT if(EvGoldsChars='', ToGoldsChars, EvGoldsChars) as GoldsChars, if(EvXNineChars='', ToXNineChars, EvXNineChars) as XNineChars, RrMatchEvent, RrMatchMatchNo, RrMatchTeam, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchConfirmed as StopScore,
       					RrMatchArrowstring as Arrowstring, RrMatchTiebreak as TieBreak, RrMatchTbClosest as TbClosest, RrMatchTbDecoded as TbDecoded,
       					IskDtMatchNo, group_concat(concat_ws(':', IskDtEndNo, IskDtArrowstring) separator '|') as IskArrowstring, max(IskDtIsClosest) as IskClosest,
       					RrLevArrows as arrows, RrLevEnds as ends, RrLevSO as so, RrLevBestRankMode
					FROM RoundRobinMatches
                    inner join Events on EvTournament=RrMatchTournament and EvTeamEvent=RrMatchTeam and EvCode=RrMatchEvent
                    inner join Tournament on ToId=RrMatchTournament
					inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel
					INNER JOIN IskData ON IskDtTournament=RrMatchTournament AND IskDtMatchNo=RrMatchMatchNo+(100*RrMatchRound)+(10000*RrMatchGroup)+(1000000*RrMatchLevel) AND IskDtEvent=RrMatchEvent AND IskDtTeamInd=RrMatchTeam AND IskDtType='M' AND IskDtTargetNo='' AND IskDtDistance=1 ".($End ? "AND IskDtEndNo={$End}" : "")."
						$Filtre
					INNER JOIN IskDevices on IskDvTournament=IskDtTournament and IskDvProActive>0 and IskDvDevice=IskDtDevice and IskDvSchedKey=".StrSafe_DB($Key)." and IskDvGroup=$Group
					WHERE RRMatchTournament={$CompId} AND RrMatchTeam={$IndTeam}
						AND CONCAT(RrMatchScheduledDate,RrMatchScheduledTime)=" . StrSafe_DB($fSes)."
					group by IskDtTournament, IskDtMatchNo, IskDtEvent, IskDtTeamInd, IskDtType, IskDtTargetNo, IskDtDistance, IskDtSession";
				$q=safe_r_SQL($SQL);
				while($r=safe_fetch($q)){
                    if($r->StopScore) {
                        // score has been confirmed so we remove the data and skip to next record
                        $Update = "DELETE FROM IskData
							WHERE IskDtTournament={$CompId} AND IskDtMatchNo={$r->IskDtMatchNo} AND IskDtEvent='{$r->RrMatchEvent}' AND IskDtTeamInd={$IndTeam} AND IskDtType='M'";
                        safe_w_SQL($Update);
                        continue;
                    }
					foreach(explode('|', $r->IskArrowstring) as $IskEnds) {
						list($IskEnd,$IskString)=explode(':', $IskEnds);

						if($IskEnd > $r->ends) {
							// we have a SO
							$End=$IskEnd - $r->ends;
							$r->TieBreak = str_pad($r->TieBreak, $End*$r->so);
							$Offset = ($End-1)*$r->so;

							for($i=0; $i<$r->so; $i++){
								if(isset($IskString[$i]) and $IskString[$i]!=' '){
									$r->TieBreak[$Offset+$i]=$IskString[$i];
								}
							}
							$r->TbClosest=$r->IskClosest;
						} else {
							// regular scoring ends
							$End=$IskEnd;
							$r->Arrowstring = str_pad($r->Arrowstring, $End*$r->arrows);
							$Offset = ($End-1)*$r->arrows;

							for($i=0; $i<$r->arrows; $i++){
								if(isset($IskString[$i]) and $IskString[$i]!=' '){
									$r->Arrowstring[$Offset+$i]=$IskString[$i];
								}
							}
						}

						$Update = "DELETE FROM IskData
							WHERE IskDtTournament={$CompId} AND IskDtMatchNo={$r->IskDtMatchNo} AND IskDtEvent='{$r->RrMatchEvent}' AND IskDtTeamInd={$IndTeam} AND IskDtType='M'
							AND IskDtTargetNo='' AND IskDtDistance=1 AND IskDtEndNo={$IskEnd}";
						safe_w_SQL($Update);
					}
					RobinUpdateNgArrowString($r, $CompId);
				}
				$Error=0;
			} else {
				$fSes=$IskSequence['maxdist'].$IskSequence['session'];
				$tblHead = ($IndTeam==0 ? 'Fin' : 'Tf');

				$SQL="SELECT if(EvGoldsChars='', ToGoldsChars, EvGoldsChars) as GoldsChars, if(EvXNineChars='', ToXNineChars, EvXNineChars) as XNineChars, FSEvent, FSMatchNo, FSTeamEvent, {$tblHead}Arrowstring as Arrowstring, {$tblHead}Tiebreak as TieBreak, {$tblHead}TbClosest as TbClosest, {$tblHead}TbDecoded as TbDecoded, GrPhase, 
       				group_concat(concat_ws(':', IskDtEndNo, IskDtArrowstring) separator '|') as IskArrowstring, max(IskDtIsClosest) as IskClosest,
       				{$tblHead}Confirmed as StopScore
					FROM FinSchedule
                    inner join Events on EvTournament=FSTournament and EvTeamEvent=FSTeamEvent and EvCode=FSEvent
                    inner join Tournament on ToId=FSTournament
					INNER JOIN Grids ON FSMatchNo=GrMatchNo
					INNER JOIN IskData ON IskDtTournament=FsTournament AND IskDtMatchNo=FsMatchNo AND IskDtEvent=FSEvent AND IskDtTeamInd=FsTeamEvent AND IskDtType='M' AND IskDtTargetNo='' AND IskDtDistance=1 ".($End ? "AND IskDtEndNo={$End}" : "")."
						$Filtre
					INNER JOIN IskDevices on IskDvTournament=IskDtTournament and IskDvProActive>0 and IskDvDevice=IskDtDevice ".(($isLiteMode OR $importAllSession) ? "" : "and IskDvSchedKey=".StrSafe_DB($Key))." and IskDvGroup=$Group
					INNER JOIN " . ($IndTeam==0 ? 'Finals' : 'TeamFinals') . " ON FsTournament={$tblHead}Tournament AND FsMatchNo={$tblHead}MatchNo AND FSEvent={$tblHead}Event
					WHERE FSTournament={$CompId} AND FsTeamEvent={$IndTeam}
					".(($isLiteMode OR $importAllSession) ? "" : "AND CONCAT(FSScheduledDate,FSScheduledTime)=" . StrSafe_DB($fSes));
                if(isset($Options['filterGroup'])) {
                    $SQL .= ' AND IskDvGroup = ' . intval($Options['filterGroup']);
                }
				$SQL.=" group by IskDtTournament, IskDtMatchNo, IskDtEvent, IskDtTeamInd, IskDtType, IskDtTargetNo, IskDtDistance, IskDtSession";
				$q=safe_r_SQL($SQL);
				while($r=safe_fetch($q)){
                    if($r->StopScore) {
                        // SCORE HAS BEEN CONFIRMED SO
                        // empty the whole match lines and skip to next record
                        $Update = "DELETE FROM IskData
							WHERE IskDtTournament={$CompId} AND IskDtMatchNo={$r->FSMatchNo} AND IskDtEvent='{$r->FSEvent}' AND IskDtTeamInd={$IndTeam} AND IskDtType='M'";
                        safe_w_SQL($Update);
                        continue;
                    }
					$obj=getEventArrowsParams($r->FSEvent,$r->GrPhase,$r->FSTeamEvent,$CompId);
					$r->so=$obj->so; // will be used later on in UpdateNgArrowString()
                    $r->startArrow = 10000;
                    $r->endArrow = 0;
					foreach(explode('|', $r->IskArrowstring) as $IskEnds) {
						list($IskEnd,$IskString)=explode(':', $IskEnds);

						if($IskEnd > $obj->ends) {
							// we have a SO
							$End=$IskEnd - $obj->ends;
							$r->TieBreak = str_pad($r->TieBreak, $End*$obj->so);
							$Offset = ($End-1)*$obj->so;

							for($i=0; $i<$obj->so; $i++){
								if(isset($IskString[$i]) and $IskString[$i]!=' '){
									$r->TieBreak[$Offset+$i]=$IskString[$i];
								}
							}
							$r->TbClosest=$r->IskClosest;
                            $r->startArrow = min($Offset, $r->startArrow);
                            $r->endArrow = max(($obj->arrows*$obj->ends)+$End*$obj->so, $r->endArrow);
						} else {
							// regular scoring ends
							$End=$IskEnd;
							$r->Arrowstring = str_pad($r->Arrowstring, $End*$obj->arrows);
							$Offset = ($End-1)*$obj->arrows;

							for($i=0; $i<$obj->arrows; $i++){
								if(isset($IskString[$i]) and $IskString[$i]!=' '){
									$r->Arrowstring[$Offset+$i]=$IskString[$i];
								}
							}
                            $r->startArrow = min($Offset, $r->startArrow);
                            $r->endArrow = max($End*$obj->arrows, $r->endArrow);
						}

						$Update = "DELETE FROM IskData
							WHERE IskDtTournament={$CompId} AND IskDtMatchNo={$r->FSMatchNo} AND IskDtEvent='{$r->FSEvent}' AND IskDtTeamInd={$IndTeam} AND IskDtType='M'
							AND IskDtTargetNo='' AND IskDtDistance=1 AND IskDtEndNo={$IskEnd}";
						safe_w_SQL($Update);
					}
					UpdateNgArrowString($r, $CompId);
				}
				$Error=0;
			}
			break;
	}
	return $Error;
}

function getSetupGlobalQrCode($ToId) {
	$QrCode=getModuleParameter('ISK-NG', 'QRCode-Setup', [], $ToId);
	if(!$QrCode) {
		return '';
	}

	// check Competition mode...
	$q=safe_r_sql("select ToOptions from Tournament where ToId=$ToId");
	if($r=safe_fetch($q) and $r->ToOptions and $Items=unserialize($r->ToOptions) and $UseApi=($Items['UseApi']??'')) {
		switch ($UseApi ?? '') {
			case ISK_NG_LITE_CODE:
			case ISK_NG_PRO_CODE:
				$JSON['items'][] = [
					'id' => 'serverUrl',
					'text' => get_text('ISK-ServerUrl', 'Api'),
					'value' => getModuleParameter('ISK-NG', 'ServerUrl', '', $ToId),
					'type' => 'text',
				];
				$JSON['items'][] = [
					'id' => 'serverUrlPin',
					'text' => get_text('ISK-ServerUrlPin', 'Api'),
					'value' => getModuleParameter('ISK-NG', 'ServerUrlPin', '', $ToId),
					'type' => 'text',
				];
				if ($UseApi == ISK_NG_PRO_CODE) {
					$JSON['items'][] = [
						'id' => 'licenseNumber',
						'text' => get_text('ISK-LicenseNumber', 'Api'),
						'value' => getModuleParameter('ISK-NG', 'LicenseNumber', '', $ToId),
						'type' => 'text',
					];
				}
				break;
			case ISK_NG_LIVE_CODE:
				$CanPrint = (
					$QrCode
					and ($QrCode['socketIP'] = getModuleParameter('ISK-NG', 'SocketIP', '', $ToId))
					and ($QrCode['socketPort'] = getModuleParameter('ISK-NG', 'SocketPort', '', $ToId)));
				if (!$CanPrint) {
					return '';
				}
				break;
			default:
				return '';
		}
	}
	return $QrCode;
}

function GetLockableSessions() {
	$SQL=array();

	// QUALIFICATIONS
	$SQL[]="select 
       concat_ws('|','Q', QuSession, DiDistance) as LockKey,
       'Q' as SesType,
       SesName as Description,
       DiDistance as Distance,
       0 as FirstPhase,
       0 as Order1, SesOrder as Order2, DiDistance as Order3
	from Qualifications
	inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']}
	inner join DistanceInformation on DiTournament=EnTournament and DiSession=QuSession and DiType='Q'
	left join Session on SesTournament=EnTournament and SesType='Q' and SesOrder=QuSession
	where QuSession>0
	group by QuSession, DiDistance";

	// ELIMINATIONS
	$SQL[]="select 
       concat_ws('|','E', ElElimPhase, ElEventCode) as LockKey,
       'E' as SesType,
       concat(SesName, ' - ', ElEventCode) as Description,
       1 as Distance,
       0 as FirstPhase,
       1 as Order1, ElElimPhase as Order2, EvProgr as Order3
	from Eliminations
	inner join Events on EvCode=ElEventCode and EvTeamEvent=0 and EvTournament=ElTournament
	left join Session on SesTournament=ElTournament and SesType='E' and SesOrder=ElSession
	where ElTournament={$_SESSION['TourId']}
	group by ElEventCode, ElElimPhase";

	// Individual Matches
	$SQL[]="select 
       concat_ws('|','I',GrPhase,FinEvent) as LockKey, 
       'I' as SesType,
       EvEventName as Description,
       GrPhase as Distance,
       EvFinalFirstPhase as FirstPhase,
       3 as Order1, EvProgr as Order2, 128-GrPhase as Order3
	from Finals
	inner join Events on EvCode=FinEvent and EvTeamEvent=0 and EvTournament=FinTournament and EvFinalFirstPhase>0
	inner join Grids on GrMatchNo=FinMatchNo
	where FinTournament={$_SESSION['TourId']}
	group by GrPhase, FinEvent";

	// Team Matches
	$SQL[]="select 
       concat_ws('|','T',GrPhase,TfEvent) as LockKey, 
       'T' as SesType,
       EvEventName as Description,
       GrPhase as Distance,
       EvFinalFirstPhase as FirstPhase,
       4 as Order1, EvProgr as Order2, 128-GrPhase as Order3
	from TeamFinals
	inner join Events on EvCode=TfEvent and EvTeamEvent=1 and EvTournament=TfTournament and EvFinalFirstPhase>0
	inner join Grids on GrMatchNo=TfMatchNo
	where TfTournament={$_SESSION['TourId']}
	group by GrPhase, TfEvent";

	// Round Robin
	$SQL[]="select 
       concat_ws('|','R',RrMatchLevel, RrMatchGroup, RrMatchRound,RrMatchEvent) as LockKey, 
       concat_ws('|', 'R', RrMatchLevel, RrMatchGroup) as SesType,
       EvEventName as Description,
       RrMatchRound as Distance,
       EvFinalFirstPhase as FirstPhase,
       2 as Order1, EvProgr as Order2, RrMatchLevel*10000+RrMatchGroup*100+RrMatchRound as Order3
	from RoundRobinMatches
	inner join Events on EvCode=RrMatchEvent and EvTournament=RrMatchTournament and EvTeamEvent=RrMatchTeam
	where RrMatchTournament={$_SESSION['TourId']}
	group by RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound";

	return "(".implode(') UNION (', $SQL).") order by Order1, Order2, Order3";
}

function CalculateRanks($Types, $CompId, $Dist) {
	// Individual categories
	if($EnIds=implode(",",$Types['RnkIndCat'])) {
		$SQL = "SELECT DISTINCT EnClass, EnDivision
			FROM Entries
			WHERE EnTournament={$CompId} AND EnId IN ($EnIds) and EnIndClEvent=1
			group by EnClass, EnDivision";
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			Obj_RankFactory::create('DivClass', array('tournament' => $CompId, 'events' => $r->EnDivision . $r->EnClass, 'dist' => $Dist))->calculate();
			if($Dist) {
				Obj_RankFactory::create('DivClass', array('tournament' => $CompId, 'events' => $r->EnDivision . $r->EnClass, 'dist' => 0))->calculate();
			}
		}
	}

	if($EnIds=implode(",",$Types['RnkTeamCat'])) {
		$SQL = "SELECT DISTINCT EnClass, EnDivision
			FROM Entries
			WHERE EnTournament={$CompId} AND EnId IN ($EnIds) and EnTeamClEvent=1
			group by EnClass, EnDivision";
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			MakeTeams(NULL, $r->EnDivision . $r->EnClass, $CompId);
		}
	}

	if($EnIds=implode(",", $Types['RnkIndFin'])) {
		// Abs recalculation
		$redoMakeIndividuals = false;
		if(module_exists('QuotaTournament') and count($maxByCountry=getModuleParameter("QuotaTournament", "maxByCountry", array(), $CompId))!=0) {
			$redoMakeIndividuals = true;
		}
		$SQL="select distinct IndEvent 
			from Individuals 
			inner join Entries on EnId=IndId and EnTournament=IndTournament and EnIndFEvent=1
			where IndId in ($EnIds) and IndTournament={$CompId}";
		$q=safe_r_sql($SQL);
		while($r=safe_fetch($q)) {
			Obj_RankFactory::create('Abs', array('tournament' => $CompId, 'events' => $r->IndEvent, 'dist' => $Dist))->calculate();
			if($Dist) {
				Obj_RankFactory::create('Abs', array('tournament' => $CompId, 'events' => $r->IndEvent, 'dist' => 0))->calculate();
			}
			ResetShootoff($r->IndEvent, 0, 0, $CompId);
			if($redoMakeIndividuals and array_key_exists($r->IndEvent,$maxByCountry)) {
				$miEvList =  array($maxByCountry[$r->IndEvent]['Ev']);
				MakeIndividuals($miEvList,$CompId);
				Obj_RankFactory::create('Abs', array('tournament' => $CompId, 'events' => $maxByCountry[$r->IndEvent]['Ev'], 'dist' => 0))->calculate();
			}
			runJack("QRRankUpdate", $CompId, array("Event"=>$r->IndEvent, "Team"=>0, "TourId"=>$CompId));
		}
	}

	// Abs Team recalc
	if($EnIds=implode(",", $Types['RnkTeamFin'])) {
		$SQL = "SELECT DISTINCT EvCode, EnDivision, EnClass
			FROM Events
			INNER JOIN EventClass ON EvCode=EcCode AND EcTeamEvent>0 AND EvTournament=EcTournament
			inner join Entries on EnId in ($EnIds) and EnTournament=EvTournament and EnDivision=EcDivision and EnClass=EcClass and if(EcSubClass='', true, EnSubClass=EcSubClass) and EnTeamFEvent+EnTeamMixEvent>0
			WHERE EvTournament={$CompId} and EvTeamEvent=1
			";
		$q=safe_r_sql($SQL);
		if(safe_num_rows($q)) {
			while ($r = safe_fetch($q)) {
				MakeTeamsAbs(NULL, $r->EnDivision, $r->EnClass, $CompId);
			}
		} else {
			$q=safe_r_sql("SELECT DISTINCT TcEvent from TeamComponent where TcTournament={$CompId} and TcId in ($EnIds)");
			if(safe_num_rows($q)) {
				MakeTeamsAbs(NULL, NULL, NULL, $CompId);
			}
		}
	}
}

function UpdateNgArrowString($r, $ToId=0, &$CHANGES=false) {
	$OppMatchno=($r->FSMatchNo%2 ? $r->FSMatchNo-1 : $r->FSMatchNo+1);

	$CompId = $ToId;
	if(empty($CompId) && !empty($_SESSION['TourId'])) {
		$CompId = $_SESSION['TourId'];
	}

	if($r->FSTeamEvent) {
		$Table = "TeamFinals";
		$TablePrefix = "Tf";
		$Filter="";
	} else {
		$Table = "Finals";
		$TablePrefix = "Fin";
	}
	$Filter="{$TablePrefix}Tie!=2
		AND {$TablePrefix}Event=". StrSafe_DB($r->FSEvent) . "
		AND {$TablePrefix}Tournament=$CompId";

	$Update=0;
	// if closest is set we must remove from the other match
	if($r->TbClosest) {
		safe_w_sql("update $Table SET {$TablePrefix}TbClosest=0, {$TablePrefix}Tie=0, {$TablePrefix}TbDecoded=replace({$TablePrefix}TbDecoded, '+','') 
	        WHERE $Filter AND {$TablePrefix}MatchNo=$OppMatchno");
		if(safe_w_affected_rows()) {
			$Update=1;
		}
	}

	// Update the regular scoring
    $Golds=0;
    $XNine=0;
    if($r->GoldsChars??'') {
        list($Score, $Golds, $XNine)=ValutaArrowStringGX($r->Arrowstring, $r->GoldsChars, $r->XNineChars);
    }

    $query="UPDATE $Table 
        SET {$TablePrefix}Arrowstring=" . StrSafe_DB($r->Arrowstring) . ", 
        	{$TablePrefix}Golds=".intval($Golds).",
        	{$TablePrefix}XNines=".intval($XNine).",
        	{$TablePrefix}DateTime={$TablePrefix}DateTime
		WHERE $Filter AND {$TablePrefix}MatchNo=$r->FSMatchNo";
	safe_w_sql($query);
	if(safe_w_affected_rows()) {
		$Update=1;
	}

	// check if the SO generates a change
	if($r->TieBreak) {
		$r->TbDecoded='';
		// check the decoded arrows of the tiebreak!
		$decoded=array();
		foreach(str_split(rtrim($r->TieBreak), $r->so) as $k) {
			if($r->so==1) {
				$decoded[]=DecodeFromLetter($k);
			} else {
				$decoded[]=ValutaArrowString($k);
			}
		}
		$r->TbDecoded=implode(',', $decoded).($r->TbClosest?'+':'');
	}
	$query="UPDATE $Table SET {$TablePrefix}TieBreak=" . StrSafe_DB($r->TieBreak) . ", {$TablePrefix}TbDecoded='$r->TbDecoded', {$TablePrefix}TbClosest=$r->TbClosest, {$TablePrefix}DateTime={$TablePrefix}DateTime
		WHERE $Filter AND {$TablePrefix}MatchNo=$r->FSMatchNo";
	safe_w_sql($query);
	if(safe_w_affected_rows()) {
		$Update=1;
	}

	if($Update) {
		$CHANGES=true;
		// updates the timestamp!
		safe_w_sql("UPDATE $Table SET {$TablePrefix}DateTime=now() WHERE $Filter AND {$TablePrefix}MatchNo in ($r->FSMatchNo, $OppMatchno)");
		return EvaluateMatch($r->FSEvent, $r->FSTeamEvent, $r->FSMatchNo, $CompId, true, '', $r->startArrow, $r->endArrow);
	}
	//print $query;
	// needs to return if the match is finished or not, so ask for the situation
	return IsMatchFinished($r->FSMatchNo, $r->FSEvent, $r->FSTeamEvent, $CompId);
}

function RobinUpdateNgArrowString($r, $CompId=0) {
	$OppMatchno=($r->RrMatchMatchNo%2 ? $r->RrMatchMatchNo-1 : $r->RrMatchMatchNo+1);

	if(empty($CompId) && !empty($_SESSION['TourId'])) {
		$CompId = $_SESSION['TourId'];
	}

	$Filter="RrMatchTie!=2 AND  RrMatchTournament=$CompId and RrMatchEvent='$r->RrMatchEvent' and RrMatchTeam=$r->RrMatchTeam and RrMatchLevel=$r->RrMatchLevel and RrMatchGroup=$r->RrMatchGroup and RrMatchRound=$r->RrMatchRound";

	$Update=0;
	// if closest is set we must remove from the other match
	if($r->TbClosest) {
		safe_w_sql("update RoundRobinMatches SET RrMatchTbClosest=0, RrMatchTie=0, RrMatchTbDecoded=replace(RrMatchTbDecoded, '+','') 
	        WHERE $Filter AND RrMatchMatchNo=$OppMatchno");
		if(safe_w_affected_rows()) {
			$Update=1;
		}
	}

	// Update the regular scoring
    $Golds=0;
    $XNine=0;
    if($r->GoldsChars??'') {
        list($Score, $Golds, $XNine)=ValutaArrowStringGX($r->Arrowstring, $r->GoldsChars, $r->XNineChars);
    }
	$query="UPDATE RoundRobinMatches 
        SET RrMatchArrowstring=" . StrSafe_DB($r->Arrowstring) . ", 
        	RrMatchGolds=".intval($Golds).",
        	RrMatchXNines=".intval($XNine).",
            RrMatchDateTime=RrMatchDateTime
		WHERE $Filter AND RrMatchMatchNo=$r->RrMatchMatchNo";
	safe_w_sql($query);
	if(safe_w_affected_rows()) {
		$Update=1;
	}

	// check if the SO generates a change
	if($r->TieBreak) {
		$r->TbDecoded='';
		// check the decoded arrows of the tiebreak!
		$decoded=array();
		foreach(str_split(rtrim($r->TieBreak), $r->so) as $k) {
			if($r->so==1) {
				$decoded[]=DecodeFromLetter($k);
			} else {
				$decoded[]=ValutaArrowString($k);
			}
		}
		$r->TbDecoded=implode(',', $decoded).($r->TbClosest?'+':'');
	}
	$query="UPDATE RoundRobinMatches SET RrMatchTiebreak=" . StrSafe_DB($r->TieBreak) . ", RrMatchTbDecoded='$r->TbDecoded', RrMatchTbClosest=$r->TbClosest, RrMatchDateTime=RrMatchDateTime
		WHERE $Filter AND RrMatchMatchNo=$r->RrMatchMatchNo";
	safe_w_sql($query);
	if(safe_w_affected_rows()) {
		$Update=1;
	}

	if($Update) {
		// updates the timestamp!
		safe_w_sql("UPDATE RoundRobinMatches SET RrMatchDateTime=now(),
				RrMatchStatus=2 
			WHERE $Filter AND RrMatchMatchNo in ($r->RrMatchMatchNo, $OppMatchno)");

		// The Winner status must be reset and the match switches back to not confirmed
		// If the BestRankSystem is based on TieSystem, reset also the tie status
		$TieStatus=($r->RrLevBestRankMode==1 ? '0' : "if(RrMatchTie=1,0,RrMatchTie)");
		safe_w_sql("update RoundRobinMatches set RrMatchConfirmed=0, RrMatchWinLose=0, RrMatchTie=$TieStatus 
				WHERE $Filter and RrMatchMatchNo in ($r->RrMatchMatchNo, $OppMatchno)");
		if($r->RrMatchConfirmed) {
			// the match was confirmed so status to 3 of the other match
			safe_w_sql("update RoundRobinMatches set RrMatchStatus=(RrMatchStatus | 2) 
					WHERE $Filter and RrMatchMatchNo = $OppMatchno");
		}
	}
	// needs to return if the match is finished or not, so ask for the situation
	return RobinMatchTotal($r->RrMatchMatchNo, $r->RrMatchEvent, $r->RrMatchTeam, $r->RrMatchLevel, $r->RrMatchGroup, $r->RrMatchRound, $CompId);
}
