<?php
/*
													- DeleteEventRule.php -
	Elimina una coppia DivClass da EventClass
*/

require_once(dirname(__DIR__) . '/config.php');

$JSON=array('error' => 1, 'msg' => 'Error');

if(!CheckTourSession() or !hasACL(AclCompetition, AclNoAccess) or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

if(IsBlocked(BIT_BLOCK_TOURDATA)) {
	$JSN['msg']=get_text('LockedProcedure', 'Errors');
	JsonOut($JSON);
}

$TEAM=intval($_REQUEST['team']??0);
$EVENT=$_REQUEST['event'??''];

require_once('Common/Fun_Sessions.inc.php');
require_once('Qualification/Fun_Qualification.local.inc.php');
require_once('Common/Fun_Various.inc.php');

switch($_REQUEST['act']) {
	case 'deleteEventClass':
	case 'deleteEventClassGroup':
		if($_REQUEST['act']=='deleteEventClassGroup') {
			$Rs=safe_w_sql("DELETE FROM EventClass 
				WHERE EcCode=" . StrSafe_DB($EVENT) . " 
					AND EcTeamEvent=$TEAM 
					AND EcTournament={$_SESSION['TourId']}");
		} else {
			$Div=$_REQUEST['div']??'';
			$Cl=$_REQUEST['cl']??'';
			$Sc=$_REQUEST['sc']??'';
			$Rs=safe_w_sql("DELETE FROM EventClass 
				WHERE EcCode=" . StrSafe_DB($EVENT) . " 
					AND EcTeamEvent=$TEAM 
					AND EcTournament={$_SESSION['TourId']}
					AND EcDivision=" . StrSafe_DB($Div) . " 
					AND EcClass=" . StrSafe_DB($Cl) . " 
					AND EcSubClass=" . StrSafe_DB($Sc));
		}

		if(safe_w_affected_rows()) {
			safe_w_sql("UPDATE Events SET EvTourRules='' where EvCode=" . StrSafe_DB($EVENT) . " AND EvTeamEvent=".($TEAM ? 1 : 0)." AND EvTournament = {$_SESSION['TourId']}");

			// SO Reset
			ResetShootoff($EVENT,$TEAM ? 1 : 0,0);
			if($TEAM) {
				$queries=[];

				// recalculate max team people
				calcMaxTeamPerson(array($EVENT));

				// rebuild teams and team components
				$queries[] = "DELETE FROM Teams WHERE TeTournament={$_SESSION['TourId']} AND TeFinEvent=1 AND TeEvent=" . StrSafe_DB($EVENT) . " ";
				$queries[] = "DELETE FROM TeamComponent WHERE TcTournament={$_SESSION['TourId']} AND TcFinEvent=1 AND TcEvent=". StrSafe_DB($EVENT) . " ";
				$queries[] = "DELETE FROM TeamFinComponent WHERE TfcTournament={$_SESSION['TourId']} AND TfcEvent=" .  StrSafe_DB($EVENT) . " ";

				// elimino le griglie
				$queries[] = "UPDATE TeamFinals SET TfTeam=0, TfSubTeam=0, TfScore=0, TfSetScore=0, TfSetPoints='', TfSetPointsByEnd='', TfWinnerSet=0, TfTie=0, 
					TfArrowstring='', TfTiebreak='', TfArrowPosition='', TfTiePosition='', TfWinLose=0, 
					TfDateTime=NOW(), TfLive=0, TfStatus=0, TfShootFirst=0, TfShootingArchers='', TfConfirmed=0, TfNotes='' 
					WHERE TfEvent=" . StrSafe_DB($EVENT) . " AND TfTournament={$_SESSION['TourId']} ";

				foreach ($queries as $q) {
					safe_w_sql($q);
				}

				// teamabs
				MakeTeamsAbs();
			} else {
				MakeIndAbs();
			}
		}

		$JSON['error']=0;
		break;
	case 'addEventRule':
		$Div=$_REQUEST['div']??[];
		$Cl=$_REQUEST['cl']??[];
		$Sc=$_REQUEST['sc']??[''];
		$Num=intval($_REQUEST['num']??0);
		$Group=0;
		if($TEAM) {
			$q=safe_r_sql("SELECT coalesce(MAX(EcTeamEvent),0)+1 AS NewGroup 
				FROM EventClass 
				WHERE EcTournament={$_SESSION['TourId']} AND EcCode=" . StrSafe_DB($EVENT));
			$r=safe_fetch($q);
			$Group=$r->NewGroup;
		}
		$SQL=[];

		foreach($Div as $d) {
			foreach($Cl as $c) {
				foreach($Sc as $sc) {
					$SQL[] = "(" . StrSafe_DB($EVENT) . ", $Group, {$_SESSION['TourId']}, " . StrSafe_DB($c) . ", " . StrSafe_DB($d) . "," . StrSafe_DB($sc) . ", $Num)";
				}
			}
		}

		safe_w_sql("insert into EventClass (EcCode,EcTeamEvent,EcTournament,EcClass,EcDivision,EcSubClass,EcNumber) VALUES ".implode(',', $SQL));

		if(safe_w_affected_rows()) {
			// SO Reset
			ResetShootoff($EVENT,$TEAM ? 1 : 0,0);
			if($TEAM) {
				$queries=[];

				// recalculate max team people
				calcMaxTeamPerson(array($EVENT));

				// rebuild teams and team components
				$queries[] = "DELETE FROM Teams WHERE TeTournament={$_SESSION['TourId']} AND TeFinEvent=1 AND TeEvent=" . StrSafe_DB($EVENT) . " ";
				$queries[] = "DELETE FROM TeamComponent WHERE TcTournament={$_SESSION['TourId']} AND TcFinEvent=1 AND TcEvent=". StrSafe_DB($EVENT) . " ";
				$queries[] = "DELETE FROM TeamFinComponent WHERE TfcTournament={$_SESSION['TourId']} AND TfcEvent=" .  StrSafe_DB($EVENT) . " ";

				// elimino le griglie
				$queries[] = "UPDATE TeamFinals SET TfTeam=0, TfSubTeam=0, TfScore=0, TfSetScore=0, TfSetPoints='', TfSetPointsByEnd='', TfWinnerSet=0, TfTie=0, 
					TfArrowstring='', TfTiebreak='', TfArrowPosition='', TfTiePosition='', TfWinLose=0, 
					TfDateTime=NOW(), TfLive=0, TfStatus=0, TfShootFirst=0, TfShootingArchers='', TfConfirmed=0, TfNotes='' 
					WHERE TfEvent=" . StrSafe_DB($EVENT) . " AND TfTournament={$_SESSION['TourId']} ";

				foreach ($queries as $q) {
					safe_w_sql($q);
				}

				// teamabs
				MakeTeamsAbs();
			} else {
				MakeIndAbs();
			}
		}

		$JSON['rows']=[];
		$Select = "SELECT * FROM EventClass
		    WHERE EcCode=" . StrSafe_DB($EVENT) . " AND EcTeamEvent=$Group AND EcTournament={$_SESSION['TourId']} 
		    ORDER BY EcDivision, EcClass, EcSubClass ";
		$Rs=safe_r_sql($Select);
		while($r=safe_fetch_assoc($Rs)) {
			$JSON['rows'][]=$r;
		}

		$JSON['error']=0;
		break;
	case 'updateData':
		$Field=$_REQUEST['field']??'';
		$Value=$_REQUEST['value']??'';
		switch($Field) {
			case 'EvMixedTeam':
			case 'EvMultiTeam':
			case 'EvMultiTeamNo':
			case 'EvTeamCreationMode':
			case 'EvPartialTeam':
			case 'EvNumQualified':
			case 'EvFirstQualified':
			case 'EvMedals':
			case 'EvWinnerFinalRank':
			case 'EvMaxTeamPerson':
				$Value=intval($Value);
				break;
			case 'EvCodeParent':
			case 'EvWaCategory':
			case 'EvRecCategory':
			case 'EvOdfCode':
				$Value=StrSafe_DB($Value);
				break;
			default:
				$JSON['msg']=$Field.' = '.$Value;
				JsonOut($JSON);
		}
		safe_w_sql("update Events set $Field=$Value where EvTournament={$_SESSION['TourId']} and EvTeamEvent=$TEAM and EvCode=".StrSafe_DB($EVENT));
		$JSON['error']=0;
		break;
}

JsonOut($JSON);

