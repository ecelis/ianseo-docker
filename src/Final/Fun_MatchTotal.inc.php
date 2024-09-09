<?php
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Common/Fun_Phases.inc.php');
require_once('Fun_ChangePhase.inc.php');
require_once('Common/Lib/Fun_Modules.php');

function UpdateArcherShooting($MatchNo, $EvCode, $ArrowIndex, $ArcherId, $ToId=0){
    $CompId = $ToId;
    if(empty($CompId) AND !empty($_SESSION['TourId'])) {
        $CompId = $_SESSION['TourId'];
    }

    // $ArrowIndex is 0-based

    $Sql = "SELECT 
        TfEvent, TfMatchNo, TfShootingArchers, CONCAT(TfArrowstring, TfTiebreak) as TfArrows, TfcId
        FROM TeamFinals 
        INNER JOIN TeamFinComponent ON TfcCoId=TfTeam AND TfcSubTeam=TfSubTeam AND TfcTournament=TfTournament AND TfcEvent=TfEvent 
        WHERE TfTournament={$CompId} AND TfMatchNo=" . StrSafe_DB($MatchNo) . " AND TfEvent=" . StrSafe_DB($EvCode) . " AND TfcId= " . StrSafe_DB($ArcherId);
    if($ArcherId == 0) {
        $Sql = "SELECT TfEvent, TfMatchNo, TfShootingArchers, CONCAT(TfArrowstring, TfTiebreak) as TfArrows
        FROM TeamFinals 
        WHERE TfTournament={$CompId} AND TfMatchNo=" . StrSafe_DB($MatchNo) . " AND TfEvent=" . StrSafe_DB($EvCode);
    }

    $q=safe_r_sql($Sql);
    if (safe_num_rows($q)==1 and $r=safe_fetch($q)) {
        $ArcherData = $r->TfShootingArchers;
        if(!empty($ArcherData)) {
            $ArcherData = json_decode($ArcherData, true);
        } else {
            $ArcherData = array();
        }
        if($ArcherId) {
            $ArcherData[$ArrowIndex] = $r->TfcId;
        } else {
            unset($ArcherData[$ArrowIndex]);
        }

        // Updates the position
        $Sql = "UPDATE TeamFinals SET TfShootingArchers =" . StrSafe_DB(count($ArcherData) ? json_encode($ArcherData) : '') .
            " WHERE TfEvent = '{$r->TfEvent}' AND TfMatchNo = {$r->TfMatchNo} AND TfTournament={$CompId}";
        safe_w_sql($Sql);
        if(strlen(trim($r->TfArrows)) > $ArrowIndex) {
            runJack("FinArrUpdate", $CompId, array("Event"=>$r->TfEvent, "Team"=>1, "MatchNo"=>($r->TfMatchNo % 2 ? $r->TfMatchNo-1 : $r->TfMatchNo), "ArrowStart"=>$ArrowIndex, "ArrowEnd"=>$ArrowIndex, "Side"=>($r->TfMatchNo % 2), "TourId"=>$CompId));
        }
        return true;
    } else {
        return false;
    }
}

function UpdateArrowPosition($MatchNo, $EvCode, $TeamEvent, $ArrowIndex, $Position=null, $Wind=null, $Time=null, $ToId=0){
    $CompId = $ToId;
    if(empty($CompId) && !empty($_SESSION['TourId'])) {
        $CompId = $_SESSION['TourId'];
    }

	$retValue = null;
	// $ArrowIndex is 1-based for consistency with UpdateArrowString()
	$ArrowIndex--;
	$ArrowTimingIndex=$ArrowIndex;

    if($MatchNo>256) {
        $Select = "SELECT 
            RrMatchEvent as EvCode, RrMatchArrowstring as ArString, RrMatchTiebreak as TbString, RrMatchArrowPosition as ArPos, RrMatchTiePosition as TbPos, FinOdfArrows,
                RrLevEnds, RrLevArrows, RrLevSO, RrLevMatchMode, EvMaxTeamPerson
            FROM RoundRobinMatches 
		    inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel		
            inner join Events on EvTournament=RrMatchTournament and EvTeamEvent=RrMatchTeam and EvCode=RrMatchEvent
            left JOIN FinOdfTiming ON FinOdfMatchno=(RrMatchLevel*1000000)+ (RrMatchGroup*10000) + (RrMatchRound*100) + RrMatchMatchNo and FinOdfTournament=RrMatchTournament and FinOdfEvent=RrMatchEvent and FinOdfTeamEvent=RrMatchTeam
            WHERE RrMatchTournament={$CompId} AND (RrMatchLevel*1000000)+ (RrMatchGroup*10000) + (RrMatchRound*100) + RrMatchMatchNo=" . intval($MatchNo) . " AND RrMatchTeam=$TeamEvent AND RrMatchEvent=" . StrSafe_DB($EvCode);

        $Rs=safe_r_sql($Select);
        if(safe_num_rows($Rs)!=1) {
            return $retValue;
        }

        $MyRow=safe_fetch($Rs);

        $obj=new StdClass();
        $obj->ends=$MyRow->RrLevEnds;
        $obj->arrows=$MyRow->RrLevArrows;
        $obj->so=$MyRow->RrLevSO;
        $obj->winAt=$MyRow->RrLevEnds+1;
        $obj->MaxTeam=$MyRow->EvMaxTeamPerson;
        $obj->EvMatchMode=$MyRow->RrLevMatchMode;
        $obj->MaxArrows=$MyRow->RrLevEnds * $MyRow->RrLevArrows;
    } else {
        $TablePrefix = "Fin";
        if($TeamEvent) {
            $TablePrefix = "Tf";
            $Select = "SELECT 
                TfEvent as EvCode, TfMatchNo as MatchNo, TfArrowString as ArString, TfTieBreak as TbString, TfArrowPosition as ArPos, TfTiePosition as TbPos, GrPhase, FinOdfArrows 
                FROM TeamFinals 
                INNER JOIN Grids ON TfMatchNo=GrMatchNo
                left JOIN FinOdfTiming ON FinOdfMatchno=TfMatchNo and FinOdfTournament=TfTournament and FinOdfEvent=TfEvent and FinOdfTeamEvent=1
                WHERE TfTournament={$CompId} AND TfMatchNo=" . StrSafe_DB($MatchNo) . " AND TfEvent=" . StrSafe_DB($EvCode);
        } else {
            $Select = "SELECT 
                FinEvent as EvCode, FinMatchNo as MatchNo, FinArrowString as ArString, FinTieBreak as TbString, FinArrowPosition as ArPos, FinTiePosition as TbPos, GrPhase, FinOdfArrows
                FROM Finals 
                INNER JOIN Grids ON FinMatchNo=GrMatchNo 
                left JOIN FinOdfTiming ON FinOdfMatchno=FinMatchNo and FinOdfTournament=FinTournament and FinOdfEvent=FinEvent and FinOdfTeamEvent=0
                WHERE FinTournament={$CompId} AND FinMatchNo=" . StrSafe_DB($MatchNo) . " AND FinEvent=" . StrSafe_DB($EvCode);
        }
        $Rs=safe_r_sql($Select);
        if(safe_num_rows($Rs)!=1) {
            return $retValue;
        }

        $MyRow=safe_fetch($Rs);

        $obj=getEventArrowsParams($MyRow->EvCode, $MyRow->GrPhase, $TeamEvent, $CompId);
    }

    $MatchUpdated=false; // server per aggiornare il timestamp
    $isShootOff = boolval($ArrowIndex >= ($obj->ends*$obj->arrows));
    if($isShootOff){
        $ArrowIndex = $ArrowIndex - intval($obj->ends*$obj->arrows);
    }

    $ArrowData = ($isShootOff ?  $MyRow->TbPos : $MyRow->ArPos);
    if(!empty($ArrowData)) {
        $ArrowData = json_decode($ArrowData, true);
    } else {
        $ArrowData = array();
    }

    // timings
    if(!empty($MyRow->FinOdfArrows)) {
        $ArrowTiming=json_decode($MyRow->FinOdfArrows, true);
    } else {
        $ArrowTiming=array();
    }
    if(!isset($ArrowTiming["$ArrowTimingIndex"]['Ts'])) {
        // NEVER updates the timestamp of the arrow once it is set
        $ArrowTiming["$ArrowTimingIndex"]['Ts']=date('Y-m-d H:i:s');
        ksort($ArrowTiming, SORT_NUMERIC);
    }

    if($Position or $Wind or $Time) {
        if(empty($ArrowData[$ArrowIndex])) {
            $ArrowData[$ArrowIndex]=[];
        }
        if(!is_null($Position)) {
            foreach($Position as $k=>$v) {
                $ArrowData[$ArrowIndex][$k]=$v;
            }
        }
        if(!is_null($Wind)) {
            foreach($Wind as $k=>$v) {
                $ArrowData[$ArrowIndex][$k]=$v;
            }
        }
        if(!is_null($Time)) {
            $ArrowData[$ArrowIndex]['T']=$Time;
        }
    }

    // Updates the position
    if($MatchNo>256) {
        $Sql = "UPDATE RoundRobinMatches
            SET " . ($isShootOff ? "RrMatchTiePosition" : "RrMatchArrowPosition") . "=" . StrSafe_DB(count($ArrowData) ? json_encode($ArrowData) : '') . ",
                RrMatchDateTime=RrMatchDateTime
            WHERE RrMatchEvent = '{$MyRow->EvCode}'
                AND (RrMatchLevel*1000000)+ (RrMatchGroup*10000) + (RrMatchRound*100) + RrMatchMatchNo = {$MatchNo}
                AND RrMatchTournament={$CompId}
                AND RrMatchTeam=$TeamEvent";
        safe_w_sql($Sql);

        if(safe_w_affected_rows()) {
            // updates the timing
            $Sql = "Insert into FinOdfTiming
			SET FinOdfArrows=".StrSafe_DB(json_encode($ArrowTiming)).",
				FinOdfEvent = '{$MyRow->EvCode}',
				FinOdfMatchNo = '{$MatchNo}',
				FinOdfTournament='{$CompId}',
				FinOdfTeamEvent=".($TeamEvent ? 1 : 0).",
				FinOdfUnconfirmed=0,
				FinOdfUnofficial=0,
				FinOdfOfficial=0
			on duplicate key update FinOdfUnconfirmed=0,
				FinOdfUnofficial=0,
				FinOdfOfficial=0, 
				FinOdfArrows=".StrSafe_DB(json_encode($ArrowTiming));
            safe_w_sql($Sql);
        }


        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $d = new DateTime( date('Y-m-d H:i:s.'.$micro, intval($t)));

        if(safe_w_affected_rows()) {
            $Sql = "UPDATE RoundRobinMatches
                SET RrMatchDateTime='".$d->format('Y-m-d H:i:s.u')."'
                WHERE RrMatchEvent = '{$MyRow->EvCode}'
                    AND (RrMatchLevel*1000000)+ (RrMatchGroup*10000) + (RrMatchRound*100) + RrMatchMatchNo = {$MatchNo}
                    AND RrMatchTournament={$CompId}
                    AND RrMatchTeam=$TeamEvent";
            safe_w_sql($Sql);
        }
        return $retValue;
    }

    // Resumes regular matches
    $Sql = "UPDATE "
        . ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
        . "SET "
        . $TablePrefix . ($isShootOff ? "TiePosition" : "ArrowPosition") . "=" . StrSafe_DB(count($ArrowData) ? json_encode($ArrowData) : '') . ", "
        . "{$TablePrefix}DateTime={$TablePrefix}DateTime "
        . "WHERE "
        . "{$TablePrefix}Event = '{$MyRow->EvCode}' "
        . "AND {$TablePrefix}MatchNo = {$MyRow->MatchNo} "
        . "AND {$TablePrefix}Tournament={$CompId}";
    safe_w_sql($Sql);

    if(safe_w_affected_rows()) {
        // updates the timing
        $Sql = "Insert into FinOdfTiming
			SET FinOdfArrows=".StrSafe_DB(json_encode($ArrowTiming)).",
				FinOdfEvent = '{$MyRow->EvCode}',
				FinOdfMatchNo = '{$MyRow->MatchNo}',
				FinOdfTournament='{$CompId}',
				FinOdfTeamEvent=".($TeamEvent ? 1 : 0).",
				FinOdfUnconfirmed=0,
				FinOdfUnofficial=0,
				FinOdfOfficial=0
			on duplicate key update FinOdfUnconfirmed=0,
				FinOdfUnofficial=0,
				FinOdfOfficial=0, 
				FinOdfArrows=".StrSafe_DB(json_encode($ArrowTiming));
        safe_w_sql($Sql);
    }


    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $d = new DateTime( date('Y-m-d H:i:s.'.$micro, intval($t)));

    if(safe_w_affected_rows()) {
        $Sql = "UPDATE "
            . ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
            . "SET "
            . "{$TablePrefix}DateTime='".$d->format('Y-m-d H:i:s.u')."' "
            . "WHERE "
            . " {$TablePrefix}Event = '{$MyRow->EvCode}' "
            . "AND {$TablePrefix}MatchNo = {$MyRow->MatchNo} "
            . "AND {$TablePrefix}Tournament={$CompId}";
        safe_w_sql($Sql);
    }
    return $retValue;
}

function DeleteArrowPosition($MatchNo, $EvCode, $TeamEvent, $ArrowIndex, $ToId=0){
    $CompId = $ToId;
    if(empty($CompId) && !empty($_SESSION['TourId'])) {
        $CompId = $_SESSION['TourId'];
    }

	$retValue = null;
	// $ArrowIndex is 1-based for consistency with UpdateArrowString()
	$ArrowIndex--;

    if($MatchNo>256) {
        $Select = "SELECT RrMatchEvent as EvCode, RrMatchMatchNo as MatchNo, RrMatchArrowstring as ArString, RrMatchTiebreak as TbString, RrMatchArrowPosition as ArPos, RrMatchTiePosition as TbPos,
            RrLevArrows as arrows, RrLevEnds as ends, RrLevSO as so
            FROM RoundRobinMatches
            inner join RoundRobinLevel on RrLevTournament=RrMatchTournament and RrLevTeam=RrMatchTeam and RrLevEvent=RrMatchEvent and RrLevLevel=RrMatchLevel
            WHERE RrMatchTournament={$CompId} AND RrMatchTeam=$TeamEvent and (RrMatchLevel*1000000)+ (RrMatchGroup*10000) + (RrMatchRound*100) + RrMatchMatchNo=" . intval($MatchNo) . " AND RrMatchEvent=" . StrSafe_DB($EvCode);

        $Rs=safe_r_sql($Select);
        if (safe_num_rows($Rs)==1) {
            $MatchUpdated=false; // server per aggiornare il timestamp

            $MyRow=safe_fetch($Rs);

            $isShootOff = boolval($ArrowIndex >= ($MyRow->ends*$MyRow->arrows));
            if($isShootOff){
                $ArrowIndex = $ArrowIndex - intval($MyRow->ends*$MyRow->arrows);
            }

            $ArrowData = ($isShootOff ?  $MyRow->TbPos : $MyRow->ArPos);
            if(!empty($ArrowData)) {
                $ArrowData = json_decode($ArrowData, true);
            } else {
                $ArrowData = array();
            }

            if(!empty($ArrowData[$ArrowIndex])) {
                unset($ArrowData[$ArrowIndex]);
            }

            $Sql = "UPDATE RoundRobinMatches
                SET " . ($isShootOff ? "RrMatchTiePosition" : "RrMatchArrowPosition") . "=" . StrSafe_DB(count($ArrowData) ? json_encode($ArrowData) : '') . ",
                    RrMatchDateTime=RrMatchDateTime
                WHERE RrMatchEvent = '{$MyRow->EvCode}'
                    AND RrMatchTeam=$TeamEvent
                    AND (RrMatchLevel*1000000)+ (RrMatchGroup*10000) + (RrMatchRound*100) + RrMatchMatchNo = {$MatchNo}
                    AND RrMatchTournament={$CompId}";
            safe_w_sql($Sql);

            $t = microtime(true);
            $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            $d = new DateTime( date('Y-m-d H:i:s.'.$micro, intval($t)));

            if(safe_w_affected_rows()) {
                $Sql = "UPDATE RoundRobinMatches
                    SET RrMatchDateTime='".$d->format('Y-m-d H:i:s.u')."'
                    WHERE RrMatchEvent = '{$MyRow->EvCode}'
                        AND RrMatchTeam=$TeamEvent
                        AND (RrMatchLevel*1000000)+ (RrMatchGroup*10000) + (RrMatchRound*100) + RrMatchMatchNo = {$MatchNo}
                        AND RrMatchTournament={$CompId}";
                safe_w_sql($Sql);
            }
        }
        return $retValue;
    }

    // resume regular matches
	$TablePrefix = "Fin";
	$Select
		= "SELECT "
		. "FinEvent as EvCode, FinMatchNo as MatchNo, FinArrowString as ArString, FinTieBreak as TbString, FinArrowPosition as ArPos, FinTiePosition as TbPos, GrPhase "
		. "FROM Finals "
		. "INNER JOIN Grids ON FinMatchNo=GrMatchNo "
		. "WHERE FinTournament={$CompId} AND FinMatchNo=" . StrSafe_DB($MatchNo) . " AND FinEvent=" . StrSafe_DB($EvCode);
	if($TeamEvent) {
		$TablePrefix = "Tf";
		$Select
			= "SELECT "
			. "TfEvent as EvCode, TfMatchNo as MatchNo, TfArrowString as ArString, TfTieBreak as TbString, TfArrowPosition as ArPos, TfTiePosition as TbPos, GrPhase "
			. "FROM TeamFinals "
			. "INNER JOIN Grids ON TfMatchNo=GrMatchNo "
			. "WHERE TfTournament={$CompId} AND TfMatchNo=" . StrSafe_DB($MatchNo) . " AND TfEvent=" . StrSafe_DB($EvCode);
	}

	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1) {
		$MatchUpdated=false; // server per aggiornare il timestamp

		$MyRow=safe_fetch($Rs);

		$obj=getEventArrowsParams($MyRow->EvCode,$MyRow->GrPhase,$TeamEvent, $CompId);
		$isShootOff = boolval($ArrowIndex >= ($obj->ends*$obj->arrows));
		if($isShootOff){
            $ArrowIndex = $ArrowIndex - intval($obj->ends*$obj->arrows);
        }

        $ArrowData = ($isShootOff ?  $MyRow->TbPos : $MyRow->ArPos);
		if(!empty($ArrowData)) {
            $ArrowData = json_decode($ArrowData, true);
        } else {
            $ArrowData = array();
        }

        if(!empty($ArrowData[$ArrowIndex])) {
            unset($ArrowData[$ArrowIndex]);
        }

		$Sql = "UPDATE "
			. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
			. "SET "
			. $TablePrefix . ($isShootOff ? "TiePosition" : "ArrowPosition") . "=" . StrSafe_DB(count($ArrowData) ? json_encode($ArrowData) : '') . ", "
			. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
			. "WHERE "
			. "{$TablePrefix}Event = '{$MyRow->EvCode}' "
			. "AND {$TablePrefix}MatchNo = {$MyRow->MatchNo} "
			. "AND {$TablePrefix}Tournament={$CompId}";
		safe_w_sql($Sql);

		$t = microtime(true);
		$micro = sprintf("%06d",($t - floor($t)) * 1000000);
		$d = new DateTime( date('Y-m-d H:i:s.'.$micro, intval($t)));

		if(safe_w_affected_rows()) {
			$Sql = "UPDATE "
				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
				. "SET "
				. "{$TablePrefix}DateTime='".$d->format('Y-m-d H:i:s.u')."' "
				. "WHERE "
				. " {$TablePrefix}Event = '{$MyRow->EvCode}' "
				. "AND {$TablePrefix}MatchNo = {$MyRow->MatchNo} "
				. "AND {$TablePrefix}Tournament={$CompId}";
			safe_w_sql($Sql);
		}
	}
	return $retValue;
}

function UpdateArrowString($MatchNo, $EvCode, $TeamEvent, $ArrowString, $ArrowStart, $ArrowEnd, $ToId=0, $Closest=0, &$CHANGES=false) {
	$CompId = $ToId;
	$OppMatchno=($MatchNo%2 ? $MatchNo-1 : $MatchNo+1);
	if(empty($CompId) && !empty($_SESSION['TourId']))
		$CompId = $_SESSION['TourId'];

	global $CFG;
	$Select ='';

	$TablePrefix = "Fin";
	$Table = "Finals";
	$Select = "SELECT
			FinEvent as EvCode, FinMatchNo as MatchNo, FinArrowString as ArString, FinTieBreak as TbString, FinConfirmed as Confirmed,
			EvMatchMode, EvMatchArrowsNo, EvGoldsChars, EvXNineChars, GrPhase, FinLive as LiveMatch, FinTbClosest as Closest, coalesce(FinOdfLive,0) as OdfLive
		FROM Finals
		INNER JOIN Grids ON FinMatchNo=GrMatchNo
		INNER JOIN Events ON FinEvent=EvCode AND FinTournament=EvTournament AND EvTeamEvent=0
		left join FinOdfTiming on FinOdfTournament=FinTournament and FinOdfEvent=FinEvent and FinOdfTeamEvent=0 and FinOdfMatchno=FinMatchNo and FinOdfLive>0
		WHERE FinTournament={$CompId} AND FinMatchNo=" . StrSafe_DB($MatchNo) . " AND FinEvent=" . StrSafe_DB($EvCode);
	if($TeamEvent) {
		$TablePrefix = "Tf";
		$Table = "TeamFinals";
		$Select = "SELECT
				TfEvent as EvCode, TfMatchNo as MatchNo, TfArrowString as ArString, TfTieBreak as TbString, TfConfirmed as Confirmed,
				EvMatchMode, EvMatchArrowsNo, EvGoldsChars, EvXNineChars, GrPhase, TfLive as LiveMatch, TfTbClosest as Closest, coalesce(FinOdfLive,0) as OdfLive
			FROM TeamFinals
			INNER JOIN Grids ON TfMatchNo=GrMatchNo
			INNER JOIN Events ON TfEvent=EvCode AND TfTournament=EvTournament AND EvTeamEvent=1
			left join FinOdfTiming on FinOdfTournament=TfTournament and FinOdfEvent=TfEvent and FinOdfTeamEvent=1 and FinOdfMatchno=TfMatchNo and FinOdfLive>0
			WHERE TfTournament={$CompId} AND TfMatchNo=" . StrSafe_DB($MatchNo) . " AND TfEvent=" . StrSafe_DB($EvCode);
	}

	$Rs=safe_r_sql($Select);
	if (safe_num_rows($Rs)==1) {
		$MatchChanged=false;
		$MyRow=safe_fetch($Rs);

		$obj=getEventArrowsParams($MyRow->EvCode,$MyRow->GrPhase,$TeamEvent,$CompId);
		$maxArrows=$obj->ends*$obj->arrows;
		$maxSoArrows=$obj->so;

		$ArrowStart--;
		$Len=$ArrowEnd-$ArrowStart;
		$Offset=($ArrowStart<$maxArrows ? 0 : $maxArrows);

		$SubArrowString=substr($ArrowString,0,$Len);
		$tmpArrowString=str_pad(($Offset==0 ? $MyRow->ArString : $MyRow->TbString),($Offset==0 ? $maxArrows : $maxSoArrows)," ",STR_PAD_RIGHT);
		$tmpArrowString=substr_replace($tmpArrowString,$SubArrowString,$ArrowStart-$Offset,$Len);
        $Golds=0;
        $XNine=0;
		if($Offset==0) {
            $tmpArrowString = substr($tmpArrowString, 0, $maxArrows);
            list($Score, $Golds, $XNine)=ValutaArrowStringGX($tmpArrowString, $MyRow->EvGoldsChars, $MyRow->EvXNineChars);
        } elseif($Closest) {
            // must first remove the closest and tie from the other match
            safe_w_sql("update ". ($TeamEvent==0 ? "Finals" : "TeamFinals") . " SET {$TablePrefix}TbClosest=0, {$TablePrefix}Tie=0, {$TablePrefix}TbDecoded=replace({$TablePrefix}TbDecoded, '+','') 
                WHERE 
                {$TablePrefix}Tie!=2 
                AND {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . "
                AND {$TablePrefix}MatchNo=$OppMatchno
                AND {$TablePrefix}Tournament=$CompId");
			$MatchChanged=safe_w_affected_rows();
		}

		$TbDecoded='';
		if($Offset) {
			// check the decoded arrows of the tiebreak!
			$decoded=array();
			foreach(str_split(rtrim($tmpArrowString), $obj->so) as $k) {
				if($obj->so==1) {
					$decoded[]=DecodeFromLetter($k);
				} else {
					$decoded[]=ValutaArrowString($k);
				}
			}
			$TbDecoded=", {$TablePrefix}TbDecoded=".StrSafe_DB(implode(',', $decoded).($Closest?'+':''));
		}

		$query="UPDATE "
			. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
			. "SET "
			. $TablePrefix . ($Offset==0 ? "ArrowString" : "Tiebreak") . "=" . StrSafe_DB($tmpArrowString) . ", "
			. ($Offset==0 ? $TablePrefix.'Golds='.intval($Golds).', ' : '')
			. ($Offset==0 ? $TablePrefix.'XNines='.intval($XNine).', ' : '')
			. ($Offset==0 ? '' : $TablePrefix.'TbClosest='.intval($Closest).', ')
			. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
			. ($TbDecoded ? $TbDecoded : '')
			. " WHERE "
			. "{$TablePrefix}Tie!=2 "
			. "AND {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
			. "AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->MatchNo) . " "
			. "AND {$TablePrefix}Tournament=". StrSafe_DB($CompId);

		safe_w_sql($query);
		if(safe_w_affected_rows() or $MatchChanged) {
			// update ODF
			if($ArrowStart==0 and $Len==1) {
				// sets the ODF live flag
				updateOdfTiming('L', $CompId,$MyRow->EvCode,$TeamEvent,$MatchNo);
			} else {
				// resets eventually the ODF end of match dates
				updateOdfTiming('R', $CompId,$MyRow->EvCode,$TeamEvent,$MatchNo);
			}

			$CHANGES=true;
			return EvaluateMatch($MyRow->EvCode, $TeamEvent, $MyRow->MatchNo, $CompId, true, '', $ArrowStart, $ArrowEnd);
		}
		//print $query;
		// needs to return if the match is finished or not, so ask for the situation
		return IsMatchFinished($MatchNo, $EvCode, $TeamEvent, $CompId);
	}
}

function IsMatchFinished($MatchNo, $EvCode, $TeamEvent, $CompId) {
	$MatchNo=intval($MatchNo/2)*2;
	if(!$CompId) {
		$CompId=$_SESSION['TourId'];
	}
	$TablePrefix = ($TeamEvent ? "Tf" : "Fin");
	$Table = ($TeamEvent ? "TeamFinals" : "Finals");

	$q=safe_r_sql("select f1.{$TablePrefix}WinLose or f2.{$TablePrefix}WinLose or f1.{$TablePrefix}IrmType>=10 or f2.{$TablePrefix}IrmType>=10 as IsFinished, coalesce(FinOdfUnconfirmed, 0) as Unconfirmed
		from {$Table} f1
		inner join {$Table} f2 on f2.{$TablePrefix}Event=f1.{$TablePrefix}Event and f2.{$TablePrefix}Tournament=f1.{$TablePrefix}Tournament and f2.{$TablePrefix}MatchNo=f1.{$TablePrefix}MatchNo+1
		left join FinOdfTiming on FinOdfTournament=$CompId and FinOdfEvent='$EvCode' and FinOdfTeamEvent=$TeamEvent and FinOdfMatchno=f1.{$TablePrefix}MatchNo and FinOdfUnconfirmed>0
		where f1.{$TablePrefix}Tournament=$CompId and f1.{$TablePrefix}MatchNo=$MatchNo and f1.{$TablePrefix}Event=".StrSafe_DB($EvCode));
	if($r=safe_fetch($q) and $r->IsFinished) {
		if(!$r->Unconfirmed) {
			updateOdfTiming('U',$CompId,$EvCode,$TeamEvent,$MatchNo);
		}
		return true;
	}
	return false;
}

/**
 * This function <b>MUST</b> be called when something has changed in one of the opponents of a match. It will re-evaluate everything and establish a winner if any.
 * @param string $Event
 * @param int $Team
 * @param int $Matchno
 * @param bool $Recalculate true means the arrowstrings and tiebreak strings will get processed, false only check the totals!
 * @param int $CompId
 * @return bool true on success, false on failure
 */
function EvaluateMatch($Event, $Team, $Matchno, $CompId=0, $Recalculate=true, $Pool='', $ArrowStart=0, $ArrowEnd=0) {
	$MatchFinished=0;
	if(!$CompId) {
		$CompId=$_SESSION['TourId'];
	}
	if($Team) {
		$Table='TeamFinals';
		$Prefix='Tf';
	} else {
		$Table='Finals';
		$Prefix='Fin';
	}
	$m1=intval($Matchno/2)*2;
    $ArrowSide = $Matchno % 2;

	// winner status, closest, tie status, etc must be reset and recalculated
	// start selecting the data we will need of the match
	$q=safe_r_sql("select EvMatchMode, EvCheckGolds, EvCheckXNines, TarStars, EvFinalTargetType, if(EvGoldsChars='', ToGoldsChars, EvGoldsChars) as Golds, if(EvXNineChars='', ToXNineChars, EvXNineChars) as XNines, GrPhase, 
        f1.{$Prefix}MatchNo as MatchNo1, f1.{$Prefix}Score as Score1, f1.{$Prefix}SetScore as SetScore1, f1.{$Prefix}SetPoints as SetPoints1, f1.{$Prefix}SetPointsByEnd as SetPointsByEnd1, 
		f1.{$Prefix}WinnerSet as WinnerSet1, f1.{$Prefix}Tie as Tie1, rtrim(f1.{$Prefix}Arrowstring) as Arrowstring1, rtrim(f1.{$Prefix}Tiebreak) as Tiebreak1, f1.{$Prefix}TbClosest as TbClosest1, f1.{$Prefix}TbDecoded as TbDecoded1, f1.{$Prefix}ArrowPosition as ArrowPosition1,
		f1.{$Prefix}WinLose as WinLose1, f1.{$Prefix}Status as Status1, f1.{$Prefix}Confirmed as Confirmed1, f1.{$Prefix}IrmType as IrmType1, f1.{$Prefix}Golds as FinGolds1, f1.{$Prefix}XNines as FinXNines1, 
		f2.{$Prefix}MatchNo as MatchNo2, f2.{$Prefix}Score as Score2, f2.{$Prefix}SetScore as SetScore2, f2.{$Prefix}SetPoints as SetPoints2, f2.{$Prefix}SetPointsByEnd as SetPointsByEnd2, 
		f2.{$Prefix}WinnerSet as WinnerSet2, f2.{$Prefix}Tie as Tie2, rtrim(f2.{$Prefix}Arrowstring) as Arrowstring2, rtrim(f2.{$Prefix}Tiebreak) as Tiebreak2, f2.{$Prefix}TbClosest as TbClosest2, f2.{$Prefix}TbDecoded as TbDecoded2, f2.{$Prefix}ArrowPosition as ArrowPosition2,
		f2.{$Prefix}WinLose as WinLose2, f2.{$Prefix}Status as Status2, f2.{$Prefix}Confirmed as Confirmed2, f2.{$Prefix}IrmType as IrmType2, f2.{$Prefix}Golds as FinGolds2, f2.{$Prefix}XNines as FinXNines2
		from {$Table} f1
		inner join {$Table} f2 on f2.{$Prefix}Event=f1.{$Prefix}Event and f2.{$Prefix}Tournament=f1.{$Prefix}Tournament and f2.{$Prefix}MatchNo=f1.{$Prefix}MatchNo+1
		inner join Events on EvTournament=f1.{$Prefix}Tournament and EvCode=f1.{$Prefix}Event and EvTeamEvent=$Team
		inner join Grids on GrMatchNo=f1.{$Prefix}MatchNo
		inner join Tournament on ToId=EvTournament
		inner join Targets on TarId=EvFinalTargetType
		where f1.{$Prefix}MatchNo=$m1 and f1.{$Prefix}Tournament={$CompId} and f1.{$Prefix}Event=".StrSafe_DB($Event));
	if(!($r=safe_fetch($q))) {
		return $MatchFinished;
	}

	$UpdateSql1=[
		'FinScore' => "{$Prefix}Score=$r->Score1",
		'FinSetScore' => "{$Prefix}SetScore=$r->SetScore1",
		'FinSetPoints' => "{$Prefix}SetPoints=".StrSafe_DB($r->SetPoints1),
		'FinSetPointsByEnd' => "{$Prefix}SetPointsByEnd=".StrSafe_DB($r->SetPointsByEnd1),
		'FinWinnerSet' => "{$Prefix}WinnerSet=$r->WinnerSet1",
		'FinTie' => "{$Prefix}Tie=$r->Tie1",
		'FinTbClosest' => "{$Prefix}TbClosest=$r->TbClosest1",
		'FinTbDecoded' => "{$Prefix}TbDecoded=".StrSafe_DB($r->TbDecoded1),
		'FinWinLose' => "{$Prefix}WinLose=$r->WinLose1",
		'FinDateTime' => "{$Prefix}DateTime=now()",
		'FinStatus' => "{$Prefix}Status=$r->Status1",
		'FinConfirmed' => "{$Prefix}Confirmed=$r->Confirmed1",
	];
	$UpdateSql2=[
		'FinScore' => "{$Prefix}Score=$r->Score2",
		'FinSetScore' => "{$Prefix}SetScore=$r->SetScore2",
		'FinSetPoints' => "{$Prefix}SetPoints=".StrSafe_DB($r->SetPoints2),
		'FinSetPointsByEnd' => "{$Prefix}SetPointsByEnd=".StrSafe_DB($r->SetPointsByEnd2),
		'FinWinnerSet' => "{$Prefix}WinnerSet=$r->WinnerSet2",
		'FinTie' => "{$Prefix}Tie=$r->Tie2",
		'FinTbClosest' => "{$Prefix}TbClosest=$r->TbClosest2",
		'FinTbDecoded' => "{$Prefix}TbDecoded=".StrSafe_DB($r->TbDecoded2),
		'FinWinLose' => "{$Prefix}WinLose=$r->WinLose1",
		'FinDateTime' => "{$Prefix}DateTime=now()",
		'FinStatus' => "{$Prefix}Status=$r->Status2",
		'FinConfirmed' => "{$Prefix}Confirmed=$r->Confirmed2",
	];

	$rOrg=clone $r;

	// get all details for the match scoring system
	$obj=getEventArrowsParams($Event, $r->GrPhase, $Team, $CompId);

	// regular ends
	$Ends1=[];
	$Ends2=[];
	$Bits1=[];
	$Bits2=[];
	if($r->SetPoints1) {
		$Ends1=explode('|', $r->SetPoints1);
	}
	if($r->SetPoints2) {
		$Ends2=explode('|', $r->SetPoints2);
	}

	// tie break ends
	$TbEnds1=[];
	$TbEnds2=[];
	$LastSOBit1='';
	$LastSOBit2='';
	if($r->TbDecoded1) {
		$TbEnds1=explode(',', $r->TbDecoded1);
	}
	if($r->TbDecoded2) {
		$TbEnds2=explode(',', $r->TbDecoded2);
	}

	if($r->EvMatchMode) {
		$Score1=$r->SetScore1;
		$Score2=$r->SetScore2;
	} else {
		$Score1=$r->Score1;
		$Score2=$r->Score2;
	}
	$r->MatchScore1=$Score1;
	$r->MatchScore2=$Score2;

	// Should we recalculate everything?
	if($Recalculate) {
		// resets winner and tie situation
		$r->Tie1=0;
		$r->Tie2=0;
		$r->WinLose1=0;
		$r->WinLose2=0;
        $r->Status1=0;
        $r->Status2=0;
        $r->Confirmed1=0;
        $r->Confirmed2=0;

		// do we have arrowstrings?
		if($r->Arrowstring1 or $r->Arrowstring2 or ($r->Arrowstring1=='' AND $r->Arrowstring2=='')) {
			// Recalculate single ends
			$Ends1=[];
			$Ends2=[];

			if($r->Arrowstring1) {
				foreach(str_split(rtrim($r->Arrowstring1), $obj->arrows) as $bit) {
					$Ends1[]=ValutaArrowString($bit);
					$Bits1[]=$bit;
				}
			}
			$r->SetPoints1=implode('|',$Ends1);

			if($r->Arrowstring2) {
				foreach (str_split(rtrim($r->Arrowstring2), $obj->arrows) as $bit) {
					$Ends2[] = ValutaArrowString($bit);
					$Bits2[] = $bit;
				}
			}
			$r->SetPoints2=implode('|',$Ends2);
		}

		// do we have tie break arrows?
		if($r->Tiebreak1 or $r->Tiebreak2  or ($r->Tiebreak1=='' AND $r->Tiebreak2=='')) {
			// Recalculate single ends
			$TbEnds1=[];
			$TbEnds2=[];

			if($r->Tiebreak1) {
				foreach(str_split(rtrim($r->Tiebreak1), $obj->so) as $bit) {
					$TbEnds1[]=ValutaArrowString($bit);
					$LastSOBit1=$bit;
				}
			}
			$r->TbDecoded1=implode(',',$TbEnds1);

			if($r->Tiebreak2) {
				foreach(str_split(rtrim($r->Tiebreak2), $obj->so) as $bit) {
					$TbEnds2[]=ValutaArrowString($bit);
					$LastSOBit2=$bit;
				}
			}
			$r->TbDecoded2=implode(',',$TbEnds2);

			// if the values are the same and no closest is set, check if we have an implicit closest!
            // This is only valid for 1 X!!!
			if(strlen($LastSOBit1)==strlen($LastSOBit2) and strlen($LastSOBit1)==$obj->so and count($TbEnds1)==count($TbEnds2) and end($TbEnds1)==end($TbEnds2) and !$r->TbClosest1 and !$r->TbClosest2) {
                $XChar=($r->EvCheckGolds ? $r->Golds : ($r->EvCheckXNines? $r->XNines : null));
                list($Tot1, $MaxWeight1, $TotStars1, $TotX1, $LettersSorted1, $Letters1)=ValutaArrowStringSO(strtoupper($LastSOBit1), $XChar, $XChar?'A':null);
				list($Tot2, $MaxWeight2, $TotStars2, $TotX2, $LettersSorted2, $Letters2)=ValutaArrowStringSO(strtoupper($LastSOBit2), $XChar, $XChar?'A':null);
				// this can be done only if we do not have any stars left on the SO!
				if($TotStars1+$TotStars2==0) {
					if($TotX1==0 and $TotX2==1) {
						// Opponent2 has an X and Opponent 1 has none, so closest!
						$r->TbClosest2=1;
						$r->TbDecoded2.='+';
					} elseif($TotX1==1 and $TotX2==0) {
						// Opponent1 has an X and Opponent 2 has none, so closest!
						$r->TbClosest1=1;
						$r->TbDecoded1.='+';
					} elseif($MaxWeight1<$MaxWeight2) {
						// Opponent2 has a more "heavy" arrow!
						$r->TbClosest2=1;
						$r->TbDecoded2.='+';
					} elseif($MaxWeight1>$MaxWeight2) {
						// Opponent1 has a more "heavy" arrow!
						$r->TbClosest1=1;
						$r->TbDecoded1.='+';
					}
				}
			}
		}

		// do we have single ends scores?
		if($Ends1 or $Ends2) {
			// calculates the end details

			// is it a set event?
			if($r->EvMatchMode) {
				$Sets1=[];
				$Sets2=[];
				$Wins1=0;
				$Wins2=0;
				// we can reset the set count only for the complete ends!
				for($i=0; $i<min(count($Ends1), count($Ends2)); $i++) {
					if((!$Bits1 and !$Bits2) or (strlen($Bits1[$i]??'') == $obj->arrows and strlen($Bits2[$i]??'') == $obj->arrows)) {
						$CHK1=$Ends1[$i]+RaiseStars($Bits1[$i]??'', $r->TarStars,'','','', $r->EvFinalTargetType);
						$CHK2=$Ends2[$i]+RaiseStars($Bits2[$i]??'', $r->TarStars,'','','', $r->EvFinalTargetType);
						if($Ends1[$i]>$CHK2) {
							// even if all the stars of opp2 are raised the result is still lower
							$Sets1[]=2;
							$Sets2[]=0;
							$Wins1++;
						} elseif($CHK1<$Ends2[$i]) {
							// even if all the stars of opp1 are raised the result is still lower
							$Sets1[]=0;
							$Sets2[]=2;
							$Wins2++;
						} elseif($Ends1[$i]==$Ends2[$i] and $CHK1==$Ends1[$i] and $CHK2==$Ends2[$i]) {
							$Sets1[]=1;
							$Sets2[]=1;
						}
					}
				}
				$Score1=array_sum($Sets1);
				$Score2=array_sum($Sets2);
				$r->SetScore1=$Score1;
				$r->SetPointsByEnd1=implode('|',$Sets1);
				$r->WinnerSet1=$Wins1;
				$r->Score1=array_sum($Ends1);
				$r->SetScore2=$Score2;
				$r->SetPointsByEnd2=implode('|',$Sets2);
				$r->WinnerSet2=$Wins2;
				$r->Score2=array_sum($Ends2);
			} else {
				$Score1=array_sum($Ends1);
				$Score2=array_sum($Ends2);
				$r->Score1=$Score1;
				$r->Score2=$Score2;
			}
		} else {
            $Score1=0;
            $Score2=0;
        }

		$r->MatchScore1=$Score1;
		$r->MatchScore2=$Score2;
	}

	// All recalculation done, do we have a winner already?
	$CheckSO=0;
	if($r->EvMatchMode) {
		if($Score1>=$obj->winAt) {
			$r->WinLose1=1;
			$r->WinLose2=0;
			$MatchFinished=1;
			if($Score2==$obj->ends) {
				// it is a tie with winner already assigned
				$r->Tie1=1;
				$r->Tie2=0;
				$r->TbClosest2=0; // force a reset on closest2!
			} else {
				$r->Tie1=0;
				$r->Tie2=0;
				$r->TbClosest1=0; // force a reset on closest1!
				$r->TbClosest2=0; // force a reset on closest2!
			}
		} elseif ($Score2>=$obj->winAt) {
			$MatchFinished=1;
			$r->WinLose1=0;
			$r->WinLose2=1;
			if($Score1==$obj->ends) {
				// it is a tie with winner already assigned
				$r->Tie1=0;
				$r->Tie2=1;
				$r->TbClosest1=0; // force a reset on closest1!
			} else {
				$r->Tie1=0;
				$r->Tie2=0;
				$r->TbClosest1=0; // force a reset on closest1!
				$r->TbClosest2=0; // force a reset on closest2!
			}
		} elseif($Score1==$Score2 and $Score1==$obj->ends) {
			$CheckSO=1;
		}
	} elseif(strlen($r->Arrowstring1)==strlen($r->Arrowstring2) and (strlen($r->Arrowstring1)==$obj->MaxArrows or $r->Arrowstring1=='')) {
		if($Score1>$Score2) {
			$MatchFinished=1;
			$r->WinLose1=1;
			$r->WinLose2=0;
			$r->Tie1=0;
			$r->Tie2=0;
			$r->TbClosest1=0; // force a reset on closest1!
			$r->TbClosest2=0; // force a reset on closest2!
		} elseif($Score1<$Score2) {
			$MatchFinished=1;
			$r->WinLose1=0;
			$r->WinLose2=1;
			$r->Tie1=0;
			$r->Tie2=0;
			$r->TbClosest1=0; // force a reset on closest1!
			$r->TbClosest2=0; // force a reset on closest2!
		} elseif($r->EvCheckGolds and $r->FinGolds1>$r->FinGolds2) {
            $MatchFinished=1;
            $r->WinLose1=1;
            $r->WinLose2=0;
            $r->Tie1=0;
            $r->Tie2=0;
            $r->TbClosest1=0; // force a reset on closest1!
            $r->TbClosest2=0; // force a reset on closest2!
		} elseif($r->EvCheckGolds and $r->FinGolds1<$r->FinGolds2) {
			$MatchFinished=1;
			$r->WinLose1=0;
			$r->WinLose2=1;
			$r->Tie1=0;
			$r->Tie2=0;
			$r->TbClosest1=0; // force a reset on closest1!
			$r->TbClosest2=0; // force a reset on closest2!
		} elseif($r->EvCheckXNines and $r->FinXNines1>$r->FinXNines2) {
            $MatchFinished=1;
            $r->WinLose1=1;
            $r->WinLose2=0;
            $r->Tie1=0;
            $r->Tie2=0;
            $r->TbClosest1=0; // force a reset on closest1!
            $r->TbClosest2=0; // force a reset on closest2!
		} elseif($r->EvCheckXNines and $r->FinXNines1<$r->FinXNines2) {
			$MatchFinished=1;
			$r->WinLose1=0;
			$r->WinLose2=1;
			$r->Tie1=0;
			$r->Tie2=0;
			$r->TbClosest1=0; // force a reset on closest1!
			$r->TbClosest2=0; // force a reset on closest2!
		} else {
			$CheckSO=1;
		}
	}

	if($CheckSO) {
		// we have a tie, check TB
		$MatchFinished=1;
		$r->WinLose1=0;
		$r->WinLose2=0;
		if($r->Tie1) {
			$r->Tie2=0;
			$r->WinLose1=1;
			$r->TbClosest2=0;
			$r->TbDecoded2=str_replace('+','', $r->TbDecoded2);
			if($r->TbDecoded1 and $r->TbDecoded1==$r->TbDecoded2 and $r->TbClosest1) {
				$r->TbDecoded1.='+';
			}
		} elseif($r->Tie2) {
			$r->Tie1=0;
			$r->WinLose2=1;
			$r->TbClosest1=0;
			$r->TbDecoded1=str_replace('+','', $r->TbDecoded1);
			if($r->TbDecoded1 and $r->TbDecoded1==$r->TbDecoded2 and $r->TbClosest2) {
				$r->TbDecoded2.='+';
			}
		} elseif(end($TbEnds1)>end($TbEnds2) and strlen($r->Tiebreak1)==strlen($r->Tiebreak2) and strlen($r->Tiebreak1)%$obj->so==0) {
			$r->Tie1=1;
			$r->Tie2=0;
			$r->WinLose1=1;
			$r->TbClosest1=0;
			$r->TbClosest2=0;
			$r->TbDecoded1=str_replace('+','', $r->TbDecoded1);
			$r->TbDecoded2=str_replace('+','', $r->TbDecoded2);
		} elseif(end($TbEnds1)<end($TbEnds2) and strlen($r->Tiebreak1)==strlen($r->Tiebreak2) and strlen($r->Tiebreak1)%$obj->so==0) {
			$r->Tie1=0;
			$r->Tie2=1;
			$r->WinLose2=1;
			$r->TbClosest1=0;
			$r->TbClosest2=0;
			$r->TbDecoded1=str_replace('+','', $r->TbDecoded1);
			$r->TbDecoded2=str_replace('+','', $r->TbDecoded2);
		} elseif($r->TbClosest1) {
			$r->Tie1=1;
			$r->Tie2=0;
			$r->WinLose1=1;
			$r->TbClosest2=0;
			$r->TbDecoded2=str_replace('+','', $r->TbDecoded2);
			if($r->TbDecoded1 and $r->TbDecoded1==$r->TbDecoded2) {
				$r->TbDecoded1.='+';
			}
		} elseif($r->TbClosest2) {
			$r->Tie1=0;
			$r->Tie2=1;
			$r->WinLose2=1;
			$r->TbClosest1=0;
			$r->TbDecoded1=str_replace('+','', $r->TbDecoded1);
			if($r->TbDecoded1 and $r->TbDecoded1==$r->TbDecoded2) {
				$r->TbDecoded2.='+';
			}
		} else {
			// No winner, outcome is not decided yet
			$MatchFinished=0;
		}
		if($r->EvMatchMode) {
			$r->SetScore1+=$r->WinLose1;
			$r->SetScore2+=$r->WinLose2;
		}
	}

	$UpdateSql1['FinScore'] = "{$Prefix}Score=$r->Score1";
	$UpdateSql1['FinSetScore'] = "{$Prefix}SetScore=$r->SetScore1";
	$UpdateSql1['FinSetPoints'] = "{$Prefix}SetPoints=".StrSafe_DB($r->SetPoints1);
	$UpdateSql1['FinSetPointsByEnd'] = "{$Prefix}SetPointsByEnd=".StrSafe_DB($r->SetPointsByEnd1);
	$UpdateSql1['FinWinnerSet'] = "{$Prefix}WinnerSet=$r->WinnerSet1";
	$UpdateSql1['FinTbDecoded'] = "{$Prefix}TbDecoded=".StrSafe_DB($r->TbDecoded1);
	$UpdateSql1['FinWinLose'] = "{$Prefix}WinLose=$r->WinLose1";
	$UpdateSql1['FinTie'] = "{$Prefix}Tie=$r->Tie1";
    $UpdateSql1['FinStatus'] = "{$Prefix}Status=$r->Status1";
    $UpdateSql1['FinConfirmed'] = "{$Prefix}Confirmed=$r->Confirmed1";

	$UpdateSql2['FinScore'] = "{$Prefix}Score=$r->Score2";
	$UpdateSql2['FinSetScore'] = "{$Prefix}SetScore=$r->SetScore2";
	$UpdateSql2['FinSetPoints'] = "{$Prefix}SetPoints=".StrSafe_DB($r->SetPoints2);
	$UpdateSql2['FinSetPointsByEnd'] = "{$Prefix}SetPointsByEnd=".StrSafe_DB($r->SetPointsByEnd2);
	$UpdateSql2['FinWinnerSet'] = "{$Prefix}WinnerSet=$r->WinnerSet2";
	$UpdateSql2['FinTbDecoded'] = "{$Prefix}TbDecoded=".StrSafe_DB($r->TbDecoded2);
	$UpdateSql2['FinWinLose'] = "{$Prefix}WinLose=$r->WinLose2";
	$UpdateSql2['FinTie'] = "{$Prefix}Tie=$r->Tie2";
    $UpdateSql2['FinStatus'] = "{$Prefix}Status=$r->Status2";
    $UpdateSql2['FinConfirmed'] = "{$Prefix}Confirmed=$r->Confirmed2";

    if($r->IrmType1 or $r->IrmType2) {
		// we have an IRM, check what happens...
		if($r->IrmType1>=10 and $r->IrmType2>=10) {
			// both opponents lose their match
			// so removes the winner and ties if any
			$UpdateSql1['FinTie'] = "{$Prefix}Tie=0";
			$UpdateSql1['FinWinLose'] = "{$Prefix}WinLose=0";
			$UpdateSql2['FinTie'] = "{$Prefix}Tie=0";
			$UpdateSql2['FinWinLose'] = "{$Prefix}WinLose=0";
		} elseif($r->IrmType1>=10) {
			// Opp2 wins the match with a bye,
			$UpdateSql1['FinTie'] = "{$Prefix}Tie=0";
			$UpdateSql1['FinWinLose'] = "{$Prefix}WinLose=0";
			$UpdateSql2['FinTie'] = "{$Prefix}Tie=2";
			$UpdateSql2['FinWinLose'] = "{$Prefix}WinLose=1";
		} elseif($r->IrmType2>=10) {
			// Opp1 wins the match with a bye,
			$UpdateSql1['FinTie'] = "{$Prefix}Tie=2";
			$UpdateSql1['FinWinLose'] = "{$Prefix}WinLose=1";
			$UpdateSql2['FinTie'] = "{$Prefix}Tie=0";
			$UpdateSql2['FinWinLose'] = "{$Prefix}WinLose=0";
		}
	}

	// updates the 2 opponents
	safe_w_SQL("update {$Table} set ".implode(',', $UpdateSql1)." where {$Prefix}Tournament=$CompId and {$Prefix}Event=".StrSafe_DB($Event)." and {$Prefix}MatchNo=$r->MatchNo1");
	$Updated=(safe_w_affected_rows()>0);
	safe_w_SQL("update {$Table} set ".implode(',', $UpdateSql2)." where {$Prefix}Tournament=$CompId and {$Prefix}Event=".StrSafe_DB($Event)." and {$Prefix}MatchNo=$r->MatchNo2");
	$Updated=($Updated and safe_w_affected_rows()>0);

	if($MatchFinished and $Updated) {
		updateOdfTiming('U', $CompId, $Event, $Team, $r->MatchNo1);
	}

	if($Matchno < 4 AND $MatchFinished) {
		if($Team) {
			move2NextPhaseTeam($r->GrPhase, $Event, $r->MatchNo1, $CompId, true, true);
		} else {
			move2NextPhase($r->GrPhase, $Event, $r->MatchNo1, $CompId, true, $Pool, true);
		}
	}

	runJack("FinArrUpdate", $CompId, array("Event"=>$Event, "Team"=>$Team, "MatchNo"=>$r->MatchNo1, "ArrowStart"=>$ArrowStart, "ArrowEnd"=>$ArrowEnd, "Side"=>$ArrowSide, "TourId"=>$CompId));
	return $MatchFinished; // ALL GOOD!!!
}

// function MatchTotal($MatchNo, $EvCode, $TeamEvent=0, $ToId=0) {
// 	$CompId = $ToId;
// 	if(empty($CompId) && !empty($_SESSION['TourId']))
// 		$CompId = $_SESSION['TourId'];
//
// 	if(is_null($MatchNo) || is_null($EvCode))	//Devono esistere sia il MatchNo che l'evento
// 		return;
//
// 	$MatchFinished=false; // serve per vedere se il match è finito
// 	$TablePrefix = "Fin";
// 	$Select = "SELECT
// 			f.FinEvent as EvCode, f.FinMatchNo as MatchNo, f2.FinMatchNo as OppMatchNo, EvMatchMode, EvMatchArrowsNo,
// 			IF(f.FinDateTime>=f2.FinDateTime, f.FinDateTime, f2.FinDateTime) AS DateTime,
// 			f.FinScore AS Score, f.FinSetScore AS SetScore, f.FinTie as Tie, IFNULL(f.FinArrowString,'') as ArString, IFNULL(f.FinTieBreak,'') as TbString, IFNULL(f.FinTbClosest,'') as TbClosest,
// 			f2.FinScore AS OppScore, f2.FinSetScore AS OppSetScore, f2.FinTie as OppTie, IFNULL(f2.FinArrowString,'') as OppArString, IFNULL(f2.FinTieBreak,'') as OppTbString, IFNULL(f2.FinTbClosest,'') as OppTbClosest,
// 			GrPhase
// 		FROM Finals AS f
// 		INNER JOIN Finals AS f2 ON f.FinEvent=f2.FinEvent AND f.FinMatchNo=IF((f.FinMatchNo % 2)=0,f2.FinMatchNo-1,f2.FinMatchNo+1) AND f.FinTournament=f2.FinTournament
// 		INNER JOIN Events ON f.FinEvent=EvCode AND f.FinTournament=EvTournament AND EvTeamEvent=0
// 		INNER JOIN Grids ON f.FinMatchNo=GrMatchNo
// 		WHERE f.FinTournament=" . StrSafe_DB($CompId) . " AND (f.FinMatchNo % 2)=0 AND GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " AND f.FinEvent=" . StrSafe_DB($EvCode) . "
// 		ORDER BY f.FinEvent, f.FinMatchNo ";
//
// 	if($TeamEvent) {
// 		$TablePrefix = "Tf";
// 		$Select = "SELECT
// 				f.TfEvent as EvCode, f.TfMatchNo as MatchNo, f2.TfMatchNo as OppMatchNo, EvMatchMode, EvMatchArrowsNo,
// 				IF(f.TfDateTime>=f2.TfDateTime, f.TfDateTime, f2.TfDateTime) AS DateTime,
// 				f.TfScore AS Score, f.TfSetScore AS SetScore, f.TfTie as Tie, IFNULL(f.TfArrowString,'') as ArString, IFNULL(f.TfTieBreak,'') as TbString, IFNULL(f.TfTbClosest,'') as TbClosest,
// 				f2.TfScore AS OppScore, f2.TfSetScore AS OppSetScore, f2.TfTie as OppTie, IFNULL(f2.TfArrowString,'') as OppArString, IFNULL(f2.TfTieBreak,'') as OppTbString, IFNULL(f2.TfTbClosest,'') as OppTbClosest,
// 				GrPhase
// 			FROM TeamFinals AS f
// 			INNER JOIN TeamFinals AS f2 ON f.TfEvent=f2.TfEvent AND f.TfMatchNo=IF((f.TfMatchNo % 2)=0,f2.TfMatchNo-1,f2.TfMatchNo+1) AND f.TfTournament=f2.TfTournament
// 			INNER JOIN Events ON f.TfEvent=EvCode AND f.TfTournament=EvTournament AND EvTeamEvent=1
// 			INNER JOIN Grids ON f.TfMatchNo=GrMatchNo
// 			WHERE f.TfTournament=" . StrSafe_DB($CompId) . " AND (f.TfMatchNo % 2)=0 AND GrMatchNo=" . StrSafe_DB(($MatchNo % 2 == 0 ? $MatchNo:$MatchNo-1)) . " AND f.TfEvent=" . StrSafe_DB($EvCode) . "
// 			ORDER BY f.TfEvent, f.TfMatchNo ";
// 	}
//
// 	//print $Select . "<br>";exit;
// 	$MatchUpdated=false; // serve per aggiornare il timestamp
// 	$Rs=safe_r_sql($Select);
// 	if (safe_num_rows($Rs)==1) {
// 		$MyRow=safe_fetch($Rs);
// 		$obj=getEventArrowsParams($MyRow->EvCode,$MyRow->GrPhase,$TeamEvent,$CompId);
// 		$TotArrows=$obj->ends*$obj->arrows;
// 		$Winner=-1;
//
// 		// set winner... of Ties
// 		if($MyRow->Tie) {
// 			$Winner=$MyRow->MatchNo;
// 			$MatchFinished=true;
// 		} elseif ($MyRow->OppTie) {
// 			$Winner=$MyRow->OppMatchNo;
// 			$MatchFinished=true;
// 		}
//
// 		$Score=ValutaArrowString(substr($MyRow->ArString, 0, $TotArrows));
// 		$OppScore=ValutaArrowString(substr($MyRow->OppArString, 0, $TotArrows));
//
// 		if($MyRow->EvMatchMode==0) {
// 			$SetPointsAth=array();
// 			$SetPointsOpp=array();
// 			for($i=0; $i<$TotArrows; $i=$i+$obj->arrows) {
// 				//Cicla per tutte le volee dell'incontro
// 				$SetPointsAth[] = ValutaArrowString(substr($MyRow->ArString, $i, $obj->arrows));
// 				$SetPointsOpp[] = ValutaArrowString(substr($MyRow->OppArString, $i, $obj->arrows));
// 			}
// 			//Sistema Cumulativo
// 			if(($a1=strlen(str_replace(' ', '', $MyRow->ArString)))==$TotArrows
// 				and ($a2=strlen(str_replace(' ', '', $MyRow->OppArString)))==$TotArrows
// 				and ($t1=strlen(str_replace(' ', '', $MyRow->TbString))) == ($t2=strlen(str_replace(' ', '', $MyRow->OppTbString)))
// 				) {
// 				// all arrows have been shot from both sides...
//
//
// 				// if match is over establish the winner
// 				// only if not already decided by the tie
// 				// and if there are no doubts
// 				// and no SO are going on
// 				if($Winner==-1) {
// 					// No winner decided yet...
// 					$Proceed=(ctype_upper($MyRow->ArString.$MyRow->OppArString));
// 					//Da Remmare dopo ANKARA
// 					$Proceed=true;
// 					if(!$Proceed) {
// 						// check if the stars would make any change
// 						$Regexp='';
// 						$RaisedScore=$Score+RaiseStars(substr($MyRow->ArString, 0, $TotArrows), $Regexp, $MyRow->EvCode, $TeamEvent, $ToId);
// 						$RaisedOppScore=$OppScore+RaiseStars(substr($MyRow->OppArString, 0, $TotArrows), $Regexp, $MyRow->EvCode, $TeamEvent, $ToId);
// 						if($RaisedScore < $OppScore or $RaisedOppScore < $Score) {
// 							// Even with all stars "in" the ath will not make more than the opponent
// 							$Proceed=true;
// 						}
// 					}
// 					if($Proceed) {
// 						if($Score>$OppScore) {
// 							$Winner=$MyRow->MatchNo;
// 							$MatchFinished=true;
// 						} elseif($Score<$OppScore) {
// 							$Winner=$MyRow->OppMatchNo;
// 							$MatchFinished=true;
// 						} else {
// 							if( strlen(str_replace(' ', '', $MyRow->TbString))!=0
// 							    and (strlen(str_replace(' ', '', $MyRow->TbString)) % $obj->so) == 0
// 								and strlen(str_replace(' ', '', $MyRow->TbString))==strlen(str_replace(' ', '', $MyRow->OppTbString))
// 								) {
// 								// Verifico le stringhe CASE INSENSITIVE - in questo momento me ne frego degli "*"
// 								list($AthTbValue, $AthWeight, $AthStars, $AthNumX, $AthArrows) = ValutaArrowStringSO($MyRow->TbString);
// 								list($OppTbValue, $OppWeight, $OppStars, $OppNumX, $OppArrows) = ValutaArrowStringSO($MyRow->OppTbString);
//
// 								$MatchFinished=true;
//
// 								if($MyRow->TbClosest) {
// 									// Athlete 1 has at a closest to center
// 									$Winner = $MyRow->MatchNo;
// 									$WinnerId = $MyRow->MatchNo;
// 								} elseif($MyRow->OppTbClosest) {
// 									// Athlete 2 has one arrow closer to center
// 									$Winner = $MyRow->OppMatchNo;
// 									$WinnerId = $MyRow->OppMatchNo;
// 								} elseif($AthTbValue > $OppTbValue) {
// 									//TbString è maggiore di OppTbString --> il primo vince
// 									$Winner = $MyRow->MatchNo;
// 									$WinnerId = $MyRow->MatchNo;
// 								} elseif($AthTbValue < $OppTbValue) {
// 									//OppTbString è maggiore di TbString --> il secondo vince
// 									$Winner = $MyRow->OppMatchNo;
// 									$WinnerId = $MyRow->OppMatchNo;
// 								} elseif($AthNumX > $OppNumX) {
// 									// Athlete 1 has more Xs than Athlete 2
// 									$Winner = $MyRow->MatchNo;
// 									$WinnerId = $MyRow->MatchNo;
// 								} elseif($AthNumX < $OppNumX) {
// 									// Athlete 2 has more Xs than Athlete 1
// 									$Winner = $MyRow->OppMatchNo;
// 									$WinnerId = $MyRow->OppMatchNo;
// 								} else {
// 									$MatchFinished=false;
// 									if($AthArrows and $OppArrows) {
// 										if($AthArrows[0] > $OppArrows[0]) {
// 											$Winner = $MyRow->MatchNo;
// 											$WinnerId = $MyRow->MatchNo;
// 											$MatchFinished=true;
// 										} elseif($AthArrows[0] < $OppArrows[0]) {
// 											$Winner = $MyRow->OppMatchNo;
// 											$WinnerId = $MyRow->OppMatchNo;
// 											$MatchFinished=true;
// 										}
// 									}
// 								}
// 							}
//
// 						}
// 					}
// 				}
// 			} else {
// 				// match is not over, so if no byes reset the winner!
// 				if($MyRow->Tie!=2 and $MyRow->OppTie!=2) {
// 					$Winner=-1;
// 				}
// 			}
//
// 			$query="UPDATE "
// 				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
// 				. "SET "
// 				. "{$TablePrefix}WinLose=" . ($Winner==$MyRow->MatchNo ? '1' : '0') . ", "
// 				. "{$TablePrefix}Score=" . $Score . ", "
// 				. "{$TablePrefix}SetScore=0, "
// 				. "{$TablePrefix}SetPoints=" . StrSafe_DB(implode('|', $SetPointsAth)) . ", "
// 				. "{$TablePrefix}Tie=" . (($Score==$OppScore and $Winner==$MyRow->MatchNo) ? '1' : '0') . ", "
// 				. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
// 				. "WHERE "
// 				. " {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
// 				. " AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->MatchNo) . " "
// 				. " AND {$TablePrefix}Tournament=". StrSafe_DB($CompId);
//
// 			safe_w_sql($query);
// 			$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());
//
// 			//print $query.'<br><br>';
//
// 			$query="UPDATE "
// 				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
// 				. "SET "
// 				. "{$TablePrefix}WinLose=" . ($Winner==$MyRow->OppMatchNo ? '1' : '0') . ", "
// 				. "{$TablePrefix}Score=" . $OppScore . ", "
// 				. "{$TablePrefix}SetScore=0, "
// 				. "{$TablePrefix}SetPoints=" . StrSafe_DB(implode('|', $SetPointsOpp)) . ", "
// 				. "{$TablePrefix}Tie=" . (($Score==$OppScore and $Winner==$MyRow->OppMatchNo) ? '1' : '0') . ", "
// 				. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
// 				. "WHERE "
// 				. " {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
// 				. " AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " "
// 				. " AND {$TablePrefix}Tournament=". StrSafe_DB($CompId);
//
// 			safe_w_sql($query);
// 			$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());
//
// 			//print $query.'<br><br>';
// 		} else {
// 			//Sistema a Set
// 			$SetPointsAth=array();
// 			$SetPointsOpp=array();
// 			$AthSpBe=array();
// 			$OppSpBe=array();
// 			$SetAth=0;
// 			$SetOpp=0;
// 			$SetAthWin=0;
// 			$SetOppWin=0;
// 			$WinnerId=-1;
// 			for($i=0; $i<$TotArrows; $i=$i+$obj->arrows) {
// 				//Cicla per tutte le volee dell'incontro
// 				$AthEndString=rtrim(substr($MyRow->ArString, $i, $obj->arrows));
// 				$OppEndString=rtrim(substr($MyRow->OppArString, $i, $obj->arrows));
// 				$MatchString=$AthEndString.$OppEndString;
// 				$AthSetPoints=ValutaArrowString($AthEndString);
// 				$OppSetPoints=ValutaArrowString($OppEndString);
// 				$SetPointsAth[] = $AthSetPoints;
// 				$SetPointsOpp[] = $OppSetPoints;
//
//
// 				if(strpos($MatchString, ' ')===false and strlen($AthEndString) and strlen($AthEndString)==strlen($OppEndString) and strlen($AthEndString)==$obj->arrows) {
// 					// All arrows of the end have been shot
// 					$Proceed=ctype_upper($MatchString); // check if there are stars
// 					//Da Remmare dopo ANKARA
// 					$Proceed=true;
// 					if(!$Proceed) {
// 						// check if stars can change result
// 						$RegExp='';
// 						$AthSetPointsUpper=$AthSetPoints+RaiseStars($AthEndString, $RegExp, $MyRow->EvCode, $TeamEvent, $ToId);
// 						$OppSetPointsUpper=$OppSetPoints+RaiseStars($OppEndString, $RegExp, $MyRow->EvCode, $TeamEvent, $ToId);
// 						if($AthSetPointsUpper < $OppSetPoints or $OppSetPointsUpper < $AthSetPoints) {
// 							// even with all stars as higher points will the score beat the opponent's score
// 							$Proceed=true;
// 						}
// 					}
// 					if($Proceed) {
// 						if($AthSetPoints>$OppSetPoints) {
// 							$SetAth += 2;
// 							$SetAthWin++;
// 							$AthSpBe[]=2;
// 							$OppSpBe[]=0;
// 						} elseif($AthSetPoints<$OppSetPoints) {
// 							$SetOpp += 2;
// 							$SetOppWin++;
// 							$AthSpBe[]=0;
// 							$OppSpBe[]=2;
// 						} else {
// 							$SetAth++;
// 							$SetOpp++;
// 							$AthSpBe[]=1;
// 							$OppSpBe[]=1;
// 						}
// 					}
// 				} else if(strlen($AthEndString)!= 0 OR strlen($OppEndString) != 0) {
//                     $AthSpBe[]=0;
//                     $OppSpBe[]=0;
//                 }
// 			}
//
// 			if($SetAth > $obj->ends+2 or $SetOpp > $obj->ends+2) {
// 				$SetAth=0;
// 				$SetOpp=0;
// 			}
//
// 			if($SetAth==$SetOpp
//                 and strlen(str_replace(' ', '', $MyRow->TbString))!=0
// 				and (strlen(str_replace(' ', '', $MyRow->TbString))%$obj->so)==0
// 				and strlen(trim($MyRow->TbString))==strlen(trim($MyRow->OppTbString))
// 				) {
// 				// Verifico le stringhe CASE INSENSITIVE - in questo momento me ne frego degli "*"
// 				list($AthTbValue, $AthWeight, $AthStars, $AthNumX, $AthArrows) = ValutaArrowStringSO($MyRow->TbString);
// 				list($OppTbValue, $OppWeight, $OppStars, $OppNumX, $OppArrows) = ValutaArrowStringSO($MyRow->OppTbString);
//
//
// 				if($MyRow->TbClosest) {
// 					// Athlete 1 has at least one arrow set as closest to center
// 					$Winner = $MyRow->MatchNo;
// 					$WinnerId = $MyRow->MatchNo;
// 					$SetAth++;
// 				} elseif($MyRow->OppTbClosest) {
// 					// Athlete 2 has one arrow closer to center
// 					$Winner = $MyRow->OppMatchNo;
// 					$WinnerId = $MyRow->OppMatchNo;
// 					$SetOpp++;
// 				} elseif($AthTbValue > $OppTbValue) {
// 					//TbString è maggiore di OppTbString --> il primo vince
// 					$Winner = $MyRow->MatchNo;
// 					$WinnerId = $MyRow->MatchNo;
// 					$SetAth++;
// 				} elseif($AthTbValue < $OppTbValue) {
// 					 //OppTbString è maggiore di TbString --> il secondo vince
// 					 $Winner = $MyRow->OppMatchNo;
// 					 $WinnerId = $MyRow->OppMatchNo;
// 					 $SetOpp++;
// 				} elseif($AthNumX > $OppNumX) {
// 					// Athlete 1 has more Xs than Athlete 2
// 					$Winner = $MyRow->MatchNo;
// 					$WinnerId = $MyRow->MatchNo;
// 					$SetAth++;
// 				} elseif($AthNumX < $OppNumX) {
// 					// Athlete 2 has more Xs than Athlete 1
// 					$Winner = $MyRow->OppMatchNo;
// 					$WinnerId = $MyRow->OppMatchNo;
// 					$SetOpp++;
// 				} else {
// 					if($AthArrows and $OppArrows) {
// 						if($AthArrows[0] > $OppArrows[0]) {
// 							$Winner = $MyRow->MatchNo;
// 							$WinnerId = $MyRow->MatchNo;
// 							$SetAth++;
// 						} elseif($AthArrows[0] < $OppArrows[0]) {
// 							$Winner = $MyRow->OppMatchNo;
// 							$WinnerId = $MyRow->OppMatchNo;
// 							$SetOpp++;
// 						}
// 					}
// 				}
// 			} elseif($SetAth>=$obj->winAt) {
// 				$Winner = $MyRow->MatchNo;
// 			} elseif($SetOpp>=$obj->winAt) {
// 				$Winner = $MyRow->OppMatchNo;
// 			}
//
// 			$query="UPDATE "
// 				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
// 				. "SET "
// 				. "{$TablePrefix}WinLose=" . ($Winner==$MyRow->MatchNo ? '1' : '0') . ", "
// 				. "{$TablePrefix}Score=" . $Score . ", "
// 				. "{$TablePrefix}SetScore=" . $SetAth . ", "
// 				. "{$TablePrefix}SetPoints=" . StrSafe_DB(implode('|', $SetPointsAth)) . ", "
// 				. "{$TablePrefix}SetPointsByEnd=" . StrSafe_DB(implode('|', $AthSpBe)) . ", "
// 				. "{$TablePrefix}WinnerSet=" . $SetAthWin . ", "
// 				. "{$TablePrefix}Tie=" . ($WinnerId == $MyRow->MatchNo ? '1':'0') . ", "
// 				. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
// 				. "WHERE "
// 				. "{$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
// 				. "AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->MatchNo) . " "
// 				. "AND {$TablePrefix}Tournament=". StrSafe_DB($CompId);
// 			safe_w_sql($query);
// 			$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());
//
// 			$query="UPDATE "
// 				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
// 				. "SET "
// 				. "{$TablePrefix}WinLose=" . ($Winner==$MyRow->OppMatchNo ? '1' : '0') . ", "
// 				. "{$TablePrefix}Score=" . $OppScore . ", "
// 				. "{$TablePrefix}SetScore=" . $SetOpp . ", "
// 				. "{$TablePrefix}SetPoints=" . StrSafe_DB(implode('|', $SetPointsOpp)) . ", "
// 				. "{$TablePrefix}SetPointsByEnd=" . StrSafe_DB(implode('|', $OppSpBe)) . ", "
// 				. "{$TablePrefix}WinnerSet=" . $SetOppWin . ", "
// 				. "{$TablePrefix}Tie=" . ($WinnerId == $MyRow->OppMatchNo ? '1':'0') . ", "
// 				. "{$TablePrefix}DateTime={$TablePrefix}DateTime "
// 				. "WHERE "
// 				. "{$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
// 				. "AND {$TablePrefix}MatchNo=". StrSafe_DB($MyRow->OppMatchNo) . " "
// 				. "AND {$TablePrefix}Tournament=". StrSafe_DB($CompId);
// 			safe_w_sql($query);
// 			$MatchUpdated = ($MatchUpdated or safe_w_affected_rows());
// 			if($SetAth >= $obj->winAt or $SetOpp >= $obj->winAt) {
// 				$MatchFinished=true;
// 			}
// 		}
//
// 		if($MatchUpdated) {
// 			$query="UPDATE "
// 				. ($TeamEvent==0 ? "Finals" : "TeamFinals") . " "
// 				. "SET "
// 				. "{$TablePrefix}DateTime='".date('Y-m-d H:i:s')."' "
// 				. "WHERE "
// 				. " {$TablePrefix}Event=". StrSafe_DB($MyRow->EvCode) . " "
// 				. "AND {$TablePrefix}MatchNo in (". StrSafe_DB($MyRow->MatchNo) . ',' . StrSafe_DB($MyRow->OppMatchNo) . ") "
// 				. "AND {$TablePrefix}Tournament=". StrSafe_DB($CompId);
// 			safe_w_sql($query);
// 		}
// 		//Serve per ricalcolare le ranking, solo medaglie
//
// 		// run jack for finished match (DT_RESULT)
// 		//if($MatchFinished) {
// 			//runJack('MatchFinished', $CompId, array("Event"=>$EvCode ,"Team"=>$TeamEvent,"MatchNo"=>$MatchNo ,"TourId"=>$CompId));
// 		//}
//
// 		if($MatchNo < 4 and $MatchFinished) {
// 			if($TeamEvent) {
// 				move2NextPhaseTeam(NULL, $EvCode, $MatchNo, $CompId);
// 			} else {
// 				move2NextPhase(NULL, $EvCode, $MatchNo, $CompId);
// 			}
// 		}
//
// 		if(!isset($_REQUEST['Changed']) or $_REQUEST['Changed']) {
// 			// this should avoid launching ODF events if nothing changed
// 			runJack("FinArrUpdate", $CompId, array("Event"=>$EvCode ,"Team"=>$TeamEvent,"MatchNo"=>$MatchNo ,"TourId"=>$CompId));
// 		}
//
// 	}
// 	return $MatchFinished;
// }
