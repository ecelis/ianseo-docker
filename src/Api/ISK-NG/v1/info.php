<?php
// Verify required parameters
if(!isset($req->data)) {
    $Json['error'] = true;
    $Json['errorMsg'] = 'Missing parameters for '.$req->action;
    return;
}
require_once(__DIR__.'/../Lib.php');

$checkQr = array(
    'enableWIFIManagement' => 'enableWifiManagement',
    'WifiReconnectTO' => 'wifiReconnectTO',
    'WifiSearch' => 'wifiSearch',
    'WifiResetCounter' => 'wifiResetCounter',
    'WifiDELETE' => 'wifiDelete',
    'WifiSSID' => 'WifiSSID',
    'WifiPWD' => 'WifiPWD',
    'WifiTgtF' => 'WifiTgtF',
    'WifiTgtT' => 'WifiTgtT',
    'enableGPS' => 'enableGps',
    'hideTotals' => 'hideTotals',
    'askTotals' => 'askTotals',
    'askSignature' => 'askSignature',
    'spottingMode' => 'spottingMode',
    'settingsPinCode' => 'settingsPinCode',
    'socketIP' => 'hostName',
    'socketPort' => 'hostPort',
);

$q = safe_r_SQL("SELECT `IskDvTournament`, `IskDvDevice` FROM IskDevices WHERE `IskDvDevice`=".StrSafe_DB($req->device));
if(safe_num_rows($q) == 1 AND $r=safe_fetch($q)) {
    $curQrCode = getSetupGlobalQrCode($r->IskDvTournament);
    $updValues = [];
    if ($req->data->battery) {
        $updValues[] = "`IskDvBattery`=" . (($req->data->battery->charging ? -1 : 1) * round($req->data->battery->charge * 100, 0));
    }
    if($req->data->settings) {
        $isSame = true;
        foreach ($checkQr as $k=>$v) {
            if($v != '') {
                if(array_key_exists($k, $curQrCode) and is_array($curQrCode[$k])) {
                    $isSame = $isSame AND (array_diff($curQrCode[$k],((array) $req->data->settings->{$v})) === array_diff(((array) $req->data->settings->{$v}),$curQrCode[$k]) AND count($curQrCode[$k])==count(((array) $req->data->settings->{$v})));
                } else if(array_key_exists($k, $curQrCode)){
                    $isSame = $isSame AND ($curQrCode[$k] == $req->data->settings->{$v});
                }
            }
        }
        $updValues[] = "`IskDvRunningConf`=" . StrSafe_DB($isSame ? json_encode($curQrCode) : '');
    }
    if (count($updValues)) {
        safe_w_sql("UPDATE IskDevices"
            . " SET " . implode(', ', $updValues)
            . " WHERE `IskDvDevice`=".StrSafe_DB($req->device));
    }
    $res = array('action' => 'noop', 'device' => $req->device);
} else {
    $res = array('action' => 'handshake', 'error' => 2, 'device' => $req->device);
}

