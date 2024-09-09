<?php

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once('Common/Lib/Fun_DateTime.inc.php');
require_once('Scheduler/LibScheduler.php');

$JSON=array('error'=>1, 'msg'=>get_text('NoPrivilege', 'Errors'));

if(!CheckTourSession() or !hasACL(AclCompetition, AclReadWrite) or empty($_REQUEST['act'])) {
	JsonOut($JSON);
}

switch($_REQUEST['act']) {
	case 'update':
		$JSON['value']='';
		if(!empty($_REQUEST['end'])) {
			foreach($_REQUEST['end'] as $Session => $Distances) {
				foreach($Distances as $Dist => $Value) {
					safe_w_sql("insert into DistanceInformation set
						DiTournament={$_SESSION['TourId']},
						DiDistance=$Dist,
						DiSession=$Session,
						DiType='Q',
						DiEnds=$Value
						on duplicate key update
						DiEnds=$Value,
						DiTourRules=''
						");
					$JSON['error']=0;
				}
			}
		} elseif(!empty($_REQUEST['arr'])) {
			foreach($_REQUEST['arr'] as $Session => $Distances) {
				foreach($Distances as $Dist => $Value) {
					safe_w_sql("insert into DistanceInformation set
						DiTournament={$_SESSION['TourId']},
						DiDistance=$Dist,
						DiSession=$Session,
						DiType='Q',
						DiArrows=$Value
						on duplicate key update
						DiArrows=$Value,
						DiTourRules=''
						");
					$JSON['error']=0;
				}
			}
		} elseif(!empty($_REQUEST['startday'])) {
			$ret=InsertSchedDate($_REQUEST['startday']);
			$JSON['error']=$ret['error'];
			$Value=$ret['day'];
		} elseif(!empty($_REQUEST['starttime'])) {
			$ret=InsertSchedTime($_REQUEST['starttime']);
			$JSON['error']=$ret['error'];
			$Value=$ret['start'];
		} elseif(!empty($_REQUEST['warmtime'])) {
			$ret=InsertSchedTime($_REQUEST['warmtime'], 'Warm');
			$JSON['error']=$ret['error'];
			$Value=$ret['warmtime'];
		} elseif(!empty($_REQUEST['duration'])) {
			$ret=InsertSchedDuration($_REQUEST['duration']);
			$JSON['error']=$ret['error'];
			$Value=$ret['duration'];
		} elseif(!empty($_REQUEST['warmduration'])) {
			$ret=InsertSchedDuration($_REQUEST['warmduration'], 'Warm');
			$JSON['error']=$ret['error'];
			$Value=$ret['warmduration'];
		} elseif(!empty($_REQUEST['comment'])) {
			$ret=InsertSchedComment($_REQUEST['comment']);
			$JSON['error']=$ret['error'];
			$Value=$ret['options'];
		} elseif(!empty($_REQUEST['shoot'])) {
			foreach($_REQUEST['shoot'] as $Session => $Distances) {
				foreach($Distances as $Dist => $Value) {
					safe_w_sql("insert into DistanceInformation set
						DiTournament={$_SESSION['TourId']},
						DiDistance=$Dist,
						DiSession=$Session,
						DiType='Q',
						DiScoringEnds=$Value
						on duplicate key update
						DiScoringEnds=$Value,
						DiTourRules=''
						");
					$JSON['error']=0;
				}
			}
		} elseif(!empty($_REQUEST['offset'])) {
			foreach($_REQUEST['offset'] as $Session => $Distances) {
				foreach($Distances as $Dist => $Value) {
					safe_w_sql("insert into DistanceInformation set
						DiTournament={$_SESSION['TourId']},
						DiDistance=$Dist,
						DiSession=$Session,
						DiType='Q',
						DiScoringOffset=$Value
						on duplicate key update
						DiScoringOffset=$Value,
						DiTourRules=''
						");
					$JSON['error']=0;
				}
			}
		}
		break;
	default:
		JsonOut($JSON);
}

$JSON['value']=$Value;
JsonOut($JSON);