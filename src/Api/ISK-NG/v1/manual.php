<?php
// Verify required parameters
if(empty($req->tocode) || (empty($req->sesstarget) && empty($req->matchid)) ) {
    $Json['error'] = true;
    $Json['errorMsg'] = 'Missing parameters for '.$req->action;
    return;
}

$q = safe_r_SQL("SELECT IskDvDevice, IskDvTournament, IskDvAppVersion FROM IskDevices WHERE `IskDvDevice`=".StrSafe_DB($req->device));
if(safe_num_rows($q) == 1) {
	// check if the competition exists
	if(!($TourId=getIdFromCode($req->tocode))) {
		$Json['error'] = true;
		$Json['errorMsg'] = 'Competition missing';
		return;
	}

	$q=safe_r_sql("select ToOptions, ToNumDist from Tournament where ToId=$TourId");
	if($r=safe_fetch($q) and !empty($r->ToOptions) and $Const=unserialize($r->ToOptions) and !empty($Const['UseApi'])) {
		if($Const['UseApi']!=ISK_NG_LITE_CODE) {
			$Json['error'] = true;
			$Json['errorMsg'] = 'Wrong NG Setting';
			return;
		}
	} else {
		$Json['error'] = true;
		$Json['errorMsg'] = 'Wrong NG Setting';
		return;
	}

	// at this point the device is requesting an existing competition that is set as "light" mode
	// so we won't question anything and insert this device in the competition requested

	// let's go through the different types
	switch($req->type) {
		case 'Q':
			safe_w_sql("update IskDevices set 
				IskDvSchedKey='Q".$r->ToNumDist.$req->sesstarget[0]."', 
				IskDvTarget=".intval(substr($req->sesstarget, 1)).",
				IskDvTournament=$TourId,
				IskDvGroup=0,
				IskDvSetup='',
				IskDvProActive=1
				where IskDvDevice=".StrSafe_DB($req->device));

			// Qualification
			$SQL="select ToCode, IF(ToNameShort!='', ToNameShort, ToName) as TournamentName, ToCategory, IskDvSchedKey as ScheduleKey,  IskDvTournament, IskDvGroup, 
	            IskDvSetup, IskDvDevice, IskDvTarget, 0 as IskDvProActive, IskDvCode, IskDvVersion
				FROM IskDevices
				INNER JOIN Tournament ON ToId=$TourId
				WHERE IskDvDevice = " .StrSafe_DB($req->device);
			$q=safe_r_sql($SQL);
			$res=getQrConfig(safe_fetch($q), false, [
				'IskKey' => "Q".$r->ToNumDist.$req->sesstarget[0],
				'type' => "Q",
				'subtype' => "",
				'session' => $req->sesstarget[0],
				'distance' => range(1, $r->ToNumDist),
				'maxdist' => $r->ToNumDist]);
			break;
		case 'E1':
		case 'E2':
			break;
		case 'MI':
		case 'MT':
		case 'RI':
		case 'RT':
			// get the info of the match
			$items=explode('|', $req->matchid);
			$team=intval($items[0]);
			$event=$items[1];
			$subtype=$req->type[1];
			$prefix=$subtype;
			$matchno=intval(end($items));
			if($req->type[0]=='R') {
				$lev=intval($items[2]);
				$grp=intval($items[3]);
				$rnd=intval($items[4]);
				$subtype='R';
				$prefix.='R';
				$q=safe_r_sql("select concat(RrMatchScheduledDate, RrMatchScheduledTime) as SchedKey, RrMatchTarget+0 as MatchTarget
					from RoundRobinMatches 
					where RrMatchTournament=$TourId and RrMatchTeam=$team and RrMatchEvent='{$event}' and RrMatchLevel=$lev and RrMatchGroup=$grp and RrMatchRound=$rnd and RrMatchMatchNo=$matchno");
			} elseif($team) {
				$q=safe_r_sql("select coalesce(concat(FSScheduledDate, FSScheduledTime),'') as SchedKey, coalesce(FSTarget+0,0) as MatchTarget
					from TeamFinals
					left join FinSchedule on FSTournament=TfTournament and FSEvent=TfEvent and FSMatchNo=TfMatchNo and FSTeamEvent=1 
					where TfEvent='{$event}' and TfMatchNo=$matchno and TfTournament=$TourId");
			} else {
                $q=safe_r_sql("select coalesce(concat(FSScheduledDate, FSScheduledTime),'') as SchedKey, coalesce(FSTarget+0,0) as MatchTarget
					from Finals
					left join FinSchedule on FSTournament=FinTournament and FSEvent=FinEvent and FSMatchNo=FinMatchNo and FSTeamEvent=0 
					where FinEvent='{$event}' and FinMatchNo=$matchno and FinTournament=$TourId");
			}
			$r=safe_fetch($q);
			if(!$r) {
				$res = array('action' => 'handshake', 'error' => 2, 'device' => $req->device);
				return;
			}
			safe_w_sql("update IskDevices set 
				IskDvSchedKey='{$prefix}{$r->SchedKey}', 
				IskDvTarget=".intval($r->MatchTarget).",
				IskDvTournament=$TourId,
				IskDvGroup=0,
				IskDvSetup='',
				IskDvProActive=1
				where IskDvDevice=".StrSafe_DB($req->device));

			// Matches
			$SQL="select ToCode, IF(ToNameShort!='', ToNameShort, ToName) as TournamentName, ToCategory, IskDvSchedKey as ScheduleKey,  IskDvTournament, IskDvGroup, 
		            IskDvSetup, IskDvDevice, IskDvTarget, 0 as IskDvProActive, IskDvCode, IskDvVersion
					FROM IskDevices
					INNER JOIN Tournament ON ToId=$TourId
					WHERE IskDvDevice = " .StrSafe_DB($req->device);
			$q=safe_r_sql($SQL);
			$res=getQrConfig(safe_fetch($q), false, [
				'IskKey'=>$prefix.$r->SchedKey,
				'type' => "M",
				'subtype' => $subtype,
				'session' => $r->SchedKey,
				'distance' => [1],
				'maxdist' => 1,
				'matchid' => $req->matchid
				]);
			break;
		default:
	        $res = array('action' => 'handshake', 'error' => 2, 'device' => $req->device);
	}
} else {
    $res = array('action' => 'handshake', 'error' => 2, 'device' => $req->device);
}

