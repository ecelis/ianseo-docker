<?php
require_once('Common/Lib/CommonLib.php');
require_once('Common/Lib/Fun_Phases.inc.php');

// Verify required parameters
if(empty($req->tocode) || empty($req->type)) {
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

switch($req->type) {
    case 'MI':
    case 'MT':
	    $SQL = "SELECT EvCode, EvEventName, EvFinalFirstPhase, EvElimType, EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
	    	FROM Events
	    	WHERE EvTournament=$CompId AND EvTeamEvent=".($req->type=='MI' ? '0' : '1')." AND EvFinalFirstPhase!=0
	    	ORDER BY EvProgr";
        break;
    case 'RI':
    case 'RT':
	    $SQL = "SELECT EvCode, EvEventName, EvFinalFirstPhase, EvElimType, RrLevEnds as EvElimEnds, RrLevArrows as EvElimArrows, RrLevSO as EvElimSO, RrLevEnds as EvFinEnds, RrLevArrows as EvFinArrows, RrLevSO as EvFinSO
	    	FROM Events
	    	inner join RoundRobinLevel on RrLevTournament=EvTournament and RrLevTeam=EvTeamEvent and RrLevEvent=EvCode and RrLevLevel=1
	    	WHERE EvTournament=$CompId AND EvTeamEvent=".($req->type=='RI' ? '0' : '1')." AND EvFinalFirstPhase!=0
	    	ORDER BY EvProgr";
        break;
    case 'E1':
        $SQL = "SELECT EvCode, EvEventName, EvFinalFirstPhase, EvElimType, EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
        	FROM Events
        	WHERE EvTournament=$CompId AND EvTeamEvent=0 and EvElimType=2 and EvElim1>0 AND EvFinalFirstPhase!=0
        	ORDER BY EvProgr";
        break;
    case 'E2':
        $SQL = "SELECT EvCode, EvEventName, EvFinalFirstPhase, EvElimType, EvElimEnds, EvElimArrows, EvElimSO, EvFinEnds, EvFinArrows, EvFinSO
        	FROM Events
        	WHERE EvTournament=$CompId AND EvTeamEvent=0 and EvElimType=1 and EvElim2>0 AND EvFinalFirstPhase!=0
        	ORDER BY EvProgr";
        break;
    default:
	    $Json['error'] = true;
	    $Json['errorMsg'] = 'No Competition with code '.$req->tocode;
		return;
}

// Retrieve the Event List
$eventJson = array();

$q = safe_r_sql($SQL);
while ($r = safe_fetch($q)) {
    $tmpPhases = array();
	switch($req->type) {
		case 'E1':
		case 'E2':
			// Old style Field Elimination
			$t = safe_r_sql("select distinct left(ElTargetNo, 3) TargetNo
					from Eliminations
					where ElTournament=$CompId
						and ElElimPhase=".($req->type[1]-1)."
						and ElEventCode='$r->EvCode'
						and ElTargetNo>''
					order by TargetNo");
			while ($u = safe_fetch($t)) {
				$tmpPhases[] = array('code' => "{$req->type}|{$r->EvCode}|{$u->TargetNo}",
					'name' => ltrim($u->TargetNo, "0"));
			}
			break;
		case 'MI':
		case 'MT':
			$phases = getPhasesId($r->EvFinalFirstPhase);
			if ($req->type == 'MI' AND ($r->EvElimType == 3 or $r->EvElimType == 4)) {
				$poolMatches = ($r->EvElimType == 3) ? getPoolMatchesHeaders() : getPoolMatchesHeadersWA();
				foreach ($poolMatches as $kPM=>$vPM) {
					$tmpPhases[] = array('code' => strval($kPM),
						'name' => $vPM);
				}
			}
			foreach ($phases as $ph) {
				$tmpPhases[] = array('code' => bitwisePhaseId($ph),
					'name' => $ph.'_Phase');
			}
			break;
		case 'RI':
		case 'RT':
			// round robin stuff...
			$t=safe_r_sql("select RrLevLevel, RrLevName, RrLevGroupArchers, group_concat(concat_ws('§', RrGrGroup, RrGrName) order by RrGrGroup separator '|') as GroupNames
				from RoundRobinLevel
				inner join RoundRobinGroup on RrGrTournament=RrLevTournament and RrGrTeam=RrLevTeam and RrGrEvent=RrLevEvent and RrGrLevel=RrLevLevel
				where RrLevTournament=$CompId and RrLevTeam=".($req->type[1]=='I' ? '0' : '1')." and RrLevEvent='$r->EvCode'
				group by RrLevLevel
				order by RrLevLevel");
			while ($u = safe_fetch($t)) {
				$arr=[
					'code' => $u->RrLevLevel,
					'name' => $u->RrLevName,
					'rounds' => $u->RrLevGroupArchers-1,
					'groups' => [],
				];
				foreach(explode('|', $u->GroupNames) as $grp) {
					list($id,$name)=explode('§', $grp);
					$arr['groups'][]=[
						'code' => $id,
						'name' => $name,
					];
				}
				$tmpPhases[] = $arr;
			}
			break;
	}
	if($req->type[0]=='R') {
		$eventJson[] = array('code' => $r->EvCode,
			'name' => $r->EvEventName,
			'ends' => $r->EvElimEnds,
			'arrows' => $r->EvElimArrows,
			'levels' => $tmpPhases);
	} else {
		$eventJson[] = array('code' => $r->EvCode,
			'name' => $r->EvEventName,
			'ends' => $r->EvElimEnds,
			'arrows' => $r->EvElimArrows,
			'phases' => $tmpPhases);
	}
}

$res = array(
    'action' => $req->action,
    'device' => $req->device,
    'tocode' => $req->tocode,
    'events' => $eventJson
);
