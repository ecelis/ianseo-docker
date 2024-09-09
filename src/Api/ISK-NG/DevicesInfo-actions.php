<?php

require_once('./config.php');
require_once('Common/Lib/CommonLib.php');
require_once(__DIR__ . '/Lib.php');

$Json=array('error'=>true);
if(!(CheckTourSession() AND checkACL(AclISKServer, AclReadWrite, false))) {
    JsonOut($Json);
    die();
}

$grpValid = preg_match("/^(\-{0,1}[0-9]+)$/",$_REQUEST["Group"]??-1,$grpId);
$rangeValid = preg_match_all("/(>?\d+\ *\-\ *\d+)|(\d+)/",$_REQUEST["Target"]??'', $rangeTgt);

if($grpValid AND ($rangeValid OR $grpId[0] != -1)) {
    $Json['devices'] = array();
    $Json['json'] = array();
    $Sql = "SELECT IskDvDevice, IskDvGroup, IskDvTarget, IskDvCode FROM IskDevices WHERE IskDvTournament=" . $_SESSION["TourId"];
    if($grpId[0] != -1) {
        $Sql .= " AND IskDvGroup={$grpId[0]}";
    }
    $WHERE = array();
    $devices=array();
    foreach ($rangeTgt[0] as $range) {
        if (preg_match("/^([0-9]+)\-([0-9]+)$/", str_replace(' ', '', $range), $Tmp)) {
            if(intval($Tmp[1]) > intval($Tmp[2])) {
                $tmp = $Tmp[1];
                $Tmp[1] = $Tmp[2];
                $Tmp[2] = $tmp;
            }
            $WHERE[] = "CAST(IskDvTarget as SIGNED) BETWEEN " . $Tmp[1] . " AND " . $Tmp[2];
            foreach (range($Tmp[1], $Tmp[2]) as $k) {
                $devices[$k] = array("device" => '---', "group" => '', "target" => $k, "code"=>'', "existent" => false);
            }
        } else if (preg_match("/^([0-9]+)$/", str_replace(' ', '', $range), $Tmp)) {
            $WHERE[] = "CAST(IskDvTarget as SIGNED) = '" . $Tmp[1] . "'";
            $devices[$Tmp[1]]=array("device"=>'---', "group"=>'', "target"=>$Tmp[1], "code"=>'', "existent"=>false);
        }
    }
    if(count($WHERE)) {
        $Sql .= " AND (" . implode(" OR ", $WHERE) . ")";
    }
    $Sql .= " ORDER BY IskDvGroup, CAST(IskDvTarget as UNSIGNED), CAST(IskDvCode as UNSIGNED)";
    $q=safe_r_SQL($Sql);
    while($r=safe_fetch($q)) {
        $devices[$r->IskDvTarget.'-'.$r->IskDvCode.'-'.$r->IskDvDevice] = array("device"=>$r->IskDvDevice, "group"=>chr(65+$r->IskDvGroup), "target"=>$r->IskDvTarget, "code"=>$r->IskDvCode, "existent"=>true);
        unset($devices[$r->IskDvTarget]);
        $Json['json'][]=array('action'=>'info', 'device'=>$r->IskDvDevice, 'sender'=>'');
    }
    ksort($devices, SORT_NATURAL);
    $Json['devices'] = array_values($devices);
    $Json['error'] = false;
}

JsonOut($Json);
