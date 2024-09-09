<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
$JSON=array('error' => 1);
if (!CheckTourSession()) {
    JsonOut($JSON);
}
checkACL(AclCompetition, AclReadWrite, false);

if (IsBlocked(BIT_BLOCK_TOURDATA)) {
    JsonOut($JSON);
}
if (isset($_REQUEST['EvCode']) and isset($_REQUEST['EvMulti'])) {
    $Update = "UPDATE Events SET EvMultiTeam=" . StrSafe_DB($_REQUEST['EvMulti']) . ", EvMultiTeamNo=" . StrSafe_DB($_REQUEST['NumMulti']??0) .
        " WHERE EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']);
    $Rs=safe_w_sql($Update);
    if(safe_w_affected_rows()) {
        MakeTeamsAbs(null,null,null);
    }
    $JSON['error']=0;
}
JsonOut($JSON);
