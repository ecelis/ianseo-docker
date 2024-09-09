<?php
// Verify required parameters
if(empty($req->device) || empty($req->latitude) || empty($req->longitude)) {
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

// Prepare the data and update the database
$position = serialize(array(
    'lat' => $req->latitude,
    'lon' => $req->longitude,
    'alt' => $req->altitude,
    'spd' => $req->speed,
    'hdg' => $req->heading,
    'acc' => $req->accuracy,
    'ts'  => $req->timestamp
));
$q=safe_w_sql("UPDATE IskDevices "
    ."SET IskDvGps='{$position}', IskDvLastSeen='" . date('Y-m-d H:i:s')."' "
    ."WHERE IskDvDevice=" . StrSafe_DB($req->device));

// Nothing to return, so just send a noop
$res = array('action' => 'noop', 'device' => $req->device);

