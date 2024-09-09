<?php
require_once(dirname(__FILE__) . '/config.php');
require_once('Common/Lib/ArrTargets.inc.php');
require_once('Final/Fun_MatchTotal.inc.php');

$TourId = 0;
if(isset($_REQUEST['CompCode']) && preg_match("/^[a-z0-9_.-]+$/i", $_REQUEST['CompCode'])) {
    $TourId = getIdFromCode($_REQUEST['CompCode']);
}

$EvType = -1;
if(isset($_REQUEST['Type']) && preg_match("/^[01]$/", $_REQUEST['Type'])) {
    $EvType = $_REQUEST['Type'];
}

$EvCode = '';
if(isset($_REQUEST['Event']) && preg_match("/^[a-z0-9_-]+$/i", $_REQUEST['Event'])) {
    $EvCode = $_REQUEST['Event'];
}

$MatchId = -1;
if(isset($_REQUEST['MatchId']) && preg_match("/^[0-9]+$/", $_REQUEST['MatchId'])) {
    $MatchId = $_REQUEST['MatchId'];
}

$JSON=array('Error'=>true);

if(!$TourId or empty($EvCode) or $EvType==-1 or $MatchId==-1) {
    SendResult($JSON);
}

$validData=GetMaxScores($EvCode, $MatchId, $EvType, $TourId);

//Parameters:
//arrSide,arrEnd,arrIndex,arrValue
//x,y,dist,rad
$arrSide = -1;
$arrEnd = -1;
$arrIndex = -1;
$arrValue = ' ';
if(isset($_REQUEST['arrSide']) && preg_match("/^[01]$/", $_REQUEST['arrSide'],$tmp)) {
    $arrSide = $tmp[0];
}
if(isset($_REQUEST['arrEnd']) && preg_match("/^[0-9]+$/", $_REQUEST['arrEnd'],$tmp)) {
    $arrEnd = $tmp[0];
}
if(isset($_REQUEST['arrIndex']) && preg_match("/^[0-9]+$/", $_REQUEST['arrIndex'],$tmp)) {
    if(($arrEnd<$validData['Ends'] AND $tmp[0]<$validData['ArrowsPerEnd']) OR ($arrEnd>=$validData['Ends'] AND $tmp[0]<$validData['SO'])) {
        $arrIndex = $tmp[0];
    }
}
if(isset($_REQUEST['arrValue'])) {
    $arrValue=GetLetterFromPrint($_REQUEST['arrValue'], $validData['Arrows']);
}

if($arrSide != -1 AND $arrEnd != -1 and $arrIndex != -1) {
    $JSON['Error']=false;
    $calculatedIndex = 0;
    if($arrEnd<$validData['Ends']) {
        $calculatedIndex = ($arrEnd*$validData['ArrowsPerEnd'])+$arrIndex;
    } else {
        $calculatedIndex = ($validData['Ends']*$validData['ArrowsPerEnd']) + (($arrEnd-$validData['Ends'])*$validData['SO']) + $arrIndex;
    }
    $calculatedIndex++;
    UpdateArrowString($MatchId+$arrSide, $EvCode, $EvType, $arrValue, $calculatedIndex, $calculatedIndex,$TourId);

	$Position=null;
	$Wind=null;
	$Time=null;
    if(isset($_REQUEST["X"]) AND preg_match("/^[\-0-9.]+$/", $_REQUEST['X']) AND isset($_REQUEST["Y"]) AND preg_match("/^[\-0-9.]+$/", $_REQUEST['Y']) AND isset($_REQUEST["R"]) AND preg_match("/^[0-9.]+$/", $_REQUEST['R']) AND isset($_REQUEST["Dist"]) AND preg_match("/^[0-9.]+$/", $_REQUEST['Dist'])) {
	    $Position=[
		    'X' => floatval($_REQUEST["X"]),
		    'Y' => floatval($_REQUEST["Y"]),
		    'R' => floatval($_REQUEST["R"]),
		    'D' => floatval($_REQUEST["Dist"]),
        ];
    }

	if(isset($_REQUEST['Ws']) and isset($_REQUEST['Wd']) and is_numeric($_REQUEST['Ws']) and is_numeric($_REQUEST['Wd'])) {
		$Wind=[
			'Ws'=>round(floatval($_REQUEST["Ws"]), 1),
			'Wd'=>intval($_REQUEST["Wd"]),
		];
	}

	if(isset($_REQUEST['T']) and is_numeric($_REQUEST['T'])) {
		$Time=intval($_REQUEST['T']);
	}

	if($Position or $Wind or $Time) {
		UpdateArrowPosition($MatchId+$arrSide, $EvCode, $EvType, $calculatedIndex, $Position, $Wind, $Time, $TourId);
	}
}


SendResult($JSON);