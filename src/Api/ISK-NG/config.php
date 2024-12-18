<?php

require_once(dirname(dirname(__FILE__)).'/config.php');
require_once('Common/Lib/Fun_Modules.php');
require_once('Common/Lib/CommonLib.php');
require_once(__DIR__.'/config_defines.php');

const reqAppVersion = '1.4.1';

define('NG_DEBUG_LOG', ($CFG->DEBUG??false) and is_dir(__DIR__.'/log') and is_writable(__DIR__.'/log'));
define('NG_DEBUG_LOGFILE', __DIR__.'/log/messages-'.date('Y-m-d').'.log');

function getQrConfig($DEVICE, $cachedData=false, $Lightmode=false, $Force=false) {
	if(!$cachedData) {
		// rebuild and stores the specific configuration for this device
		if($ResetDevice=rebuildQrConfig($DEVICE, $Lightmode, $Force)) {
			return $ResetDevice;
		}
	}

	// get the stored configuration
	$q=safe_r_sql("select IskDvSetup from IskDevices where IskDvDevice=".StrSafe_DB($DEVICE->IskDvDevice));
	$r=safe_fetch($q);
	if(!$r or !($json_array=@json_decode($r->IskDvSetup, true))) {
		return resetDevice($DEVICE->IskDvDevice, $DEVICE->IskDvTarget, $Force);
	}

	$toId = getIdFromCode($DEVICE->ToCode);
	if($Lightmode) {
		$IskSequence=$Lightmode;
	} else {
		if($IskGroup=getModuleParameter('ISK-NG', 'Sequence', null, $toId)) {
			$IskSequence=$IskGroup[$DEVICE->IskDvGroup];
		} else {
			return resetDevice($DEVICE->IskDvDevice, $DEVICE->IskDvTarget, $Force);
		}
	}

	switch($json_array['action']) {
		case 'reconfigure':
			$json_array['configHash']=md5($r->IskDvSetup);
			switch($IskSequence['type']) {
				case 'Q':
					// needs to get the total of the previous distances and the arrowstrings
					$Session = $IskSequence['session'];
					$tgtAssigned=array($DEVICE->IskDvTarget);
					$Filter="QuTarget={$DEVICE->IskDvTarget} and QuSession=$Session";
					$Grouping=getModuleParameter('ISK-NG', 'Grouping', '', $toId);

					if($Grouping[$DEVICE->IskDvGroup] ?? 0) {
						$TargetNo=getGroupedTargets($DEVICE->IskDvTarget, $toId, $DEVICE->IskDvGroup);
						$tgtAssigned=explode("','", $TargetNo);
						$Filter="QuTarget in ($TargetNo) and QuSession=$Session";
					}

					$SQL='Select QuD1Arrowstring, 0 as D1Previous, 0 as D1PreviousGold, 0 as D1PreviousXnine, ';
					$PrevS="QuD1Score";
					$PrevG="QuD1Gold";
					$PrevX="QuD1Xnine";
					for($n=2;$n<=8;$n++) {
						$SQL.="QuD{$n}Arrowstring, $PrevS as D{$n}Previous, $PrevG as D{$n}PreviousGold, $PrevX as D{$n}PreviousXnine, ";
						$PrevS.="+QuD{$n}Score";
						$PrevG.="+QuD{$n}Gold";
						$PrevX.="+QuD{$n}Xnine";
					}
					$SQL.= "QuTargetNo, group_concat(concat_ws('-', IskDtDistance, IskDtEndNo, IskDtArrowstring) separator '|') as TempArrows
						FROM Qualifications
						INNER JOIN Entries ON EnId=QuId and EnTournament=$toId  AND EnAthlete=1 AND EnStatus <= 1
						left join IskData on IskDtTournament=EnTournament and IskDtType='Q' and IskDtTargetNo=QuTargetNo
						WHERE QuIrmType=0 AND {$Filter}
						group by QuTargetNo";
					// Retrieve the competitor info
					$q=safe_r_sql($SQL);
					while($r=safe_fetch($q)) {
						if($tmpArrows=($r->TempArrows??'')) {
							$tmpArrows=[];
							foreach(explode('|', $r->TempArrows) as $item) {
								list($a,$b,$c)=explode('-', $item);
								$tmpArrows[$a][$b]=$c;
							}
						}

						foreach(($json_array['archers'][$r->QuTargetNo]['scoring']??[]) as $dKey=>$distance) {
							$arrowstring=str_pad($r->{"QuD{$distance['distance']}Arrowstring"}, $distance['arrows']*$distance['ends'], ' ', STR_PAD_RIGHT);
							if(!empty($tmpArrows[$distance['distance']])) {
								foreach($tmpArrows[$distance['distance']] as $End => $AS) {
									$offset=($End-1)*$distance['arrows'];
									$arrowstring=substr_replace($arrowstring, $AS, $offset, strlen($AS));
								}
							}
                            $json_array['archers'][$r->QuTargetNo]['scoring'][$dKey]['arrowstring']=$arrowstring;
                            $json_array['archers'][$r->QuTargetNo]['scoring'][$dKey]['previous']=$r->{"D{$distance['distance']}Previous"};
                            $json_array['archers'][$r->QuTargetNo]['scoring'][$dKey]['previousGold']=$r->{"D{$distance['distance']}PreviousGold"};
                            $json_array['archers'][$r->QuTargetNo]['scoring'][$dKey]['previousXnine']=$r->{"D{$distance['distance']}PreviousXnine"};
						}
					}
					break;
				case 'M':
					$Filter="Target1=$DEVICE->IskDvTarget";
					$tgtAssigned=array($DEVICE->IskDvTarget);
					$Grouping=getModuleParameter('ISK-NG', 'Grouping', null, $toId);

					if($Grouping[$DEVICE->IskDvGroup] ?? 0) {
						$TargetNo=getGroupedTargets($DEVICE->IskDvTarget, $toId, $DEVICE->IskDvGroup);
						$tgtAssigned=explode("','", $TargetNo);
						$Filter="Target1 in ($TargetNo)";
					}

					// fetches the match on this target for this session...
					$Date=substr($IskSequence['session'], 0, 10);
					$Time=substr($IskSequence['session'], 10);

					switch($IskSequence['subtype']) {
						case 'I':
							// regular individual matches
							if(isset($IskSequence['matchid'])) {
								list($Team, $Event, $Match)=explode('|', $IskSequence['matchid']);
								$Team=intval($Team);
								$Match=intval($Match/2)*2;
								$MainFilter="FsTournament=$toId and FsTarget>'' and FsTeamEvent=$Team and FsEvent=".StrSafe_DB($Event). " and GrPhase = (select GrPhase from Grids where GrMatchNo=$Match)";
							} else {
								$MainFilter="FsTournament=$toId and FsTarget>'' and FsTeamEvent=0 and FsScheduledDate='$Date' and FsScheduledTime='$Time'";
							}
							$SQL="select EvCode, EvTeamEvent, 
		                            concat_ws('|', EvTeamEvent, EvCode, FsMatchNo1) as refKey1,
		                            concat_ws('|', EvTeamEvent, EvCode, FsMatchNo2) as refKey2,
									tgt1.*, tgt2.*
								from (
									select FinArrowstring Arrowstring1, FinTieBreak TieBreak1, FSTarget+0 as Target1, FsMatchNo FsMatchNo1, FsEvent FsEvent1, 
							        	FinWinLose as Win1, FinTbClosest as Closest1, 
							        	coalesce(group_concat(concat_ws('-', IskDtEndNo, IskDtArrowstring) separator '|'), '') as TempArrows1
									from FinSchedule
								    inner join Grids on GrMatchNo=FSMatchNo
									inner join Finals on FsEvent=FinEvent and FinTournament=$toId and FsMatchNo=FinMatchNo
									left join IskData on IskDtTournament=FsTournament and IskDtType='M' and IskDtMatchNo=FSMatchNo and IskDtEvent=FSEvent and IskDtTeamInd=FSTeamEvent
									where $MainFilter and FsMatchNo%2=0
									group by FsEvent, FSTeamEvent, FSMatchNo) tgt1
								inner join (
									select FinArrowstring Arrowstring2, FinTieBreak TieBreak2, FSTarget+0 as Target2, FsMatchNo FsMatchNo2, FsEvent FsEvent2, 
							       		FinWinLose as Win2, FinTbClosest as Closest2, 
							       		coalesce(group_concat(concat_ws('-', IskDtEndNo, IskDtArrowstring) separator '|'),'') as TempArrows2
									from FinSchedule
								    inner join Grids on GrMatchNo=FSMatchNo
									inner join Finals on FsEvent=FinEvent and FsTournament=FinTournament and FsMatchNo=FinMatchNo
									left join IskData on IskDtTournament=FsTournament and IskDtType='M' and IskDtMatchNo=FSMatchNo and IskDtEvent=FSEvent and IskDtTeamInd=FSTeamEvent
									where $MainFilter
									group by FsEvent, FSTeamEvent, FSMatchNo) tgt2
									on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
								inner join Events on FsEvent1=EvCode and EvTeamEvent=0 and EvTournament=$toId
								where $Filter";
							break;
						case 'R':
							// Round Robin Matches
							$Team=($IskSequence['IskKey'][0]=='I' ? 0 : 1);
							if(isset($IskSequence['matchid'])) {
								list($Team, $Event, $Level, $Group, $Round, $Match)=explode('|', $IskSequence['matchid']);
								$Team=intval($Team);
								$Level=intval($Level);
								$Group=intval($Group);
								$Round=intval($Round);
								$Match=intval($Match/2)*2;
								$MainFilter="RrMatchTournament=$toId  and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round";
							} else {
								$MainFilter="RrMatchTournament=$toId  and RrMatchTeam=$Team and RrMatchScheduledDate='$Date' and RrMatchScheduledTime='$Time'";
							}
							$SQL="select EvCode, EvTeamEvent,
									tgt1.*, tgt2.*
								from (
                                    select RrMatchArrowstring Arrowstring1, RrMatchTiebreak TieBreak1, RrMatchTarget+0 as Target1, RrMatchMatchNo+100*RrMatchRound+10000*RrMatchGroup+1000000*RrMatchLevel FsMatchNo1, RrMatchEvent FsEvent1, 
							            RrMatchWinLose as Win1, RrMatchTbClosest as Closest1, 
							            coalesce(group_concat(concat_ws('-', IskDtEndNo, IskDtArrowstring) separator '|'), '') as TempArrows1,
										concat_ws('|', RrMatchTeam, RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) as refKey1
									from RoundRobinMatches
									left join IskData on IskDtTournament=RrMatchTournament and IskDtType='M' 
				                         and IskDtMatchNo=RrMatchMatchNo+100*RrMatchRound+10000*RrMatchGroup+1000000*RrMatchLevel
				                         and IskDtEvent=RrMatchEvent and IskDtTeamInd=RrMatchTeam
									where $MainFilter and RrMatchMatchNo % 2=0
									group by RrMatchEvent, RrMatchTeam, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) tgt1
								inner join (
								    select RrMatchArrowstring Arrowstring2, RrMatchTiebreak TieBreak2, RrMatchTarget+0 as Target2, RrMatchMatchNo+100*RrMatchRound+10000*RrMatchGroup+1000000*RrMatchLevel FsMatchNo2, RrMatchEvent FsEvent2, 
							            RrMatchWinLose as Win2, RrMatchTbClosest as Closest2, 
							            coalesce(group_concat(concat_ws('-', IskDtEndNo, IskDtArrowstring) separator '|'),'') as TempArrows2,
										concat_ws('|', RrMatchTeam, RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) as refKey2
									from RoundRobinMatches
									left join IskData on IskDtTournament=RrMatchTournament and IskDtType='M' 
				                         and IskDtMatchNo=RrMatchMatchNo+100*RrMatchRound+10000*RrMatchGroup+1000000*RrMatchLevel
				                         and IskDtEvent=RrMatchEvent and IskDtTeamInd=RrMatchTeam
									where $MainFilter and RrMatchMatchNo % 2=1
									group by RrMatchEvent, RrMatchTeam, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) tgt2 on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
								inner join Events on FsEvent1=EvCode and EvTeamEvent=$Team and EvTournament=$toId
								where $Filter";
							break;
						case 'T':
							// team matches
							if(isset($IskSequence['matchid'])) {
								list($Team, $Event, $Match)=explode('|', $IskSequence['matchid']);
								$Team=intval($Team);
								$Match=intval($Match/2)*2;
								$MainFilter="FsTournament=$toId and FsTarget>'' and FsTeamEvent=$Team and FsEvent=".StrSafe_DB($Event). " and GrPhase = (select GrPhase from Grids where GrMatchNo=$Match)";
							} else {
								$MainFilter="FsTournament=$toId and FsTarget>'' and FsTeamEvent=1 and FsScheduledDate='$Date' and FsScheduledTime='$Time'";
							}
							$SQL="select EvCode, EvTeamEvent, 
		                            concat_ws('|', EvTeamEvent, EvCode, FsMatchNo1) as refKey1,
		                            concat_ws('|', EvTeamEvent, EvCode, FsMatchNo2) as refKey2,
									tgt1.*, tgt2.*
								from (
									select TfArrowstring Arrowstring1, TfTieBreak TieBreak1, FSTarget+0 as Target1, FsMatchNo FsMatchNo1, FsEvent FsEvent1, 
							       		TfWinLose as Win1, TfTbClosest as Closest1, 
							       		coalesce(group_concat(concat_ws('-', IskDtEndNo, IskDtArrowstring) separator '|'),'') as TempArrows1
									from FinSchedule
								    inner join Grids on GrMatchNo=FSMatchNo
									inner join TeamFinals on FsEvent=TfEvent and TfTournament=$toId and FsMatchNo=TfMatchNo
									left join IskData on IskDtTournament=FsTournament and IskDtType='M' and IskDtMatchNo=FSMatchNo and IskDtEvent=FSEvent and IskDtTeamInd=FSTeamEvent
									where $MainFilter and FsMatchNo%2=0
									group by FsEvent, FSTeamEvent, FSMatchNo) tgt1
								inner join (
									select TfArrowstring Arrowstring2, TfTieBreak TieBreak2, FSTarget+0 as Target2, FsMatchNo FsMatchNo2, FsEvent FsEvent2, 
						                TfWinLose as Win2, TfTbClosest as Closest2, 
						                coalesce(group_concat(concat_ws('-', IskDtEndNo, IskDtArrowstring) separator '|'),'') as TempArrows2
									from FinSchedule
								    inner join Grids on GrMatchNo=FSMatchNo
									inner join TeamFinals on FsEvent=TfEvent and FsTournament=TfTournament and FsMatchNo=TfMatchNo
									left join IskData on IskDtTournament=FsTournament and IskDtType='M' and IskDtMatchNo=FSMatchNo and IskDtEvent=FSEvent and IskDtTeamInd=FSTeamEvent
									where $MainFilter
									group by FsEvent, FSTeamEvent, FSMatchNo) tgt2
									on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
								inner join Events on FsEvent1=EvCode and EvTeamEvent=1 and EvTournament=$toId
								where $Filter";
							break;
					}

					// Retrieve the "on the fly" info
					$q=safe_r_sql($SQL);
					while($r=safe_fetch($q)) {
						foreach(($json_array['archers'][$r->refKey1]['scoring']??[]) as $dKey=>$distance) {
							$tmpArrows=[];
							if($r->TempArrows1) {
								foreach(explode('|', $r->TempArrows1) as $item) {
									list($a,$b)=explode('-', $item);
									$tmpArrows[$a]=$b;
								}
							}

							$arrowstring=str_pad($r->Arrowstring1, $distance['arrows']*$distance['ends'], ' ', STR_PAD_RIGHT);
							if(rtrim($r->TieBreak1)) {
								$arrowstring.=rtrim($r->TieBreak1);
							}
							foreach($tmpArrows as $End => $AS) {
								if($End>$distance['ends']) {
									$offset=($distance['arrows']*$distance['ends'])+(($distance['ends']-($End-1))*$distance['shootOff']);
								} else {
									$offset=($End-1)*$distance['arrows'];
								}
								$arrowstring=substr_replace($arrowstring, $AS, $offset, strlen($AS));
							}
                            $json_array['archers'][$r->refKey1]['scoring'][$dKey]['arrowstring']=$arrowstring;
                            $json_array['archers'][$r->refKey1]['scoring'][$dKey]['soClosest']=(string) $r->Closest1;

							if(strlen($arrowstring)<=($distance['arrows']*$distance['ends'])) {
								$soEnds=1;
							} else {
								$soEnds=ceil((strlen($arrowstring)-($distance['arrows']*$distance['ends']))/$distance['shootOff']);
							}
                            $json_array['archers'][$r->refKey1]['scoring'][$dKey]['soEnds']=$soEnds;

							$tmpArrows=[];
							if($r->TempArrows2) {
								foreach(explode('|', $r->TempArrows2) as $item) {
									list($a,$b)=explode('-', $item);
									$tmpArrows[$a]=$b;
								}
							}
							$arrowstring=str_pad($r->Arrowstring2, $distance['arrows']*$distance['ends'], ' ', STR_PAD_RIGHT);
							if(rtrim($r->TieBreak2)) {
								$arrowstring.=rtrim($r->TieBreak2);
							}
							foreach($tmpArrows as $End => $AS) {
								if($End>$distance['ends']) {
									$offset=($distance['arrows']*$distance['ends'])+(($distance['ends']-($End-1))*$distance['shootOff']);
								} else {
									$offset=($End-1)*$distance['arrows'];
								}
								$arrowstring=substr_replace($arrowstring, $AS, $offset, strlen($AS));
							}
                            $json_array['archers'][$r->refKey2]['scoring'][$dKey]['arrowstring']=$arrowstring;
                            $json_array['archers'][$r->refKey2]['scoring'][$dKey]['soClosest']=(string) $r->Closest2;

							if(strlen($arrowstring)<=($distance['arrows']*$distance['ends'])) {
								$soEnds=1;
							} else {
								$soEnds=ceil((strlen($arrowstring)-($distance['arrows']*$distance['ends']))/$distance['shootOff']);
							}
                            $json_array['archers'][$r->refKey2]['scoring'][$dKey]['soEnds']=$soEnds;
						}
					}
					break;
			}
			$json_array['archers']=array_values($json_array['archers']);
			break;
	}

    return $json_array;
}

function getTargetFaces($DEVICE, $Targets) {
    global $CFG;

    $json_array=array(
        "action"=> "targetimages",
        "device"=> $DEVICE->IskDvDevice,
        'images' => array(),
    );

    $TgtFaceList=[];
    foreach(($Targets ?? []) as $tgt) {
        $TgtFaceList[intval($tgt)]=[];
    }
    // manage target list
    require_once('Common/Lib/ArrTargets.inc.php');
    if($TgtFaceList) {
        $q=safe_r_sql("select * from Targets where TarId in (".implode(',', array_keys($TgtFaceList)).")");
        while($r=safe_fetch($q)) {
            $img=$CFG->DOCUMENT_PATH.'Common/Images/Targets/'.$r->TarId.'.svgz';
            if(is_file($img)) {
                $TgtFaceList[$r->TarId]["imgName"] = $r->TarId.'.svg';
                $TgtFaceList[$r->TarId]["imgGzip"] = base64_encode(file_get_contents($img));
                $TgtFaceList[$r->TarId]["imgBase64Prefix"] = 'data:image/svg+xml;base64,';
            } else {
                $img=$CFG->DOCUMENT_PATH.'Common/Images/Targets/99.svgz';
                $TgtFaceList[$r->TarId]["imgName"] = '99.svg';
                $TgtFaceList[$r->TarId]["imgGzip"] = base64_encode(file_get_contents($img));
                $TgtFaceList[$r->TarId]["imgBase64Prefix"] = 'data:image/svg+xml;base64,';
            }
        }
    }
    $json_array['images']=array_values($TgtFaceList);

    // sets the JSON directly into the device
    // in this way single tablets can be on a different setup

    return $json_array;
}

function rebuildQrConfig($DEVICE, $Lightmode=false, $Force=false) {
	global $CFG;

    list($maj,$min,$dub)=explode('.', $DEVICE->IskDvVersion, 3);
    $NeedImages = !(intval($maj)>1 or intval($min)>=3);

    $Cat='U'; // Unknown
    switch($DEVICE->ToCategory) {
        case '8':
            $Cat='3'; // 3D
            break;
        case '4':
            $Cat='F'; // Field
            break;
        case '2':
            $Cat='I'; // Indoor
            break;
        case '1':
            $Cat='O'; // Outdoor
            break;
    }
    $toId = getIdFromCode($DEVICE->ToCode);


	if($Lightmode) {
		$IskSequence=$Lightmode;
	} else {
	    $IskGroup=getModuleParameter('ISK-NG', 'Sequence', null, $toId);
	    if(empty($IskGroup[$DEVICE->IskDvGroup])) {
		    return resetDevice($DEVICE->IskDvDevice, $DEVICE->IskDvTarget, $Force);
	    }

	    $IskSequence=$IskGroup[$DEVICE->IskDvGroup];
	}

	// if($DEVICE->ScheduleKey!=$IskSequence['type'].$IskSequence['maxdist'].$IskSequence['session']) {
	if(array_key_exists('IskKey', $IskSequence) AND $DEVICE->ScheduleKey!=$IskSequence['IskKey']) {
		// this will save into the device as well
		$DEVICE->ScheduleKey=$IskSequence['IskKey'];
	}

    $json_array=array(
        'action' => 'reconfigure',
        'msgIdentifier' => time(),
        'device'=>$DEVICE->IskDvDevice,
        'toCode' => $DEVICE->ToCode,
        'toName' => $DEVICE->TournamentName,
        'devCode'=>$DEVICE->IskDvCode,
        'targetAssigned'=>$DEVICE->IskDvTarget,
        'type'=>'',
        'ctype'=>$Cat,
        'schedule'=>$DEVICE->ScheduleKey,
        'session'=>'',
        'sessionName'=>'',
        'archers' => array(),
        'targetFaces' => array(),
    );

    $TgtFaceList = array();
	$TgtFaceTemplate=[
		"code" => "0",
		"id" => "",
		"name" => "",
		"imgName" => '0.svg',
		"cols" => 1,
		"rows" => 1,
		"gold" => "",
		"xnine" => "",
		"goldlbl" => "",
		"xninelbl" => "",
		"letterPoint" => ["letter" => "A", "point" => "M", "num" => 0, "bg" => "#999999", "fg" => "#000000"]
		];

    if($NeedImages) {
        $TgtFaceTemplate['imgGzip']=base64_encode(file_get_contents($CFG->DOCUMENT_PATH.'Common/Images/Targets/0.svgz'));
        $TgtFaceTemplate['imgBase64Prefix']='data:image/svg+xml;base64,';
    }

	//  check if there are specific accreditations
	$ExtraSql='';
	$ExtraField='EnCode';
	if($Specific=getModuleParameterLike('Accreditation', 'Matches-A-%', $toId)) {
		$f=array();
		foreach($Specific as $SpecType=>$SpecCats) {
			$tmp=explode('-', $SpecType);
			$f[]="(IceCardNumber=".end($tmp)." and find_in_set(concat(EnDivision,EnClass), '$SpecCats'))";
		}
		$ExtraSql="left join IdCardElements on IceTournament=EnTournament and IceType='AthQrCode' and IceCardType='A' and (".implode(' or ', $f).") ";
		$ExtraField='coalesce(IceContent, EnCode)';
	}

	switch($IskSequence['type']) {
        case 'Q':
	        $Session = $IskSequence['session'];
	        $tgtAssigned=array($DEVICE->IskDvTarget);
			if($DEVICE->IskDvTarget) {
				$Filter="QuTarget={$DEVICE->IskDvTarget} and QuSession=$Session";
			} else {
				$Filter="false";
			}
			$Grouping=getModuleParameter('ISK-NG', 'Grouping', null, $toId);

	        if($DEVICE->IskDvTarget and ($Grouping[$DEVICE->IskDvGroup] ?? 0)) {
		        $TargetNo=getGroupedTargets($DEVICE->IskDvTarget, $toId, $DEVICE->IskDvGroup);
		        $tgtAssigned=explode("','", $TargetNo);
		        $Filter="QuTarget in ($TargetNo) and QuSession=$Session";
	        }

	        $json_array['targetAssigned']=implode(', ', $tgtAssigned);
	        $json_array['type']='Q';
	        $json_array['session']=$Session;
	        $json_array['sessionName']='Session '.$Session;

	        $GoldsChars=[];
	        $XnineChars=[];
	        foreach($IskSequence['distance'] as $d) {
		        $GoldsChars[]="IF(TfGoldsChars{$d}='',IF(TfGoldsChars='',ToGoldsChars,TfGoldsChars),TfGoldsChars{$d})";
		        $XnineChars[]="IF(TfXNineChars{$d}='',IF(TfXNineChars='',ToXNineChars,TfXNineChars),TfXNineChars{$d})";
	        }

	        // Prepare the select used to retrieve competitor information
	        $SQL = "SELECT EnId, EnCode, EnName, ucase(EnFirstName) as EnFirstName, EnNameOrder, 
	                EnSex, EnDivision, DivDescription, EnClass, ClDescription,
	                CoCode, CoName, QuTargetNo, SUBSTRING(QuTargetNo,2) AS TargetNo, ToNumDist, ToCode,
	                IF(TfGolds='',ToGolds,TfGolds) as Golds, IF(TfXnine='',ToXnine,TfXnine) as Xnine, SesName,
	                SesTar4Session, TfName as TargetName,
	                GROUP_CONCAT(DiEnds ORDER BY DiDistance ASC SEPARATOR ',') as Ends,
	                GROUP_CONCAT(DiArrows ORDER BY DiDistance ASC SEPARATOR ',') as Arrows,
	                GROUP_CONCAT(DiDistance ORDER BY DiDistance ASC SEPARATOR ',') as Distance,
	                GROUP_CONCAT(DiScoringEnds ORDER BY DiDistance ASC SEPARATOR ',') as ScoringEnds,
	                GROUP_CONCAT(DiScoringOffset ORDER BY DiDistance ASC SEPARATOR ',') as ScoringOffset,
	                CONCAT_WS(',', TfT".implode(", TfT", $IskSequence['distance']).") as TargetFace,
	                CONCAT_WS(',', Td".implode(", Td", $IskSequence['distance']).") as DiName,
	                CONCAT_WS(',', ".implode(", ", $GoldsChars).") as GoldsChars,
	                CONCAT_WS(',', ".implode(", ", $XnineChars).") as XNineChars,
	                $ExtraField as QrCode
	            FROM Entries
	            INNER JOIN `Qualifications` ON EnId=QuId and QuIrmType=0
				INNER JOIN `Countries` ON EnCountry=CoId AND EnTournament=CoTournament
				INNER JOIN `Tournament` ON ToId=EnTournament
				INNER JOIN `Classes` ON EnClass=ClId AND EnTournament=ClTournament
				INNER JOIN `Divisions` ON EnDivision=DivId AND EnTournament=DivTournament
				INNER JOIN `TargetFaces` ON EnTargetFace=TfId AND EnTournament=TfTournament
				INNER JOIN `TournamentDistances` ON ToType=TdType and TdTournament=ToId AND CONCAT(TRIM(EnDivision),TRIM(EnClass)) LIKE TdClasses
				INNER JOIN `DistanceInformation` ON EnTournament=DiTournament and DiSession=QuSession and DiType='Q' and DiDistance in (".implode(',', $IskSequence['distance']).")
				INNER JOIN `Session` on SesTournament=EnTournament and SesType='Q' and SesOrder=QuSession
				$ExtraSql
				WHERE ToId=$toId AND EnAthlete=1 AND EnStatus <= 1 AND {$Filter}
				GROUP BY QuTargetNo
				ORDER BY QuTargetNo ";
	        // Retrieve the competitor info
	        $q=safe_r_sql($SQL);
	        if(safe_num_rows($q) !== 0) {
		        while ($r = safe_fetch($q)) {
			        $replacements=array(
				        '{ENCODE}'=>$r->EnCode,
				        '{COUNTRY}'=>$r->CoCode,
				        '{DIVISION}'=>$r->EnDivision,
				        '{CLASS}'=>$r->EnClass,
				        '{TOURNAMENT}'=>$r->ToCode,
			        );

			        // Now load the json array with the info we need
			        $row_array = array();
			        $row_array["refKey"] = $r->QuTargetNo;
			        $row_array["encode"] = $r->EnCode;
			        $row_array["name"] = $r->EnFirstName . ' ' . $r->EnName;
			        $row_array["placement"] = '';
			        $row_array["noc"] = $r->CoCode;
			        $row_array["nocname"] = $r->CoName;
			        $row_array["event"] = $r->EnDivision . $r->EnClass;
			        $row_array["eventname"] = $r->DivDescription . " " . $r->ClDescription;
                    $row_array["matchMode"] = 0;
			        $row_array["goldLbl"] = $r->Golds;
			        $row_array["xnineLbl"] = $r->Xnine;
			        $row_array["qrCode"] = str_replace(array_keys($replacements), array_values($replacements), $r->QrCode);
			        if($r->SesName) {
						$json_array['sessionName']=$r->SesName;
			        }
			        $tmpScoring = array();

			        $tmpEnds = explode(',', $r->Ends);
			        $tmpArrows = explode(',', $r->Arrows);
			        $tmpDistance = explode(',', $r->Distance);
			        $tmpDistName = explode(',', $r->DiName);
			        $tmpScoreEnds = explode(',', $r->ScoringEnds);
			        $tmpScoreOffset = explode(',', $r->ScoringOffset);
			        $tmpTargetFace = explode(',', $r->TargetFace);
			        $tmpGoldsChars = explode(',', $r->GoldsChars);
			        $tmpXNineChars = explode(',', $r->XNineChars);

			        foreach($tmpEnds as $i => $j) {
						if(!$row_array["placement"]) {
							$ScoringEnds=$tmpEnds[$i];
							$EndStart=1;
							$TotalTargets=$r->SesTar4Session;
							if($DEVICE->ToCategory==4 or $DEVICE->ToCategory==8) {
								$TotalTargets=$tmpEnds[$i];
								$row_array["placement"] = checkBisTargets($r->TargetNo, $TotalTargets);
								$EndStart=intval($row_array["placement"]);

							} else {
								$row_array["placement"] = ltrim($r->TargetNo, '0');
							}
						}
				        $tmpScoring[] = array(
					        'distance' => $tmpDistance[$i],
					        'distanceName' => $tmpDistName[$i],//.(($DEVICE->ToCategory==4 or $DEVICE->ToCategory==8) ? '-'.$r->TargetName : ''),
					        'ends' => $tmpEnds[$i],
					        'arrows' => $tmpArrows[$i],
					        'shootOff' => 0,
					        'soEnds' => 1,
					        'soClosest' => '0',
					        'gold' => $tmpGoldsChars[$i],
					        'xnine' => $tmpXNineChars[$i],
					        'targetface' => $tmpTargetFace[$i],
					        'arrowstring' => '',
					        'previous' => 0,
					        'previousGold' => 0,
					        'previousXnine' => 0,
					        'endStartNumber' => ((intval($EndStart+$tmpScoreOffset[$i])-1) % $TotalTargets)+1,
			                'scoringEnds' => intval($tmpScoreEnds[$i] ?: $tmpEnds[$i]),
			                'totalTargets' => intval($TotalTargets)
				        );
				        $TgtFaceList[$tmpTargetFace[$i]] = $TgtFaceTemplate;
				        $TgtFaceList[$tmpTargetFace[$i]]["gold"] = $tmpGoldsChars[$i];
				        $TgtFaceList[$tmpTargetFace[$i]]["xnine"] = $tmpXNineChars[$i];
				        $TgtFaceList[$tmpTargetFace[$i]]["goldlbl"] = $r->Golds;
				        $TgtFaceList[$tmpTargetFace[$i]]["xninelbl"] = $r->Xnine;
			        }

			        $row_array["scoring"] = $tmpScoring;
			        $json_array['archers'][$r->QuTargetNo] = $row_array;
		        }
	        } else {
		        return resetDevice($DEVICE->IskDvDevice, $json_array['targetAssigned']);
	        }
	        break;
        // case 'E1':
        // case 'E2':
        //     $Session = $IskSequence['session'];
        //     $Phase=substr($IskSequence['type'], 1)-1;
        //     // Add leading zeroes because the app doesn't send them
        //     $TargetNo=$Session.sprintf("%03s", $DEVICE->IskDvTarget);
        //     $tgtAssigned=array($DEVICE->IskDvTarget);
		//
        //     $json_array['targetAssigned']=implode(', ', $tgtAssigned);
        //     $json_array['type']=$IskSequence['type'];
        //     $json_array['current']=array(
        //         'session'=>$Session,
        //         'distance'=>'0',
        //         'end'=>$IskSequence['end'],
        //         'sessionName'=>'Session '.$Session,
        //         'distanceName'=>$IskSequence['distance'],
        //         'ends' => 0,
        //         'arrows' => 0,
        //         'shootOff' => 3,
        //         'soEnds'=>array()
        //     );
		//
        //     // get the distances, Course name, Ends and arrows/end
        //     $SQL="select distinct if(ElElimPhase=0, EvE1Ends, EvE2Ends) as DiEnds, if(ElElimPhase=0, EvE1Arrows, EvE2Arrows) as DiArrows, SesName
		// 		FROM Events
		// 		INNER JOIN Eliminations on ElTournament=EvTournament and ElElimPhase=$Phase and EvCode=ElEventCode and EvTeamEvent=0 and EvElim".($Phase+1).">0
		// 		Left join Session on SesTournament=EvTournament and ElSession=SesOrder AND SesType='E'
		// 		WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " and ElSession=$Session
		// 		";
		//
        //     $q=safe_r_sql($SQL);
        //     if($r=safe_fetch($q)) {
        //         if($r->SesName) {
        //             $json_array['current']['sessionName']=$r->SesName;
        //         }
        //         $json_array['current']['distanceName']='';
        //         $json_array['current']['ends']=$r->DiEnds;
        //         $json_array['current']['arrows']=$r->DiArrows;
        //     } else {
        //         return resetDevice($DEVICE->IskDvDevice, $json_array['targetAssigned']);
        //     }
		//
        //     // Prepare the select used to retrieve competitor information
        //     $Select = "SELECT EnId, EnCode, EnName, ucase(EnFirstName) as EnFirstName, EnNameOrder, EnSex, EvCode, EvEventName, CoCode, CoName,
		// 			ElTargetNo,
		// 			SUBSTRING(ElTargetNo,2) AS TargetNo,
		//             EvFinalTargetType,
		//             ElArrowstring ArrowString
		// 		FROM Entries
		// 		INNER JOIN Eliminations ON EnId=ElId and ElElimPhase={$Phase} and ElSession=$Session
		// 		INNER JOIN Countries ON EnCountry=CoId AND EnTournament=CoTournament
		// 		INNER JOIN Events  on EvTournament=EnTournament and EvCode=ElEventCode and EvTeamEvent=0
		// 		WHERE EnTournament=$toId
		// 			AND EnAthlete=1
		// 			AND EnStatus <= 1
		// 			AND left(concat(ElSession, ElTargetNo), 4) in ('".$TargetNo."')
		// 		ORDER BY ElTargetNo ";
        //     // Retrieve the competitor info
        //     $Rs=safe_r_sql($Select);
		//
        //     while ($MyRow=safe_fetch($Rs)) {
        //         // Now load the json array with the info we need
        //         $row_array=array();
        //         $row_array["encode"] = $MyRow->EnCode;
        //         $row_array["name"] = $MyRow->EnFirstName . ' ' . $MyRow->EnName;
        //         $row_array["placement"] = ltrim($MyRow->TargetNo, '0');
        //         $row_array["noc"] = $MyRow->CoCode;
        //         $row_array["nocname"] = $MyRow->CoName;
        //         $row_array["event"] = $MyRow->EvCode;
        //         $row_array["eventname"] = $MyRow->EvEventName;
        //         $row_array["qutarget"] = $MyRow->ElTargetNo;
        //         $row_array["targetface"] = $MyRow->EvFinalTargetType;
        //         $row_array["arrowstring"] = str_pad($MyRow->ArrowString, $json_array['current']['ends']*$json_array['current']['arrows'] , ' ', STR_PAD_RIGHT);
		//
        //         // adjust with what we have in the temporary table
        //         $t=safe_r_sql("select * from IskData
		// 				where IskDtTournament=$toId
		// 					and IskDtTargetNo='$MyRow->ElTargetNo'
		// 					and IskDtType='{$IskSequence['type']}'
		// 					and IskDtDevice=".StrSafe_DB($DEVICE->IskDvDevice));
		//
        //         while($u=safe_fetch($t)) {
        //             $row_array["arrowstring"]=substr_replace($row_array["arrowstring"], $u->IskDtArrowstring, intval(($u->IskDtEndNo-1)*$json_array['current']['arrows']), intval($json_array['current']['arrows']));
        //         }
        //         $json_array['current']['soEnds'][]=1;
        //         $json_array['archers'][]=$row_array;
        //     }
		//
        //     if(empty($json_array['archers'])) {
        //         return resetDevice($DEVICE->IskDvDevice, $json_array['targetAssigned']);
        //     }
		//
        //     // sets the status!
        //     safe_w_sql("update IskDevices SET IskDvProActive = 1 WHERE IskDvDevice = ".StrSafe_DB($DEVICE->IskDvDevice));
        //     break;
        case 'M':
			require_once('Common/Lib/CommonLib.php');
	        require_once('Common/Lib/Fun_Phases.inc.php');

	        $Filter="Target1=$DEVICE->IskDvTarget";
	        $tgtAssigned=array($DEVICE->IskDvTarget);
	        $Grouping=getModuleParameter('ISK-NG', 'Grouping', null, $toId);

	        if($Grouping[$DEVICE->IskDvGroup] ?? 0) {
                $TargetNo=getGroupedTargets($DEVICE->IskDvTarget, $toId, $DEVICE->IskDvGroup);
		        $tgtAssigned=explode("','", $TargetNo);
                $Filter="Target1 in ($TargetNo)";
            }

			$Schedule=getApiScheduledSessions(['TourId' => $toId, 'Short' =>1]);

	        $json_array['targetAssigned']=implode(', ', $tgtAssigned);
	        $json_array['type']='M';
	        $json_array['subType']=$IskSequence['subtype'];
			if(isset($Schedule[$IskSequence['IskKey']])) {
				$json_array['session']=substr($Schedule[$IskSequence['IskKey']]->Description,0,strpos($Schedule[$IskSequence['IskKey']]->Description,':',strpos($Schedule[$IskSequence['IskKey']]->Description,':')+1)+3);
				$json_array['sessionName']=substr($Schedule[$IskSequence['IskKey']]->Description,strlen($json_array['session'])+1);
			} else {
				$json_array['session']='';
				$json_array['sessionName']='';
			}

	        // fetches the match on this target for this session...
	        $Date=substr($IskSequence['session'], 0, 10);
	        $Time=substr($IskSequence['session'], 10);

			switch($IskSequence['subtype']) {
				case 'I':
					// regular individual matches
					if(isset($IskSequence['matchid'])) {
						list($Team, $Event, $Match)=explode('|', $IskSequence['matchid']);
						$Team=intval($Team);
						$Match=intval($Match/2)*2;
						$MainFilter="FsTournament=$toId and FsTarget>'' and FsTeamEvent=$Team and FsEvent=".StrSafe_DB($Event). " and GrPhase = (select GrPhase from Grids where GrMatchNo=$Match)";
					} else {
						$MainFilter="FsTournament=$toId and FsTarget>'' and FsTeamEvent=0 and FsScheduledDate='$Date' and FsScheduledTime='$Time'";
					}
					$SQL="select EvDistance, EvFinalTargetType, EvCode, EvTeamEvent, EvMatchMode, EvEventName, GrPhase, EvFinalFirstPhase, 
                            EvCheckGolds as CheckGolds, EvCheckXNines as CheckXNines,
       						if(EvGolds!='', EvGolds, ToGolds) as GoldLabel, 
       						if(EvXNine!='', EvXNine, ToXNine) as XNineLabel, 
       						if(EvGoldsChars!='', EvGoldsChars, ToGoldsChars) as GoldsChars, 
       						if(EvXNineChars!='', EvXNineChars, ToXNineChars) as XNineChars,
       						concat_ws('|', EvTeamEvent, EvCode, FsMatchNo1) as refKey1,
       						concat_ws('|', EvTeamEvent, EvCode, FsMatchNo2) as refKey2,
							tgt1.*, tgt2.*,
							if(GrPhase1 & EvMatchArrowsNo, EvElimArrows, EvFinArrows) Arrows, if(GrPhase1 & EvMatchArrowsNo, EvElimEnds, EvFinEnds) Ends, if(GrPhase1 & EvMatchArrowsNo, EvElimSO, EvFinSO) SO
						from (select concat(date_format(FSScheduledDate, '%e %b'), ' ', date_format(FSScheduledTime, '%H:%i')) as Scheduled, GrBitPhase as GrPhase1, 
				            	EnCode EnCode1, CoCode CoCode1, CoName Country1, concat(ucase(EnFirstName), ' ', EnName) Athlete1, FinAthlete Entry1, 
				            	FinArrowstring Arrowstring1, FinTieBreak TieBreak1, FsTarget+0 Target1, substr(FsLetter, length(FsTarget)+1, 1) Letter1, 
				            	FsLetter FsLetter1, FsMatchNo FsMatchNo1, FsEvent FsEvent1, FinWinLose as Win1, FinTbClosest as Closest1
							from FinSchedule
							inner join Grids on FsMatchNo=GrMatchno
							inner join Finals on FsEvent=FinEvent and FinTournament=$toId and FsMatchNo=FinMatchNo
							inner join Entries on EnId=FinAthlete
							inner join Countries on CoId=EnCountry
							where $MainFilter and FsMatchNo%2=0) tgt1
						inner join (select 
			                	EnCode EnCode2, CoCode CoCode2, CoName Country2, concat(ucase(EnFirstName), ' ', EnName) Athlete2, FinAthlete Entry2, 
			                	FinArrowstring Arrowstring2, FinTieBreak TieBreak2, FsTarget+0 Target2, substr(FsLetter, length(FsTarget)+1, 1) Letter2, 
			                	FsLetter FsLetter2, FsMatchNo FsMatchNo2, FsEvent FsEvent2, FinWinLose as Win2, FinTbClosest as Closest2
							from FinSchedule
							inner join Grids on FsMatchNo=GrMatchno
							inner join Finals on FsEvent=FinEvent and FsTournament=FinTournament and FsMatchNo=FinMatchNo
							inner join Entries on EnId=FinAthlete
							inner join Countries on CoId=EnCountry
							where $MainFilter) tgt2
							on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
						inner join Events on FsEvent1=EvCode and EvTeamEvent=0 and EvTournament=$toId
						inner join Grids on FsMatchNo1=GrMatchNo
						inner join Tournament on ToId=EvTournament
						where $Filter
						order by EvCode, Target1, FsLetter1";
					break;
				case 'R':
					// Round Robin Matches
					$Team=($IskSequence['IskKey'][0]=='I' ? 0 : 1);
					if(isset($IskSequence['matchid'])) {
						list($Team, $Event, $Level, $Group, $Round, $Match)=explode('|', $IskSequence['matchid']);
						$Team=intval($Team);
						$Level=intval($Level);
						$Group=intval($Group);
						$Round=intval($Round);
						$Match=intval($Match/2)*2;
						$MainFilter="RrMatchTournament=$toId  and RrMatchTeam=$Team and RrMatchEvent=".StrSafe_DB($Event)." and RrMatchLevel=$Level and RrMatchGroup=$Group and RrMatchRound=$Round";
					} else {
						$MainFilter = "RrMatchTournament=$toId  and RrMatchTeam=$Team and RrMatchScheduledDate='$Date' and RrMatchScheduledTime='$Time'";
					}

					if($Team) {
						$SQL="select EvDistance, EvFinalTargetType, EvCode, EvTeamEvent, RrLevMatchMode as EvMatchMode, EvEventName, 999 GrPhase, EvFinalFirstPhase, RrLevName, RrGrName, RrMatchRound,
       						concat('L',RrMatchLevel,'G',RrMatchGroup,'R',RrMatchRound) as PhaseCode, concat_ws(' ', RrLevName, RrGrName) as PhaseName,
                            RrLevCheckGolds as CheckGolds, RrLevCheckXNines as CheckXNines,
       						if(EvGolds!='', EvGolds, ToGolds) as GoldLabel, 
                            if(EvXNine!='', EvXNine, ToXNine) as XNineLabel, 
                            if(EvGoldsChars!='', EvGoldsChars, ToGoldsChars) as GoldsChars, 
                            if(EvXNineChars!='', EvXNineChars, ToXNineChars) as XNineChars,
							tgt1.*, tgt2.*,
							RrLevArrows as Arrows, RrLevEnds as Ends, RrLevSO as SO
						from (
						    select CoCode EnCode1, CoCode CoCode1, CoName Country1, CoName Athlete1, RrMatchAthlete Entry1, RrMatchArrowstring Arrowstring1, RrMatchTiebreak TieBreak1, RrMatchTarget+0 Target1, right(RrMatchTarget, 1) Letter1, RrMatchTarget FsLetter1, RrMatchMatchNo+(RrMatchRound*100)+(RrMatchGroup*10000)+(RrMatchLevel*1000000) FsMatchNo1, RrMatchEvent FsEvent1, RrMatchWinLose as Win1,
					           	RrMatchLevel, RrMatchGroup, RrMatchRound, concat(date_format(RrMatchScheduledDate, '%e %b'), ' ', date_format(RrMatchScheduledTime, '%H:%i')) as Scheduled,
								concat_ws('|', RrMatchTeam, RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) as refKey1
							from RoundRobinMatches
							left join Countries on CoId=RrMatchAthlete
							where $MainFilter and RrMatchMatchNo % 2=0) tgt1
						inner join (
						    select CoCode EnCode2, CoCode CoCode2, CoName Country2, CoName Athlete2, RrMatchAthlete Entry2, RrMatchArrowstring Arrowstring2, RrMatchTiebreak TieBreak2, RrMatchTarget+0 Target2, right(RrMatchTarget, 1) Letter2, RrMatchTarget FsLetter2, RrMatchMatchNo+(RrMatchRound*100)+(RrMatchGroup*10000)+(RrMatchLevel*1000000) FsMatchNo2, RrMatchEvent FsEvent2, RrMatchWinLose as Win2,
								concat_ws('|', RrMatchTeam, RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) as refKey2
							from RoundRobinMatches
							left join Countries on CoId=RrMatchAthlete
							where $MainFilter and RrMatchMatchNo % 2=1) tgt2
							on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
						inner join Events on FsEvent1=EvCode and EvTeamEvent=$Team and EvTournament=$toId
						inner join RoundRobinLevel on RrLevLevel=RrMatchLevel and RrLevEvent=FsEvent1 and RrLevTeam=$Team and RrLevTournament=$toId
						inner join RoundRobinGroup on RrGrLevel=RrMatchLevel and RrGrGroup=RrMatchGroup and RrGrEvent=FsEvent1 and RrGrTeam=$Team and RrLevTournament=$toId
						inner join Tournament on ToId=EvTournament
						where $Filter
						order by greatest(FsLetter1, FsLetter2)";
					} else {
						$SQL="select EvDistance, EvFinalTargetType, EvCode, EvTeamEvent, RrLevMatchMode as EvMatchMode, EvEventName, 999 GrPhase, EvFinalFirstPhase, RrLevName, RrGrName, RrMatchRound,
       						concat('L',RrMatchLevel,'G',RrMatchGroup,'R',RrMatchRound) as PhaseCode, concat_ws(' ', RrLevName, RrGrName) as PhaseName,
                            RrLevCheckGolds as CheckGolds, RrLevCheckXNines as CheckXNines,
                            if(EvGolds!='', EvGolds, ToGolds) as GoldLabel, 
                            if(EvXNine!='', EvXNine, ToXNine) as XNineLabel, 
                            if(EvGoldsChars!='', EvGoldsChars, ToGoldsChars) as GoldsChars, 
                            if(EvXNineChars!='', EvXNineChars, ToXNineChars) as XNineChars,
							tgt1.*, tgt2.*,
							RrLevArrows as Arrows, RrLevEnds as Ends, RrLevSO as SO
						from (
						    select EnCode EnCode1, CoCode CoCode1, CoName Country1, concat(ucase(EnFirstName), ' ', EnName) Athlete1, RrMatchAthlete Entry1, RrMatchArrowstring Arrowstring1, RrMatchTiebreak TieBreak1, RrMatchTarget+0 Target1, right(RrMatchTarget, 1) Letter1, RrMatchTarget FsLetter1, RrMatchMatchNo+(RrMatchRound*100)+(RrMatchGroup*10000)+(RrMatchLevel*1000000) FsMatchNo1, RrMatchEvent FsEvent1, RrMatchWinLose as Win1,
					           	RrMatchLevel, RrMatchGroup, RrMatchRound, concat(date_format(RrMatchScheduledDate, '%e %b'), ' ', date_format(RrMatchScheduledTime, '%H:%i')) as Scheduled,
								concat_ws('|', RrMatchTeam, RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) as refKey1
							from RoundRobinMatches
							left join Entries on EnId=RrMatchAthlete
							left join Countries on CoId=EnCountry
							where $MainFilter and RrMatchMatchNo % 2=0) tgt1
						inner join (
						    select EnCode EnCode2, CoCode CoCode2, CoName Country2, concat(ucase(EnFirstName), ' ', EnName) Athlete2, RrMatchAthlete Entry2, RrMatchArrowstring Arrowstring2, RrMatchTiebreak TieBreak2, RrMatchTarget+0 Target2, right(RrMatchTarget, 1) Letter2, RrMatchTarget FsLetter2, RrMatchMatchNo+(RrMatchRound*100)+(RrMatchGroup*10000)+(RrMatchLevel*1000000) FsMatchNo2, RrMatchEvent FsEvent2, RrMatchWinLose as Win2,
								concat_ws('|', RrMatchTeam, RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo) as refKey2
							from RoundRobinMatches
							left join Entries on EnId=RrMatchAthlete
							left join Countries on CoId=EnCountry
							where $MainFilter and RrMatchMatchNo % 2=1) tgt2
							on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
						inner join Events on FsEvent1=EvCode and EvTeamEvent=$Team and EvTournament=$toId
						inner join RoundRobinLevel on RrLevLevel=RrMatchLevel and RrLevEvent=FsEvent1 and RrLevTeam=$Team and RrLevTournament=$toId
						inner join RoundRobinGroup on RrGrLevel=RrMatchLevel and RrGrGroup=RrMatchGroup and RrGrEvent=FsEvent1 and RrGrTeam=$Team and RrLevTournament=$toId
						inner join Tournament on ToId=EvTournament
						where $Filter
						order by greatest(FsLetter1, FsLetter2)";
					}
					break;
				case 'T':
					// team matches
					if(isset($IskSequence['matchid'])) {
						list($Team, $Event, $Match)=explode('|', $IskSequence['matchid']);
						$Team=intval($Team);
						$Match=intval($Match/2)*2;
						$MainFilter="FsTournament=$toId and FsTarget>'' and FsTeamEvent=$Team and FsEvent=".StrSafe_DB($Event). " and GrPhase = (select GrPhase from Grids where GrMatchNo=$Match)";
					} else {
						$MainFilter="FsTournament=$toId and FsTarget>'' and FsTeamEvent=1 and FsScheduledDate='$Date' and FsScheduledTime='$Time'";
					}
					$SQL="select EvDistance, EvFinalTargetType, EvCode, EvTeamEvent, EvMatchMode, EvEventName, GrPhase, EvFinalFirstPhase,
                            EvCheckGolds as CheckGolds, EvCheckXNines as CheckXNines,
                            if(EvGolds!='', EvGolds, ToGolds) as GoldLabel, 
       						if(EvXNine!='', EvXNine, ToXNine) as XNineLabel, 
       						if(EvGoldsChars!='', EvGoldsChars, ToGoldsChars) as GoldsChars, 
       						if(EvXNineChars!='', EvXNineChars, ToXNineChars) as XNineChars,
       						concat_ws('|', EvTeamEvent, EvCode, FsMatchNo1) as refKey1,
       						concat_ws('|', EvTeamEvent, EvCode, FsMatchNo2) as refKey2,
							tgt1.*, tgt2.*,
							if(GrPhase1 & EvMatchArrowsNo, EvElimArrows, EvFinArrows) Arrows, if(GrPhase1 & EvMatchArrowsNo, EvElimEnds, EvFinEnds) Ends, if(GrPhase1 & EvMatchArrowsNo, EvElimSO, EvFinSO) SO
						from (select concat(date_format(FSScheduledDate, '%e %b'), ' ', date_format(FSScheduledTime, '%H:%i')) as Scheduled, GrPhase as GrPhase1,
				            	CoCode EnCode1, CoCode CoCode1, CoName Country1, CoName Athlete1, '' Entry1, TfArrowstring Arrowstring1, TfTieBreak TieBreak1, 
								FsTarget+0 Target1, substr(FsLetter, length(FsTarget)+1, 1) Letter1,
								FsLetter FsLetter1, FsMatchNo FsMatchNo1, FsEvent FsEvent1, TfWinLose as Win1, TfTbClosest as Closest1
							from FinSchedule
							inner join Grids on FsMatchNo=GrMatchno
							inner join TeamFinals on FsEvent=TfEvent and TfTournament=$toId and FsMatchNo=TfMatchNo
							inner join Teams on TeCoId=TfTeam and TeSubTeam=TfSubTeam and TeEvent=TfEvent and TeTournament=$toId and TeFinEvent=1
							inner join Countries on CoId=TeCoId
							where $MainFilter and FsMatchNo%2=0) tgt1
						inner join (select 
			                	CoCode EnCode2, CoCode CoCode2, CoName Country2, CoName Athlete2, '' Entry2, TfArrowstring Arrowstring2, TfTieBreak TieBreak2, 
			                	FsTarget+0 Target2, substr(FsLetter, length(FsTarget)+1, 1) Letter2, 
			                	FsLetter FsLetter2, FsMatchNo FsMatchNo2, FsEvent FsEvent2, TfWinLose as Win2, TfTbClosest as Closest2
							from FinSchedule
							inner join Grids on FsMatchNo=GrMatchno
							inner join TeamFinals on FsEvent=TfEvent and TfTournament=$toId and FsMatchNo=TfMatchNo
							inner join Teams on TeCoId=TfTeam and TeSubTeam=TfSubTeam and TeEvent=TfEvent and TeTournament=$toId and TeFinEvent=1
							inner join Countries on CoId=TeCoId
							where $MainFilter) tgt2
							on FsEvent1=FsEvent2 and FsMatchNo2=FsMatchNo1+1
						inner join Events on FsEvent1=EvCode and EvTeamEvent=1 and EvTournament=$toId
						inner join Grids on FsMatchNo1=GrMatchNo
						inner join Tournament on ToId=EvTournament
						where $Filter
						order by EvCode, Target1, FsLetter1";
					break;
			}
	        $q=safe_r_sql($SQL);
	        if(!safe_num_rows($q)) {
		        return resetDevice($DEVICE->IskDvDevice, $json_array['targetAssigned'], $Force);
	        }

	        while($r=safe_fetch($q)) {
		        // there is a "left target" associated with this device on this session...
		        $TgtFaceList[$r->EvFinalTargetType] = $TgtFaceTemplate;
		        $TgtFaceList[$r->EvFinalTargetType]["gold"] = $r->GoldsChars;
		        $TgtFaceList[$r->EvFinalTargetType]["xnine"] = $r->XNineChars;
		        $TgtFaceList[$r->EvFinalTargetType]["goldlbl"] = $r->GoldLabel;
		        $TgtFaceList[$r->EvFinalTargetType]["xninelbl"] = $r->XNineLabel;

				if($r->GrPhase=='999') {
					$EventCode = $r->PhaseCode . ' ' . $r->EvCode;
					$EventName = $r->PhaseName . ' ' . get_text('RoundNum','RoundRobin', $r->RrMatchRound) . ' - '  . $r->EvEventName;
				} else {
					$EventCode = get_text(namePhase($r->EvFinalFirstPhase,$r->GrPhase).'_Phase') . ' ' . $r->EvCode;
					$EventName = get_text(namePhase($r->EvFinalFirstPhase,$r->GrPhase).'_Phase') . ' - '  . $r->EvEventName;
				}

		        // LEFT archer
		        $row_array=array();
		        $row_array["refKey"] = $r->refKey1;
		        $row_array["encode"] = $r->EnCode1;
		        $row_array["name"] = $r->Athlete1;
		        $row_array["placement"] = ltrim($r->FsLetter1, '0');
		        $row_array["noc"] = $r->CoCode1;
		        $row_array["nocname"] = $r->Country1;
		        $row_array["event"] = $EventCode;
		        $row_array["eventname"] = $EventName;
		        $row_array['matchmode'] = $r->EvMatchMode;
		        $row_array['checkGolds'] = (bool) $r->CheckGolds;
		        $row_array['checkXnines'] = (bool) $r->CheckXNines;

		        $row_array["scoring"] = [[
			        "distance" => "1",
                    "distanceName" => $r->EvDistance,
                    "ends" => $r->Ends,
                    "arrows" => $r->Arrows,
                    "shootOff" => $r->SO,
                    "soEnds" => 1,
                    "soClosest" => '0',
                    "gold" => $r->GoldsChars,
                    "xnine" => $r->XNineChars,
                    "targetface" => $r->EvFinalTargetType,
                    "arrowstring" => '',
                    "previous" => "0",
                    "previousGold" => "0",
                    "previousXnine" => "0",
			        'endStartNumber' => 1,
			        'scoringEnds' => intval($r->Ends),
			        'totalTargets' => intval($r->Ends)
		        ]];

		        $json_array['archers'][$r->refKey1]=$row_array;

		        // RIGHT archer
		        $row_array=array();
		        $row_array["refKey"] = $r->refKey2;
		        $row_array["encode"] = $r->EnCode2;
		        $row_array["name"] = $r->Athlete2;
		        $row_array["placement"] = ltrim($r->FsLetter2, '0');
		        $row_array["noc"] = $r->CoCode2;
		        $row_array["nocname"] = $r->Country2;
		        $row_array["event"] = $EventCode;
		        $row_array["eventname"] = $EventName;
		        $row_array['matchmode'] = $r->EvMatchMode;
                $row_array['checkGolds'] = (bool) $r->CheckGolds;
                $row_array['checkXnines'] = (bool) $r->CheckXNines;

		        $row_array["scoring"] = [[
			        "distance" => "1",
			        "distanceName" => $r->EvDistance,
			        "ends" => $r->Ends,
			        "arrows" => $r->Arrows,
			        "shootOff" => $r->SO,
			        "soEnds" => 1,
			        "soClosest" => '0',
			        "gold" => $r->GoldsChars,
			        "xnine" => $r->XNineChars,
			        "targetface" => $r->EvFinalTargetType,
			        "arrowstring" => '',
			        "previous" => "0",
			        "previousGold" => "0",
			        "previousXnine" => "0",
			        'endStartNumber' => 1,
			        'scoringEnds' => intval($r->Ends),
			        'totalTargets' => intval($r->Ends)
		        ]];

		        $json_array['archers'][$r->refKey2]=$row_array;

	        }

	        // ROUND ROBIN STUFF
	        // if(!empty($IskSequence['subtype'])) {
	        //
	        //     while($r=safe_fetch($q)) {
	        //         // there is a "left target" associated with this device on this session...
	        //         $json_array['current']=array(
	        //             'session'=>'',
	        //             'distance'=>'0',
	        //             'end'=>$IskSequence['end'],
	        //             'sessionName'=>get_text('R-Session', 'Tournament').' - '.$r->Scheduled,
	        //             'distanceName'=>$r->EvDistance,
	        //             'ends' => $r->Ends,
	        //             'arrows' => $r->Arrows,
	        //             'shootOff' => $r->SO,
	        //             'soEnds'=>array()
	        //         );
	        //
	        //         // LEFT archer
	        //         $row_array=array();
	        //         $row_array["encode"] = $r->EnCode1;
	        //         $row_array["name"] = $r->Athlete1;
	        //         $row_array["placement"] = ltrim($r->FsLetter1, '0');
	        //         $row_array["noc"] = $r->CoCode1;
	        //         $row_array["nocname"] = $r->Country1;
	        //         $row_array["event"] = $r->EvCode;
	        //         $row_array["eventname"] = $r->RrLevName.' '.$r->RrGrName.' '.get_text('RoundNum', 'RoundRobin', $r->RrMatchRound);
	        //         $row_array['matchmode'] = $r->EvMatchMode;
	        //         $row_array["qutarget"] = $r->EvCode.'|'.$r->FsMatchNo1;
	        //         $row_array["targetface"] = $r->EvFinalTargetType;
	        //         $row_array["arrowstring"] = str_pad($r->Arrowstring1, $json_array['current']['ends']*$json_array['current']['arrows'] , ' ', STR_PAD_RIGHT) .
	        //             (strlen(trim($r->TieBreak1))!=0 ? trim($r->TieBreak1) : "");
	        //
	        //         // adjust with what we have in the temporary table
	        //         $t=safe_r_sql("select * from IskData
	        // 			where IskDtTournament=$toId
	        // 			and IskDtMatchNo='$r->FsMatchNo1'
	        // 			and IskDtEvent='$r->EvCode'
	        // 			and IskDtTeamInd=0
	        // 			and IskDtType='{$IskSequence['type']}'
	        // 			and IskDtDevice=".StrSafe_DB($DEVICE->IskDvDevice));
	        //
	        //         while($u=safe_fetch($t)) {
	        //             $row_array["arrowstring"]=substr_replace($row_array["arrowstring"], $u->IskDtArrowstring, ($u->IskDtEndNo-1)*$json_array['current']['arrows'], $json_array['current']['arrows']);
	        //         }
	        //         if(strlen($row_array["arrowstring"])<=($r->Ends*$r->Arrows)) {
	        //             $soEnds[]=1;
	        //         } else {
	        //             $soEnds[]=ceil((strlen($row_array["arrowstring"])-($r->Ends*$r->Arrows))/$r->SO);
	        //         }
	        //         $json_array['archers'][]=$row_array;
	        //
	        //         // RIGHT archer
	        //         $row_array=array();
	        //         $row_array["encode"] = $r->EnCode2;
	        //         $row_array["name"] = $r->Athlete2;
	        //         $row_array["placement"] = ltrim($r->FsLetter2, '0');
	        //         $row_array["noc"] = $r->CoCode2;
	        //         $row_array["nocname"] = $r->Country2;
	        //         $row_array["event"] = $r->EvCode;
	        //         $row_array["eventname"] = $r->RrLevName.' '.$r->RrGrName.' '.get_text('RoundNum', 'RoundRobin', $r->RrMatchRound);
	        //         $row_array['matchmode'] = $r->EvMatchMode;
	        //         $row_array["qutarget"] = $r->EvCode.'|'.$r->FsMatchNo2;
	        //         $row_array["targetface"] = $r->EvFinalTargetType;
	        //         $row_array["arrowstring"] = str_pad($r->Arrowstring2, $json_array['current']['ends']*$json_array['current']['arrows'] , ' ', STR_PAD_RIGHT) .
	        //             (strlen(trim($r->TieBreak2))!=0 ? trim($r->TieBreak2) : "");
	        //
	        //         // adjust with what we have in the temporary table
	        //         $t=safe_r_sql("select * from IskData
	        // 			where IskDtTournament=$toId
	        // 			and IskDtMatchNo='$r->FsMatchNo2'
	        // 			and IskDtEvent='$r->EvCode'
	        // 			and IskDtTeamInd=0
	        // 			and IskDtType='{$IskSequence['type']}'
	        // 			and IskDtDevice=".StrSafe_DB($DEVICE->IskDvDevice));
	        //
	        //         while($u=safe_fetch($t)) {
	        //             $row_array["arrowstring"]=substr_replace($row_array["arrowstring"], $u->IskDtArrowstring, ($u->IskDtEndNo-1)*$json_array['current']['arrows'], $json_array['current']['arrows']);
	        //         }
	        //         if(strlen($row_array["arrowstring"])<=($r->Ends*$r->Arrows)) {
	        //             $soEnds[]=1;
	        //         } else {
	        //             $soEnds[]=ceil((strlen($row_array["arrowstring"])-($r->Ends*$r->Arrows))/$r->SO);
	        //         }
	        //         $json_array['archers'][]=$row_array;
	        //
	        //     }
	        //     $json_array['current']['soEnds']=$soEnds;
	        // } else {
	        // }
            break;
        default:
            return resetDevice($DEVICE->IskDvDevice, $DEVICE->IskDvTarget, $Force);
    }

	// manage target list
	require_once('Common/Lib/ArrTargets.inc.php');
	if($TgtFaceList) {
		$q=safe_r_sql("select * from Targets where TarId in (".implode(',', array_keys($TgtFaceList)).")");
		while($r=safe_fetch($q)) {
			$TgtFaceList[$r->TarId]['code']=$r->TarId;
			$TgtFaceList[$r->TarId]["id"] = $r->TarDescr;
			$TgtFaceList[$r->TarId]["name"] = get_text($r->TarDescr);
			$img=$CFG->DOCUMENT_PATH.'Common/Images/Targets/'.$r->TarId.'.svgz';
			if(is_file($img)) {
				$TgtFaceList[$r->TarId]["imgName"] = $r->TarId.'.svg';
                if($NeedImages) {
                    $TgtFaceList[$r->TarId]["imgGzip"] = base64_encode(file_get_contents($img));
                    $TgtFaceList[$r->TarId]["imgBase64Prefix"] = 'data:image/svg+xml;base64,';
                }
			} else {
				$img=$CFG->DOCUMENT_PATH.'Common/Images/Targets/99.svgz';
				$TgtFaceList[$r->TarId]["imgName"] = '99.svg';
                if($NeedImages) {
                    $TgtFaceList[$r->TarId]["imgGzip"] = base64_encode(file_get_contents($img));
                    $TgtFaceList[$r->TarId]["imgBase64Prefix"] = 'data:image/svg+xml;base64,';
                }
			}
			$TgtFaceList[$r->TarId]["letterPoint"] = GetTargetNgInfo($r->TarId);
			if(!$TgtFaceList[$r->TarId]["letterPoint"]) {
				$TgtFaceList[$r->TarId]["letterPoint"] = ["letter" => "A", "point" => "M", "num" => 0, "bg" => "#999999", "fg" => "#000000"];
			}

			$NumValues=count($TgtFaceList[$r->TarId]["letterPoint"]);
			$TgtFaceList[$r->TarId]["rows"] = ceil($NumValues/3);
			$TgtFaceList[$r->TarId]["cols"] = min(3, $NumValues);
			// if($TgtFaceList[$r->TarId]["rows"]*$TgtFaceList[$r->TarId]["cols"] != $NumValues) {
			// 	for($n=0; $n < $TgtFaceList[$r->TarId]["rows"]*$TgtFaceList[$r->TarId]["cols"]-$NumValues; $n++) {
			// 		$TgtFaceList[$r->TarId]["letterPoint"][]=["letter" => "", "point" => "", "num" => 0, "bg" => "", "fg" => ""];
			// 	}
			// }
		}
	}
	$json_array['targetFaces']=array_values($TgtFaceList);

	// sets the JSON directly into the device
    // in this way single tablets can be on a different setup
	safe_w_sql("update IskDevices set IskDvSchedKey=".StrSafe_DB($DEVICE->ScheduleKey).", IskDvSetupConfirmed=0, IskDvSetup=".StrSafe_DB(json_encode($json_array))." where IskDvDevice='$DEVICE->IskDvDevice'");
}

function resetDevice($devId, $tgt=0, $Force=false) {
    $JSON=array('action'=>'reset', 'device'=>$devId, 'resetMessage' => 'Device not in use', 'resetSubMsg' => '', 'resetTarget' => '');
    if(empty($tgt)) {
        $JSON['resetSubMsg']='Return to Results Crew';
    } else {
        $JSON['resetTarget']=$tgt;
    }
    if($devId and $Force) {
        safe_w_sql("update IskDevices set IskDvSchedKey='', IskDvSetup=".StrSafe_DB(json_encode($JSON))." where IskDvDevice='{$devId}'");
    }
    return $JSON;
}

function getGroupedTargets($TargetNo, $ToId=0, $Group=0) {
    global $CompId;

    if(!$ToId) {
        $ToId=$CompId;
    }

    // get all targets associated/grouped together with the target requested
    $Tmp=array();
    $q=safe_r_sql("Select TgTargetNo
		from TargetGroups
		where TgTournament=$ToId and TgSession=$Group
		and TgGroup in (select TgGroup
			from TargetGroups
			where TgTournament=$ToId and TgSession=$Group
			and TgTargetNo='$TargetNo')
		order by TgTargetNo");

    while($r=safe_fetch($q)) $Tmp[]=$r->TgTargetNo;

    if($Tmp) {
        $TargetNo=implode(',', $Tmp);
    }
    return $TargetNo;
}

