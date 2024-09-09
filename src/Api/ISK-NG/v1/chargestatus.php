<?php
// Verify required parameters
if(empty($req->device)) {
    $Json['error'] = true;
    $Json['errorMsg'] = 'Missing parameters for '.$req->action;
    return;
}

// Verify device is registered
$q = safe_r_SQL("SELECT `IskDvDevice` FROM IskDevices WHERE `IskDvDevice`=".StrSafe_DB($req->device));
if(safe_num_rows($q) == 0) {
    $res = array('action' => 'handshake', 'error' => 2, 'device' => $req->device);
    return;
}

// Update the database and return a response
safe_w_sql("UPDATE IskDevices "
    ."SET `IskDvBattery`=".(($req->charging ? -1 : 1) * intval($req->charge??0))
    ." WHERE `IskDvDevice`='{$req->device}'");

$res = array('action' => 'noop', 'device' => $req->device);
