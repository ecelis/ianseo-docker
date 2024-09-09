<?php

if(empty($req->device) or !isset($req->tocode) or empty($req->payload)) {
    $Json['error'] = true;
    $Json['errorMsg'] = 'Missing parameters for '.$req->action;
    return;
}

$Payload=@json_decode($req->payload);

if(!$Payload or empty($Payload->scanTrigger)) {
    $Json['error'] = true;
    $Json['errorMsg'] = 'Missing parameters for '.$req->action;
    return;
}

// check the device is in the correct competition, even if not active
// only "pre-registered devices" can scan codes and "switch" targets

$q=safe_r_sql("select ToCode, IF(ToNameShort!='', ToNameShort, ToName) as TournamentName, ToCategory,
       IskDvGroup, IskDvDevice, IskDvTarget, IskDvSetup, IskDvSetupConfirmed, IskDvAppVersion, IskDvTournament,
       IskDvPersonal, IskDvSchedKey as ScheduleKey, IskDvProActive, IskDvCode, IskDvGroup, IskDvVersion
    FROM IskDevices 
    inner join Tournament on ToId=IskDvTournament
    WHERE IskDvDevice=".StrSafe_DB($req->device));
$DEVICE=safe_fetch($q);

if(!$DEVICE) {
    $Json['error'] = true;
    $Json['errorMsg'] = 'Missing parameters for '.$req->action;
    return;
}

// Device is known to the system and is in the correct competition...

// Analyze payload
switch($Payload->scanTrigger) {
    case 'TargetRequest':
        // Only in PRO/LIVE if user devices are used so that they can score on their next target
        // only if IskDvPersonal is switched on in the Devices page
        // only if the UsePersonalDevices has been specifically switched on Competition setup
        // And the code sent must be the same as the device are registered

        if(!($DEVICE->IskDvAppVersion==ISK_NG_LIVE_CODE or $DEVICE->IskDvAppVersion==ISK_NG_PRO_CODE)
                or !$DEVICE->IskDvPersonal
                or !getModuleParameter('ISK-NG', 'UsePersonalDevices', '', $DEVICE->IskDvTournament)
                or $Payload->ToCode!=$DEVICE->ToCode
                ) {
            $res = getQrConfig($DEVICE, true);
            return;
        }

        // Check if the Payload has also a group definition
        if(isset($Payload->Group)) {
            // update the group of the device
            $DEVICE->IskDvGroup=intval($Payload->Group);
        }

        // change has been accepted, switch target!
        // select the devices to reset
        $q=safe_r_SQL("select IskDvDevice, IskDvAppVersion
            from IskDevices 
            where IskDvGroup={$DEVICE->IskDvGroup} and IskDvTournament=$DEVICE->IskDvTournament and IskDvTarget=".intval($Payload->Target));
        while($r=safe_fetch($q)) {
            safe_w_sql("update IskDevices set IskDvProActive=0, IskDvTarget=0 where IskDvDevice=".StrSafe_DB($r->IskDvDevice));
            $tmpRes=resetDevice($r->IskDvDevice, 0, true);
            if($r->IskDvAppVersion!=ISK_NG_LIVE_CODE) {
                $MultipleRes[]=$tmpRes;
            }
        }

        // switch on the device
        safe_w_sql("update IskDevices 
            set IskDvTarget=".intval($Payload->Target).", IskDvSetupConfirmed=0, IskDvProActive=1, IskDvGroup={$DEVICE->IskDvGroup}
            where IskDvDevice=".StrSafe_DB($req->device));
//        resetDevice($req->device, $Payload->Target, true);
        $DEVICE->IskDvTarget=intval($Payload->Target);
        $res = getQrConfig($DEVICE, false, false, true);
        break;
    default:
        $res = array('action' => 'noop', 'device' => $req->device);
}


