<?php
/*
Common Setup for "Run Archery"
*/

// global $TourId, $SubRule, $TourType;

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
// run archery has only 1 division, RA
CreateRunArcheryDivisions($TourId);

// default Classes
CreateRunArcheryClasses($TourId, 1);

// default Distance
CreateDistanceNew($TourId, $TourType, '%',  array(array('18 m',18)));

// Events to create are both Individual and teams, operator should remove the unwanted events
// default Events
CreateRunArcheryEvents($TourId, 1);

// Classes in Events
InsertRunArcheryEvents($TourId, $SubRule);

// Default Target
switch($SubRule) {
	case 1:
		CreateTargetFace($TourId, 1, '~Default', '%', '1', 7, 16);
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 24, 8);

// Update Tour details
$tourDetails=array(
	'ToCollation' => $tourCollation,
	'ToTypeName' => $tourDetTypeName,
	'ToNumDist' => $tourDetNumDist,
	'ToNumEnds' => $tourDetNumEnds,
	'ToMaxDistScore' => $tourDetMaxDistScore,
	'ToMaxFinIndScore' => $tourDetMaxFinIndScore,
	'ToMaxFinTeamScore' => $tourDetMaxFinTeamScore,
	'ToCategory' => $tourDetCategory,
	'ToElabTeam' => $tourDetElabTeam,
	'ToElimination' => $tourDetElimination,
	'ToGolds' => $tourDetGolds,
	'ToXNine' => $tourDetXNine,
	'ToGoldsChars' => $tourDetGoldsChars,
	'ToXNineChars' => $tourDetXNineChars,
	'ToDouble' => $tourDetDouble,
//	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);
