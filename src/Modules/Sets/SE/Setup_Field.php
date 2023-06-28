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
        CreateDistanceNew($TourId, $TourType, '_U13_', array(array('Omärkt-Vit', 0), array('Märkt-Vit', 0)));
        CreateDistanceNew($TourId, $TourType, 'R_',    array(array('Omärkt-Röd', 0), array('Märkt-Röd', 0)));
        CreateDistanceNew($TourId, $TourType, 'RU16_', array(array('Omärkt-Svt', 0), array('Märkt-Svt', 0)));
        CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('Omärkt-Röd', 0), array('Märkt-Röd', 0)));
        CreateDistanceNew($TourId, $TourType, 'R21_',  array(array('Omärkt-Blå', 0), array('Märkt-Blå', 0)));
        CreateDistanceNew($TourId, $TourType, 'R50_',  array(array('Omärkt-Blå', 0), array('Märkt-Blå', 0)));
        CreateDistanceNew($TourId, $TourType, 'R60_',  array(array('Omärkt-Blå', 0), array('Märkt-Blå', 0)));
        CreateDistanceNew($TourId, $TourType, 'B_',    array(array('Omärkt-Blå', 0), array('Märkt-Blå', 0)));
        CreateDistanceNew($TourId, $TourType, 'BU16_', array(array('Omärkt-SvV', 0), array('Märkt-SvV', 0)));
        CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('Omärkt-Blå', 0), array('Märkt-Blå', 0)));
        CreateDistanceNew($TourId, $TourType, 'B21_',  array(array('Omärkt-Svt', 0), array('Märkt-Svt', 0)));
        CreateDistanceNew($TourId, $TourType, 'B50_',  array(array('Omärkt-Svt', 0), array('Märkt-Svt', 0)));
        CreateDistanceNew($TourId, $TourType, 'B60_',  array(array('Omärkt-Svt', 0), array('Märkt-Svt', 0)));
        CreateDistanceNew($TourId, $TourType, 'C_',    array(array('Omärkt-Röd', 0), array('Märkt-Röd', 0)));
        CreateDistanceNew($TourId, $TourType, 'CU16_', array(array('Omärkt-Svt', 0), array('Märkt-Svt', 0)));
        CreateDistanceNew($TourId, $TourType, 'CU21_', array(array('Omärkt-Röd', 0), array('Märkt-Röd', 0)));
        CreateDistanceNew($TourId, $TourType, 'C21_',  array(array('Omärkt-Blå', 0), array('Märkt-Blå', 0)));
        CreateDistanceNew($TourId, $TourType, 'C50_',  array(array('Omärkt-Röd', 0), array('Märkt-Röd', 0)));
        CreateDistanceNew($TourId, $TourType, 'C60_',  array(array('Omärkt-Blå', 0), array('Märkt-Blå', 0)));
        CreateDistanceNew($TourId, $TourType, 'L_',    array(array('Omärkt-Svt', 0), array('Märkt-Svt', 0)));
        CreateDistanceNew($TourId, $TourType, 'LU16_', array(array('Omärkt-SvV', 0), array('Märkt-SvV', 0)));
        CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('Omärkt-Svt', 0), array('Märkt-Svt', 0)));
        CreateDistanceNew($TourId, $TourType, 'L21_',  array(array('Omärkt-SvV', 0), array('Märkt-SvV', 0)));
        CreateDistanceNew($TourId, $TourType, 'L50_',  array(array('Omärkt-SvV', 0), array('Märkt-SvV', 0)));
        CreateDistanceNew($TourId, $TourType, 'L60_',  array(array('Omärkt-SvV', 0), array('Märkt-SvV', 0)));
        CreateDistanceNew($TourId, $TourType, 'T_',    array(array('Omärkt-Svt', 0), array('Märkt-Svt', 0)));
        CreateDistanceNew($TourId, $TourType, 'TU16_', array(array('Omärkt-SvV', 0), array('Märkt-SvV', 0)));
        CreateDistanceNew($TourId, $TourType, 'TU21_', array(array('Omärkt-Svt', 0), array('Märkt-Svt', 0)));
        CreateDistanceNew($TourId, $TourType, 'T21_',  array(array('Omärkt-SvV', 0), array('Märkt-SvV', 0)));
        CreateDistanceNew($TourId, $TourType, 'T50_',  array(array('Omärkt-SvV', 0), array('Märkt-SvV', 0)));
        CreateDistanceNew($TourId, $TourType, 'T60_',  array(array('Omärkt-SvV', 0), array('Märkt-SvV', 0)));
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
        CreateTargetFace($TourId, $i++, 'Svart påle',     'REG-^[LT](U21)?[MW]{1,1}', '1', 6, 0, 6, 0);
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
