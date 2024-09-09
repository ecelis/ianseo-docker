<?php
require_once('Common/Fun_FormatText.inc.php');
require_once('Common/Fun_Sessions.inc.php');
require_once('Common/Lib/CommonLib.php');

// Verify required parameters
if(empty($req->tocode)) {
    $Json['error'] = true;
    $Json['errorMsg'] = 'Missing parameters for '.$req->action;
    return;
}

// Verify device is registered
$q = safe_r_SQL("SELECT `IskDvDevice` FROM IskDevices WHERE `IskDvDevice`=".StrSafe_DB($req->device));
if(safe_num_rows($q) == 0) {
    $res = array('action' => 'handshake', 'error' => 2, 'device' => $req->device);
    return;
}

// Retrieve the tournament info and format the data
$CompId = getIdFromCode($req->tocode);

$Sql	= "SELECT ToId,ToType,ToCode,ToName,ToCommitee,ToComDescr,ToWhere, "
    . "       DATE_FORMAT(ToWhenFrom,'" . get_text('DateFmtDB') . "') AS DtFrom, "
    . "       DATE_FORMAT(ToWhenTo,'" . get_text('DateFmtDB') . "') AS DtTo, "
    . "       ToNumSession, ToTypeName, ToNumDist, ToNumEnds, ToCategory "
    . "  FROM Tournament  "
    . " WHERE ToId=$CompId";

// Retrieve the Tournament info
$Rs=safe_r_sql($Sql);
if (safe_num_rows($Rs) == 0) {
    // Competition not found with given code, return an error
    $res = formatCompError($req, 1);
    return;
}

// Get the ISK Options
$iskNgModePro = false;
$iskNgModeLive = false;
if($type=getModuleParameter(ISK_NG_MODULE_ID, 'Mode', '', $CompId)) {
    if ($type == ISK_NG_PRO) {
        $iskNgModePro = true;
    } elseif ($type == ISK_NG_LIVE) {
        $iskNgModeLive = true;
    } elseif ($type !== ISK_NG_LITE) {
        // Competition is not an ISK-NG type, return an error
        $res = formatCompError($req,2);
        return;
    }
}

$res = array(
	'action' => $req->action,
	'device' => $req->device
);

$TourRow=safe_fetch($Rs);
$res["tocode"] = $TourRow->ToCode . (empty($CompPin) ? '' : '|'.$CompPin);
$res["toname"] = $TourRow->ToName;
$res["tocategory"] = $TourRow->ToCategory;
$res["id"] = ($iskNgModePro || $iskNgModeLive)  ? getModuleParameter(ISK_NG_MODULE_ID, 'LicenseNumber', '', $CompId) : '';
$res["numdist"] = (int)$TourRow->ToNumDist;
$res["numsession"] = (int)$TourRow->ToNumSession;

//Retrieve all the stages

// QUALIFICATION
$tmp_stage = array();
$tmp_stage[] = STAGE_QUALIFICATION;
// Retrieve all the Qualification sessions that have been defined for the tournament
// and create a sessions array in the json data
$Sql = "SELECT ToCategory&12 as IsField3D, ToNumEnds, QuSession, SesName, GROUP_CONCAT(DISTINCT QuTarget ORDER BY if(ToCategory&12, (QuTarget-1)%ToNumEnds, QuTarget), QuTarget SEPARATOR ',') as Tgts
    FROM Qualifications
    INNER JOIN Entries ON QuId=EnId 
    inner join Tournament on ToId=EnTournament
    left JOIN Session ON SesTournament=EnTournament AND SesOrder=QuSession AND SesType='Q'
    WHERE EnTournament={$CompId} and QuTarget>0
    GROUP BY QuSession, SesName";
$q = safe_r_SQL($Sql);
while($r = safe_fetch($q)) {
	$row_array=[
		"sessid" => (int) $r->QuSession,
		"sessdesc" => ($r->SesName ?: get_text('Session').' '.$r->QuSession),
		"sesstype" => 'Q',
		"sesstargets" => []
	];
	foreach (explode(',',$r->Tgts) as $tgt) {
		if($r->IsField3D and $tgt > $r->ToNumEnds) {
			$row_array["sesstargets"][]=[
				'id' => $tgt,
				'name' => CheckBisTargets($tgt, $r->ToNumEnds),
			];
		} else {
			$row_array["sesstargets"][]=[
				'id' => $tgt,
				'name' => $tgt,
			];
		}
	}
	$res["sessions"][]= $row_array;
}

$Sql = "SELECT DISTINCTROW EvTeamEvent, EvElimType, EvFinalFirstPhase, RrPartEvent is null as NoRRMatches
	FROM Events
	left join (select RrPartTeam, RrPartEvent
       	from RoundRobinParticipants
       	where RrPartTournament=$CompId
    		and RrPartLevel=0
			and RrPartGroup=0
			group by RrPartTeam, RrPartEvent
       	    ) RoundRobinParticipants on RrPartTeam=EvTeamEvent and RrPartEvent=EvCode
	WHERE EvTournament=$CompId
	order by EvTeamEvent, EvElimType desc";
$Rs=safe_r_sql($Sql);
while ($StageRow=safe_fetch($Rs)) {
	$AddSession=false;
	switch($StageRow->EvElimType) {
		case '2':
			// TWO elimination rounds, so E1 and E2
			if (!in_array('E1', $tmp_stage) and $StageRow->EvFinalFirstPhase) {
				$tmp_stage[] = 'E1';
				$AddSession=true;
			}
			// NO BREAK AS IT CONTINUES IN THE NEXT CASE
		case '1':
			// ONE elimination round only, so only E2
			if (!in_array('E2', $tmp_stage) and $StageRow->EvFinalFirstPhase) {
				$tmp_stage[] = 'E2';
				$AddSession=true;
			}

			if($AddSession) {
				// need to get the sessions as well!
				$Sql = "SELECT ElSession, SesName, ElElimPhase, GROUP_CONCAT(DISTINCT ElTargetNo+0 ORDER BY ElTargetNo SEPARATOR ',') as Tgts
			    FROM Eliminations 
			    left JOIN Session ON SesTournament=ElTournament AND SesOrder=ElSession AND SesType='E'
			    WHERE ElTournament={$CompId} and ElTargetNo!=''
			    GROUP BY ElElimPhase, ElSession, SesName";
				$q = safe_r_SQL($Sql);
				while($r = safe_fetch($q)) {
					$row_array=[
						"sessid" => (int) $r->ElSession,
						"sessdesc" => ($r->SesName ?: get_text('Session').' '.$r->ElSession),
						"sesstype" => 'E'.($r->ElElimPhase?'2':'1'),
						"sesstargets" => []
					];
					foreach (explode(',',$r->Tgts) as $tgt) {
						$row_array["sesstargets"][]=[
							'id' => $tgt,
							'name' => $tgt,
						];
					}
					$res["sessions"][]= $row_array;
				}
			}

			break;
		case '3':
		case '4':
			// these are pool matches
			if (!in_array('P', $tmp_stage) and $StageRow->EvFinalFirstPhase) {
		        $tmp_stage[] = 'P';
			}
			break;
		case '5':
			// these are Round Robin
			$stage='R'.($StageRow->EvTeamEvent ? 'T' : 'I');
			if (!in_array($stage, $tmp_stage)) {
		        $tmp_stage[] = $stage;
			}

			// check if this RR has also matches as final outcome

			break;
	}
	if($StageRow->EvElimType!=5 or !$StageRow->NoRRMatches) {
		if ($StageRow->EvTeamEvent==0 and !in_array(STAGE_MATCH_INDIVIDUAL, $tmp_stage) and $StageRow->EvFinalFirstPhase) {
			$tmp_stage[] = STAGE_MATCH_INDIVIDUAL;
		}
		if ($StageRow->EvTeamEvent == 1 and !in_array(STAGE_MATCH_TEAM, $tmp_stage) and $StageRow->EvFinalFirstPhase) {
			$tmp_stage[] = STAGE_MATCH_TEAM;
		}
	}
}
$res["stages"] = $tmp_stage;


return;


/**
 * Formats the competition error code data
 */
function formatCompError($req, $errorCode) {
    return array(
        'action' => $req->action,
        'device' => $req->device,
        'tocode' => $req->tocode,
        'errorcode' => $errorCode
    );
}
