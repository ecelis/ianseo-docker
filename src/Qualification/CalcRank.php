<?php
/*
															- CalcRank.php -
	Calcola la rank (anche l'abs).
	Se riceve Dist=1,2,.... calcola la rank sulla distanza Dist; se non lo riceve, calcola la rank sul totale
*/

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Fun_Qualification.local.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Lib/Obj_RankFactory.php');

$JSON=[
    'error'=>1,
    'msg'=>get_text('CalcRankError','Tournament'),
];

if(!hasACL(AclQualification, AclReadWrite) or !CheckTourSession() or IsBlocked(BIT_BLOCK_QUAL)) {
    JsonOut($JSON);
}

$Dist=intval($_REQUEST['Dist'] ?? 0);
$JSON['error']=CalcRank($Dist);
if($JSON['error']==0) {
    $JSON['msg']=get_text('CalcRankOk','Tournament');
}

JsonOut($JSON);