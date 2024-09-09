<?php
// Verify required parameters
$req->iskType=intval($req->iskType??0);
$req->tournament=trim($req->tournament??'');
if(empty($req->uuid) or empty($req->version) or !$req->iskType) {
    $Json['error'] = true;
    $Json['errorMsg'] = 'Missing parameters for '.$req->action;
    return;
}

// CALL SEEMS OK so there is a res to send
$res = array(
	"action" => "handshake",
	"device" => $req->uuid,
	"devCode" => '',
	"error" => 1,				// 0 - no error, 1 - not allowed, 2 - unknown device
	"socketid" => '',	// Value returned for Live comps, Lite/Pro have no value
	"key" => ""	// Value returned for Pro/Live comps, Lite has no value
);

if(($req->iskType==ISK_NG_LITE_CODE or $req->iskType==ISK_NG_PRO_CODE) and !$req->tournament) {
	// Lite and PRO MUST have a tournament code request
	// => reject
	return;
}

// Retrieve the tournament ID if any for the given code and add the device if match is found
$tourId = 0;
$UseApi = 0;
if($req->tournament) {
	$q = safe_r_sql("SELECT ToId, ToOptions FROM Tournament where `ToCode`=".StrSafe_DB($req->tournament));
	if($r=safe_fetch($q) and $r->ToOptions and $Items=unserialize($r->ToOptions) and $UseApi=($Items['UseApi']??'')) {
		$tourId = $r->ToId;
	}
	if(!$UseApi or $UseApi!=$req->iskType) {
		// if competition is not set as ISK
		// or if the competition mode is not matching the app mode
		// => rejects
		return;
	}
}

$newDevice = false;
$q = safe_r_SQL("SELECT IskDvDevice, IskDvAppVersion, IskDvCode, IskDvVersion, IskDvProActive, ToId, ToOptions 
	FROM IskDevices 
	left join Tournament on ToId=IskDvTournament
	WHERE `IskDvDevice`=".StrSafe_DB($req->uuid));
if(safe_num_rows($q) == 0) {
	// new device, add in the DB
    $newDevice = true;
    $iskCode="0001";
    $q = safe_r_sql("SELECT IskDvCode FROM IskDevices where length(IskDvCode)=4 ORDER BY IskDvCode DESC");
    if($r=safe_fetch($q)) {
        $iskCode = str_pad(base_convert(base_convert($r->IskDvCode,36,10)+1,10,36),4,'0',STR_PAD_LEFT);
    }

    $res['devCode']=$iskCode;

    safe_w_SQL("INSERT INTO IskDevices
        (IskDvTournament, IskDvDevice, IskDvCode, IskDvVersion, IskDvAppVersion, IskDvProActive, IskDvLastSeen) VALUES
        ({$tourId}, ".StrSafe_DB($req->uuid).", '{$iskCode}', ".StrSafe_DB($req->version??'').", {$req->iskType}, '" .($req->iskType == ISK_NG_LITE_CODE ? '1':'0'). "', '".date('Y-m-d H:i:s')."')");
} else {
	// device already exists, check what to do
	$RESET=false;
	$r=safe_fetch($q);
    $res['devCode']=$r->IskDvCode;
	switch($req->iskType) {
		case ISK_NG_LITE_CODE:
		case ISK_NG_PRO_CODE:
			// we already know the ISK mode matches the competition mode
			if(!$r->ToId or $r->ToId!=$tourId) {
				// resets the device to the requested competition
				$RESET=true;
			}
            if(/*$req->iskType == ISK_NG_LITE_CODE AND*/ ($r->IskDvProActive == 0 OR $r->IskDvAppVersion != $req->iskType OR $r->IskDvVersion != $req->version)) {
                safe_w_sql("update IskDevices set IskDvProActive=1, IskDvVersion=".StrSafe_DB($req->version??'').", IskDvAppVersion={$req->iskType} where IskDvDevice=".StrSafe_DB($req->uuid));
            }
			break;
		case ISK_NG_LIVE_CODE:
			if(!$r->ToId or $tourId) {
				// no device-linked competition
				// if tourid is defined we already know the ISK and comp modes match
				$RESET=true;
			} else {
				$tourId=$r->ToId;
				if($r->ToOptions and $Items=unserialize($r->ToOptions) and $UseApi=($Items['UseApi']??'')) {
					if($UseApi==$req->iskType) {
						// updates the device type
						$RESET=true;
					} else {
						if($UseApi!=$r->IskDvAppVersion) {
							// if the ISK mode is not matching the attached competition mode,
							// removes the app from the competition to avoid further issues
							safe_w_sql("update IskDevices set IskDvTournament=0 where IskDvDevice=".StrSafe_DB($req->uuid));
						}
						return;
					}
				} else {
					return;
				}
			}
			break;
	}
	if($RESET) {
		// this device will probably need an update
		safe_w_sql("update IskDevices set 
				IskDvTournament=$tourId, 
				IskDvAppVersion=$req->iskType
			where IskDvDevice=".StrSafe_DB($req->uuid));
		if(safe_w_affected_rows()) {
			// if competition or app mode changes then a more indepth reset is performed
			$Fields="";
			if($req->iskType==ISK_NG_LITE_CODE) {
				// the app advertises itself as a lite so overwrite everything
				$Fields.="IskDvGroup=0,
					IskDvTarget='',
					IskDvTargetReq='', ";
			}
			safe_w_sql("update IskDevices set 
				" . $Fields . "
                IskDvPersonal=0, 
				IskDvSchedKey='',
				IskDvState=0,
				IskDvAuthRequest=0,
				IskDvProActive=0,
				IskDvProConnected=0,
				IskDvSetup='',
				IskDvRunningConf='',
				IskDvUrlDownload='',
				IskDvGps='',  IskDvLastSeen='".date('Y-m-d H:i:s')."',
				IskDvVersion=".StrSafe_DB($req->version??'')."
				where IskDvDevice=".StrSafe_DB($req->uuid));
		} else {
			safe_w_sql("update IskDevices 
				set IskDvLastSeen='".date('Y-m-d H:i:s')."',
				IskDvVersion=".StrSafe_DB($req->version??'')."
				where IskDvDevice=".StrSafe_DB($req->uuid));
		}
	}
}

$res['error'] = 0;
$res['newdevice'] = $newDevice;

switch($req->iskType) {
	case ISK_NG_PRO_CODE:
		$res['key'] = getModuleParameter('ISK-NG', 'LicenseNumber', '', $tourId);
		break;
	case ISK_NG_LIVE_CODE:
		$res['key'] = '2100-12-31';
		$res['socketid'] = $req->uuid;
		break;
}

