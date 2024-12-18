<?php
/*
													- UpdateFieldEventList.php -
	Aggiorna il campo di Events passato in querystring.
*/

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
if (!CheckTourSession()) {
	print get_text('CrackError');
	exit;
}
checkACL(AclCompetition, AclReadWrite, false);

$JSON=array('error' => 1, 'which' => '#');

if(IsBlocked(BIT_BLOCK_TOURDATA) or empty($_REQUEST['field']) or !isset($_REQUEST['value'])) {
	JsonOut($JSON);
}

$Key=$_REQUEST['field'];
$Value=$_REQUEST['value'];

if (substr($Key,0,2)=='d_') {
	$JSON['which'] = $Key;
	$cc = '';
	$ee = '';
	list (,$cc,$ee)=explode('_',$Key, 3);

	if(!preg_match('/^[0-9a-z]+$/i', $cc)) {
		JsonOut($JSON);
	}

	$Update
		= "UPDATE Events SET "
		. $cc . "=" . StrSafe_DB($Value) . " "
		. "WHERE EvCode=" . StrSafe_DB($ee) . " AND EvTeamEvent='0' AND EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " ";
	$RsUp=safe_w_sql($Update);

	if ($cc == 'EvMatchMode' || $cc == 'EvFinalTargetType') {
		if(safe_w_affected_rows()!=0) {
			safe_w_sql("UPDATE Events SET EvTourRules='' where EvCode=" . StrSafe_DB($ee) . " AND EvTeamEvent='0' AND EvTournament = " . StrSafe_DB($_SESSION['TourId']));

			// check secondary competitions and in case delete them if phase is not coherent
		}
	}

	$JSON['error']=0;
}

JsonOut($JSON);
