<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/Obj_RankFactory.php');

$TourId = 0;
if(isset($_REQUEST['CompCode']) AND preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
	$TourId = getIdFromCode($_REQUEST['CompCode']);
}

$EvType = -1;
if(isset($_REQUEST['Type']) AND preg_match("/^[01]$/", $_REQUEST['Type'])) {
	$EvType = $_REQUEST['Type'];
}

$EvCode = '....';
if(isset($_REQUEST['Event']) AND preg_match("/^[a-z0-9_-]+$/i", $_REQUEST['Event'])) {
	$EvCode = $_REQUEST['Event'];
}

if(isset($_REQUEST["CutPosition"]) AND preg_match("/^[0-9]+$/i", $_REQUEST['CutPosition'])) {
	$options['cutRank'] = $_REQUEST['CutPosition'];
}

$Extended = false;
if($EvType==0 AND isset($_REQUEST['Extended']) AND preg_match("/^[01]$/", $_REQUEST['Extended'])) {
    $Extended  = ($_REQUEST['Extended']==1);
}

$json_array=array();

$options['tournament']=$TourId;
$options['events']=$EvCode;
$options['dist'] = 0;

$rank=null;
if($EvType) {
	$rank=Obj_RankFactory::create('AbsTeam',$options);
} else {
	$rank=Obj_RankFactory::create('Abs',$options);
}
$rank->read();
$Data=$rank->getData();

foreach($Data['sections'] as $kSec=>$vSec) {
	$json_array = Array("Event"=>$EvCode, "Type"=>$EvType, "Results"=>array());
    $numDist=0;
    $arrDist = array_fill(1, $numDist, 0);
    if($Extended) {
        $numDist = intval($vSec['meta']['numDist']);
        for ($i = 1; $i <= $numDist; $i++) {
            $arrDist[$i] = intval($vSec['meta']['distanceInfo']['dist_' . $i]['ends']) * intval($vSec['meta']['distanceInfo']['dist_' . $i]['arr']);
        }
    }
	foreach($vSec['items'] as $kItem=>$vItem) {
		$tmp = array("Rank"=>$vItem["rank"]);
		if($EvType==0) {
			$tmp += array("Id"=>$vItem["localbib"], "FamilyName"=>$vItem["familyname"], "GivenName"=>$vItem["givenname"], "NameOrder"=>$vItem["nameOrder"], "Gender"=>$vItem["gender"]);
		}
		$tmp += array("TeamCode"=>$vItem["countryCode"], "TeamName"=>$vItem["countryName"]);
		if($EvType==1) {
			$tmpAth=array();
			foreach($vItem["athletes"] as $kAth=>$vAth) {
				$tmpAth[$kAth]= array("Id"=>$vAth["localbib"], "FamilyName"=>$vAth["familyname"], "GivenName"=>$vAth["givenname"], "NameOrder"=>$vAth["nameOrder"], "Gender"=>$vAth["gender"]);
			}
			$tmp["Components"] = $tmpAth;
		}
		$tmp += array("Score"=>$vItem["score"], "Gold"=>$vItem["gold"], "XNine"=>$vItem["xnine"], "IRM"=> $vItem["irmText"],
            "CT"=>(($vItem["so"]==0 AND $vItem["ct"]>1) ? "1":"0"), "SO"=>($vItem["so"]>0 ? "1":"0"));
		if($vItem["so"]>0) {
            $tmp += array("SOValue" => $vItem["tiebreakDecoded"]);
        }
        if($Extended) {
            $tmpPartialArray = array();
            for($i=1; $i<=$numDist; $i++) {
                list($dRank,$dScore,$dGold,$dXnine) = explode('|',$vItem["dist_".$i]);
                $Arrows = array();
                for($j=0; $j<$arrDist[$i]; $j++) {
                    $Arrows[] = DecodeFromLetter($vItem["D".$i."Arrowstring"][$j]);
                }
                $tmpPartialArray[] = array("Partial"=>$i, "Rank"=>$dRank, "Score"=>$dScore, "Gold"=>$dGold, "XNine"=>$dXnine, "Arrows" => $Arrows);
            }
            $tmp += array("Partials"=>$tmpPartialArray);
        }
		$json_array["Results"][] = $tmp;
	}
}

// Return the json structure with the callback function that is needed by the app
SendResult($json_array);
