<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('Common/Fun_Phases.inc.php');
checkACL(AclInternetPublish, AclReadWrite);
CheckTourSession(true);

if(empty($_SESSION['OnlineAuth']) or empty($_SESSION['OnlineServices']) or !($_SESSION['OnlineServices']&2)) {
    $return='Modules/UpdateWeb/UpdateWeb.php';
    $Credentials=getModuleParameter('SendToIanseo', 'Credentials', (object) array('OnlineId' => 0, 'OnlineAuth' => ''));
    if($Credentials and $Credentials->OnlineId>0) {
		require_once('Common/Lib/CommonLib.php');
		if($ErrorMessage=CheckCredentials($Credentials->OnlineId, $Credentials->OnlineAuth, $return)) {
			safe_error($ErrorMessage);
		} else {
			cd_redirect($CFG->ROOT_DIR . $return);
		}
	} else {
        cd_redirect($CFG->ROOT_DIR . 'Tournament/SetCredentials.php?return='.$return);
	}
}

// VERSION 0.2010-07-13-08.25

/************************************

CONFIGURATION SECTION

*************************************/

// Error on video! Only debug purpose
define('ERROR_REPORT', true);

// Time between 2 reloads (only web interface and if Timed is selected!
$REFRESH = 20;

// array of web listeners
$LISTENERS=array(
 	$CFG->IanseoServer.'TourGetArrows.php',
);

/******************************************

END CONFIGURATION

******************************************/

/**********************************************

GET VARIABLES from outside...

***********************************************/

// The Local DB will be updated to this time of syncro
$CurrentTime=date('Y-m-d H:i:s');

$UpdateBase       = false;
$UpdateFins       = false;
$UpdateTeam       = false;
$Timed            = false;
$TournamentId	  = $_SESSION['OnlineId'];
$EventName        = false;
$EventPhase       = false;
$TimeStamp        = false;
$UpdateTourImg    = false;
$UpdateFinImg     = false;

$AllParamGood     = true;



// Command line interface
if(isset($argv)) {
	for($ii=1; $ii<=$argc; $ii++) {
		parse_str($argv[$ii]);
		if($argv[$ii]=='UpdateBase') {
			$UpdateBase = true;
		} elseif($argv[$ii]=='UpdateFins') {
			$UpdateFins = true;
		} elseif($argv[$ii]=='UpdateTeam') {
			$UpdateTeam = true;
		} elseif($argv[$ii]=='UpdateTourImg') {
			$UpdateTourImg = true;
		} elseif($argv[$ii]=='UpdateFinImg') {
			$UpdateFinImg = true;
		} elseif($argv[$ii]=='Timed') {
			$Timed = true;
		} elseif(strstr($argv[$ii], 'TournamentId=')!=$argv[$ii] and strstr($argv[$ii], 'EventName=')!=$argv[$ii] and strstr($argv[$ii], 'TimeStamp=')!=$argv[$ii] and strstr($argv[$ii], 'EventPhase=')!=$argv[$ii]) {
			$AllParamGood = false;
		}
	}
}

// WEB URL Interface
if($_GET) {
	if(isset($_GET['Refresh'])) $REFRESH=intval($_GET['Refresh']);
	$UpdateBase       = isset($_GET['UpdateBase']);
	$UpdateFins       = isset($_GET['UpdateFins']);
	$UpdateTeam       = isset($_GET['UpdateTeam']);
	$Timed            = isset($_GET['Timed']);
	if(isset($_GET['EventName'])) $EventName  = stripslashes($_GET['EventName']);
	if(isset($_GET['EventPhase'])) $EventPhase = stripslashes($_GET['EventPhase']);
	if(isset($_GET['TimeStamp'])) $TimeStamp  = stripslashes($_GET['TimeStamp']);
	$UpdateTourImg    = isset($_GET['UpdateTourImg']);
	$UpdateFinImg    = isset($_GET['UpdateFinImg']);
}


if(IsBlocked(BIT_BLOCK_PUBBLICATION) or !$AllParamGood or (!$UpdateBase and !$UpdateFinImg and !$UpdateTourImg and !$UpdateFins and !$UpdateTeam) ) {
	if(isset($argv)) {
		echo "\n\nSyntax: " . basename(__FILE__) . " [UpdateBase] [UpdateTourImg] [UpdateFins] [UpdateTeam] [Timed | TimeStamp=n] [TournamentId=n] [EventName=n] [EventPhase=n]\n\n";
	} else {
		print_config();
	}
	die();
}

// array of Data to send
$DATA=array(
	'OnlineId' => $_SESSION['OnlineId'],
	'OnlineEventCode' => $_SESSION['OnlineEventCode'],
	'OnlineAuth' => $_SESSION['OnlineAuth'],
	'QUERIES' => array(),
	'IMG' => array(),
);

if($UpdateTourImg) {
	// Updates Tour Images!
	$q=safe_r_sql("select * from Tournament where ToId={$_SESSION['TourId']}");
	$r=safe_fetch($q);
	if($r->ToImgL) $DATA['IMG']['TOPLEFT']  = $r->ToImgL;
	if($r->ToImgR) $DATA['IMG']['TOPRIGHT'] = $r->ToImgR;
	if($r->ToImgB) $DATA['IMG']['BOTTOM'] = $r->ToImgB;
	if($_REQUEST['UpdateTourTopLURL']) $DATA['IMG']['TOPLEFTURL']  = stripslashes($_REQUEST['UpdateTourTopLURL']);
	if($_REQUEST['UpdateTourTopRURL']) $DATA['IMG']['TOPRIGHTURL']  = stripslashes($_REQUEST['UpdateTourTopRURL']);
	if($_REQUEST['UpdateTourBotURL']) $DATA['IMG']['BOTURL']  = stripslashes($_REQUEST['UpdateTourBotURL']);
}

if($UpdateFinImg) {
	// Updates Tour Pictures!
	$q=safe_r_sql("select distinct FinAthlete, EnCode from Finals inner join Entries on FinAthlete=EnId where FinTournament={$_SESSION['TourId']} and EnCode>''");
	while($r=safe_fetch($q)) {
		if(file_exists($img=$CFG->DOCUMENT_PATH.'TV/Photos/'.$_SESSION['TourCodeSafe'].'-En-'.$r->FinAthlete.'.jpg')) $DATA['IMG']['ARCHERSPIC'][$r->EnCode]=file_get_contents($img);
	}
}

// array of local updates if everything went ok
$UPDATES=array();

/*************************************
CHECK IF UPDATE BASE IS SELECTED
**************************************/
if($UpdateBase) {
	// update tables Countries, Entries, Events

	/*********************
	  select countries
	**********************/
	$query = "select `CoTournament`, `CoCode`, `CoName` from `Countries` where `CoTournament`='".addslashes($_SESSION['TourId'])."'";

	$q = safe_r_sql($query);
	while($r = safe_fetch($q)) {
		// UPDATE DESTINATION Countries
		$quer=array();
		$quer[] = "`CoTournament`='".addslashes($TournamentId)."'";
		$quer[] = "`CoCode`='".addslashes($r->CoCode)."'";
		$quer[] = "`CoName`='".addslashes($r->CoName)."'";

		$DATA['QUERIES'][]="INSERT INTO `Countries` set " . implode(', ', $quer) . " ON DUPLICATE KEY UPDATE " . implode(', ', $quer);
	}

	/*********************
	  select Entries
	**********************/
	$query = "select";
	$query.= " `EnID`,";
	$query.= " `EnCode`,";
	$query.= " `EnTournament`,";
	$query.= " `EnDivision`,";
	$query.= " `EnClass`,";
	$query.= " `CoCode` as `EnCountry`,";
	$query.= " `EnName`,";
	$query.= " `EnFirstName`,";
	$query.= " `EnSex`,";
	$query.= " `IndEvent` as `EnEvent`,";
	$query.= " `IndRank` as `EnQuRank`,";
	$query.= " `QuScore` as `EnQuScore`,";
	$query.= " `QuGold` as `EnQuGold`,";
	$query.= " `QuXnine` as `EnQuXnine` ";
	$query.= "FROM `Entries` ";
	$query.= "INNER JOIN `Qualifications` ";
	$query.= "   on `Entries`.`EnID` = `Qualifications`.`QuId` ";
	$query.= "INNER JOIN `Individuals` ";
	$query.= "   on `Entries`.`EnID` = `Individuals`.`IndId` ";
	$query.= "   and `Entries`.`EnTournament` = `Individuals`.`IndTournament` ";
	$query.= "INNER JOIN `Countries` ";
	$query.= "   on `Entries`.`EnCountry` = `Countries`.`CoId` ";
	$query.=" where `EnTournament`='".addslashes($_SESSION['TourId'])."'";
	$q = safe_r_sql($query);

	while($r = safe_fetch($q)) {
		// UPDATE DESTINATION Entries
		$quer=array();
		$quer[] = "`EnTournament`='".addslashes($TournamentId)."'";
		$quer[] = "`EnCode`='".addslashes($r->EnCode)."'";
		$quer[] = "`EnID`='".addslashes($r->EnCode)."'";
		$quer[] = "`EnEvent`='".addslashes($r->EnEvent)."'";
		$quer[] = "`EnDivision`='".addslashes($r->EnDivision)."'";
		$quer[] = "`EnClass`='".addslashes($r->EnClass)."'";
		$quer[] = "`EnCountry`='".addslashes($r->EnCountry)."'";
		$quer[] = "`EnName`='".addslashes($r->EnName)."'";
		$quer[] = "`EnFirstName`='".addslashes($r->EnFirstName)."'";
		$quer[] = "`EnSex`='".addslashes($r->EnSex)."'";
		$quer[] = "`EnQuRank`='".addslashes($r->EnQuRank)."'";
		$quer[] = "`EnQuScore`='".addslashes($r->EnQuScore)."'";
		$quer[] = "`EnQuGold`='".addslashes($r->EnQuGold)."'";
		$quer[] = "`EnQuXnine`='".addslashes($r->EnQuXnine)."'";

		$DATA['QUERIES'][] = "INSERT INTO `Entries` set " . implode(', ', $quer) . " ON DUPLICATE KEY UPDATE " . implode(', ', $quer);
	}

	/*********************
	  select Events
	**********************/
	$query = "select";
	$query.= " `EvTournament`,";
	$query.= " `EvCode`,";
	$query.= " `EvTeamEvent`,";
	$query.= " `EvEventName`,";
	$query.= " `EvFinalFirstPhase` as `EvStartPhase`,";
	$query.= " `TarArray` as `EvTarget` ";
	$query.= "FROM `Events` ";
	$query.= "LEFT JOIN `Targets` ";
	$query.= "   on `Targets`.`TarId` = `Events`.`EvFinalTargetType` ";
	$query.=" where `EvTournament`='".addslashes($_SESSION['TourId'])."'";
	$q = safe_r_sql($query);

	while($r = safe_fetch($q)) {
		// UPDATE DESTINATION Events
		$quer=array();
		$quer[] = "`EvTournament`='".addslashes($TournamentId)."'";
		$quer[] = "`EvCode`='".addslashes($r->EvCode)."'";
		$quer[] = "`EvTeamEvent`='".addslashes($r->EvTeamEvent)."'";
		$quer[] = "`EvEventName`='".addslashes($r->EvEventName)."'";
		$quer[] = "`EvStartPhase`='".addslashes($r->EvStartPhase)."'";
		$quer[] = "`EvTarget`='".addslashes($r->EvTarget)."'";

		$DATA['QUERIES'][] = "INSERT INTO `Events` set " . implode(', ', $quer) . " ON DUPLICATE KEY UPDATE " . implode(', ', $quer);
	}
}

/*************************************
CHECK IF UPDATE FINALS IS SELECTED
**************************************/
if($UpdateFins) {
	// update table Finals

	/*********************
	  select Finals
	**********************/
	$query = "select";
	$query.= " `FinEvent`,";
	$query.= " `FinMatchNo`,";
	$query.= " `FinTournament`,";
	$query.= " `FinRank`,";
	$query.= " `EnCode` as `FinAthlete`,";
	$query.= " `FinScore`,";
	$query.= " `FinSetScore`,";
	$query.= " `FinSetPoints`,";
	$query.= " `FinSetPointsByEnd`,";
	$query.= " `FinTie`,";
	$query.= " `FinArrowstring`,";
	$query.= " `FinTiebreak`,";
	$query.= " `FinArrowPosition`,";
	$query.= " `FinTiePosition`,";
	$query.= " `FinWinLose`,";
	$query.= " `FinFinalRank`,";
	$query.= " `FinDateTime`,";
	$query.= " `FinLive`,";
	$query.= " `FinVxF`, ";
	$query.= " `EvMatchMode`, ";
	$query.= " `EvMatchArrowsNo`, ";
	$query.= " `GrPhase`, ";
	$query.= " @elimination:=if(GrPhase=0, 1, pow(2, ceil(log2(GrPhase))+1)) & EvMatchArrowsNo "
		. " , if(@elimination, EvElimEnds, EvFinEnds) CalcEnds "
		. " , if(@elimination, EvElimArrows, EvFinArrows) CalcArrows "
		. " , if(@elimination, EvElimSO, EvFinSO) CalcSO ";
	$query.= "FROM `Finals` ";
	$query.= "INNER JOIN `Events` ";
	$query.= "   ON `EvCode`=`FinEvent` AND `EvTeamEvent`='0' AND `EvTournament`=`FinTournament` ";
	$query.= "INNER JOIN `Grids` ";
	$query.= "   ON `FinMatchNo`=`GrMatchNo` ";
	$query.= "LEFT JOIN `Entries` ";
	$query.= "   on `Entries`.`EnID` = `Finals`.`FinAthlete` ";
	$filter=array();

	$filter[]="`FinTournament`='".addslashes($_SESSION['TourId'])."'";
	if($EventName) $filter[]="`FinEvent`='".addslashes($EventName)."'";
	if($EventPhase) {
		$filter[]="`GrPhase`='".addslashes($EventPhase)."'";
	}
	if($Timed) {
		$filter[]="`FinDateTime` > `FinSyncro`";
	} elseif($TimeStamp) {
		$filter[]="`FinDateTime` > '".date('Y-m-d H:i:s', $TimeStamp)."'";
	}

	if($filter) {
		$query.= ' where ' . implode(' and ', $filter);
	}

	$q = safe_r_sql($query);

	while($r = safe_fetch($q)) {
		// UPDATE DESTINATION Finals
		$quer=array();
		$quer[] = "`FinEvent`='".addslashes($r->FinEvent)."'";
		$quer[] = "`FinMatchNo`='".addslashes($r->FinMatchNo)."'";
		$quer[] = "`FinTournament`='".addslashes($TournamentId)."'";
		$quer[] = "`FinRank`='".addslashes($r->FinRank)."'";
		$quer[] = "`FinAthlete`='".addslashes($r->FinAthlete??'')."'";
		$quer[] = "`FinScore`='".addslashes($r->FinScore)."'";
		$quer[] = "`FinSetScore`='".addslashes($r->FinSetScore)."'";
		$quer[] = "`FinSetPoints`='".addslashes($r->FinSetPoints)."'";
// 		$quer[] = "`FinSetPointsByEnd`='".addslashes($r->FinSetPointsByEnd)."'";
		$quer[] = "`FinTie`='".addslashes($r->FinTie)."'";
		$quer[] = "`FinArrowstring`='".addslashes($r->FinArrowstring)."'";
		$quer[] = "`FinTiebreak`='".addslashes($r->FinTiebreak)."'";
		$quer[] = "`FinTiePosition`='".addslashes($r->FinTiePosition)."'";
		$quer[] = "`FinArrowPosition`='".addslashes($r->FinArrowPosition)."'";
		$quer[] = "`FinWinLose`='".addslashes($r->FinWinLose)."'";
		$quer[] = "`FinFinalRank`='".addslashes($r->FinFinalRank)."'";
		$quer[] = "`FinDateTime`='".addslashes($r->FinDateTime)."'";
		$quer[] = "`FinLive`='".addslashes($r->FinLive)."'";
		$quer[] = "`FinMatchMode`='".addslashes($r->EvMatchMode)."'";
		$quer[] = "`FinEndNo`='".addslashes($r->CalcEnds)."'";
		$quer[] = "`FinArrNo`='".addslashes($r->CalcArrows)."'";
		$quer[] = "`FinSo`='".addslashes($r->CalcSO)."'";
		$quer[] = "`FinVxF`='".addslashes($r->FinVxF)."'";

		$DATA['QUERIES'][] = "INSERT INTO `Finals` set " . implode(', ', $quer) . " ON DUPLICATE KEY UPDATE " . implode(', ', $quer);

		// updates original data that have been transmitted
		// this should be done always to take track of what has been updated and what not!
		// WARNING! WARNING! WARNING!
		// => FinDateTime MUST BE SET TO ITSELF to preserve
		// =>   whatever time it has at the moment of the update
		$upd  = "UPDATE `Finals` set ";
		$upd .= " `FinDateTime`=`FinDateTime`,";
		$upd .= " `FinSyncro`='".addslashes($CurrentTime)."' ";
		$upd .= "WHERE";
		$upd .= " `FinEvent`='".addslashes($r->FinEvent)."'";
		$upd .= " AND `FinMatchNo`='".addslashes($r->FinMatchNo)."'";
		$upd .= " AND `FinTournament`='".addslashes($r->FinTournament)."'";

		$UPDATES[] = $upd;
	}


}

/*************************************
CHECK IF UPDATE TEAMFINALS IS SELECTED
**************************************/
if($UpdateTeam) {
	// update table TeamFinals

	/*********************
	  select TeamFinals
	**********************/
	$query = "select";
	$query.= " `TfEvent`,";
	$query.= " `TfMatchNo`,";
	$query.= " `TfTournament`,";
	$query.= " `CoCode` as `TfTeam`,";
	$query.= " `TfRank`,";
	$query.= " `TfScore`,";
	$query.= " `TfSetScore`,";
	$query.= " `TfSetPoints`,";
	$query.= " `TfSetPointsByEnd`,";
	$query.= " `TfTie`,";
	$query.= " `TfArrowstring`,";
	$query.= " `TfTiebreak`,";
	$query.= " `TfArrowPosition`,";
	$query.= " `TfTiePosition`,";
	$query.= " `TfWinLose`,";
	$query.= " `TfFinalRank`,";
	$query.= " `TfDateTime`,";
	$query.= " `TfLive`,";
	$query.= " `TfVxF`, ";
	$query.= " `EvMatchMode`, ";
	$query.= " @elimination:=if(GrPhase=0, 1, pow(2, ceil(log2(GrPhase))+1)) & EvMatchArrowsNo "
		. " , if(@elimination, EvElimEnds, EvFinEnds) CalcEnds "
		. " , if(@elimination, EvElimArrows, EvFinArrows) CalcArrows "
		. " , if(@elimination, EvElimSO, EvFinSO) CalcSO ";
	$query.= "FROM `TeamFinals` ";
	$query.= "INNER JOIN `Events` ";
	$query.= "   ON `EvCode`=`TfEvent` AND `EvTeamEvent`='1' AND `EvTournament`=`TfTournament` ";
	$query.= "INNER JOIN `Grids` ";
	$query.= "   ON `TfMatchNo`=`GrMatchNo` ";
	$query.= "LEFT JOIN `Countries` ";
	$query.= "   on `Countries`.`CoID` = `TeamFinals`.`TfTeam` ";
	$filter=array();

	$filter[]="`TfTournament`='".addslashes($_SESSION['TourId'])."'";
	if($EventName) $filter[]="`TfEvent`='".addslashes($EventName)."'";
	if($EventPhase) {
		$query.= "LEFT JOIN `Grids` ";
		$query.= "   on `TeamFinals`.`TfMatchNo` = `Grids`.`GrMatchNo` ";
		$filter[]="`GrPhase`='".addslashes($EventPhase)."'";
	}
	if($Timed) {
		$filter[]="`TfDateTime` > `TfSyncro`";
	} elseif($TimeStamp) {
		$filter[]="`TfDateTime` > '".date('Y-m-d H:i:s', $TimeStamp)."'";
	}

	if($filter) {
		$query.= ' where ' . implode(' and ', $filter);
	}

	$q = safe_r_sql($query);

	while($r = safe_fetch($q)) {
		// UPDATE DESTINATION Finals
		$quer=array();
		$quer[] = "`TfEvent`='".addslashes($r->TfEvent)."'";
		$quer[] = "`TfMatchNo`='".addslashes($r->TfMatchNo)."'";
		$quer[] = "`TfTournament`='".addslashes($TournamentId)."'";
		$quer[] = "`TfTeam`='".addslashes($r->TfTeam??'')."'";
		$quer[] = "`TfRank`='".addslashes($r->TfRank)."'";
		$quer[] = "`TfScore`='".addslashes($r->TfScore)."'";
		$quer[] = "`TfSetScore`='".addslashes($r->TfSetScore)."'";
		$quer[] = "`TfSetPoints`='".addslashes($r->TfSetPoints)."'";
// 		$quer[] = "`TfSetPointsByEnd`='".addslashes($r->TfSetPointsByEnd)."'";
		$quer[] = "`TfTie`='".addslashes($r->TfTie)."'";
		$quer[] = "`TfArrowstring`='".addslashes($r->TfArrowstring)."'";
		$quer[] = "`TfTiebreak`='".addslashes($r->TfTiebreak)."'";
		$quer[] = "`TfArrowPosition`='".addslashes($r->TfArrowPosition)."'";
		$quer[] = "`TfTiePosition`='".addslashes($r->TfTiePosition)."'";
		$quer[] = "`TfWinLose`='".addslashes($r->TfWinLose)."'";
		$quer[] = "`TfFinalRank`='".addslashes($r->TfFinalRank)."'";
		$quer[] = "`TfDateTime`='".addslashes($r->TfDateTime)."'";
		$quer[] = "`TfLive`='".addslashes($r->TfLive)."'";
		$quer[] = "`TfMatchMode`='".addslashes($r->EvMatchMode)."'";
		$quer[] = "`TfEndNo`='".addslashes($r->CalcEnds)."'";
		$quer[] = "`TfArrNo`='".addslashes($r->CalcArrows)."'";
		$quer[] = "`TfSo`='".addslashes($r->CalcSO)."'";
		$quer[] = "`TfVxF` ='".addslashes($r->TfVxF)."'";

		$DATA['QUERIES'][] = "INSERT INTO `TeamFinals` set " . implode(', ', $quer) . " ON DUPLICATE KEY UPDATE " . implode(', ', $quer);

		// updates original data that have been transmitted
		// this should be done always to take track of what has been updated and what not!
		// WARNING! WARNING! WARNING!
		// => TfDateTime MUST BE SET TO ITSELF to preserve
		// =>   whatever time it has at the moment of the update
		$upd  = "UPDATE `TeamFinals` set ";
		$upd .= " `TfDateTime`=`TfDateTime`,";
		$upd .= " `TfSyncro`='".addslashes($CurrentTime)."' ";
		$upd .= "WHERE";
		$upd .= " `TfEvent`='".addslashes($r->TfEvent)."'";
		$upd .= " AND `TfMatchNo`='".addslashes($r->TfMatchNo)."'";
		$upd .= " AND `TfTournament`='".addslashes($r->TfTournament)."'";

		$UPDATES[] = $upd;
	}
}

// send to all the listeners
if($DATA['QUERIES'] or $DATA['IMG']) {
	$toSend=urlencode(gzdeflate(serialize($DATA), 9));
	foreach($LISTENERS as $LISTENER) {
		$ch=curl_init($LISTENER);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "arrows=$toSend");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($varResponse=curl_exec($ch)) {
			if($varResponse=='OK') {
				// do the updates
				foreach($UPDATES as $Update) safe_w_sql($Update);
			} else {
				die("Server response: " . $varResponse);
			}
		} else {
			die("Server not responding");
		}
		curl_close($ch);
	}
}
/*

base

1) uno script che aggiorna le tabelle di inizializzazione (Countries, Entries, Events)
2) uno script che aggiorna la tabella Finals con 3 diversi parametri:
    -   nessuno: aggiorna tutto
    -  TournamentId e Fase: Aggiorna tutti i match di quella fase
    - TournamentId, Fase e Timestamp: aggiorna i match di quella fase successivi a timestamp
3) come 2) ma per TeamFinals

*/

if(isset($_GET) and isset($_SERVER) /*and $Timed and ($UpdateTeam or $UpdateFins)*/) {
	// so we are pretty sure we are on a web URL CALL
	// BUT it only makes sense if we are on a "timed" sessione
	// that is, if we have to update Finals and TeamFinals every X seconds!
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<?php
if($REFRESH) {
	$url = $_SERVER['PHP_SELF'] . '?';
	$gets=array();
	$gets[]='Refresh='.$REFRESH;
	if($Timed) $gets[]='Timed=1';
		elseif($TimeStamp) $gets[]='TimeStamp='.stripslashes($TimeStamp);
	if($EventName) $gets[]='EventName='.stripslashes($EventName);
	if($EventPhase) $gets[]='EventPhase='.stripslashes($EventPhase);

	// stores syncro data when stopping execution!
	$STOP=implode('&', $gets);

	if($UpdateBase) $gets[]='UpdateBase=1';
	if($UpdateFins) $gets[]='UpdateFins=1';
	if($UpdateTeam) $gets[]='UpdateTeam=1';

	echo '<meta http-equiv="refresh" content="' . $REFRESH . ';url=' . $url . implode('&', $gets) . '" />';
}
?>
<title>Sync</title>
    <style type="text/css">
        table, th, td {
            border: 1px solid black;
            border-spacing: 0;
            margin-left: auto;
            margin-right: auto;
        }
        th, td {
            padding: 5px;
            background-color: unset;
        }
    </style>
</head>
<body>
<h1 style="text-align: center; font-weight: bold; font-size: xxx-large; color: darkred;">Syncro Data</h1>
<?php
echo '<table>';



echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: center; background-color: darkred; font-size: x-large; font-weight: bold;" colspan="2"><a style="color: white; text-decoration: none;" href="'.$_SERVER['PHP_SELF'].($STOP?"?$STOP":'').'">STOP UPDATING!</a></td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Last Update</td>';
echo '<td>' . $CurrentTime . '</td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Refresh time</td>';
echo '<td>' . $REFRESH . '</td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Update Base Info</td>';
echo '<td>' . ($UpdateBase?'YES':'no') . '</td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Update Individual Finals Scores</td>';
echo '<td>' . ($UpdateFins?'YES':'no') . '</td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Update Team Finals Scores</td>';
echo '<td>' . ($UpdateTeam?'YES':'no') . '</td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Update changes since last update</td>';
echo '<td>' . ($Timed?'YES':'no') . '</td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Update changes since</td>';
echo '<td>' . ($TimeStamp?$TimeStamp:'-') . '</td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Tournament Id</td>';
echo '<td>' . $_SESSION['TourId'] . '</td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Remote Tournament Code</td>';
echo '<td>' . ($TournamentId?$TournamentId:'ALL') . '</td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Event Name</td>';
echo '<td>' . ($EventName?$EventName:'ALL') . '</td>';
echo '</tr>';

echo '<tr style="vertical-align: top;">';
echo '<td style="text-align: right; font-weight: bold;">Event Phase</td>';
echo '<td>' . ($EventPhase?$EventPhase:'ALL') . '</td>';
echo '</tr>';

echo '</table>';
?>
</body>
</html>
    <?php

} else {
	print "Done<br>";
	print '<a href="' . $_SERVER["PHP_SELF"] . '?TournamentId=' . $TournamentId . '">Back</a>';
}


function print_config() {
	global $ORIGIN, $EventPhase, $TournamentId, $TimeStamp, $Timed, $REFRESH, $EventName;

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	echo '<html xmlns="http://www.w3.org/1999/xhtml">';
	echo '<head>';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	echo '<meta http-equiv="Cache-Control" content="no-cache" />';
	echo '<meta http-equiv="Pragma" content="no-cache" />';
	echo '<meta http-equiv="Expires" content="-1" />';
	echo '<title>Sync</title>';
    echo '<style type="text/css">
        table, th, td {
            border: 1px solid black;
            border-spacing: 0;
            margin-left: auto;
            margin-right: auto;
        }
        th, td {
            padding: 5px;
            background-color: unset;
        }
        </style>';
	echo '</head>';
	echo '<body>';
	echo '<h1 style="text-align: center; font-weight: bold; font-size: xxx-large; color: darkblue;">Configuration</h1>';
?>

<form action="" method="get">
<table>
<tr style="vertical-align: top;">
	<td><b>Tournament</b></td>
	<td colspan="2"><?php echo $TournamentId ?> - <?php echo $_SESSION['TourName']; ?></td>
</tr>
<tr style="vertical-align: top;">
	<td nowrap="nowrap"><b>Reload Time</b></td>
	<td colspan="2"><input type="text" name="Refresh" value="<?php echo $REFRESH ?>" size="5"/> (AutoUpdate)</td>
</tr>
<tr style="vertical-align: top;">
	<td nowrap="nowrap"><b>Update Base info</b></td>
	<td colspan="2"><input type="checkbox" name="UpdateBase"/></td>
</tr>
<tr style="vertical-align: top;">
	<td nowrap="nowrap" rowspan="3"><b>Update Tournament </b></td>
	<td rowspan="3"><input type="checkbox" name="UpdateTourImg"/></td>
	<td><input type="text" name="UpdateTourTopLURL"/>TopLeft URL</td>
</tr>
<tr style="vertical-align: top;">
	<td><input type="text" name="UpdateTourTopRURL"/>TopRight URL</td>
</tr>
<tr style="vertical-align: top;">
	<td><input type="text" name="UpdateTourBotURL"/>Bottom URL</td>
</tr>
<tr style="vertical-align: top;">
	<td nowrap="nowrap"><b>Update Final pictures</b></td>
	<td colspan="2"><input type="checkbox" name="UpdateFinImg"/></td>
</tr>
<tr style="vertical-align: top;">
	<td nowrap="nowrap"><b>Update Finals info</b></td>
	<td><input type="checkbox" name="UpdateFins"/></td>
	<td rowspan="2"><table cellpadding="2" cellspacing="0" border="0">
		<tr style="vertical-align: top;">
			<td nowrap="nowrap"><b>Update changes since last update</b></td>
			<td><input type="checkbox" name="Timed"<?php echo ($Timed?' checked':'') ?>/></td>
		</tr>
		<tr style="vertical-align: top;">
			<td nowrap="nowrap"><b>Update changes since</b> (Unix Timestamp)</td>
			<td><input type="text" name="TimeStamp" value="<?php echo $TimeStamp ?>"/></td>
		</tr>
		<tr style="vertical-align: top;">
			<td nowrap="nowrap"><b>Update Event</b></td>
			<td><input type="text" name="EventName" value="<?php echo $EventName ?>"/></td>
		</tr>
		<tr style="vertical-align: top;">
			<td nowrap="nowrap"><b>Update Phase</b></td>
			<td><input type="text" name="EventPhase" value="<?php echo $EventPhase ?>"/></td>
		</tr>
	</table></td>
</tr>
<tr style="vertical-align: top;">
	<td nowrap="nowrap"><b>Update TeamFinals info</b></td>
	<td><input type="checkbox" name="UpdateTeam"/></td>
</tr>
<tr style="vertical-align: top;" align="center">
	<td colspan="3"><input type="submit" value="Start Syncro"/></td>
</tr>
</table>
</form>
<?php
	echo '</body>';
	echo '</html>';
}
?>