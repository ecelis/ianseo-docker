<?php
/*
															- MakeSnapshot.php -
	Genera lo snapshot
*/

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');

$JSON=[
    'error'=>1,
    'msg'=>get_text('MakeSnapshotError','Tournament'),
];

if(!hasACL(AclQualification, AclReadWrite) or IsBlocked(BIT_BLOCK_QUAL)) {
    JsonOut($JSON);
};


$Session = (isset($_REQUEST["Session"]) ? intval($_REQUEST["Session"]) : 0);
$Distance = (isset($_REQUEST["Distance"]) ? intval($_REQUEST["Distance"]) : 0);
$FromTarget = ($_REQUEST["fromTarget"] ? intval($_REQUEST["fromTarget"]) : 0);
$ToTarget = ($_REQUEST["toTarget"] ? intval($_REQUEST["toTarget"]) : 0);

if (!($Session and $Distance and $FromTarget and $ToTarget)) {
    JsonOut($JSON);
}

$JSON['error']=0;
$num=array();
if(isset($_REQUEST["numArrows"]) && preg_match("/^[0-9]{1,2}$/",$_REQUEST["numArrows"])) {
    if($_REQUEST["numArrows"]==0) {
        // get num of ends for that session and distance
        $obj=getArrowEnds($Session, $Distance);
        for($i=$obj[$Distance]['arrows']; $i<=$obj[$Distance]['arrows']*$obj[$Distance]['ends']; $i+=$obj[$Distance]['arrows'] ) {
            $tmp=useArrowsSnapshot($Session, $Distance, $FromTarget, $ToTarget,$i);
            $num[]=$tmp;
        }
    } else {
        $tmp=useArrowsSnapshot($Session, $Distance, $FromTarget, $ToTarget, $_REQUEST["numArrows"]);
        $num[]=$tmp;
    }
} else {
    $num[]='current';
}
$JSON['msg']=get_text('SnapshotRecalculated', 'Tournament', implode(', ', $num));

JsonOut($JSON);
