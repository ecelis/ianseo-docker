<?php
// Verify required parameters
if(empty($req->device) or empty($req->configHash)) {
    $Json['error'] = true;
    $Json['errorMsg'] = 'Missing parameters for '.$req->action;
    return;
}

// Verify device is registered
$q=safe_r_sql("select ToCode, IskDvSetup, IF(ToNameShort!='', ToNameShort, ToName) as TournamentName, IskDvGroup, IskDvDevice, IskDvTarget, IskDvSetup, IskDvVersion
	FROM IskDevices
	INNER JOIN Tournament ON ToId=IskDvTournament
	WHERE IskDvDevice = ".StrSafe_DB($req->device));
$r = safe_fetch($q);
if(!$r) {
    $res = array('action' => 'handshake', 'error' => 2, 'device' => $req->device);
    return;
}

// Update the database and return a response
// first check the hash
if(md5($r->IskDvSetup)==$req->configHash) {
    safe_w_sql("UPDATE IskDevices
        SET IskDvSetupConfirmed=1
        WHERE IskDvDevice = ".StrSafe_DB($req->device));

    $res = array('action' => 'noop', 'device' => $req->device);
} else {
    $res = getQrConfig($r, true);
}
