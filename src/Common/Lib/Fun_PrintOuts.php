<?php

function CleanEvents($Events, $Field) {
	$ret='';
	if (is_array($Events)) {
		$tmp=array();
		foreach ($Events as $ev) {
			@list($e, $p)=explode('@', $ev);
			if(!in_array($e, $tmp) and preg_match('/^[0-9A-Z-]+$/i',$e)) $tmp[]="'$e'";
		}
		if ($tmp) {
			sort($tmp);
			$ret.= " AND $Field in (" . implode(", ",$tmp) . ") ";
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
?>