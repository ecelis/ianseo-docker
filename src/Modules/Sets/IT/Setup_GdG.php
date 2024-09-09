<?php
/*
15 	Type_GiochiGioventu

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (2)

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateDivision($TourId, 1, 'OL', 'Arco Olimpico', 1, 'R', 'R');
CreateDivision($TourId, 2, 'CO', 'Arco Compound', 1, 'C', 'C');

// default Classes
CreateStandardGdGClasses($TourId, $SubRule);

// default Distances
CreateDistanceNew($TourId, $TourType, '__G_', array(array('10m-1',10), array('10m-2',10)));
CreateDistanceNew($TourId, $TourType, '___0', array(array('15m-1',15), array('15m-2',15)));
CreateDistanceNew($TourId, $TourType, '___3', array(array('20m-1',20), array('20m-2',20)));
CreateDistanceNew($TourId, $TourType, '___4', array(array('20m-1',20), array('20m-2',20)));
CreateDistanceNew($TourId, $TourType, 'COX', array(array('20m-1',20), array('20m-2',20)));


// Default Target
CreateTargetFace($TourId, 2, '80 Ridotto', 'REG-^OL[MF][34]|^CO', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
CreateTargetFace($TourId, 1, '80 Completo', 'REG-^OL([G]|[MF]0)', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);

//Load a different set of names
$tourDetIocCode         = 'ITA_p';

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 32, 4);

// // Update Tour details
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
	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

?>
