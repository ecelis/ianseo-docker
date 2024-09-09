<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite);

$JSON=array(
    'error'=>1,
);

$Sql = "SELECT DISTINCT EnClass, EnDivision, ClDescription, DivDescription, (ClIsPara * DivIsPara) as isPara
    FROM Entries
    INNER JOIN Classes ON EnClass=ClId AND ClTournament=EnTournament
    INNER JOIN Divisions ON EnDivision=DivId AND DivTournament=EnTournament
    LEFT JOIN EventClass ON EnClass=EcClass AND EnDivision=EcDivision AND EcTournament=EnTournament AND EcTeamEvent=0
    WHERE EnTournament=".StrSafe_DB($_SESSION['TourId'])." AND EcCode IS NULL
    ORDER BY DivViewOrder, ClViewOrder";
$q = safe_r_SQL($Sql);

if(isset($_REQUEST["checkEvents"])) {
    $JSON["error"]=0;
    $JSON["Add"]=safe_num_rows($q);
    $JSON["Del"]=0;
    $JSON["DelList"]='';
    $Sql = "SELECT Count(distinct EcCode) as DelNo, GROUP_CONCAT(EcCode SEPARATOR ', ') as DelList
        FROM EventClass
        LEFT JOIN Entries ON EnClass=EcClass AND EnDivision=EcDivision AND EcTournament=EnTournament and EnIndFEvent=1
        WHERE EcTournament=".StrSafe_DB($_SESSION['TourId'])." AND EcTeamEvent=0 AND EnId IS NULL";
    $q = safe_r_SQL($Sql);
    if($r=safe_fetch($q)) {
        $JSON["Del"] = intval($r->DelNo);
        $JSON["DelList"] = $r->DelList;
    }
}

if(isset($_REQUEST["addEvents"]) AND intval($_REQUEST["addEvents"]) == safe_num_rows($q)) {
    $cnt = 1;
    $q2 = safe_r_SQL("SELECT IFNULL(MAX(EvProgr)+1,1) as evNext FROM `Events` WHERE `EvTeamEvent` = 0 AND `EvTournament` = ".StrSafe_DB($_SESSION['TourId']));
    if($r = safe_fetch($q2)) {
        $cnt = $r->evNext;
    }
    include_once('Modules/Sets/lib.php');
    $Options = array(
        'EvFinalFirstPhase' => 0,
        'EvNumQualified' => 0,
        'EvFinalTargetType' => TGT_OUT_FULL,
        'EvTargetSize' => 0,
        'EvDistance' => 0,
        'EvMedals' => 1,
        'EvIsPara' => 0,
    );
    while ($r = safe_fetch($q)) {
        $Options["EvIsPara"] = ($r->isPara == 0 ? 0 : 1);
        CreateEventNew($_SESSION['TourId'], trim($r->EnDivision) . trim($r->EnClass), $r->DivDescription . ' ' . $r->ClDescription, $cnt++, $Options);
        InsertClassEvent($_SESSION['TourId'], 0, 1, trim($r->EnDivision) . trim($r->EnClass), $r->EnDivision, $r->EnClass);
    }
    require_once('Qualification/Fun_Qualification.local.inc.php');
    MakeIndAbs();
    $JSON["error"]=0;
}

if(isset($_REQUEST["delEvents"])) {
    $Sql = "SELECT EcCode
        FROM EventClass
        LEFT JOIN Entries ON EnClass=EcClass AND EnDivision=EcDivision AND EcTournament=EnTournament
        WHERE EcTournament=".StrSafe_DB($_SESSION['TourId'])." AND EcTeamEvent=0 AND EnId IS NULL";
    $q=safe_r_SQL($Sql);
    if(intval($_REQUEST["delEvents"]) == safe_num_rows($q)) {
        require_once('Qualification/Fun_Qualification.local.inc.php');
        while ($r = safe_fetch($q)) {
            deleteEvent($r->EcCode, 0);
        }
    }
    $JSON["error"]=0;
}

JsonOut($JSON);