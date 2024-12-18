<?php

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule, 'FIELD'); // $SubRule force to 1 (ALL CLASSES)

// default Distances
switch($TourType) {
    case 10:    // WA HF 24+24
    case 12:    // WA HF 12+12
        CreateDistanceNew($TourId, $TourType, '%', array(array('Omärkt', 0), array('Märkt', 0)));
        break;
}

// Default Target
$i=1;
switch($TourType) {
    case 10:    // WA HF 24+24
    case 12:    // WA HF 12+12
        CreateTargetFace($TourId, $i++, 'Röd påle',       'REG-^[C](U21|50)?[MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Röd påle',       'REG-^[R](U21)?[MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Blå påle',       'REG-^[B](U21)?[MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Blå påle',       'REG-^[C](21|60)[MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Blå påle',       'REG-^[R](21|50|60)[MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Blå påle',       'REG-^[T][MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Svart påle',     'REG-^[L][MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Svart påle',     'REG-^[LT](U21)[MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Svart påle',     'REG-^[RC](U16)[MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Svart påle',     'REG-^[B](21|50|60)[MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Svart/vit påle', 'REG-^[LT](U16|21|50|60)[MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Svart/vit påle', 'REG-^[B](U16)[MW]{1,1}', '1', 6, 0, 6, 0);
        CreateTargetFace($TourId, $i++, 'Vit påle',       'REG-^[RCBLT](U13)[MW]{1,1}', '1', 6, 0, 6, 0);
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, ($TourType==10 ? 24 : 12));

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
	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);
