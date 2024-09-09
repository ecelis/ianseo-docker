<?php
require_once(dirname(__FILE__) . '/config.php');

CheckTourSession(false);
checkACL(AclISKServer, AclReadWrite, false);

$JSON=array('error' => true);
$IskGroup=getModuleParameter('ISK-NG', 'Sequence', null, $_SESSION['TourId']);

if(!empty($_REQUEST['delGroup'])) {
	safe_w_sql("delete from TargetGroups
		where TgTournament={$_SESSION['TourId']}
		and TgGroup=".StrSafe_DB($_REQUEST['delGroup']));
	$JSON['error'] = false;
} else if(isset($_REQUEST['activateGrouping'])) {
	$GroupNum=intval($_REQUEST['groupNum']);
	$OldActive=getModuleParameter('ISK-NG', 'Grouping', array_fill(0, count($IskGroup??[0]), 0));
	if(!is_array($OldActive)){
		$OldActive=[$OldActive];
	}
	if(count($OldActive)!=count($IskGroup??[0])) {
		foreach($IskGroup??[0] as $k=>$v) {
			$OldActive[$k]=($OldActive[$k]??0);
		}
	}
	$OldActive[$GroupNum]=intval($_REQUEST['activateGrouping']);
    setModuleParameter('ISK-NG', 'Grouping', $OldActive);
    $JSON['error'] = false;
} else if($agStep=intval($_REQUEST['agStep']??0) AND $agFrom=intval($_REQUEST['agFrom']??0) AND $agTo=intval($_REQUEST['agTo']??0)) {
    $Group=intval($_REQUEST['agGroup']??0);
	for($i=$agFrom; $i<=$agTo; $i=$i+$agStep) {
        for($tgt=$i; $tgt<=min($i+$agStep,$agTo); $tgt++) {
            safe_w_sql("insert into TargetGroups
            set TgTournament={$_SESSION['TourId']},
                TgSession=$Group,
                TgSesType='A',
                TgTargetNo='$tgt',
                TgGroup=" . StrSafe_DB("Group ".chr(65+$Group)." ".str_pad($i,3,"0",STR_PAD_LEFT)." - ".str_pad(min($i+$agStep-1,$agTo),3,"0",STR_PAD_LEFT)) . "
            on duplicate key update
                TgGroup=" . StrSafe_DB("Group ".chr(65+$Group)." ".str_pad($i,3,"0",STR_PAD_LEFT)." - ".str_pad(min($i+$agStep-1,$agTo),3,"0",STR_PAD_LEFT)));
        }
    }
    $JSON['error'] = false;
} else if(!empty($_REQUEST['addGroup'])) {
	$Group=preg_replace('/[^0-9a-z_ -]/sim', '', $_REQUEST['addGroup']);
	$GroupNum=intval(preg_replace('/[^0-9]/sim', '', $_REQUEST['groupNum']));
    $Targets=explode('|',preg_replace('/[^0-9|]/sim', '', $_REQUEST['groupTargets']));
    safe_w_sql("DELETE FROM TargetGroups ".
        "WHERE TgTournament={$_SESSION['TourId']} and TgSession=$GroupNum and TgGroup=".StrSafe_DB($Group)." and TgTargetNo NOT IN ('".implode("','",$Targets)."')" );
    foreach ($Targets as $tgt) {
        safe_w_sql("INSERT INTO TargetGroups
            SET TgTournament={$_SESSION['TourId']}, TgSession=$GroupNum, TgSesType='A', TgTargetNo='$tgt', TgGroup=".StrSafe_DB($Group)."
        on duplicate key update TgGroup=".StrSafe_DB($Group));
    }
}

$JSON['Groups']=array();
$q=safe_r_sql("SELECT TgSession, TgGroup, GROUP_CONCAT(TgTargetNo ORDER BY CAST(TgTargetNo AS SIGNED) SEPARATOR ',') as TgtList from TargetGroups
    WHERE TgTournament={$_SESSION['TourId']}
    GROUP BY TgSession, TgGroup");
while($r=safe_fetch($q)) {
    $JSON['Groups'][intval($r->TgSession)][]=array('gId'=>hash('md5', $r->TgSession.$r->TgGroup), 'gGroup'=>intval($r->TgSession), 'gName'=>$r->TgGroup, 'gTargets'=>explode(',',$r->TgtList));
}
$JSON['error'] = false;


JsonOut($JSON);



