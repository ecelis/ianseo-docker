<?php
// Verify required parameters
if(empty($req->tocode)) {
    $Json['error'] = true;
    $Json['errorMsg'] = 'Missing parameters for '.$req->action;
    return;
}

$q = safe_r_SQL("SELECT IskDvDevice, IskDvTarget, IskDvGroup, IskDvSchedKey, IskDvAppVersion, IskDvTournament, coalesce(ToId,0) as ToId
	FROM IskDevices 
	left join Tournament on ToCode=".StrSafe_DB($req->tocode)."
	WHERE `IskDvDevice`=".StrSafe_DB($req->device));
if(safe_num_rows($q) == 1 and $DEVICE=safe_fetch($q)) {
	if(!$DEVICE->IskDvTarget or $DEVICE->IskDvTournament!=$DEVICE->ToId) {
		$res = resetDevice($DEVICE->IskDvDevice, $DEVICE->IskDvTarget);
	} else {
		$req->ToId=$DEVICE->ToId;
		$req->IskDvGroup=$DEVICE->IskDvGroup;
		$req->IskDvTarget=$DEVICE->IskDvTarget;
		$req->IskDvSchedKey=$DEVICE->IskDvSchedKey;
		$req->IskDvAppVersion=$DEVICE->IskDvAppVersion;
		if(!$req->ToId) {
			$Json['error'] = true;
			$Json['errorMsg'] = 'No Competition with code '.$req->tocode;
			return ;
		}

		// updates the arrowstring of one archer
		require_once(dirname(__DIR__).'/Lib.php');
		switch($Message=readJsonData($req)) {
			case 'OK':
				$res = array('action' => 'noop', 'device' => $req->device);
				break;
            case 'RECONFIGURE':
			case 'RESET':
				$res = resetDevice($DEVICE->IskDvDevice, $DEVICE->IskDvTarget);
				break;
			default:
				$Json['error'] = true;
				$Json['errorMsg'] = $Message;
				return;
		}
	}
} else {
    $res = array('action' => 'handshake', 'error' => 2, 'device' => $req->device);
}

