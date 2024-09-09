<?php

function CleanEvents($Events, $Field) {
	$ret='';
	if (is_array($Events)) {
		$tmp=array();
		foreach ($Events as $ev) {
			$e=explode('@', $ev);
			if(!in_array($e[0], $tmp) and preg_match('/^[0-9A-Z-]+$/i',$e[0])) {
				$tmp[]=$e[0];
			}
		}
		if ($tmp) {
			sort($tmp);
			$ret.= " AND $Field in (" . implode(", ", StrSafe_DB($tmp)) . ") ";
		}

		return $ret;
	}

    $e=$Events;
	if(strpos($Events,'@')!==false) {
        @list($e, $p) = explode('@', $Events);
    }
	if( preg_match('/^[0-9A-Z_%-]+$/i', $e)) {
		if(strstr($e,'%') or strstr($e, '_')) {
			$ret.= " AND $Field LIKE '" . $e . "' ";
		} else {
			$ret.= " AND $Field = '" . $e . "' ";
		}
	}

	return $ret;
}

function getTopEvent($Event, $Team, $TourId) {
	$q=safe_r_sql("select EvCodeParent from Events where EvTeamEvent=$Team and EvCode='$Event' and EvTournament=$TourId");
	if($r=safe_fetch($q) and $r->EvCodeParent) {
		return getTopEvent($r->EvCodeParent, $Team, $TourId);
	}

	return $Event;
}