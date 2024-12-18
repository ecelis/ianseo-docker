<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
checkACL(AclRoot, AclReadWrite);
if (!CheckTourSession())
    exit;

$Json = array();
if(!empty($_REQUEST["IP"]) AND $_REQUEST["IP"]!='127.0.0.1') {
    $name = empty($_REQUEST["Name"]) ? "" : StrSafe_DB($_REQUEST["Name"],true);
    if(!empty($_REQUEST["isTemplate"])) {
        $Sql = "INSERT INTO AclTemplates (AclTeTournament, AclTePattern, AclTeNick, AclTeEnabled) VALUES (" . StrSafe_DB($_SESSION['TourId']) . ",".StrSafe_DB($_REQUEST["IP"]).",'{$name}',1) 
            ON DUPLICATE KEY UPDATE AclTeNick='{$name}'";
        $q = safe_w_SQL($Sql);
    } else if($ip = checkValidIP($_REQUEST["IP"])) {
        $Sql = "INSERT INTO ACL (AclTournament, AclIP, AclNick, AclEnabled) VALUES (" . StrSafe_DB($_SESSION['TourId']) . ",'{$ip}','{$name}',1) 
            ON DUPLICATE KEY UPDATE AclNick='{$name}'";
        $q = safe_w_SQL($Sql);
    }
}
if(!empty($_REQUEST["deleteIP"])) {
    if(!empty($_REQUEST["isTemplate"])) {
        $Sql = "DELETE FROM `AclTemplates`  WHERE `AclTeTournament`=" . StrSafe_DB($_SESSION['TourId']) . " AND `AclTePattern`=" . StrSafe_DB($_REQUEST["deleteIP"]);
        $q = safe_w_SQL($Sql);
    } else if($ip = checkValidIP($_REQUEST["deleteIP"])) {
        $Sql = "DELETE FROM `AclDetails` WHERE `AclDtTournament`=" . StrSafe_DB($_SESSION['TourId']) . " AND `AclDtIP`='{$ip}'";
        $q = safe_w_SQL($Sql);
        $Sql = "DELETE FROM `ACL` WHERE `AclTournament`=" . StrSafe_DB($_SESSION['TourId']) . " AND `AclIP`='{$ip}'";
        $q = safe_w_SQL($Sql);
    }
}

if(isset($_REQUEST["featureIP"]) AND isset($_REQUEST["levelID"]) AND preg_match("/^[0-2]{1}$/",$_REQUEST["levelID"])) {
    $level = intval($_REQUEST["levelID"]);
    if(!empty($_REQUEST["isTemplate"])) {
        $tmpFeatures = array_fill(0,count($listACL),0);
        if($level !== AclNoAccess) {
            foreach ($listACL as $k=>$v) {
                $tmpFeatures[$k] = (array_key_exists($k, $limitedACL) ?  ($limitedACL[$k]<=$level ? $limitedACL[$k] : 0) : $level);
            }
        }
        $tmpToSave = array();
        foreach ($tmpFeatures as $k=>$f) {
            if($f) {
                $tmpToSave[] = $k.'|0|'.$f;
            }
        }
        safe_w_SQL("UPDATE `AclTemplates` SET `AclTeFeatures`='".implode('#',$tmpToSave)."' WHERE `AclTeTournament`=" . StrSafe_DB($_SESSION['TourId']) . " AND `AclTePattern`=" . StrSafe_DB($_REQUEST["featureIP"]));
    } else if($ip = checkValidIP($_REQUEST["featureIP"])) {
        if($level == AclNoAccess OR (isStarIP($ip) AND $level>AclReadOnly)) {
            $Sql = "DELETE FROM AclDetails WHERE AclDtTournament=".StrSafe_DB($_SESSION['TourId'])." AND AclDtIP='{$ip}'";
            safe_w_SQL($Sql);
        } else {
            foreach ($listACL as $k=>$v) {
                $lvl = (array_key_exists($k, $limitedACL) ?  ($limitedACL[$k]<=$level ? $limitedACL[$k] : 0) : $level);
                $Sql = "INSERT INTO AclDetails (AclDtTournament, AclDtIP, AclDtFeature, AclDtLevel) 
              VALUES (".StrSafe_DB($_SESSION['TourId']).", '{$ip}', {$k}, {$lvl}) 
              ON DUPLICATE KEY UPDATE AclDtLevel={$lvl}";
                safe_w_SQL($Sql);
            }
        }
    }
}

if(isset($_REQUEST["featureIP"]) AND  isset($_REQUEST["featureID"]) AND preg_match("/^[0-9]+$/",$_REQUEST["featureID"]) AND ($feature = intval($_REQUEST["featureID"]))<count($listACL)) {
    if(!empty($_REQUEST["isTemplate"])) {
        $tmpFeatures = array_fill(0,count($listACL),0);
        $Sql = "SELECT `AclTeFeatures` FROM `AclTemplates` WHERE `AclTeTournament`=" . StrSafe_DB($_SESSION['TourId']) . " AND `AclTePattern`=" . StrSafe_DB($_REQUEST["featureIP"]);
        $q = safe_r_SQL($Sql);
        if ($r = safe_fetch($q)) {
            if ($r->AclTeFeatures) {
                foreach (explode('#', $r->AclTeFeatures) as $ft) {
                    $tmp = explode("|", $ft);
                    $tmpFeatures[$tmp[0]] = intval($tmp[2]);
                }
            }
            if ($tmpFeatures[$feature]++ == 2 or (array_key_exists($feature, $limitedACL) and $tmpFeatures[$feature] > $limitedACL[$feature])) {
                $tmpFeatures[$feature] = 0;
            } else {
                if (array_key_exists($feature, $limitedACL)) {
                    $tmpFeatures[$feature] = $limitedACL[$feature];
                }
            }
            $tmpToSave = array();
            foreach ($tmpFeatures as $k=>$f) {
                if($f) {
                    $tmpToSave[] = $k.'|0|'.$f;
                }
            }
            safe_w_SQL("UPDATE `AclTemplates` SET `AclTeFeatures`='".implode('#',$tmpToSave)."' WHERE `AclTeTournament`=" . StrSafe_DB($_SESSION['TourId']) . " AND `AclTePattern`=" . StrSafe_DB($_REQUEST["featureIP"]));
        }
    } else if($ip = checkValidIP($_REQUEST["featureIP"])) {
        $lvl = 0;
        $Sql = "SELECT AclDtLevel FROM AclDetails WHERE AclDtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AclDtIP='{$ip}' && AclDtFeature={$feature}";
        $q = safe_r_SQL($Sql);
        if ($r = safe_fetch($q)) {
            $lvl = $r->AclDtLevel;
        }
        if ($lvl++ == 2 or (array_key_exists($feature, $limitedACL) and $lvl > $limitedACL[$feature]) or (isStarIP($ip) and $lvl != AclReadOnly)) {
            $Sql = "DELETE FROM AclDetails WHERE AclDtTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND AclDtIP='{$ip}' && AclDtFeature={$feature}";
        } else {
            if (array_key_exists($feature, $limitedACL)) {
                $lvl = $limitedACL[$feature];
            }
            $Sql = "INSERT INTO AclDetails (AclDtTournament, AclDtIP, AclDtFeature, AclDtLevel) 
          VALUES (" . StrSafe_DB($_SESSION['TourId']) . ", '{$ip}', {$feature}, {$lvl}) 
          ON DUPLICATE KEY UPDATE AclDtLevel={$lvl}";
        }
        $q = safe_w_SQL($Sql);
    }
}

if(!empty($_REQUEST['export'])) {
	$Export=array('ACL' => array(), 'AclDetails' => array(), 'AclTemplates' => array());
	$q=safe_r_sql("select * from ACL where AclTournament={$_SESSION['TourId']}");
	while($r=safe_fetch($q)) {
		$Export['ACL'][]=$r;
	}
	$q=safe_r_sql("select * from AclDetails where AclDtTournament={$_SESSION['TourId']}");
	while($r=safe_fetch($q)) {
		$Export['AclDetails'][]=$r;
	}
    $q=safe_r_sql("select * from AclTemplates where AclTeTournament={$_SESSION['TourId']}");
    while($r=safe_fetch($q)) {
        $Export['AclTemplates'][]=$r;
    }
	// We'll be outputting a gzipped TExt File in UTF-8 pretending it's binary
	header('Content-type: application/octet-stream');
	header("Content-Disposition: attachment; filename=\"{$_SESSION['TourCode']}-ACL.ianseo\"");
	echo gzcompress(serialize($Export),9);
	exit();
}

if(isset($_REQUEST["AclOnOff"]) AND preg_match("/^[0|1]$/",$_REQUEST["AclOnOff"]) AND isset($_REQUEST["AclRecord"]) AND preg_match("/^[0|1]$/",$_REQUEST["AclRecord"])) {
    setModuleParameter("ACL","AclEnable",$_REQUEST["AclOnOff"] . $_REQUEST["AclRecord"]);
    $lockEnabled = getModuleParameter("ACL","AclEnable","00",0,true);
    $Json['AclEnable'] = substr($lockEnabled,0,1);
    $Json['AclRecord'] = substr($lockEnabled,1,1);
} else {
    $Sql = "SELECT AclIP, AclNick, GROUP_CONCAT(CONCAT_WS('|',AclDtFeature,AclDtSubFeature,AclDtLevel) separator '#') as Features
      FROM ACL
      LEFT JOIN  AclDetails ON AclTournament=AclDtTournament AND AclIP=AclDtIP
      WHERE AclTournament=" . StrSafe_DB($_SESSION['TourId']) . "
      GROUP BY AclTournament, AclIP
      ORDER BY AclIP";
    $q = safe_r_SQL($Sql);
    $tmpIP = array();
    while ($r = safe_fetch($q)) {
        $tmpFeatures = array_fill(0,count($listACL),0);
        if ($r->Features) {
            foreach (explode('#', $r->Features) as $ft) {
                $tmp = explode("|", $ft);
                if($tmp[0]<count($listACL)) {
                    $tmpFeatures[$tmp[0]] = intval($tmp[2]);
                }
            }
        }
        $tmpIP[$r->AclIP] = array("Ip" => $r->AclIP, "Name" => $r->AclNick, "Value"=>addslashes($r->AclNick), "Opt" => $tmpFeatures);
    }
    $tmpSort=array_keys($tmpIP);
    natsort($tmpSort);
    foreach ($tmpSort as $vSort) {
        $Json['AclList'][] = $tmpIP[$vSort];
    }
    $Sql = "SELECT AclTePattern, AclTeNick, AclTeFeatures as Features
      FROM AclTemplates
      WHERE AclTeTournament=" . StrSafe_DB($_SESSION['TourId']) . "
      ORDER BY AclTePattern";
    $q = safe_r_SQL($Sql);
    $tmpIP = array();
    while ($r = safe_fetch($q)) {
        $tmpFeatures = array_fill(0,count($listACL),0);
        if ($r->Features) {
            foreach (explode('#', $r->Features) as $ft) {
                $tmp = explode("|", $ft);
                if($tmp[0]<count($listACL)) {
                    $tmpFeatures[$tmp[0]] = intval($tmp[2]);
                }
            }
        }
        $tmpIP[$r->AclTePattern] = array("Ip" => $r->AclTePattern, "Name" => $r->AclTeNick, "Value"=>addslashes($r->AclTePattern), "Opt" => $tmpFeatures);
    }
    $tmpSort=array_keys($tmpIP);
    natsort($tmpSort);
    foreach ($tmpSort as $vSort) {
        $Json['AclTemplates'][] = $tmpIP[$vSort];
    }

}

JsonOut($Json, 'callback');

function checkValidIP($ip) {
    /*
     /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/
     */
    if(preg_match("/^(\d{1,3}\.)+\d{1,3}$/",$ip)) {
        return filter_var($ip, FILTER_VALIDATE_IP);
    } else if(preg_match("/^(\d{1,3}\.)+\*$/",$ip)) {
        return($ip);
    } else {
        return false;
    }
}

function isStarIP($ip) {
    return preg_match("/^\d(\d{1,3}\.)+\*$/",$ip);
}
