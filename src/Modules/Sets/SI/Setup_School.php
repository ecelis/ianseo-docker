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
CreateStandardDivisions($TourId,$TourType, $SubRule);

// default Classes
CreateClass($TourId, 1, 1, 8, 1, 'O1-3Ž', 'O1-3Ž', 'Učenke 1-3 razred', 1);
CreateClass($TourId, 2, 1, 8, 0, 'O1-3M', 'O1-3M', 'Učenci 1-3 razred', 1);
CreateClass($TourId, 3, 9, 11, 1, 'O4-6Ž', 'O4-6Ž', 'Učenke 4-6 razred', 1);
CreateClass($TourId, 4, 9, 11, 0, 'O4-6M', 'O4-6M', 'Učenci 4-6 razred', 1);
CreateClass($TourId, 5, 12, 14, 1, 'O7-9Ž', 'O7-9Ž', 'Učenke 7-9 razred', 1);
CreateClass($TourId, 6, 12, 14, 0, 'O7-9M', 'O7-9M', 'Učenci 7-9 razred', 1);
CreateClass($TourId, 7, 15, 18, 1, 'S1-4Ž', 'S1-4Ž', 'Dijakinje', 1);
CreateClass($TourId, 8, 15, 18, 0, 'S1-4M', 'S1-4M', 'Dijaki', 1);

// default Distances
if($SubRule == 1) {
    CreateDistanceNew($TourId, $TourType, '__O1-3%', array(array('10m',10)));
    CreateDistanceNew($TourId, $TourType, '__O4-6%', array(array('15m',15)));
    CreateDistanceNew($TourId, $TourType, '__O7-9%', array(array('18m',18)));
    CreateDistanceNew($TourId, $TourType, '__S1-4%', array(array('18m',18)));
    CreateTargetFace($TourId, 1, '80cm', 'REG-^ULO|^GLO', '',TGT_OUT_FULL, 80);
    CreateTargetFace($TourId, 2, '60cm', 'REG-^SLO', '',TGT_OUT_FULL, 60);
    CreateTargetFace($TourId, 3, '40cm', 'REG-^ULS|^GLS', '',TGT_OUT_FULL, 40);
    CreateTargetFace($TourId, 4, '40cm-3', 'REG-^SLS', '',TGT_IND_6_big10, 40);
} else {
    CreateDistanceNew($TourId, $TourType, '__O1-3%', array(array('15m',15)));
    CreateDistanceNew($TourId, $TourType, '__O4-6%', array(array('20m',20)));
    CreateDistanceNew($TourId, $TourType, '__O7-9%', array(array('25m',25)));
    CreateDistanceNew($TourId, $TourType, '__S1-4%', array(array('25m',25)));
    CreateTargetFace($TourId, 1, '122cm', 'REG-^ULO|^GLO', '',TGT_OUT_FULL, 122);
    CreateTargetFace($TourId, 2, '80cm', 'REG-^SLO|^ULS|^GLS', '',TGT_OUT_FULL, 80);
    CreateTargetFace($TourId, 3, '60cm-3', 'REG-^SLS', '',TGT_IND_6_big10, 60);
}

CreateDistanceInformation($TourId, $DistanceInfoArray[$SubRule], 32, 4);

CreateEvent($TourId, 1, 1, 0, 0, TGT_OUT_FULL, 5, 6, 3, 5, 6, 3, 'O',  'OŠ', 1, 0, 0, 0, 0, '', '', 0, 0);
CreateEvent($TourId, 2, 1, 0, 0, TGT_OUT_FULL, 5, 6, 3, 5, 6, 3, 'S',  'SŠ', 1, 0, 0, 0, 0, '', '', 0, 0);
foreach (array('UL','SL','GL') as $vDiv) {
    foreach(array('O1-3Ž','O1-3M','O4-6Ž','O4-6M','O7-9Ž','O7-9M','S1-4Ž','S1-4M') as $vClass) {
        InsertClassEvent($TourId, 1, 3, substr($vClass,0,1), $vDiv,  $vClass);
    }
}

// // Update Tour details
$tourDetails=array(
	'ToCollation' => $tourCollation,
	'ToTypeName' => $tourDetTypeName,
	'ToNumDist' => $tourDetNumDist,
	'ToNumEnds' => $tourDetNumEnds[$SubRule],
	'ToMaxDistScore' => $tourDetMaxDistScore[$SubRule],
	'ToMaxFinIndScore' => $tourDetMaxFinIndScore,
	'ToMaxFinTeamScore' => $tourDetMaxFinTeamScore,
	'ToCategory' => $tourDetCategory[$SubRule],
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


