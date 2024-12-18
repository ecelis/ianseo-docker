<?php
require_once('./config.php');
require_once('Common/Lib/CommonLib.php');
require_once(__DIR__.'/Lib.php');

/*
 * Structure for letters ['letters'] array =
    'l' => Letter (A,B,C,etc.)
    'e' => entry is present (0 no, 1 yes)
    'd'[] => data in DB are present, (0 no, 1 yes)
    't'[] => data in Temporary Table are present, (0 no, 1 yes)
 */
$isRW = hasACL(AclISKServer, AclReadWrite, false);

$Json=array('error'=>true);
if(!(CheckTourSession() AND checkACL(AclISKServer, AclReadOnly, false))) {
    JsonOut($Json);
    die();
}

$tmpDevices = array();

$Json['error'] = false;
$Json['isUpdate'] = !empty($_REQUEST['isUpdate']);
$Sequences = getModuleParameter('ISK-NG', 'Sequence');

if(empty($_REQUEST) OR !empty($_REQUEST['isUpdate']) OR !empty($_REQUEST['autoImport']) OR !empty($_REQUEST['partialImport']) OR !empty($_REQUEST['groups'])  ) {
    $AutoImport = getModuleParameter('ISK-NG', 'AutoImport', array(), $_SESSION['TourId']);
    $PartialImport = getModuleParameter('ISK-NG', 'PartialImport', array(), $_SESSION['TourId']);
    if($isRW) {
        /* SET/RESET AutoImport */
        if (!empty($_REQUEST['autoImport']) and isset($_REQUEST['grpId'])) {
            $AutoImport[intval($_REQUEST['grpId'])] = filter_var($_REQUEST['autoImport'], FILTER_VALIDATE_BOOLEAN);
            setModuleParameter('ISK-NG', 'AutoImport', $AutoImport, $_SESSION['TourId']);
            if ($AutoImport[intval($_REQUEST['grpId'])] and array_key_exists(intval($_REQUEST['grpId']), $PartialImport) and $PartialImport[intval($_REQUEST['grpId'])]) {
                $PartialImport[intval($_REQUEST['grpId'])] = false;
                setModuleParameter('ISK-NG', 'PartialImport', $PartialImport, $_SESSION['TourId']);
            }
        } else if (!empty($_REQUEST['partialImport']) and isset($_REQUEST['grpId'])) {
            if (array_key_exists(intval($_REQUEST['grpId']), $AutoImport) and $AutoImport[intval($_REQUEST['grpId'])]) {
                $PartialImport[intval($_REQUEST['grpId'])] = false;
            } else {
                $PartialImport[intval($_REQUEST['grpId'])] = filter_var($_REQUEST['partialImport'], FILTER_VALIDATE_BOOLEAN);
            }
            setModuleParameter('ISK-NG', 'PartialImport', $PartialImport, $_SESSION['TourId']);
        }
    }

    /* Prepare Groups info */
    $Json['Groups'] = array();
    $Sessions = getApiScheduledSessions(['TourId' => $_SESSION['TourId']]);
    foreach ($Sequences as $Group => $Sequence) {
        $Json['Groups'][$Group] = array(
            'gId' => $Group,
            'gName' => chr(65 + $Group),
            'gSession' => ($Sessions[$Sequence['IskKey']]->Description ?? ''),
            'gSequence' => $Sequence['IskKey'],
            'gAutoImport' => (array_key_exists($Group, $AutoImport) and $AutoImport[$Group]),
            'gPartialImport' => (array_key_exists($Group, $PartialImport) and $PartialImport[$Group]),
            'gDistances' => [],
        );
        foreach ($Sequence['distance'] as $d) {
            $Json['Groups'][$Group]['gDistances'][] = ['value' => $d, 'text' => $d];
        }
        if(!array_key_exists('g'.$Group,$_REQUEST['groups']??[])) {
            $_REQUEST['groups']['g'.$Group]['s'] = $Sequence['session'];
            $_REQUEST['groups']['g'.$Group]['seq'] = $Sequence['IskKey'];
            if(!in_array('d', $_REQUEST['groups']['g'.$Group]) OR !in_array($_REQUEST['groups']['g'.$Group]['d'], $Sequence['distance'])) {
                $_REQUEST['groups']['g' . $Group]['d'] = ($Sequence['distance'][0]??[]);
            }
        }
    }
    ksort($Json['Groups']);
    $Json['Groups'] = array_values($Json['Groups']);
}

if($isRW) {
    switch ($_REQUEST['act'] ?? '') {
        case 'setDNS':
            $EnId = intval($_REQUEST['archerId'] ?? 0);
            safe_w_sql("update Qualifications set QuIrmType=10 where QuId=$EnId");
            safe_w_sql("update Individuals set IndIrmType=10 where IndId=$EnId");
            $Json['isUpdate'] = true;
            $Json['error'] = false;
            break;
        case 'unsetDNS':
            $Dist = intval($_REQUEST['d'] ?? 0);
            $EnId = intval($_REQUEST['archerId'] ?? 0);
            safe_w_sql("update Qualifications set QuIrmType=0, QuConfirm = QuConfirm & (255-" . pow(2, $Dist) . ") where QuId=$EnId");
            safe_w_sql("update Individuals set IndIrmType=0 where IndId=$EnId");
            $Json['isUpdate'] = true;
            $Json['error'] = false;
            break;
        case 'setDNF':
            $EnId = intval($_REQUEST['archerId'] ?? 0);
            // get the max hits for that distance
            $QuDistHits = 0;
            $Dist = intval($_REQUEST['d'] ?? 1);
            $q = safe_r_sql("select DiEnds*DiArrows as MaxArrows 
            from Qualifications
            inner join Entries on EnId=QuId
            inner join DistanceInformation on DiTournament=EnTournament and DiSession=QuSession and DiDistance={$Dist}
            where QuId={$EnId}");
            if ($r = safe_fetch($q)) {
                $QuDistHits = $r->MaxArrows;
            }
            safe_w_sql("update Qualifications set QuIrmType=5, QuD{$Dist}Hits=$QuDistHits, QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits where QuId={$EnId}");
            safe_w_sql("update Individuals set IndIrmType=5 where IndId={$EnId}");
            $Json['isUpdate'] = true;
            $Json['error'] = false;
            break;
        case 'unsetDNF':
            $Dist = intval($_REQUEST['d'] ?? 0);
            $EnId = intval($_REQUEST['archerId'] ?? 0);
            safe_w_sql("update Qualifications set QuIrmType=0, QuConfirm=QuConfirm & (255-" . pow(2, $Dist) . "), QuD{$Dist}Hits=length(trim(QuD{$Dist}Arrowstring)), QuHits=QuD1Hits+QuD2Hits+QuD3Hits+QuD4Hits+QuD5Hits+QuD6Hits+QuD7Hits+QuD8Hits where QuId=$EnId");
            safe_w_sql("update Individuals set IndIrmType=0 where IndId=$EnId");
            $Json['isUpdate'] = true;
            $Json['error'] = false;
            break;
        case 'partial':
            // imports a whole sequence of arrowstrings based on the group, distance and end
            $group = intval($_REQUEST['g'] ?? -1);
            $dist = intval($_REQUEST['d'] ?? -1);
            $end = intval($_REQUEST['e'] ?? -1);
            $id = ($_REQUEST['id'] ?? '');
            if (in_array(-1, [$group, $dist, $end])
                or in_array(0, [$dist, $end])
                or !$id
                or empty($Sequences[$group])
            ) {
                break;
            }

            $Options = array(
                'TourId' => $_SESSION['TourId'],
                'dist' => $dist,
                'end' => $end,
                // 'ses' => $Sequences[$group],
                'group' => $group,
            );
            switch ($Sequences[$group]['type']) {
                case 'Q':
                    // check the distance is OK
                    if (!in_array($dist, $Sequences[$group]['distance'])) {
                        break 2;
                    }
                    // $id is the division+class of the archers involved in this group
                    $Options['Category'] = $id;
                    if ($err = DoImportData($Options)) {
                        $Json['error'] = true;
                    } else {
                        $Json['isUpdate'] = true;
                    }
                    break;
                case 'M':
                    // id is the concatenation of teamevent, event and left matchno
                    if(is_array($id)) {
                        $Options['event'] = array();
                        $Options['matchno'] = array();
                        foreach ($id as $sglImport) {
                            $bits = explode('|', $sglImport);
                            $Options['team'] = $bits[0];
                            $Options['event'][] = $bits[1];
                            $Options['matchno'][] = $bits[2] . ',' . ($bits[2] + 1);
                        }
                    } else {
                        $bits = explode('|', $id);

                        $Options['team'] = $bits[0];
                        $Options['event'] = $bits[1];
                        $Options['matchno'] = $bits[2] . ',' . ($bits[2] + 1);
                    }
                    if ($err = DoImportData($Options)) {
                        $Json['error'] = true;
                    } else {
                        $Json['isUpdate'] = true;
                    }
                    break;
            }

            break;
        case 'import':
        case 'importGroup':
            // imports all data from a single device/match
            $group = intval($_REQUEST['g'] ?? -1);
            $dist = intval($_REQUEST['d'] ?? -1);
            $end = intval($_REQUEST['e'] ?? -1);
            $id = ($_REQUEST['id'] ?? '');
            if (in_array(-1, [$group, $dist, $end])
                or in_array(0, [$dist, $end])
                or (!$id and $_REQUEST['act'] == 'import')
                or empty($Sequences[$group])
            ) {
                break;
            }

            $Options = array(
                'TourId' => $_SESSION['TourId'],
                'dist' => $dist,
                'end' => $end,
                // 'ses' => $Sequences[$group],
                'group' => $group,
            );
            switch ($Sequences[$group]['type']) {
                case 'Q':
                    // check the distance is OK
                    if (!in_array($dist, $Sequences[$group]['distance'])) {
                        break 2;
                    }
                    // $id is the target number of that group
                    if ($_REQUEST['act'] == 'import') {
                        $Options['target'] = intval($id);
                    } else {
                        $Options['filterGroup'] = intval($group);
                    }
                    if ($err = DoImportData($Options)) {
                        $Json['error'] = true;
                    } else {
                        $Json['isUpdate'] = true;
                    }
                    break;
                case 'M':
                    // id is the concatenation of teamevent, event and left matchno
                    if ($_REQUEST['act'] == 'import') {
                        $bits = explode('|', $id);

                        $Options['team'] = $bits[0];
                        $Options['event'] = $bits[1];
                        $Options['matchno'] = $bits[2] . ',' . ($bits[2] + 1);
                    } else {
                        $Options['filterGroup'] = intval($group);
                    }
                    if ($err = DoImportData($Options)) {
                        $Json['error'] = true;
                    } else {
                        $Json['isUpdate'] = true;
                    }
                    break;
            }
            break;
        case 'delete':
            // removes the data from the device
            $group = intval($_REQUEST['g'] ?? -1);
            $dist = intval($_REQUEST['d'] ?? -1);
            $end = intval($_REQUEST['e'] ?? -1);
            $id = explode(',', $_REQUEST['id'] ?? '');
            $key = ($_REQUEST['key'] ?? '');
            if (in_array(-1, [$group, $dist, $end])
                or in_array(0, [$dist, $end])
                or !in_array($dist, $Sequences[$group]['distance'])
                or !$id
                or !$key
                or empty($Sequences[$group])
            ) {
                break;
            }

            switch ($Sequences[$group]['type']) {
                case 'Q':
                    $Target = intval($key);
                    $SQL = "select QuTargetNo
					from Entries
				    inner join Qualifications on QuId=EnId and QuSession={$Sequences[$group]['session']} and QuTarget={$Target}
					inner join IskData on IskDtTargetNo=QuTargetNo and IskDtType='Q' and IskDtDevice IN ('" . implode("','", $id) . "') and IskDtEndNo={$end} and IskDtDistance={$dist} and IskDtTournament={$_SESSION['TourId']}
				    inner join IskDevices on IskDvTournament=IskDtTournament and IskDvDevice=IskDtDevice
					where EnTournament={$_SESSION['TourId']}";
                    $q = safe_r_sql($SQL);
                    while ($r = safe_fetch($q)) {
                        safe_w_sql("delete from IskData where IskDtType='Q' and IskDtDevice IN ('" . implode("','", $id) . "') and IskDtEndNo={$end} and IskDtDistance={$dist} and IskDtTournament={$_SESSION['TourId']} and IskDtTargetNo='{$r->QuTargetNo}'");
                    }
                    $Json['isUpdate'] = true;
                    break;
                case 'M':
                    // // id is the concatenation of teamevent, event and left matchno
                    $bits = explode('|', $key);

                    $team = intval($bits[0]);
                    $event = $bits[1];
                    $matchnos = intval($bits[2]) . ',' . intval($bits[2] + 1);

                    safe_w_sql("delete from IskData where IskDtType='M' and IskDtDevice IN ('" . implode("','", $id) . "') and IskDtEndNo={$end} and IskDtTeamInd={$team} and IskDtMatchNo in ({$matchnos}) and IskDtTournament={$_SESSION['TourId']} and IskDtEvent=" . StrSafe_DB($event));

                    $Json['isUpdate'] = true;
                    break;
            }

            // updates the code to force a reconfigure on next request
            if ($_SESSION['UseApi'] == ISK_NG_PRO_CODE) {
                $q = safe_r_sql("select ToCode, ToCategory, IF(ToNameShort!='', ToNameShort, ToName) as TournamentName, " . StrSafe_DB($Sequences[intval($_REQUEST['groupId'])]['IskKey']) . " as ScheduleKey, IskDvGroup, IskDvSetup, IskDvDevice, IskDvTarget, IskDvProActive, IskDvCode, IskDvVersion
			    FROM IskDevices
			    INNER JOIN Tournament ON ToId=IskDvTournament
			    WHERE IskDvTournament='{$_SESSION["TourId"]}' AND IskDvDevice IN ('" . implode("','", $id) . "')");
                $Json['json'] = array();
                while ($r = safe_fetch($q)) {
                    if ($r->IskDvProActive) {
                        getQrConfig($r);
                    } else {
                        resetDevice($r->IskDvDevice, $r->IskDvTarget);
                    }
                }
            }
            break;
        case 'truncate':
            $GroupedDevices = getModuleParameter('ISK-NG', 'Grouping');
            $gId = intval($_REQUEST['g'] ?? -1);
            $AssignedTargets = [];

            $SQL = "select IskDvTarget, IskDvDevice, '' as GroupedTargets
            from IskDevices 
            where IskDvTournament={$_SESSION['TourId']} and IskDvGroup=$gId and IskDvTarget!=0 and IskDvProActive=1";
            if (!empty($GroupedDevices[$gId])) {
                $SQL = "select IskDvTarget, IskDvDevice, coalesce(GroupedTargets, '') as GroupedTargets
            from IskDevices 
            left join (
                select group_concat(TgTargetNo) as GroupedTargets, min(TgTargetNo) as TargetLead 
                from TargetGroups 
                where TgSession=$gId and TgTournament={$_SESSION['TourId']} 
                group by TgGroup
                ) TargetGroups on TargetLead=IskDvTarget
            where IskDvTournament={$_SESSION['TourId']} and IskDvGroup=$gId and IskDvTarget!=0 and IskDvProActive=1";
            }
            $q = safe_r_sql($SQL);
            while ($r = safe_fetch($q)) {
                $AssignedTargets[$r->IskDvTarget] = $r->IskDvDevice;
                if ($r->GroupedTargets) {
                    foreach (explode(',', $r->GroupedTargets) as $tgt) {
                        $AssignedTargets[$tgt] = $r->IskDvDevice;
                    }
                }
            }
            if (count($AssignedTargets) != 0) {
                safe_w_sql("delete from IskData where IskDtDevice IN ('" . implode("', '", $AssignedTargets) . "') and IskDtTournament={$_SESSION['TourId']}");
            }
            break;
        case 'details':
            // imports all data from a single device/match
            $group = intval($_REQUEST['g'] ?? -1);
            $dist = intval($_REQUEST['d'] ?? -1);
            $end = intval($_REQUEST['e'] ?? -1);
            $id = explode(',', $_REQUEST['id'] ?? '');
            $key = ($_REQUEST['key'] ?? '');
            if (in_array(-1, [$group, $dist, $end])
                or in_array(0, [$dist, $end])
                or !in_array($dist, $Sequences[$group]['distance'])
                or !$id
                or !$key
                or empty($Sequences[$group])
            ) {
                // $Json['error']=true;
                break;
            }

            require_once('Common/Lib/ArrTargets.inc.php');
            switch ($Sequences[$group]['type']) {
                case 'Q':
                    // fetches the imported and temporary table arrows of that end for the key
                    $Target = intval($key);
                    $SQL = "select EnId, EnName, ucase(EnFirstName) as EnFirstName, QuTarget, QuLetter, QuTargetNo, QuD{$dist}Arrowstring as QuArrows, DiArrows, coalesce(IskDtArrowstring, '') as IskArrows, QuScore, QuIrmType
					from Entries
				    inner join Qualifications on QuId=EnId and QuSession={$Sequences[$group]['session']} and QuTarget={$Target}
					inner join DistanceInformation on DiDistance=$dist and DiSession=QuSession and DiTournament=EnTournament and DiType='Q'
					left join (
						select IskDtArrowstring, IskDtTargetNo
					    from IskData
					    inner join IskDevices on IskDvTournament=IskDtTournament and IskDvDevice=IskDtDevice
					    where IskDtType='Q' and IskDtDevice IN ('" . implode("','", $id) . "') and IskDtEndNo={$end} and IskDtDistance={$dist} and IskDtTournament={$_SESSION['TourId']}
					    ) IskData on IskDtTargetNo=QuTargetNo
					where EnTournament={$_SESSION['TourId']} ORDER BY QuTargetNo";
                    $q = safe_r_sql($SQL);
                    while ($r = safe_fetch($q)) {
                        $QuArrows = substr($r->QuArrows, $r->DiArrows * ($end - 1), $r->DiArrows);
                        if ($QuArrows == $r->IskArrows) {
                            // remove arrows from IskData
                            safe_w_sql("delete from IskData where IskDtType='Q' and IskDtDevice IN ('" . implode("','", $id) . "') and IskDtEndNo={$end} and IskDtDistance={$dist} and IskDtTournament={$_SESSION['TourId']} and IskDtTargetNo='{$r->QuTargetNo}'");
                            $r->IskArrows = '';
                        }
                        $Json['archer'][] = [
                            'Id' => intval($r->EnId),
                            'Letter' => $r->QuLetter,
                            'FamName' => $r->EnFirstName,
                            'GivName' => $r->EnName,
                            'DbArrows' => DecodeFromString(str_pad($QuArrows, $r->DiArrows), false, true),
                            'IskArrows' => DecodeFromString(str_pad($r->IskArrows, $r->DiArrows), false, true),
                            'CanDNS' => (intval($r->QuScore) === 0) ? ($r->QuIrmType == 0 ? 1 : -1) : 0,
                            'CanDNF' => (intval($r->QuScore) !== 0) ? ($r->QuIrmType == 0 ? 1 : -1) : 0
                        ];
                    }
                    break;
                case 'M':
                    // id is the concatenation of teamevent, event and left matchno
                    $bits = explode('|', $key);

                    $team = intval($bits[0]);
                    $event = $bits[1];
                    $matchnos = intval($bits[2]) . ',' . intval($bits[2] + 1);

                    switch ($Sequences[$group]['subtype']) {
                        case 'I':
                            $SQL = "select EnName, ucase(EnFirstName) as EnFirstName, right(FsLetter,1) as QuLetter, FinArrowstring as QuArrows, 
       						FinTiebreak as SoArrows, FinTbClosest as TbClosest, FsMatchNo%2 as dx,
       						if(EvMatchArrowsNo & GrBitPhase, EvElimArrows, EvFinArrows) DiArrows, 
       						if(EvMatchArrowsNo & GrBitPhase, EvElimEnds, EvFinEnds) DiEnds, 
       						if(EvMatchArrowsNo & GrBitPhase, EvElimSO, EvFinSO) DiSO, 
       						coalesce(IskDtArrowstring, '') as IskArrows, coalesce(IskDtIsClosest, '') as IskClosest  
							from Events
						    inner join Finals on FinEvent=EvCode and FinTournament=EvTournament and FinMatchNo in ({$matchnos})
							inner join Grids on GrMatchNo=FinMatchNo
						    inner join FinSchedule on FSEvent=EvCode and FSTeamEvent=EvTeamEvent and FSTournament=EvTournament and FSMatchNo=FinMatchNo
							inner join Entries on EnId=FinAthlete and EnTournament=EvTournament
							left join (
								select IskDtArrowstring, IskDtIsClosest, IskDtMatchNo
							    from IskData
							    inner join IskDevices on IskDvTournament=IskDtTournament and IskDvDevice=IskDtDevice
							    where IskDtType='M' and IskDtDevice IN ('" . implode("','", $id) . "') and IskDtEndNo={$end} and IskDtTeamInd=0 and IskDtEvent=" . StrSafe_DB($event) . " and IskDtTournament={$_SESSION['TourId']}
							    ) IskData on IskDtMatchNo=FinMatchNo
							where EvTournament={$_SESSION['TourId']} and EvTeamEvent=0 and EvCode=" . StrSafe_DB($event) . " ORDER BY FinMatchNo";
                            break;
                        case 'T':
                            $SQL = "select if(TfSubTeam=0, CoName, concat(CoName, ' ', TfSubTeam)) as EnName, if(TfSubTeam=0, CoCode, concat(CoCode, ' ', TfSubTeam)) as EnFirstName, right(FsLetter,1) as QuLetter, TfArrowstring as QuArrows, 
       						TfTiebreak as SoArrows, TfTbClosest as TbClosest, FsMatchNo%2 as dx,
       						if(EvMatchArrowsNo & GrBitPhase, EvElimArrows, EvFinArrows) DiArrows, 
       						if(EvMatchArrowsNo & GrBitPhase, EvElimEnds, EvFinEnds) DiEnds, 
       						if(EvMatchArrowsNo & GrBitPhase, EvElimSO, EvFinSO) DiSO, 
       						coalesce(IskDtArrowstring, '') as IskArrows, coalesce(IskDtIsClosest, '') as IskClosest  
							from Events
						    inner join TeamFinals on TfEvent=EvCode and TfTournament=EvTournament and TfMatchNo in ({$matchnos})
							inner join Grids on GrMatchNo=TfMatchNo
						    inner join FinSchedule on FSEvent=EvCode and FSTeamEvent=EvTeamEvent and FSTournament=EvTournament and FSMatchNo=TfMatchNo
							inner join Countries on CoId=TfTeam and CoTournament=EvTournament
							left join (
								select IskDtArrowstring, IskDtIsClosest, IskDtMatchNo
							    from IskData
							    inner join IskDevices on IskDvTournament=IskDtTournament and IskDvDevice=IskDtDevice
							    where IskDtType='M' and IskDtDevice IN ('" . implode("','", $id) . "') and IskDtEndNo={$end} and IskDtTeamInd=1 and IskDtEvent=" . StrSafe_DB($event) . " and IskDtTournament={$_SESSION['TourId']}
							    ) IskData on IskDtMatchNo=FsMatchNo
							where EvTournament={$_SESSION['TourId']} and EvTeamEvent=1 and EvCode=" . StrSafe_DB($event) . " ORDER BY TfMatchNo";
                            break;
                        case 'R':
                            $SQL = "select 
                                if(RrMatchTeam=0, EnName, if(RrMatchSubTeam=0, CoName, concat(CoName, ' ', RrMatchSubTeam))) as EnName, 
       							if(RrMatchTeam=0, EnFirstName, if(RrMatchSubTeam=0, CoCode, concat(CoCode, ' ', RrMatchSubTeam))) as EnFirstName, 
       							right(RrMatchTarget,1) as QuLetter, RrMatchArrowstring as QuArrows, 
	                            RrMatchTiebreak as SoArrows, RrMatchTbClosest as TbClosest, RRMatchMatchNo%2 as dx,
	                            RrLevArrows DiArrows, 
	                            RrLevEnds DiEnds, 
	                            RrLevSO DiSO, 
	                            coalesce(IskDtArrowstring, '') as IskArrows, coalesce(IskDtIsClosest, '') as IskClosest  
							from Events
						    inner join RoundRobinMatches on RrMatchEvent=EvCode and RrMatchTournament=EvTournament and RrMatchMatchNo+100*RrMatchRound+10000*RrMatchGroup+1000000*RrMatchLevel in ({$matchnos})
						    inner join RoundRobinLevel on RrLevTournament=EvTournament and RrLevTeam=EvTeamEvent and RrLevEvent=EvCode and RrLevLevel=RrMatchLevel
							left join Countries on CoId=RrMatchAthlete and CoTournament=EvTournament and RrMatchTeam=1
							left join Entries on EnId=RrMatchAthlete and EnTournament=EvTournament and RrMatchTeam=0
							left join (
								select IskDtArrowstring, IskDtIsClosest, IskDtMatchNo
							    from IskData
							    inner join IskDevices on IskDvTournament=IskDtTournament and IskDvDevice=IskDtDevice
							    where IskDtType='M' and IskDtDevice IN ('" . implode("','", $id) . "') and IskDtEndNo={$end} and IskDtTeamInd=1 and IskDtEvent=" . StrSafe_DB($event) . " and IskDtTournament={$_SESSION['TourId']}
							    ) IskData on IskDtMatchNo=RrMatchMatchNo+100*RrMatchRound+10000*RrMatchGroup+1000000*RrMatchLevel
							where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$team and EvCode=" . StrSafe_DB($event) . " ORDER BY RrMatchMatchNo";
                            break;
                    }
                    $q = safe_r_sql($SQL);
                    while ($r = safe_fetch($q)) {
                        if ($end > $r->DiEnds) {
                            $QuArrows = str_pad(substr($r->SoArrows, $r->DiSO * ($end - $r->DiEnds - 1), $r->DiSO), $r->DiSO);
                        } else {
                            $QuArrows = str_pad(substr($r->QuArrows, $r->DiArrows * ($end - 1), $r->DiArrows), $r->DiArrows);
                        }
                        $Json['archer'][] = [
                                'Letter' => is_numeric($r->QuLetter) ? ($r->dx ? 'B' : 'A') : $r->QuLetter,
                                'FamName' => $r->EnFirstName,
                                'GivName' => $r->EnName,
                                'DbArrows' => DecodeFromString($QuArrows, false, true),
                                'IskArrows' => DecodeFromString(str_pad($r->IskArrows, ($end > $r->DiEnds ? $r->DiSO : $r->DiArrows)), false, true)
                            ] + (($end > $r->DiEnds) ? ['DbClosest' => $r->TbClosest, 'IskClosest' => $r->IskClosest] : []);
                    }
                    break;
            }
            break;
    }
}

/* Device Infos - if $_REQUEST['groups'] is set */
if (!empty($_REQUEST['groups']) and is_array($_REQUEST['groups'] ?? [])) {
    $Json['Devices'] = array();
    $GroupedDevices = getModuleParameter('ISK-NG', 'Grouping');
    // foreach requested group gets the devices
    foreach ($_REQUEST['groups'] as $gItems) {
        if ($gItems['s'] == 0) {
            continue;
        }
        $gId = intval($gItems['i'] ?? -1);
        $gDist = intval($gItems['d'] ?? 0);
        if (empty($Sequences[$gId])) {
            continue;
        }

        $AssignedTargets = array();
        $DevicesTargets = array();

        $SQL = "select IskDvTarget, IskDvDevice, IskDvCode, '' as GroupedTargets
            from IskDevices 
            where IskDvTournament={$_SESSION['TourId']} and IskDvGroup=$gId and IskDvTarget!=0 and IskDvProActive=1";
        if (!empty($GroupedDevices[$gId])) {
            $SQL = "select IskDvTarget, IskDvDevice, IskDvCode, coalesce(GroupedTargets, '') as GroupedTargets
            from IskDevices 
            left join (
                select group_concat(TgTargetNo order by TgTargetNo+0) as GroupedTargets, min(TgTargetNo+0) as TargetLead 
                from TargetGroups 
                where TgSession=$gId and TgTournament={$_SESSION['TourId']} 
                group by TgGroup
                ) TargetGroups on TargetLead=IskDvTarget
            where IskDvTournament={$_SESSION['TourId']} and IskDvGroup=$gId and IskDvTarget!=0 and IskDvProActive=1 and GroupedTargets!=''";
        }
        $q = safe_r_sql($SQL);
        while ($r = safe_fetch($q)) {
            $AssignedTargets[] = $r->IskDvTarget;
            if(!array_key_exists($r->IskDvTarget,$DevicesTargets)) {
                $DevicesTargets[$r->IskDvTarget] = array();
            }
            $DevicesTargets[$r->IskDvTarget][$r->IskDvCode] = $r->IskDvDevice;
            if ($r->GroupedTargets) {
                foreach (explode(',', $r->GroupedTargets) as $tgt) {
					if(!in_array($tgt, $AssignedTargets)) {
						$AssignedTargets[] = $tgt;
					}
                    if(!array_key_exists($tgt,$DevicesTargets)) {
                        $DevicesTargets[$tgt] = array();
                    }
                    $DevicesTargets[$tgt][$r->IskDvCode] = $r->IskDvDevice;
                }
            }
        }
        if(count($AssignedTargets) == 0) {
            $AssignedTargets[-1]='-1';
        }
        // $AssignedTargets = array_unique($AssignedTargets);
        switch ($Sequences[$gId]['type']) {
            case 'Q':
                // we could have more than one distance in the setup, so first check we have a single distance selected!
                if (!$gDist) {
                    continue 2;
                }
                // gets the bare qualification slots
                $q = safe_r_sql("select ToCategory&12 as IsField3D, ToNumEnds, QuTarget, QuLetter, SesAth4Target, concat(EnDivision,EnClass) as EnEvent
                    from Qualifications
                    inner join Entries on EnId=QuId and EnTournament={$_SESSION['TourId']}
                    inner join Session on SesTournament=EnTournament and SesOrder=QuSession and SesType='Q'
                    inner join Tournament on ToId=EnTournament
                    where QuSession={$Sequences[$gId]['session']} and QuIrmType=0 and QuTarget in (" . implode(',', $AssignedTargets) . ")
                    order by if(IsField3D, (QuTarget-1)%ToNumEnds, QuTarget), QuTargetNo");
                $OldTarget = 0;
                $item = [];
                while ($r = safe_fetch($q)) {
                    if ($OldTarget != $r->QuTarget) {
                        if ($item) {
	                        while ($AvailableLetters) {
		                        $l = array_shift($AvailableLetters);
		                        $item['letters'][] = ['l' => $l, 'e' => 0];
	                        }
                            $Json['Devices'][] = $item;
                        }
                        $AvailableLetters = range('A', chr(64 + $r->SesAth4Target));
						$PrnTgt=($r->IsField3D ? CheckBisTargets($r->QuTarget, $r->ToNumEnds) : $r->QuTarget);
                        $item = [
							'key' => $r->QuTarget,
	                        'target' => $PrnTgt.($PrnTgt!=$r->QuTarget ? " ($r->QuTarget)" : ''),
	                        'group' => $gId,
	                        'letters' => [],
	                        'dev' => $DevicesTargets[$r->QuTarget],
	                        'autoimport' => 0,
	                        ];
                    }
                    while ($AvailableLetters and $r->QuLetter != $AvailableLetters[0]) {
                        $l = array_shift($AvailableLetters);
                        $item['letters'][] = ['l' => $l, 'e' => 0];
                    }
                    if ($AvailableLetters) {
                        $l = array_shift($AvailableLetters);
                        $item['letters'][] = ['l' => $l, 'e' => 1, 'k'=>$r->EnEvent];
                    }
                    $OldTarget = $r->QuTarget;
                }
                if ($item) {
                    while ($AvailableLetters) {
                        $l = array_shift($AvailableLetters);
                        $item['letters'][] = ['l' => $l, 'e' => 0];
                    }
                    $Json['Devices'][] = $item;
                }
                break;
	        case 'M':
		        $Date=substr($Sequences[$gId]['session'], 0, 10);
		        $Time=substr($Sequences[$gId]['session'], 10);

				switch($Sequences[$gId]['subtype']) {
					case 'I':
						$SQL="select EvCode, EvTeamEvent, f1.FsTarget+0 as Target1, right(f1.FsLetter,1) as Letter1, f2.FsTarget+0 as Target2, right(f2.FsLetter,1) as Letter2, f1.FSMatchNo as Matchno1, f2.FSMatchNo as Matchno2, EvMatchMultipleMatches & GrBitPhase as MultipleMatch
							from FinSchedule f1
							inner join FinSchedule f2 on f2.FSTournament=f1.FSTournament and f2.FSEvent=f1.FSEvent and f2.FSTeamEvent=f1.FSTeamEvent and f2.FSMatchNo=f1.FSMatchNo+1
							inner join Finals tf1 on tf1.FinAthlete!=0 and tf1.FinEvent=f1.FSEvent and tf1.FinMatchNo=f1.FSMatchNo and tf1.FinTournament=f1.FSTournament
							inner join Finals tf2 on tf2.FinAthlete!=0 and tf2.FinEvent=f2.FSEvent and tf2.FinMatchNo=f2.FSMatchNo and tf2.FinTournament=f2.FSTournament
							inner join Grids on GrMatchNo=f1.FSMatchNo
							inner join Events on EvCode=f1.FSEvent and EvTeamEvent=f1.FSTeamEvent and EvTournament=f1.FSTournament
							where f1.FSMatchNo%2=0 
						    	and f1.FsTarget+0 in (" . implode(',', $AssignedTargets) . ") 
						    	and f1.FSTournament={$_SESSION['TourId']} 
						    	and f1.FSTeamEvent=0
						    	and f1.FSScheduledDate='$Date' and f1.FSScheduledTime='$Time'
							order by Target1, Letter1
							";
						break;
					case 'R':
						$Team=intval($Sequence['IskKey'][0]=='T');
						$SQL="select EvCode, EvTeamEvent, RrGrArcherWaves as MultipleMatch,
       							f1.RrMatchTarget+0 as Target1, right(f1.RrMatchTarget, 1) as Letter1, f1.RrMatchMatchNo+f1.RrMatchRound*100+f1.RrMatchGroup*10000+f1.RrMatchLevel*1000000 as Matchno1, 
       							f2.RrMatchTarget+0 as Target2, right(f2.RrMatchTarget, 1) as Letter2, f2.RrMatchMatchNo+f2.RrMatchRound*100+f2.RrMatchGroup*10000+f2.RrMatchLevel*1000000 as Matchno2
							from RoundRobinMatches f1
							inner join RoundRobinMatches f2 on f2.RrMatchTournament=f1.RrMatchTournament and f2.RrMatchEvent=f1.RrMatchEvent and f2.RrMatchTeam=f1.RrMatchTeam and f2.RrMatchLevel=f1.RrMatchLevel and f2.RrMatchGroup=f1.RrMatchGroup and f2.RrMatchRound=f1.RrMatchRound and f2.RrMatchMatchNo=f1.RrMatchMatchNo+1
							inner join RoundRobinGroup on RrGrTournament=f1.RrMatchTournament and RrGrTeam=f1.RrMatchTeam and RrGrEvent=f1.RrMatchEvent and RrGrLevel=f1.RrMatchLevel and RrGrGroup=f1.RrMatchGroup						    
						    inner join Events on EvCode=f1.RrMatchEvent and EvTeamEvent=f1.RrMatchTeam and EvTournament=f1.RrMatchTournament
							where f1.RrMatchMatchNo%2=0 
						    	and f1.RrMatchTarget+0 in (" . implode(',', $AssignedTargets) . ") 
						    	and f1.RrMatchTournament={$_SESSION['TourId']} 
						    	and f1.RrMatchTeam=$Team
						    	and f1.RrMatchScheduledDate='$Date' and f1.RrMatchScheduledTime='$Time'
						    	and f1.RrMatchAthlete!=0 and f2.RrMatchAthlete!=0
							order by Target1, Letter1
							";
						break;
					case 'T':
						$SQL="select EvCode, EvTeamEvent, f1.FsTarget+0 as Target1, right(f1.FsLetter,1) as Letter1, f2.FsTarget+0 as Target2, right(f2.FsLetter,1) as Letter2, f1.FSMatchNo as Matchno1, f2.FSMatchNo as Matchno2, EvMatchMultipleMatches & GrBitPhase as MultipleMatch
							from FinSchedule f1
							inner join FinSchedule f2 on f2.FSTournament=f1.FSTournament and f2.FSEvent=f1.FSEvent and f2.FSTeamEvent=f1.FSTeamEvent and f2.FSMatchNo=f1.FSMatchNo+1
							inner join TeamFinals tf1 on tf1.TfTeam!=0 and tf1.TfEvent=f1.FSEvent and tf1.TfMatchNo=f1.FSMatchNo and tf1.TfTournament=f1.FSTournament
							inner join TeamFinals tf2 on tf2.TfTeam!=0 and tf2.TfEvent=f2.FSEvent and tf2.TfMatchNo=f2.FSMatchNo and tf2.TfTournament=f2.FSTournament
							inner join Grids on GrMatchNo=f1.FSMatchNo
							inner join Events on EvCode=f1.FSEvent and EvTeamEvent=f1.FSTeamEvent and EvTournament=f1.FSTournament
							where f1.FSMatchNo%2=0 
						    	and f1.FsTarget+0 in (" . implode(',', $AssignedTargets) . ") 
						    	and f1.FSTournament={$_SESSION['TourId']} 
						    	and f1.FSTeamEvent=1
						    	and f1.FSScheduledDate='$Date' and f1.FSScheduledTime='$Time'
							order by Target1, Letter1
							";
						break;
				}
		        $q = safe_r_sql($SQL);
		        $OldTarget = '';
		        $item = [];
		        while ($r = safe_fetch($q)) {
					$TgtName=$r->Target1;
					if($r->Target1!=$r->Target2) {
						$TgtName.='-'.$r->Target2;
					}
					if($r->MultipleMatch) {
						if(!is_numeric($r->Letter1)) {
							if($r->Letter1==$r->Letter2) {
								$TgtName.=' '.$r->Letter1;
                                $r->Letter1 = $r->Target1;
                                $r->Letter2 = $r->Target2;
							} else {
								$TgtName.=' '.$r->Letter1.$r->Letter2;
							}
						} else {
							$TgtName.=' AB';
						}
					}
			        if ($OldTarget != $TgtName) {
				        if ($item) {
					        $Json['Devices'][] = $item;
				        }
				        $item = [
							'key' => "{$r->EvTeamEvent}|{$r->EvCode}|{$r->Matchno1}",
					        'target' => $TgtName,
					        'group' => $gId,
					        'letters' => [],
					        'dev' => $DevicesTargets[$r->Target1],
					        'autoimport'=>1,
				        ];
			        }
					if(!is_numeric($r->Letter1)) {
				        $item['letters'][] = ['l' => $r->Letter1, 'e' => 1, 'k'=>$TgtName];
				        $item['letters'][] = ['l' => $r->Letter2, 'e' => 1, 'k'=>$TgtName];
					} else {
						$item['letters'][] = ['l' => 'A', 'e' => 1, 'k'=>$TgtName];
						$item['letters'][] = ['l' => 'B', 'e' => 1, 'k'=>$TgtName];
					}
			        $OldTarget = $TgtName;
		        }
		        if ($item) {
			        $Json['Devices'][] = $item;
		        }
				break;
        }
        $_REQUEST['status'] = $_REQUEST['groups'];
    }
}

/* Tablet status - if $_REQUEST['status'] is set */
if(!empty($_REQUEST['status']) AND is_array($_REQUEST['status']??[])) {
    $Json['Status'] = array();
    $GroupedDevices = getModuleParameter('ISK-NG', 'Grouping');
    $Sequences = getModuleParameter('ISK-NG', 'Sequence');
// foreach requested group gets the devices
    foreach ($_REQUEST['status'] as $gItems) {
        if ($gItems['s'] == 0) {
            continue;
        }
        $gId = intval($gItems['i'] ?? -1);
        $gDist = intval($gItems['d'] ?? 0);
        if (empty($Sequences[$gId])) {
            continue;
        }

        $AssignedTargets = [];
        $SQL = "select IskDvTarget, IskDvDevice, '' as GroupedTargets
                from IskDevices 
                where IskDvTournament={$_SESSION['TourId']} and IskDvGroup=$gId and IskDvTarget!=0";
        if (!empty($GroupedDevices[$gId])) {
            $SQL = "select IskDvTarget, IskDvDevice, coalesce(GroupedTargets, '') as GroupedTargets
                    from IskDevices 
                    left join (
                        select group_concat(TgTargetNo order by TgTargetNo+0) as GroupedTargets, min(TgTargetNo+0) as TargetLead 
                        from TargetGroups 
                        where TgSession=$gId and TgTournament={$_SESSION['TourId']} 
                        group by TgGroup
                        ) TargetGroups on TargetLead=IskDvTarget
                    where IskDvTournament={$_SESSION['TourId']} and IskDvGroup=$gId and IskDvTarget!=0";
        }
        $q = safe_r_sql($SQL);
        while ($r = safe_fetch($q)) {
            $AssignedTargets[] = $r->IskDvTarget;
            if ($r->GroupedTargets) {
                foreach (explode(',', $r->GroupedTargets) as $tgt) {
                    $AssignedTargets[] = $tgt;
                }
            }
        }
		if(!$AssignedTargets) {
			$AssignedTargets[]=-1;
		}
        switch ($Sequences[$gId]['type']) {
            case 'Q':
                // we could have more than one distance in the setup, so first check we have a single distance selected!
                if (!$gDist) {
                    continue 2;
                }
                // gets the bare qualification Info
                $SQL = "select ToCategory&12 as IsField3D, DiEnds, QuTarget, QuLetter, SesAth4Target, QuD{$gDist}ArrowString as Arrowstring, DiEnds, DiArrows, ifnull(dListDist,'') as otherDistances,
                        GROUP_CONCAT(CONCAT(IskDtEndNo,'#',IskDtArrowstring) separator ',') as TempTable, concat(EnDivision, EnClass) as Category
                    from Entries
                    inner join Qualifications on EnId=QuId 
                    inner join Session on SesTournament=EnTournament and SesOrder=QuSession and SesType='Q'
                    inner join DistanceInformation on DiTournament=EnTournament AND DiType=SesType and DiSession=QuSession and DiDistance={$gDist} 
                    inner join Tournament on ToId=EnTournament
                    left join IskData on IskDtTournament=EnTournament AND IskDtType=SesType AND IskDtTargetNo=QuTargetNo AND IskDtDistance={$gDist} 
                    left join (
                        select QuTarget as dQuTarget, QuLetter dQuLetter, GROUP_CONCAT(DISTINCT IskDtDistance) as dListDist 
                        from Entries 
                        inner join Qualifications on EnId=QuId 
                        inner join IskData on IskDtTournament=EnTournament AND IskDtType='Q' AND IskDtTargetNo=QuTargetNo AND IskDtDistance!={$gDist}
                        where EnTournament={$_SESSION['TourId']} AND QuSession={$Sequences[$gId]['session']} and QuTarget in (" . implode(',', $AssignedTargets) . ")
                        Group by QuTargetNo 
                        order by QuTargetNo
                    ) as Sqy on QuTarget=dQuTarget AND QuLetter=dQuLetter
                    where EnTournament={$_SESSION['TourId']} AND QuIrmType=0 AND QuSession={$Sequences[$gId]['session']} and QuTarget in (" . implode(',', $AssignedTargets) . ")
                    Group by QuTargetNo 
                    order by if(IsField3D, (QuTarget-1)%DiEnds, QuTarget), QuTargetNo";
                $q = safe_r_sql($SQL);
                $OldEndNum = 0;
                $OldTarget = 0;
                $item = [];
                while ($r = safe_fetch($q)) {
                    if ($OldTarget != $r->QuTarget) {
                        if ($item) {
	                        while ($AvailableLetters) {
		                        $l = array_shift($AvailableLetters);
		                        $item['letters'][] = ['l' => $l, 'e' => 0, 'd' => array_fill(0, $OldEndNum, 0), 't' => array_fill(0, $OldEndNum, 0)];
	                        }
                            $Json['Status'][] = $item;
                        }
                        $AvailableLetters = range('A', chr(64 + $r->SesAth4Target));
                        $item = [
							'key' => $r->QuTarget,
	                        'target' => $r->IsField3D ? CheckBisTargets($r->QuTarget, $r->DiEnds)." ($r->QuTarget)" : $r->QuTarget,
	                        'group' => $gId,
	                        'letters' => [],
	                        'over' => false,
                            'otherdistances' => $r->otherDistances
	                        ];
                    }
                    while ($AvailableLetters and $r->QuLetter != $AvailableLetters[0]) {
                        $l = array_shift($AvailableLetters);
                        $item['letters'][] = ['l' => $l, 'e' => 0, 'd' => array_fill(0, $r->DiEnds, 0), 't' => array_fill(0, $r->DiEnds, 0)];
                    }
                    if ($AvailableLetters) {
                        $l = array_shift($AvailableLetters);
                        $item['letters'][$l] = ['l' => $l, 'e' => 1, 'd' => array_fill(0, $r->DiEnds, 0), 't' => array_fill(0, $r->DiEnds, 0)];
                        foreach (str_split($r->Arrowstring, $r->DiArrows) as $k => $v) {
                            if(strlen(str_replace(' ','',$v))==$r->DiArrows) {
                                $item['letters'][$l]['d'][$k]=1;
                            } else if(strlen(str_replace(' ','',$v))!=0) {
                                $item['letters'][$l]['d'][$k]=2;
                            }
                        }
                        if($r->TempTable) {
                            foreach (explode(',', $r->TempTable) as $tVal) {
                                list($k, $v) = explode('#', $tVal);
                                $k = intval($k)-1;
                                if (strlen(str_replace(' ', '', $v)) == $r->DiArrows) {
                                    $item['letters'][$l]['t'][$k] = 1;
                                } else if (strlen(str_replace(' ', '', $v)) != 0) {
                                    $item['letters'][$l]['t'][$k] = 2;
                                }
                            }
                        }
                        $item['letters'] = array_values($item['letters']);
                    }
                    $OldTarget = $r->QuTarget;
                    $OldEndNum = $r->DiEnds;
                }
                if ($item) {
                    while ($AvailableLetters) {
                        $l = array_shift($AvailableLetters);
                        $item['letters'][] = ['l' => $l, 'e' => 0, 'd' => array_fill(0, $OldEndNum, 0), 't' => array_fill(0, $OldEndNum, 0)];
                    }
                    $Json['Status'][] = $item;
                }
				break;
	        case 'M':
		        $Date=substr($Sequences[$gId]['session'], 0, 10);
		        $Time=substr($Sequences[$gId]['session'], 10);

		        switch($Sequences[$gId]['subtype']) {
			        case 'I':
				        $SQL="select EvTeamEvent, EvCode, f1.*, f2.*, EvMatchMultipleMatches & GrBitPhase as MultipleMatch,
       						if(EvMatchArrowsNo & GrBitPhase, EvElimArrows, EvFinArrows) DiArrows, 
       						if(EvMatchArrowsNo & GrBitPhase, EvElimEnds, EvFinEnds) DiEnds, 
       						if(EvMatchArrowsNo & GrBitPhase, EvElimSO, EvFinSO) DiSO
							from (
							    select FsEvent as Event1, FsTarget+0 as Target1, right(FsLetter,1) as Letter1, FSMatchNo as Matchno1,
				                    FinArrowstring as Arrowstring1, FinTiebreak as Tiebreak1, FinWinLose as Win1,
       								GROUP_CONCAT(CONCAT(IskDtEndNo,'#',IskDtArrowstring) separator ',') as TempTable1
						        from FinSchedule
								inner join Finals on FinAthlete!=0 and FinEvent=FSEvent and FinMatchNo=FSMatchNo and FinTournament=FSTournament
		                    	left join IskData on IskDtTournament=FSTournament AND IskDtType='M' AND IskDtMatchNo=FSMatchNo AND IskDtEvent=FSEvent and IskDtTeamInd=FSTeamEvent
						        where FSMatchNo%2=0 and FSTeamEvent=0 and FSTournament={$_SESSION['TourId']} and FSScheduledDate='$Date' and FSScheduledTime='$Time'
						        group by FSEvent, FSMatchNo
							) f1
							inner join (
							    select FsEvent as Event2, FsTarget+0 as Target2, right(FsLetter,1) as Letter2, FSMatchNo as Matchno2,
				                    FinArrowstring as Arrowstring2, FinTiebreak as Tiebreak2, FinWinLose as Win2,
       								GROUP_CONCAT(CONCAT(IskDtEndNo,'#',IskDtArrowstring) separator ',') as TempTable2
						        from FinSchedule
								inner join Finals on FinAthlete!=0 and FinEvent=FSEvent and FinMatchNo=FSMatchNo and FinTournament=FSTournament
		                    	left join IskData on IskDtTournament=FSTournament AND IskDtType='M' AND IskDtMatchNo=FSMatchNo AND IskDtEvent=FSEvent and IskDtTeamInd=FSTeamEvent
						        where FSMatchNo%2=1 and FSTeamEvent=0 and FSTournament={$_SESSION['TourId']} and FSScheduledDate='$Date' and FSScheduledTime='$Time'
						        group by FSEvent, FSMatchNo
							) f2 on Event2=Event1 and Matchno2=Matchno1+1
							inner join Grids on GrMatchNo=Matchno1
							inner join Events on EvCode=Event1 and EvTeamEvent=0 and EvTournament={$_SESSION['TourId']}
							where Target1 in (" . implode(',', $AssignedTargets) . ") 
							order by Target1, Letter1
							";
				        break;
			        case 'R':
						$Team=intval($Sequences[$gId]['IskKey'][0]=='T');
				        $SQL="select EvTeamEvent, EvCode, f1.*, f2.*, RrGrArcherWaves as MultipleMatch,
       						RrLevArrows DiArrows, 
       						RrLevEnds DiEnds, 
       						RrLevSO DiSO
							from (
							    select RrMatchEvent as Event1, RrMatchLevel, RrMatchGroup, RrMatchTarget+0 as Target1, right(RrMatchTarget,1) as Letter1, RrMatchMatchNo+RrMatchRound*100+RrMatchGroup*10000+RrMatchLevel*1000000 as Matchno1,
				                    RrMatchArrowstring as Arrowstring1, RrMatchTiebreak as Tiebreak1, RrMatchWinLose as Win1,
       								GROUP_CONCAT(CONCAT(IskDtEndNo,'#',IskDtArrowstring) separator ',') as TempTable1
						        from RoundRobinMatches
		                    	left join IskData on IskDtTournament=RrMatchTournament AND IskDtType='M' AND IskDtMatchNo=RrMatchMatchNo+RrMatchRound*100+RrMatchGroup*10000+RrMatchLevel*1000000 AND IskDtEvent=RrMatchEvent and IskDtTeamInd=RrMatchTeam
						        where RrMatchAthlete!=0 and RrMatchMatchNo%2=0 and RrMatchTeam=$Team and RrMatchTournament={$_SESSION['TourId']} and RrMatchScheduledDate='$Date' and RrMatchScheduledTime='$Time'
						        group by RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo
							) f1
							inner join (
							    select RrMatchEvent as Event2, RrMatchTarget+0 as Target2, right(RrMatchTarget,1) as Letter2, RrMatchMatchNo+RrMatchRound*100+RrMatchGroup*10000+RrMatchLevel*1000000 as Matchno2,
				                    RrMatchArrowstring as Arrowstring2, RrMatchTiebreak as Tiebreak2, RrMatchWinLose as Win2,
       								GROUP_CONCAT(CONCAT(IskDtEndNo,'#',IskDtArrowstring) separator ',') as TempTable2
						        from RoundRobinMatches
		                    	left join IskData on IskDtTournament=RrMatchTournament AND IskDtType='M' AND IskDtMatchNo=RrMatchMatchNo+RrMatchRound*100+RrMatchGroup*10000+RrMatchLevel*1000000 AND IskDtEvent=RrMatchEvent and IskDtTeamInd=RrMatchTeam
						        where RrMatchAthlete!=0 and RrMatchMatchNo%2=1 and RrMatchTeam=$Team and RrMatchTournament={$_SESSION['TourId']} and RrMatchScheduledDate='$Date' and RrMatchScheduledTime='$Time'
						        group by RrMatchEvent, RrMatchLevel, RrMatchGroup, RrMatchRound, RrMatchMatchNo
							) f2 on Event2=Event1 and Matchno2=Matchno1+1
						    inner join RoundRobinLevel on RrLevTournament={$_SESSION['TourId']} and RrLevTeam=$Team and RrLevEvent=Event1 and RrLevLevel=f1.RrMatchLevel
						    inner join RoundRobinGroup on RrGrTournament={$_SESSION['TourId']} and RrGrTeam=$Team and RrGrEvent=Event1 and RrGrLevel=RrMatchLevel and RrGrGroup=RrMatchGroup
							inner join Events on EvCode=Event1 and EvTeamEvent=$Team and EvTournament={$_SESSION['TourId']}
							where Target1 in (" . implode(',', $AssignedTargets) . ") 
							order by Target1, Letter1
							";
				        break;
			        case 'T':
				        $SQL="select EvTeamEvent, EvCode, f1.*, f2.*, EvMatchMultipleMatches & GrBitPhase as MultipleMatch,
       						if(EvMatchArrowsNo & GrBitPhase, EvElimArrows, EvFinArrows) DiArrows, 
       						if(EvMatchArrowsNo & GrBitPhase, EvElimEnds, EvFinEnds) DiEnds, 
       						if(EvMatchArrowsNo & GrBitPhase, EvElimSO, EvFinSO) DiSO
							from (
							    select FsEvent as Event1, FsTarget+0 as Target1, right(FsLetter,1) as Letter1, FSMatchNo as Matchno1,
				                    TfArrowstring as Arrowstring1, TfTiebreak as Tiebreak1, TfWinLose as Win1,
       								GROUP_CONCAT(CONCAT(IskDtEndNo,'#',IskDtArrowstring) separator ',') as TempTable1
						        from FinSchedule
								inner join TeamFinals on TfTeam!=0 and TfEvent=FSEvent and TfMatchNo=FSMatchNo and TfTournament=FSTournament
		                    	left join IskData on IskDtTournament=FSTournament AND IskDtType='M' AND IskDtMatchNo=FSMatchNo AND IskDtEvent=FSEvent and IskDtTeamInd=FSTeamEvent
						        where FSMatchNo%2=0 and FSTeamEvent=1 and FSTournament={$_SESSION['TourId']} and FSScheduledDate='$Date' and FSScheduledTime='$Time'
						        group by FSEvent, FSMatchNo
							) f1
							inner join (
							    select FsEvent as Event2, FsTarget+0 as Target2, right(FsLetter,1) as Letter2, FSMatchNo as Matchno2,
				                    TfArrowstring as Arrowstring2, TfTiebreak as Tiebreak2, TfWinLose as Win2,
       								GROUP_CONCAT(CONCAT(IskDtEndNo,'#',IskDtArrowstring) separator ',') as TempTable2
						        from FinSchedule
								inner join TeamFinals on TfTeam!=0 and TfEvent=FSEvent and TfMatchNo=FSMatchNo and TfTournament=FSTournament
		                    	left join IskData on IskDtTournament=FSTournament AND IskDtType='M' AND IskDtMatchNo=FSMatchNo AND IskDtEvent=FSEvent and IskDtTeamInd=FSTeamEvent
						        where FSMatchNo%2=1 and FSTeamEvent=1 and FSTournament={$_SESSION['TourId']} and FSScheduledDate='$Date' and FSScheduledTime='$Time'
						        group by FSEvent, FSMatchNo
							) f2 on Event2=Event1 and Matchno2=Matchno1+1
							inner join Grids on GrMatchNo=Matchno1
							inner join Events on EvCode=Event1 and EvTeamEvent=1 and EvTournament={$_SESSION['TourId']}
							where Target1 in (" . implode(',', $AssignedTargets) . ") 
							order by Target1, Letter1
							";
				        break;
		        }
		        $q = safe_r_sql($SQL);
		        $OldTarget = '';
		        $item = [];
		        while ($r = safe_fetch($q)) {
			        $TgtName=$r->Target1;
			        if($r->Target1!=$r->Target2) {
				        $TgtName.='-'.$r->Target2;
			        }
			        if($r->MultipleMatch) {
				        if(!is_numeric($r->Letter1)) {
					        if($r->Letter1==$r->Letter2) {
						        $TgtName.=' '.$r->Letter1;
                                $r->Letter1 = $r->Target1;
                                $r->Letter2 = $r->Target2;
					        } else {
						        $TgtName.=' '.$r->Letter1.$r->Letter2;
					        }
				        } else {
					        $TgtName.=' AB';
				        }
			        }
			        if ($OldTarget != $TgtName) {
				        if ($item) {
					        $Json['Status'][] = $item;
				        }
				        $item = [
					        'key' => "{$r->EvTeamEvent}|{$r->EvCode}|{$r->Matchno1}",
					        'target' => $TgtName,
					        'group' => $gId,
					        'letters' => [],
					        'over' => ($r->Win1 or $r->Win2),
				        ];
			        }
			        if(!is_numeric($r->Letter1)) {
						$aLetters=[$r->Letter1, $r->Letter2];
			        } else {
						$aLetters=['A', 'B'];
			        }
					foreach($aLetters as $idx => $l) {
				        $item['letters'][$l] = ['l' => $l, 'e' => 1, 'd' => array_fill(0, $r->DiEnds, 0), 't' => array_fill(0, $r->DiEnds, 0)];
				        foreach (str_split($r->{'Arrowstring'.($idx+1)}, $r->DiArrows) as $k => $v) {
                            $item['letters'][$l]['d'][$k] = 0;
                            $item['letters'][$l]['t'][$k] = 0;
					        if(strlen(str_replace(' ','',$v))==$r->DiArrows) {
						        $item['letters'][$l]['d'][$k]=1;
					        } else if(strlen(str_replace(' ','',$v))!=0) {
						        $item['letters'][$l]['d'][$k]=2;
					        }
				        }
						if($r->{'Tiebreak'.($idx+1)}) {
							foreach (str_split($r->{'Tiebreak'.($idx+1)}, $r->DiSO) as $k => $v) {
                                $item['letters'][$l]['d'][$r->DiEnds+$k] = 0;
                                $item['letters'][$l]['t'][$r->DiEnds+$k] = 0;
                                if(strlen(str_replace(' ','',$v))==$r->DiSO) {
									$item['letters'][$l]['d'][$r->DiEnds+$k]=1;
								} else if(strlen(str_replace(' ','',$v))!=0) {
									$item['letters'][$l]['d'][$r->DiEnds+$k]=2;
								}
							}
						}
				        if($r->{'TempTable'.($idx+1)}) {
					        foreach (explode(',', $r->{'TempTable'.($idx+1)}) as $tVal) {
						        list($k, $v) = explode('#', $tVal);
						        $k = intval($k)-1;
						        if (strlen(str_replace(' ', '', $v)) == ($k<$r->DiEnds ? $r->DiArrows:$r->DiSO)) {
							        $item['letters'][$l]['t'][$k] = 1;
						        } else if (strlen(str_replace(' ', '', $v)) != 0) {
							        $item['letters'][$l]['t'][$k] = 2;
						        }
					        }
				        }
					}
			        $item['letters'] = array_values($item['letters']);
			        $OldTarget = $TgtName;
		        }
		        if ($item) {
			        $Json['Status'][] = $item;
		        }
				break;
        }
    }

}
safe_close();
JsonOut($Json);