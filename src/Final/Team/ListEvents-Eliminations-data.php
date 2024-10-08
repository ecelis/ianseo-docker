<?php
define('debug',false);	// settare a true per l'output di debug

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
CheckTourSession(true);
checkACL(AclCompetition, AclReadWrite);

require_once('Common/Lib/CommonLib.php');
//require_once('Common/Lib/ArrTargets.inc.php');
//require_once('Common/Fun_Various.inc.php');

$JSON=array(
    'error'=>1,
    'html'=>'',
    'msg'=>'',
    );

$SQL='';

if(empty($_REQUEST['ev']) or empty($_REQUEST['act'])) {
    JsonOut($JSON);
}

$EventSQL="SELECT * FROM Events 
	inner join Tournament on ToId=EvTournament
	WHERE EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent='1' and EvCode=".StrSafe_DB($_REQUEST['ev'])."
	ORDER BY EvProgr ASC,EvCode ASC, EvTeamEvent ASC ";
$q=safe_r_sql($EventSQL);

$EVENT=safe_fetch($q);

if(!$EVENT) {
    JsonOut($JSON);
}

switch($_REQUEST['act']) {
    case 'get':
        if(!is_numeric($_REQUEST['type'])) {
            JsonOut($JSON);
        }
        $Type=intval($_REQUEST['type']);

		if($EVENT->EvElimType==5 and $Type!=5) {
			// destroys all the round robin thing!
			safe_w_sql("delete from RoundRobinGrids where RrGridTournament={$_SESSION['TourId']} and RrGridTeam=1 and RrGridEvent=".StrSafe_DB($_REQUEST['ev']));
			safe_w_sql("delete from RoundRobinGroup where RrGrTournament={$_SESSION['TourId']} and RrGrTeam=1 and RrGrEvent=".StrSafe_DB($_REQUEST['ev']));
			safe_w_sql("delete from RoundRobinLevel where RrLevTournament={$_SESSION['TourId']} and RrLevTeam=1 and RrLevEvent=".StrSafe_DB($_REQUEST['ev']));
			safe_w_sql("delete from RoundRobinMatches where RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=1 and RrMatchEvent=".StrSafe_DB($_REQUEST['ev']));
			safe_w_sql("delete from RoundRobinParticipants where RrPartTournament={$_SESSION['TourId']} and RrPartTeam=1 and RrPartEvent=".StrSafe_DB($_REQUEST['ev']));
			// check if there are still Round Robin EVents
			$q=safe_r_sql("select EvElimType from Events where EvTournament={$_SESSION['TourId']} and EvTeamEvent=1 and EvElimType=5");
			$_SESSION['HasRobin']=(safe_num_rows($q) ? 1 : 0);
		}

        switch($Type) {
	        case 5: // Round Robin, We only ask for number of levels in EvELim1
            	if($EVENT->EvElimType!=$Type) {
	                safe_w_sql("update Events set 
						EvElim1=2, EvE1Arrows=0, EvE1Ends=0, EvE1SO=0, 
						EvElim2=0, EvE2Arrows=0, EvE2Ends=0, EvE2SO=0, 
						EvFinalAthTarget=0, EvMatchArrowsNo=0,
						EvElimEnds=0, EvElimArrows=0, EvElimSO=0,
						EvElimType=$Type where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']));
		            $q=safe_r_sql($EventSQL);
		            $EVENT=safe_fetch($q);
					// check if the final level => Brackets is needed!
		            if($EVENT->EvFinalFirstPhase) {
						@include_once('Modules/RoundRobin/Lib.php');
						if(function_exists('CreateFinalLevel')) {
							CreateFinalLevel(0, $_REQUEST['ev'], $EVENT->EvNumQualified);
						}
		            }
		            $_SESSION['HasRobin']=1;
	            }
                $JSON['html'].='<tr><th colspan="2">'.get_text('R-Session', 'Tournament').'</th></tr>';
                $JSON['html'].='<tr><th>'.get_text('LevelsHelp', 'RoundRobin').'</th><td><input type="number" id="EvElim1" value="'.$EVENT->EvElim1.'" onchange="SetField(this)"></td></tr>';
                break;
            // case 4:
            // 	$ArNum=($EVENT->ToElabTeam==2 ? 1 : 3);
            // 	if($EVENT->EvElimType!=$Type) {
	        //         safe_w_sql("update Events set
			// 			EvElim1=0, EvE1Arrows=0, EvE1Ends=0, EvE1SO=0,
			// 			EvElim2=22, EvE2Arrows=0, EvE2Ends=0, EvE2SO=0,
			// 			EvFinalAthTarget=255, EvMatchArrowsNo=248,
			// 			EvElimEnds=6, EvElimArrows=$ArNum, EvElimSO=1,
			// 			EvElimType=$Type where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']));
		    //         $q=safe_r_sql($EventSQL);
		    //         $EVENT=safe_fetch($q);
	        //     }
            //     // World Archery 2018 elimination "Winner stays in" model
            //     $JSON['html'].='<tr><th colspan="2">'.get_text('StagePool4', 'ISK').'</th></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Archers').'</th><td><input type="number" id="EvElim2" value="'.$EVENT->EvElim2.'" onchange="SetField(this)"></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Ends', 'Tournament').'</th><td><input type="number" id="EvElimEnds" value="'.$EVENT->EvElimEnds.'" readonly></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Arrows', 'Tournament').'</th><td><input type="number" id="EvElimArrows" value="'.$EVENT->EvElimArrows.'" readonly></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('ShotOff', 'Tournament').'</th><td><input type="number" id="EvElimSO" value="'.$EVENT->EvElimSO.'" readonly></td></tr>';
            //     break;
            // case 3:
            // 	$ArNum=($EVENT->ToElabTeam==2 ? 1 : 3);
            // 	if($EVENT->EvElimType!=$Type) {
	        //         safe_w_sql("update Events set
			// 			EvElim1=0, EvE1Arrows=0, EvE1Ends=0, EvE1SO=0,
			// 			EvElim2=12, EvE2Arrows=0, EvE2Ends=0, EvE2SO=0,
			// 			EvFinalAthTarget=255, EvMatchArrowsNo=248,
			// 			EvElimEnds=6, EvElimArrows=$ArNum, EvElimSO=1,
			// 			EvElimType=$Type where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']));
		    //         $q=safe_r_sql($EventSQL);
		    //         $EVENT=safe_fetch($q);
	        //     }
            //     // World Games 2017 elimination "Winner stays in" model
            //     $JSON['html'].='<tr><th colspan="2">'.get_text('StagePool2', 'ISK').'</th></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Archers').'</th><td><input type="number" id="EvElim2" value="'.$EVENT->EvElim2.'" onchange="SetField(this)"></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Ends', 'Tournament').'</th><td><input type="number" id="EvElimEnds" value="'.$EVENT->EvElimEnds.'" readonly></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Arrows', 'Tournament').'</th><td><input type="number" id="EvElimArrows" value="'.$EVENT->EvElimArrows.'" readonly></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('ShotOff', 'Tournament').'</th><td><input type="number" id="EvElimSO" value="'.$EVENT->EvElimSO.'" readonly></td></tr>';
            //     break;
            // case 2:
            // 	$ArNum=($EVENT->ToElabTeam==2 ? 1 : 3);
            //     // Two "standard" elimination rounds
            // 	if($EVENT->EvElimType!=$Type) {
	        //         safe_w_sql("update Events set
			// 			EvElim1=16, EvE1Arrows=3, EvE1Ends=12, EvE1SO=1,
			// 			EvElim2=8, EvE2Arrows=3, EvE2Ends=8, EvE2SO=3,
			// 			EvFinalAthTarget=255, EvMatchArrowsNo=248,
			// 			EvElimEnds=5, EvElimArrows=$ArNum, EvElimSO=1,
			// 			EvElimType=2 where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']));
		    //         $q=safe_r_sql($EventSQL);
		    //         $EVENT=safe_fetch($q);
	        //     }
            //     $JSON['html'].='<tr><th colspan="2">'.get_text('StageE1', 'ISK').'</th></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Archers').'</th><td><input type="number" id="EvElim1" value="'.$EVENT->EvElim1.'" onchange="SetField(this)"></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Ends', 'Tournament').'</th><td><input type="number" id="EvE1Ends" value="'.$EVENT->EvE1Ends.'" onchange="SetField(this)"></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Arrows', 'Tournament').'</th><td><input type="number" id="EvE1Arrows" value="'.$EVENT->EvE1Arrows.'" onchange="SetField(this)"></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('ShotOff', 'Tournament').'</th><td><input type="number" id="EvE1SO" value="'.$EVENT->EvE1SO.'" onchange="SetField(this)"></td></tr>';
            // case 1:
            // 	$ArNum=($EVENT->ToElabTeam==2 ? 1 : 3);
            // 	if($EVENT->EvElimType!=$Type) {
	        //         safe_w_sql("update Events set
			// 			EvElim1=0, EvE1Arrows=0, EvE1Ends=0, EvE1SO=0,
			// 			EvElim2=8, EvE2Arrows=3, EvE2Ends=8, EvE2SO=1,
			// 			EvFinalAthTarget=255, EvMatchArrowsNo=248,
			// 			EvElimEnds=5, EvElimArrows=$ArNum, EvElimSO=1,
			// 			EvElimType=1 where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']));
		    //         $q=safe_r_sql($EventSQL);
		    //         $EVENT=safe_fetch($q);
	        //     }
            //     // One "standard" elimination round only
            //     $JSON['html'].='<tr><th colspan="2">'.get_text('StageE2', 'ISK').'</th></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Archers').'</th><td><input type="number" id="EvElim2" value="'.$EVENT->EvElim2.'" onchange="SetField(this)"></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Ends', 'Tournament').'</th><td><input type="number" id="EvE2Ends" value="'.$EVENT->EvE2Ends.'" onchange="SetField(this)"></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('Arrows', 'Tournament').'</th><td><input type="number" id="EvE2Arrows" value="'.$EVENT->EvE2Arrows.'" onchange="SetField(this)"></td></tr>';
            //     $JSON['html'].='<tr><th>'.get_text('ShotOff', 'Tournament').'</th><td><input type="number" id="EvE2SO" value="'.$EVENT->EvE2SO.'" onchange="SetField(this)"></td></tr>';
			//
            //     // set the elimination type accordingly
            //     safe_w_sql("update Events set EvElimType=$Type where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']));
            //     if($Type==1) {
            //         safe_w_sql("update Events set EvElim1=0, EvE1Arrows=0, EvE1Ends=0, EvE1SO=0 where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']));
            //     }
	        //     break;
            case 0:
            case 999:
                // set the elimination type accordingly
                safe_w_sql("update Events set EvElimType=0, EvElim1=0, EvE1Arrows=0, EvE1Ends=0, EvE1SO=0, EvElim2=0, EvE2Arrows=0, EvE2Ends=0, EvE2SO=0 where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']));
                break;
            default:
                JsonOut($JSON);
                break;
        }
        $JSON['error']=0;
        break;
    case 'set':
        $Value=intval($_REQUEST['value']);
        $JSON['error']=0;
        switch($_REQUEST['field']) {
            case 'EvElim1':
                if($Value) {
                    $SQL="update Events set EvElim1=$Value where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']);
                    if($EVENT->EvElimType==5) {
                        // Round Robin, destroys all what is more than that level
                        safe_w_sql("delete from RoundRobinGrids where RrGridLevel>$Value and RrGridTournament={$_SESSION['TourId']} and RrGridTeam=1 and RrGridEvent=".StrSafe_DB($_REQUEST['ev']));
                        safe_w_sql("delete from RoundRobinGroup where RrGrLevel>$Value and RrGrTournament={$_SESSION['TourId']} and RrGrTeam=1 and RrGrEvent=".StrSafe_DB($_REQUEST['ev']));
                        safe_w_sql("delete from RoundRobinLevel where RrLevLevel>$Value and RrLevTournament={$_SESSION['TourId']} and RrLevTeam=1 and RrLevEvent=".StrSafe_DB($_REQUEST['ev']));
                        safe_w_sql("delete from RoundRobinMatches where RrMatchLevel>$Value and RrMatchTournament={$_SESSION['TourId']} and RrMatchTeam=1 and RrMatchEvent=".StrSafe_DB($_REQUEST['ev']));
                        safe_w_sql("delete from RoundRobinParticipants where RrPartLevel>$Value and RrPartTournament={$_SESSION['TourId']} and RrPartTeam=1 and RrPartEvent=".StrSafe_DB($_REQUEST['ev']));
                    }
                } else {
                    $SQL="update Events set EvElim1=0, EvE1Ends=0, EvE1Arrows=0, EvE1SO=0, EvElimType=1 where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']);
                }
                break;
            case 'EvElim2':
            	// check which kind of elimination it is
				if($EVENT->EvElimType==3 and $Value>12) {
                    $JSON['error']=1;
                    $JSON['msg']=get_text('TooManyElimQualified', 'Errors', array(get_text('WG_Pool2'), 12));
                    JsonOut($JSON);
				} elseif($EVENT->EvElimType==4 and $Value>22) {
                    $JSON['error']=1;
                    $JSON['msg']=get_text('TooManyElimQualified', 'Errors', array(get_text('WA_Pool4'), 22));
                    JsonOut($JSON);
				} else {
	                if($Value) {
	                    $SQL="update Events set EvElim2=$Value where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']);
	                } else {
	                    $SQL="update Events set EvElim2=0, EvE2Ends=0, EvE2Arrows=0, EvE2SO=0, EvElimType=2 where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']);
	                }
				}
                break;
	        case 'EvE1Ends':
	        case 'EvE1Arrows':
	        case 'EvE1SO':
	        	// check if that much arrows can stay in the arrowstring structure
				$q=safe_r_sql("SELECT character_maximum_length FROM   information_schema.columns WHERE table_schema = DATABASE() and table_name = 'Eliminations'  AND column_name = 'ElArrowstring';");
	        	if($r=safe_fetch($q)) {
	        		$EVENT->{$_REQUEST['field']}=$Value;
	        		if($EVENT->EvE1Ends*$EVENT->EvE1Arrows > $r->character_maximum_length) {
	        			$JSON['error']=1;
	        			$JSON['msg']=get_text('TooManyArrows', 'Errors', $r->character_maximum_length);
	        			JsonOut($JSON);
			        }
		        }
                $SQL="update Events set {$_REQUEST['field']}=$Value where EvElim1>0 and EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']);
	        	break;
	        case 'EvE2Ends':
	        case 'EvE2Arrows':
	        case 'EvE2SO':
                $SQL="update Events set {$_REQUEST['field']}=$Value where EvElim2>0 and EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']);
	        	break;
	        case 'EvElimEnds':
	        case 'EvElimArrows':
	        case 'EvElimSO':
                $SQL="update Events set {$_REQUEST['field']}=$Value where EvTournament=" . StrSafe_DB($_SESSION['TourId']) . " AND EvTeamEvent=1 and EvCode=".StrSafe_DB($_REQUEST['ev']);
	        	break;
            default:
                $JSON['msg']=$_REQUEST['field'];
                $JSON['error']=1;
                JsonOut($JSON);
        }
        if($SQL) safe_w_sql($SQL);
        break;
}

JsonOut($JSON);
