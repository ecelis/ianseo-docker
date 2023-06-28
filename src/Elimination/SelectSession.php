<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');

$JSON=array('error'=>1, 'min'=>'', 'max'=>'','coalesce'=>false);
if (!CheckTourSession() or !hasACL(AclEliminations, AclReadOnly)) {
    JsonOut($JSON);
}

	$Errore=0;
	$First='';
	$Last='';

if (isset($_REQUEST['Ses'])) {
    $Select
        = "SELECT SUBSTRING(MIN(ElTargetNo),1," . TargetNoPadding . ") AS Minimo, SUBSTRING(MAX(ElTargetNo),1," . TargetNoPadding . ") AS Massimo "
        . "FROM Eliminations INNER JOIN Entries ON ElId=EnId AND EnTournament=" . StrSafe_DB($_SESSION['TourId']) . " "
        . "WHERE ElElimPhase = " . intval($_REQUEST['Ses']) . " AND ElTargetNo!='' ";
    $Rs=safe_r_sql($Select);
    if (safe_num_rows($Rs)==1) {
        $MyRow=safe_fetch($Rs);
        $JSON['min']=$MyRow->Minimo;
        $JSON['max']=$MyRow->Massimo;
        $JSON['error']=0;
    }

}

JsonOut($JSON);