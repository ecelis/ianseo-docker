<?php
require_once('./config.php');
require_once('Common/Lib/CommonLib.php');
require_once(__DIR__.'/Lib.php');

$Json=array('error'=>true);
if(!(CheckTourSession() AND checkACL(AclISKServer, AclReadWrite, false))) {
    JsonOut($Json);
    die();
}

$tmpDevices = array();

if(isset($_REQUEST['Action']) && preg_match("/^(tSendMessage|tPersonal|tSendQrSetup|tSingleSequence|tStatus|tStatusGroup|tGroupTarget|tGroup|tTargetFrom|tCompetition|tDelete|tSequence|tSchedule)$/",$_REQUEST['Action'], $action)) {
	$SEQTYPE = '';
    $GROUPID = -1;
    $storeSeq=getModuleParameter('ISK-NG', 'Sequence', array());

    if(isset($_REQUEST['deviceId']) && preg_match("/^[0-9A-Z\-]+$/i",$_REQUEST['deviceId'],$tmp)) {
        $tmpDevices[] = $tmp[0];
    }
    if(isset($_REQUEST['deviceList']) && is_array($_REQUEST['deviceList'])) {
        foreach ($_REQUEST['deviceList'] as $dev){
            if(preg_match("/^[0-9A-Z\-]+$/i",$dev,$tmp)) {
                $tmpDevices[] = $tmp[0];
            }
        }
    }
    if(isset($_REQUEST['groupId']) && is_numeric($_REQUEST['groupId'])) {
        $GROUPID = intval($_REQUEST['groupId']);
	    $SEQTYPE=($storeSeq[$GROUPID]['type'][0] ?? '');
        $SQL = "SELECT IskDvDevice FROM IskDevices WHERE IskDvTournament=".$_SESSION["TourId"] ." AND IskDvGroup={$GROUPID}";
        $q = safe_r_SQL($SQL);
        while($r = safe_fetch($q)) {
            $tmpDevices[] = $r->IskDvDevice;
        }
    }
    if(isset($_REQUEST['newGrp']) && is_numeric($_REQUEST['newGrp'])) {
        $NewGroup=intval($_REQUEST['newGrp']);
        if($NewGroup>=0 and $NewGroup<26) {
            $SEQTYPE=($storeSeq[$_REQUEST['newGrp']]['type'][0] ?? '');
        }
    }
    $SQL = array();
	$NeedChanges=true;
    switch($action[0]) {
        case 'tSchedule':
            $options=[];
            if($_REQUEST['today']) {
                $options['OnlyToday']=true;
            }
            if($_REQUEST['unfinished']) {
                $options['Unfinished']=true;
            }
            $Json['schedule']=[];
            foreach(getApiScheduledSessions($options) as $r) {
                $Json['schedule'][$r->keyValue] = array(
                    'key'=>$r->keyValue,
                    'value'=>$r->Description,
                    'type'=>$r->Type,
                    'distances'=>($r->Type =='Q' ? count(explode(',',$r->MaxEnds)) : 0)
                );
            }
            $Json['error']=0;
            JsonOut($Json);
            break;
        case 'tSendMessage':
            $SQL='';
            switch($SEQTYPE) {
                case'Q':
                    $SQL = "SELECT DISTINCT IskDvDevice, IskDvTarget, ((IFNULL(QuTarget,0) * IskDvProActive)!=0) as isActive  
                        FROM IskDevices
                        LEFT JOIN Qualifications ON QuSession={$storeSeq[$GROUPID]['session']} AND IskDvTarget=QuTarget 
                        LEFT JOIN Entries ON EnId=QuId and EnTournament=IskDvTournament
                        WHERE IskDvTournament='{$_SESSION["TourId"]}' AND IskDvDevice IN ('" .implode("','",$tmpDevices). "')";

                    break;
                case 'M':
                    $isTeam = ($storeSeq[$GROUPID]['subtype']=='T' ? 1:0);
                    $SQL = "SELECT DISTINCT IskDvDevice, IskDvTarget, ((IFNULL(FSTarget,0) * IskDvProActive)!=0) as isActive   
                        FROM IskDevices 
                        LEFT JOIN FinSchedule ON FSTournament=IskDvTournament AND (FSMatchNo % 2)=0 AND CAST(IskDvTarget as UNSIGNED)=CAST(FSTarget AS UNSIGNED) AND FSTeamEvent={$isTeam} AND CONCAT(FSScheduledDate,FSScheduledTime)='{$storeSeq[$GROUPID]['session']}' 
                        WHERE IskDvTournament='{$_SESSION["TourId"]}' AND IskDvDevice IN ('" .implode("','",$tmpDevices). "')";
                    break;
            }
            $q=safe_r_SQL($SQL);
            while($r=safe_fetch($q)) {
                $Json['json'][]=array('action'=>'dialog', 'device'=>$r->IskDvDevice, 'dialogMessage' => ($r->isActive ? 'Device in use' : 'Device NOT IN USE'), 'dialogType' => ($r->isActive ? 1 : -1), 'dialogTarget' => $r->IskDvTarget, 'autoCloseSeconds'=>30);
            }
            $Json['error'] = false;
            JsonOut($Json);
            break;
        case 'tSendQrSetup':
			if(!isset($_REQUEST['deviceId']) or $_SESSION['UseApi']!=ISK_NG_LIVE_CODE or !($QrCode=getSetupGlobalQrCode($_SESSION['TourId']))) {
				JsonOut($Json);
			}

			$QrCode['device']=$_REQUEST['deviceId'];
	        $Json['json'][]=$QrCode;
	        $Json['error'] = false;
	        JsonOut($Json);
			break;
        case 'tSingleSequence':
			if(!isset($_REQUEST['groupId']) or !isset($_REQUEST['deviceId']) or ($_SESSION['UseApi']!=ISK_NG_LIVE_CODE and $_SESSION['UseApi']!=ISK_NG_PRO_CODE)) {
				JsonOut($Json);
			}
			$Sequences=getModuleParameter('ISK-NG', 'Sequence');
			if(!empty($Sequences[intval($_REQUEST['groupId'])]['IskKey'])) {
		        $q=safe_r_sql("select ToCode, ToCategory, IF(ToNameShort!='', ToNameShort, ToName) as TournamentName, ".StrSafe_DB($Sequences[intval($_REQUEST['groupId'])]['IskKey'])." as ScheduleKey, IskDvGroup, IskDvSetup, IskDvDevice, IskDvTarget, IskDvProActive, IskDvCode, IskDvVersion ".
			        "FROM IskDevices ".
			        "INNER JOIN Tournament ON ToId=IskDvTournament ".
			        "WHERE IskDvTournament='{$_SESSION["TourId"]}' AND IskDvDevice=" .StrSafe_DB($_REQUEST['deviceId']));
		        $Json['json'] = array();
		        while($r=safe_fetch($q)) {
			        if($r->IskDvProActive) {
				        $Json['json'][]=getQrConfig($r);
                        // reset the status of the sequence
                        safe_w_sql("update IskDevices set IskDvSetupConfirmed=0 where IskDvDevice=".StrSafe_DB($_REQUEST['deviceId']));
			        } else {
				        $Json['json'][]=resetDevice($r->IskDvDevice, $r->IskDvTarget);
			        }
		        }
		        $Json['error'] = false;
			}
	        JsonOut($Json);
			break;
        case 'tSequence':
            if(!(isset($_REQUEST["sequenceId"]) and preg_match("/^[A-Z0-9:\-]+$/",$_REQUEST["sequenceId"],$tmpSeq) and !empty($_REQUEST["distanceId"]) and is_array($_REQUEST["distanceId"]))) {
                JsonOut($Json);
            }
	        $Json['assigned']=$_REQUEST["sequenceId"];
	        $IskSeq = array('IskKey'=>'', "type"=>'', "subtype"=>'', "session"=>'', "distance"=>[], "maxdist"=>'');
            if(array_key_exists($tmpSeq[0], getApiScheduledSessions())) {
                $type = substr($tmpSeq[0],0,1);
	            $SEQTYPE = $type;
                $subtype='';
                $maxDist = '';
				switch($type) {
					case 'Q':
						$maxDist = substr($tmpSeq[0],1,1);
						$ses = substr($tmpSeq[0],2);
						break;
					case 'E':
						$type='Q';
						$subtype=substr($tmpSeq[0],0,2);
						$ses = substr($tmpSeq[0],2);
						break;
					default:
						$subtype=$type;
						$ses = substr($tmpSeq[0],1);
						if($ses[0]=='R') {
							$subtype='R';
							$ses = substr($ses,1);
						}
						$_REQUEST["distanceId"]=[1];
						$type='M';
				}
				$Dists=[];
				foreach($_REQUEST["distanceId"] as $d) {
					if(!preg_match("/^[0-8]$/", $d)) {
						JsonOut($Json);
					}
					$Dists[]=intval($d);
				}
                $IskSeq = array('IskKey'=>$tmpSeq[0], "type"=>$type, "subtype"=>$subtype, "session"=>$ses, "distance"=>$Dists, "maxdist"=>$maxDist);
            }
            if(count($storeSeq)==0) {
                $storeSeq = array($GROUPID => $IskSeq);
            } else {
                $storeSeq[$GROUPID] = $IskSeq;
            }
            setModuleParameter('ISK-NG', 'Sequence', $storeSeq);
            //on sequence send, reset AutoImport and Partial Import flags for the group
            $AutoImport = getModuleParameter('ISK-NG', 'AutoImport', array());
            $AutoImport[intval($GROUPID)] = false;
            setModuleParameter('ISK-NG', 'AutoImport', $AutoImport);
            $PartialImport = getModuleParameter('ISK-NG', 'PartialImport', array());
            $PartialImport[intval($GROUPID)] = false;
            setModuleParameter('ISK-NG', 'PartialImport', $PartialImport);

            // simply clears the parameter forcing to be reread on next invocation...
            $tmp = getModuleParameter('ISK-NG', 'Sequence', null, $_SESSION["TourId"], true);

            $q=safe_r_sql("select ToCode, ToCategory, IF(ToNameShort!='', ToNameShort, ToName) as TournamentName, IskDvSchedKey as ScheduleKey, IskDvGroup, IskDvSetup, IskDvDevice, IskDvTarget, IskDvProActive, IskDvCode, IskDvVersion ".
                "FROM IskDevices ".
                "INNER JOIN Tournament ON ToId=IskDvTournament ".
                "WHERE IskDvTournament='{$_SESSION["TourId"]}' AND IskDvDevice IN ('" .implode("','",$tmpDevices). "')");
            $Json['json'] = array();
            while($r=safe_fetch($q)) {
                if($r->IskDvProActive) {
                    $Json['json'][]=getQrConfig($r, false, false, true);
                } else {
                    $Json['json'][]=resetDevice($r->IskDvDevice, $r->IskDvTarget, true);
                }
            }
            $Json['error'] = false;
            JsonOut($Json);
            break;
        case 'tDelete':
            safe_w_SQL("DELETE FROM IskDevices WHERE IskDvDevice IN ('" .implode("','",$tmpDevices). "')");
            break;
        case 'tStatus':
            $SQL[] = "IskDvProActive=IF(IskDvTarget=0, 0, 1-IskDvProActive), IskDvSetupConfirmed=if(IskDvProActive, IskDvSetupConfirmed, 0) ";
            break;
        case 'tPersonal':
            $SQL[] = "IskDvPersonal=1-IskDvPersonal";
            break;
        case 'tStatusGroup':
            if(preg_match("/^(0|1)$/",$_REQUEST["doEnable"],$tmp)) {
                $SQL[] = "IskDvProActive=if(IskDvTarget='',0,{$tmp[0]})";
            }
            if(!$_REQUEST["doEnable"]) {
                $SQL[] = "IskDvSetupConfirmed=0";
            }
            break;
        case 'tGroupTarget':
            if(preg_match("/^[0-9]+$/",$_REQUEST["newGrp"],$tmpGrp) AND $tmpGrp[0]<=26 AND preg_match("/^[0-9]+$/",$_REQUEST["newTgt"],$tmpTgt) AND $tmpTgt[0]<=999) {
				$SQL[] = "IskDvGroup={$tmpGrp[0]}, IskDvTarget={$tmpTgt[0]}, IskDvSetup=''";
            }
            break;
        case 'tGroup':
            if(preg_match("/^[0-9]+$/",$_REQUEST["newGrp"],$tmpGrp) AND $tmpGrp[0]<=26) {
                $SQL[] = "IskDvGroup={$tmpGrp[0]}, IskDvSetup=''";
            }
            break;
        case 'tCompetition':
            $SQL[] = "IskDvTournament=IF(IskDvTournament=".$_SESSION["TourId"].", 0, ".$_SESSION["TourId"].") ";
            break;
        case 'tTargetFrom':
            if(preg_match("/^[0-9]+$/",$_REQUEST["newTgt"],$tmpTgt) AND $tmpTgt[0]<=999) {
                foreach ($tmpDevices as $tmpDev) {
                    safe_w_SQL("UPDATE IskDevices SET IskDvTarget='".($tmpTgt[0]++) ."' WHERE IskDvDevice = '{$tmpDev}'");
                }
                $SQL[] = "IskDvSetup=''";
	            $NeedChanges=false;
            }
            break;

    }
    if(count($SQL)) {
        safe_w_SQL("UPDATE IskDevices SET " . implode(',', $SQL) . " WHERE IskDvDevice IN ('" .implode("','",$tmpDevices). "')");
        if(safe_w_affected_rows() === 0 and $NeedChanges) {
            JsonOut($Json);
        } else {
	        $SQL="select IFNULL(ToCode,'') AS ToCode, IFNULL(IF(ToNameShort!='', ToNameShort, ToName),'') as TournamentName, IFNULL(ToCategory,0) AS ToCategory, ".StrSafe_DB($SEQTYPE)." as ScheduleKey, IskDvTournament, IskDvGroup, IskDvSetup, IskDvDevice, IskDvTarget, IskDvProActive, IskDvCode, IskDvVersion ".
		        "FROM IskDevices ".
		        "LEFT JOIN Tournament ON ToId=IskDvTournament ".
		        "WHERE IskDvDevice IN ('" .implode("','",$tmpDevices). "')";
            // $SQL = "SELECT IskDvDevice, IskDvTournament, IskDvProActive, IskDvTarget FROM IskDevices WHERE IskDvDevice IN ('" .implode("','",$tmpDevices). "')";
            $q = safe_r_SQL($SQL);
            $Json['json']=array();
            while ($r = safe_fetch($q)) {
                if($r->IskDvTournament == $_SESSION["TourId"] and !$r->IskDvProActive) {
                    $Json['json'][] = resetDevice($r->IskDvDevice, $r->IskDvTarget, true);
                } elseif($r->IskDvTournament != $_SESSION["TourId"] OR !$r->IskDvProActive) {
                    $Json['json'][] = resetDevice($r->IskDvDevice, ($r->IskDvTournament == $_SESSION["TourId"] ? $r->IskDvTarget : 0));
                } else {
                    $Json['json'][] = getQrConfig($r);
                }
            }
        }
    }
}

$SQL = "SELECT IskDevices.*, if(IskDvLastSeen=0, 0, TIMESTAMPDIFF(second,CONVERT_TZ(IskDvLastSeen,'+00:00','{$_SESSION['TourTimezone']}'),now())) as Seconds, 	
	least(3, round(TIMESTAMPDIFF(second,CONVERT_TZ(IskDvLastSeen,'+00:00','{$_SESSION['TourTimezone']}'),now())/65)) as Difference
	FROM IskDevices
	ORDER BY IskDvGroup, IskDvTournament={$_SESSION['TourId']} desc, IskDvTarget+0, IskDvProActive desc, IskDvAppVersion desc, IskDvProConnected desc, IskDvTargetReq, IskDvCode";
$q=safe_r_sql($SQL);
$Json['error'] = false;
$Json['Groups'] = array();
$Json['Devices'] = array();

$curQrCode = json_encode(getSetupGlobalQrCode($_SESSION['TourId']));
$curSequences = getModuleParameter('ISK-NG', 'Sequence');
$usePersonalDevices=getModuleParameter('ISK-NG', 'UsePersonalDevices');
$actSequence = [];

if (safe_num_rows($q)>0) {
    $Groups=array();
    while ($r=safe_fetch($q)) {
        if(!array_key_exists(intval($r->IskDvGroup),$Json['Groups'])) {
            $Json['Groups'][intval($r->IskDvGroup)]=array(
                'gId' => intval($r->IskDvGroup),
                'gName' => chr(65+$r->IskDvGroup),
                // 'gSequence' => (empty($curSequences[intval($r->IskDvGroup)]) ? null : $curSequences[intval($r->IskDvGroup)]['type'].$curSequences[intval($r->IskDvGroup)]['maxdist'].$curSequences[intval($r->IskDvGroup)]['session']),
                'gSequence' => (empty($curSequences[intval($r->IskDvGroup)]) ? null : $curSequences[intval($r->IskDvGroup)]['IskKey']),
                'gDistance' => ($curSequences[intval($r->IskDvGroup)]['distance'] ?? []),
                'gDevicesCnt' => 0
            );
			if(!is_array($Json['Groups'][intval($r->IskDvGroup)]['gDistance'])) {
				$Json['Groups'][intval($r->IskDvGroup)]['gDistance']=[$Json['Groups'][intval($r->IskDvGroup)]['gDistance']];
			}
        }
		$isUsed=($setup=json_decode($r->IskDvSetup) and $setup->action=='reconfigure' and $r->IskDvTournament==$_SESSION['TourId'] and $setup->toCode==$_SESSION['TourCode'] and $r->IskDvProActive);
        $Json['Devices'][]=array(
            'tDevice' => $r->IskDvDevice ,
            'tGId' => intval($r->IskDvGroup) ,
            'tTourId' => ($r->IskDvTournament==$_SESSION['TourId']),
            'tCode' => $r->IskDvCode ,
            'tState' => intval($r->IskDvProActive),
            'tTgt' => intval($r->IskDvTarget),
            'tTgtReq' => intval($r->IskDvTargetReq ? : $r->IskDvTarget) ,

            'tSetupConfirmed' => intval($r->IskDvSetupConfirmed),
			'tUsed' => $isUsed,
			'tPersonal' => $usePersonalDevices ? intval($r->IskDvPersonal) : -1,

            'tAppVersion' => $r->IskDvVersion,
            'tApp' => AppNames[$r->IskDvAppVersion],
            'tBattery' => intval($r->IskDvBattery),
            'tAuthReq' => intval($r->IskDvAuthRequest),
            'tIp' => $r->IskDvIpAddress ,
            'tCurrentQRCode' => ($r->IskDvRunningConf == $curQrCode),
            'tLastOp' => $r->IskDvLastSeen=='0000-00-00 00:00:00' ? '' : $r->IskDvLastSeen ,
			'tElapsed' => ($r->Difference==3 ? '>03:30' : date('i:s', $r->Seconds)).' m',
            'tLastOpClass' => 'tLastOp-'.$r->Difference,
        );
        if($r->IskDvTournament==$_SESSION['TourId']) {
            $Json['Groups'][intval($r->IskDvGroup)]['gDevicesCnt']++;
        }
		if(isset($curSequences[intval($r->IskDvGroup)]) and !isset($actSequence[intval($r->IskDvGroup)])) {
			$actSequence[intval($r->IskDvGroup)]=$curSequences[intval($r->IskDvGroup)];
		}
    }
    ksort($Json['Groups']);
    ksort($actSequence);
	setModuleParameter('ISK-NG', 'Sequence', $actSequence);
    $Json['Groups'] = array_values($Json['Groups']);
}


JsonOut($Json);