<?php
require_once(dirname(__FILE__) . '/config.php');

$RankType = '';
if(isset($_REQUEST['RankType']) && preg_match("/^(WCUP)+$/i", $_REQUEST['RankType'])) {
	$RankType = $_REQUEST['RankType'];
}

$EvType = -1;
if(isset($_REQUEST['Type']) && preg_match("/^[01]$/", $_REQUEST['Type'])) {
	$EvType = $_REQUEST['Type'];
}

$EvCode = false;
if(isset($_REQUEST['Event']) && preg_match("/^[a-z0-9_-]+$/i", $_REQUEST['Event'])) {
	$EvCode = $_REQUEST['Event'];
}

$CutRank=false;
if(isset($_REQUEST["CutPosition"]) && preg_match("/^[0-9]+$/i", $_REQUEST['CutPosition'])) {
	$CutRank = $_REQUEST['CutPosition'];
}

$json_array=array();

switch($RankType) {
	case 'WCUP':
		require_once('Modules/WorldCup/config.inc.php');
		require_once('Modules/WorldCup/elab.php');
		
		if($EvType==0) { 
			require_once('Modules/WorldCup/index-Common.php');
            foreach($ScoreInd as $ev=>$ath) {
				if($EvCode!=$ev) {
					continue;
				}
                $json_array = array("RankingName"=>$TVMainTitle, "Event"=>$ev, "EventName"=>"", "Type"=>"1", "Results"=>array(), "RankType"=>$RankType);
				$Select = "SELECT EvEventName as Name FROM Events WHERE EvCode=" . StrSafe_DB($EvCode) . " AND EvTeamEvent=0 AND EvTournament=" . StrSafe_DB(getIdFromCode($competitions[0]));
				$Rs=safe_r_sql($Select);
				if (safe_num_rows($Rs)==1) {
					$r = safe_fetch($Rs);
					$json_array["EventName"] = $r->Name;
				}


                foreach($PosInd[$ev] as $waid=>$rank) {
                    if($ScoreInd[$ev][$waid] == 0 and $ListInd[$ev][$waid]['Running'] == 0 ) {
                        continue;
                    }
					$tmpRow = array();
		
					$tmpRow["Rank"] = strval($rank);
					$tmpRow["Id"] = strval($waid);
					$tmpRow["FamilyName"] = $ListInd[$ev][$waid]['FamilyName'];
					$tmpRow["GivenName"] = $ListInd[$ev][$waid]['GivenName'];
					$tmpRow["NameOrder"] = $ListInd[$ev][$waid]['NameOrder'];
					$tmpRow["TeamCode"] = $ListInd[$ev][$waid]['NOC'];
					$tmpRow["TeamName"] = $ListInd[$ev][$waid]['Country'];
					$tmpRow["Points"] = strval($ScoreInd[$ev][$waid]);
					$tmpRow["Status"] = strval($ListInd[$ev][$waid]['Status']);
					$tmpRow["StatusText"] = WcupStatusTvText[$ListInd[$ev][$waid]['Status']];
					$tmpRow["Stages"] = array();


                    for($i=0; $i<count($compPlaces); $i++) {
						$Stage=array(
                            "Stage"=>strval($i+1),
                            "Points"=> ($ListInd[$ev][$waid]['QBonus'][$i]+abs($ListInd[$ev][$waid]['Points'][$i])),
                            "PresentInStage"=>boolval($ListInd[$ev][$waid]['isPresent'][$i]),
                            "StillCompeting"=>boolval($ListInd[$ev][$waid]['Rank'][$i]<0)
                        );
                        $tmpRow["Stages"][] = $Stage;
					}

                    if($CutRank === false || $CutRank>=$rank) {
						$json_array["Results"][] = $tmpRow;
					}
				}
			}
		}
		break;
	default:
		break;
}


// Return the json structure with the callback function that is needed by the app
SendResult($json_array);