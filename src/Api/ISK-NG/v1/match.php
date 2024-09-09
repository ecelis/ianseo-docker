<?php
require_once('Common/Lib/Obj_RankFactory.php');

// Verify required parameters
if(empty($req->tocode) || empty($req->type) || empty($req->event) || !isset($req->phase)) {
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

// Retrieve the match info
$CompId = getIdFromCode($req->tocode);
$matchJson = array();

$isTeamEvent = intval($req->type[1]=='T');
switch($req->type[0]) {
	case 'M':
		$options['tournament'] = $CompId;
		$options['events'] = array();
		$options['events'][] =  $req->event . '@' . $req->phase;

		$nameField = ($isTeamEvent ? 'countryName' : 'familyName');
		$oppNameField = ($isTeamEvent ? 'oppCountryName' : 'oppFamilyName');

		$rank=null;
		if($isTeamEvent) {
			$rank = Obj_RankFactory::create('GridTeam', $options);
		} else  {
			$rank = Obj_RankFactory::create('GridInd', $options);
		}

		$rank->read();
		$Data = $rank->getData();

		// Format the match data and return
		foreach($Data['sections'] as $kSec=>$vSec) {
			foreach($vSec['phases'] as $kPh=>$vPh) {
				foreach($vPh['items'] as $kItem=>$vItem) {
					$matchJson[] = Array("code"=>$vItem['matchNo'], "name"=>$vItem[$nameField]."/".$vItem[$oppNameField]);
				}
			}
		}
		break;
	case 'R':
		list($lev, $grp, $rnd) = explode('|', $req->phase);
        $lev=intval($lev);
		$grp=intval($grp);
		$rnd=intval($rnd);
		if($isTeamEvent) {
			$SQL="select r1.RrMatchMatchNo, concat_ws('/', if(r1.RrMatchSubTeam=0, e1.CoName, concat(e1.CoName,'-',r1.RrMatchSubTeam)), if(r2.RrMatchSubTeam=0, e2.CoName, concat(e2.CoName,'-',r2.RrMatchSubTeam))) as MatchNames
				from RoundRobinMatches r1
				inner join RoundRobinMatches r2 on r2.RrMatchTournament=r1.RrMatchTournament and r2.RrMatchTeam=r1.RrMatchTeam and r2.RrMatchEvent=r1.RrMatchEvent and r2.RrMatchLevel=r1.RrMatchLevel and r2.RrMatchGroup=r1.RrMatchGroup and r2.RrMatchRound=r1.RrMatchRound and r2.RrMatchMatchNo=r1.RrMatchMatchNo+1 				    
			    inner join Countries e1 on e1.CoId=r1.RrMatchAthlete and e1.CoTournament=r1.RrMatchTournament
			    inner join Countries e2 on e2.CoId=r2.RrMatchAthlete and e2.CoTournament=r2.RrMatchTournament
				where r1.RrMatchMatchNo%2=0 
					and r1.RrMatchTournament=$CompId 
				  	and r1.RrMatchEvent='$req->event'
					and r1.RrMatchTeam=$isTeamEvent
					and r1.RrMatchLevel=$lev
					and r1.RrMatchGroup=$grp
					and r1.RrMatchRound=$rnd	
 			    order by r1.RrMatchMatchNo";
		} else {
			$SQL="select r1.RrMatchMatchNo, concat_ws('/', e1.EnFirstName, e2.EnFirstName) as MatchNames
				from RoundRobinMatches r1
				inner join RoundRobinMatches r2 on r2.RrMatchTournament=r1.RrMatchTournament and r2.RrMatchTeam=r1.RrMatchTeam and r2.RrMatchEvent=r1.RrMatchEvent and r2.RrMatchLevel=r1.RrMatchLevel and r2.RrMatchGroup=r1.RrMatchGroup and r2.RrMatchRound=r1.RrMatchRound and r2.RrMatchMatchNo=r1.RrMatchMatchNo+1 				    
			    inner join Entries e1 on e1.EnId=r1.RrMatchAthlete and e1.EnTournament=r1.RrMatchTournament
			    inner join Entries e2 on e2.EnId=r2.RrMatchAthlete and e2.EnTournament=r2.RrMatchTournament
				where r1.RrMatchMatchNo%2=0 
					and r1.RrMatchTournament=$CompId 
				  	and r1.RrMatchEvent='$req->event'
					and r1.RrMatchTeam=$isTeamEvent
					and r1.RrMatchLevel=$lev
					and r1.RrMatchGroup=$grp
					and r1.RrMatchRound=$rnd	
 			    order by r1.RrMatchMatchNo";
		}
		$q=safe_r_SQL($SQL);
		while($r=safe_fetch($q)) {
			$matchJson[] = [
				"code"=>$r->RrMatchMatchNo,
				"name"=>$r->MatchNames
			];
		}
		break;
	default:
		$Json['error'] = true;
		$Json['errorMsg'] = 'No Competition with code '.$req->tocode;
		return;
}

$res = array(
    'action' => $req->action,
    'device' => $req->device,
    'tocode' => $req->tocode,
    'matches' => $matchJson
);

