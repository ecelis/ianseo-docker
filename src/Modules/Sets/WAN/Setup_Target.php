<?php

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType)
{
    case 3:
        CreateDistanceNew($TourId, $TourType, 'RU16_', array(array('40m-1',40), array('40m-2',40)));
        CreateDistanceNew($TourId, $TourType, 'CU16_', array(array('30m-1',30), array('30m-2',30)));
        CreateDistanceNew($TourId, $TourType, 'BU16_', array(array('30m-1',30), array('30m-2',30)));
        CreateDistanceNew($TourId, $TourType, 'LU16_', array(array('30m-1',30), array('30m-2',30)));
        CreateDistanceNew($TourId, $TourType, 'TU16_', array(array('30m-1',30), array('30m-2',30)));
        CreateDistanceNew($TourId, $TourType, 'RU18_', array(array('60m-1',60), array('60m-2',60)));
        CreateDistanceNew($TourId, $TourType, 'CU18_', array(array('50m-1',50), array('50m-2',50)));
        CreateDistanceNew($TourId, $TourType, 'BU18_', array(array('40m-1',40), array('40m-2',40)));
        CreateDistanceNew($TourId, $TourType, 'LU18_', array(array('30m-1',30), array('30m-2',30)));
        CreateDistanceNew($TourId, $TourType, 'TU18_', array(array('30m-1',30), array('30m-2',30)));
        CreateDistanceNew($TourId, $TourType, 'RU21O', array(array('50m-1',70), array('50m-2',70)));
        CreateDistanceNew($TourId, $TourType, 'RU21M', array(array('70m-1',70), array('70m-2',70)));
        CreateDistanceNew($TourId, $TourType, 'RU21W', array(array('70m-1',70), array('70m-2',70)));
        CreateDistanceNew($TourId, $TourType, 'CU21_', array(array('50m-1',50), array('50m-2',50)));
        CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('50m-1',50), array('50m-2',50)));
        CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('30m-1',30), array('30m-2',30)));
        CreateDistanceNew($TourId, $TourType, 'TU21_', array(array('30m-1',30), array('30m-2',30)));

        break;
}

if($TourType==3) {
	// default Events
	CreateStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
$i=1;
switch($TourType)
{
    case 3:  // 70m/50m round
        CreateTargetFace($TourId, $i++, '10 ring 122cm', '%', '1', 5, 122, 5, 122);
        CreateTargetFace($TourId, $i++, '6 ring 80cm',   'C%', '1', 9, 80, 9, 80);
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 16, 4);

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

?>
