<?php
require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
CheckTourSession(false);
checkACL(AclParticipants, AclReadWrite, false);

$JSON=array('error' => 1, 'keys'=>array(), 'value'=>0);

if(empty($_REQUEST['fld']) or !isset($_REQUEST['value']) or IsBlocked(BIT_BLOCK_PARTICIPANT)) {
    JsonOut($JSON);
}

const tblDecode = array ('e' => array('Entries','EnId'), 'c' => array('Countries','CoId'));
$flds = array($_REQUEST['fld']);
if(is_array($_REQUEST['fld'])) {
    $flds = $_REQUEST['fld'];
}
$value = '';
$whatRecalc=array('iq'=>array(),'tq'=>array(),'if'=>array(),'tf'=>array());

foreach($flds as $fld) {
    list($mode,$tbl,$fldName,$key) = explode('_',$fld);
    $validData = true;
    if ($mode === 'd') {
        switch ($fldName) {
            case 'EnIndClEvent':
            case 'EnTeamClEvent':
            case 'EnIndFEvent':
            case 'EnTeamFEvent':
            case 'EnTeamMixEvent':
                if (preg_match('/^(0|1)$/',$_REQUEST['value'])) {
                    $value=intval($_REQUEST['value']);
                    $validData = true;
                }
                break;
            case 'EnDoubleSpace':
            case 'EnWChair':
                if (preg_match('/^(0|1)$/',$_REQUEST['value'])) {
                    $value=intval($_REQUEST['value']);
                    $validData = true;
                }
                break;
        }

        $Update = "UPDATE " . tblDecode[$tbl][0]  . " SET {$fldName}=" . StrSafe_DB($value) . " WHERE " . tblDecode[$tbl][1] . "=" . StrSafe_DB($key);
        $RsUp=safe_w_sql($Update);
        if(safe_w_affected_rows()) {
            switch ($fldName) {
                case 'EnIndClEvent':
                    $whatRecalc['iq'][] = $key;
                    break;
                case 'EnTeamClEvent':
                    $whatRecalc['tq'][] = $key;
                    break;
                case 'EnIndFEvent':
                    $whatRecalc['if'][] = $key;
                    break;
                case 'EnTeamFEvent':
                case 'EnTeamMixEvent':
                    $whatRecalc['tf'][] = $key;
                    break;
            }
            $JSON['error']=0;
        }
    }
}
if(count($whatRecalc['iq'])!==0) {
    recalculateIndividualClassDiv($whatRecalc['iq']);
}
if(count($whatRecalc['if'])!==0) {
    recalculateIndividualFinals($whatRecalc['if']);
}
if(count($whatRecalc['tq'])!==0) {
    recalculateTeamClassDiv($whatRecalc['tq']);
}
if(count($whatRecalc['tf'])!==0) {
    recalculateTeamFinals($whatRecalc['tf']);
}

$JSON['keys'] = $flds;
$JSON['value'] = $value;
JsonOut($JSON);