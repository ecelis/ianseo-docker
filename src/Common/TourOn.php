<?php
/*
													- TourOn.php -
	Imposta la sessione per il torneo selezionato.
	Se ci sono problemi, distrugge la sessione e (di default) rimanda all'index principale.
*/

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/UpdatePreOpen.inc.php');
require_once('Common/CheckPictures.php');

$BackTo = $CFG->ROOT_DIR . 'Main.php';	// pagina a cui ritornare in caso di successo

if (!(isset($_REQUEST['ToId']) AND is_numeric($_REQUEST['ToId'])>0)) {
	print get_text('CrackError');
	exit;
} else {
	if (isset($_REQUEST['BackTo'])) {
		$BackTo=$_REQUEST['BackTo'];
	}
}

if($CFG->USERAUTH AND !empty($_SESSION['AUTH_ENABLE']) AND empty($_SESSION['AUTH_ROOT'])) {
    $AuthFiler = array();
    if($CFG->USERAUTH AND !empty($_SESSION['AUTH_ENABLE']) AND empty($_SESSION['AUTH_ROOT'])) {
        $compList = array();
        foreach ($_SESSION["AUTH_COMP"] as $comp) {
            if(preg_match('/%/',$comp)) {
                $AuthFiler[] = 'ToCode LIKE ' . StrSafe_DB($comp);
            } else {
                $compList[] = $comp;
            }
        }
        if(count($compList)) {
            $AuthFiler[] = 'FIND_IN_SET(ToCode, \'' . implode(',', $compList) . '\') != 0 ';
        }
    }
    $q = safe_r_SQL("SELECT ToId FROM Tournament WHERE ToId=" .$_REQUEST['ToId'] . ' AND (' . (count($AuthFiler) ? implode(' OR ', $AuthFiler) : 'FALSE'). ')');
    if(safe_num_rows($q)!=1){
        CD_redirect($CFG->ROOT_DIR);
        exit;
    }
}

UpdatePreOpen($_REQUEST['ToId']);

if ($Tour=CreateTourSession($_REQUEST['ToId'])) {
	header('Location: ' . $BackTo);
	exit;
} else {
	EraseTourSession();
	header('Location: '.$CFG->ROOT_DIR.'index.php');
	exit;
}

