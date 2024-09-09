<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
require_once('Common/Fun_Various.inc.php');

$JSON=array('error' => 1);
if (!CheckTourSession()) {
    JsonOut($JSON);
}
checkACL(AclCompetition, AclReadWrite, false);

if (IsBlocked(BIT_BLOCK_TOURDATA)) {
    JsonOut($JSON);
}

if (isset($_REQUEST['EvCode']) and isset($_REQUEST['DelGroup'])) {
    $Delete = "";
    if(isset($_REQUEST['Cl']) OR isset($_REQUEST['Div']) OR isset($_REQUEST['SubCl']) OR isset($_REQUEST['AddOns'])) {
        $Delete = "DELETE FROM EventClass WHERE EcCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EcTeamEvent=" . StrSafe_DB($_REQUEST['DelGroup']) . " AND EcTournament=" . StrSafe_DB($_SESSION['TourId']).
            " AND EcClass=" . StrSafe_DB($_REQUEST['Cl']??'') . " AND EcDivision=" . StrSafe_DB($_REQUEST['Div']??'') . " AND EcSubClass=" . StrSafe_DB($_REQUEST['SubCl']??'') . " AND EcExtraAddons=" . intval($_REQUEST['AddOns']??0);
    } else {
        $Delete = "DELETE FROM EventClass WHERE EcCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EcTeamEvent=" . StrSafe_DB($_REQUEST['DelGroup']) . " AND EcTournament=" . StrSafe_DB($_SESSION['TourId']);
    }
    $Rs=safe_w_sql($Delete);
    if(safe_w_affected_rows()) {
        //Calculate MAX no of team component
        calcMaxTeamPerson(array($_REQUEST['EvCode']));

        // Delete Teams row for the event
        $queries[] = "DELETE FROM Teams WHERE TeTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TeFinEvent=1 AND TeEvent=" . StrSafe_DB($_REQUEST['EvCode']);
        // Delete Names
        $queries[] = "DELETE FROM TeamComponent WHERE TcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TcFinEvent=1 AND TcEvent=". StrSafe_DB($_REQUEST['EvCode']);
        // Delete Final Names
        $queries[] = "DELETE FROM TeamFinComponent WHERE TfcTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND TfcEvent=" .  StrSafe_DB($_REQUEST['EvCode']);
        // Clear Grids
        $queries[] = "UPDATE TeamFinals SET TfTeam=0, TfSubTeam=0, TfScore=0, TfSetScore=0, TfSetPoints='', TfSetPointsByEnd='', TfWinnerSet=0, TfTie=0, 
                  TfArrowstring='', TfTiebreak='', TfArrowPosition='', TfTiePosition='', TfWinLose=0, 
                  TfDateTime=NOW(), TfLive=0, TfStatus=0, TfShootFirst=0, TfShootingArchers='', TfConfirmed=0, TfNotes='' 
                  WHERE TfEvent=" . StrSafe_DB($_REQUEST['EvCode']) . " AND TfTournament=" . StrSafe_DB($_SESSION['TourId']);
        foreach ($queries as $q) {
            safe_w_sql($q);
        }
        safe_w_sql("UPDATE Events SET EvTourRules='' where EvCode=" . StrSafe_DB($_REQUEST['EvCode']) . " AND EvTeamEvent='1' AND EvTournament = " . StrSafe_DB($_SESSION['TourId']));
        // reset shootoff
        ResetShootoff($_REQUEST['EvCode'],1,0);
        //Rebuild Teams
        MakeTeamsAbs(null,null,null);
        $JSON['error']=0;
    }
}
JsonOut($JSON);
