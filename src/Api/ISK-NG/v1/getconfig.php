<?php
$q=safe_r_sql("select ToCode, ToId=IskDvTournament as InTournament, IF(ToNameShort!='', ToNameShort, ToName) as TournamentName, IskDvGroup, IskDvDevice, IskDvTarget, IskDvSetup, IskDvSetupConfirmed, IskDvAppVersion, IskDvVersion
	FROM IskDevices
	left JOIN Tournament ON ToId=IskDvTournament
	WHERE IskDvDevice = ".StrSafe_DB($req->device));

if(safe_num_rows($q) == 0) {
    $res = array('action' => 'handshake', 'error' => 2, 'device' => $req->device);
    return;
} else if($r=safe_fetch($q) and $r->IskDvSetup and $r->InTournament) {
    $res = getQrConfig($r, true);
} else if(!$r->IskDvSetupConfirmed and ($r->IskDvAppVersion==ISK_NG_PRO_CODE or $r->IskDvAppVersion==ISK_NG_LIVE_CODE)) {
    $res = resetDevice($r->IskDvDevice, $r->IskDvTarget, true);
} else {
    $res = array('action' => 'noop', 'device' => $req->device);
}
