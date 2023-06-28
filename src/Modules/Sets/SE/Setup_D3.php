<?php
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule, '3D'); //

// default Distances
switch($TourType) {
    case 11:    // WA 3D, 1 distance
        CreateDistanceNew($TourId, $TourType, 'C_',    array(array('Röd påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'C50_',  array(array('Röd påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'CU21_', array(array('Röd påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'R_',    array(array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'B_',    array(array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'L_',    array(array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'T_',    array(array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'C21_',  array(array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'C60_',  array(array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'TU21_', array(array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'RU16_', array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'CU16_', array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'BU16_', array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'LU16_', array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'TU16_', array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'R21_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'R50_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'R60_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'B21_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'B50_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'B60_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'L21_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'L50_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'L60_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'T21_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'T50_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'T60_',  array(array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, '_U13_', array(array('Vit påle', 0)));
        break;

    case 13:    // WA 3D, 2 distances
        CreateDistanceNew($TourId, $TourType, 'C_',    array(array('Röd påle', 0), array('Röd påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'C50_',  array(array('Röd påle', 0), array('Röd påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'CU21_', array(array('Röd påle', 0), array('Röd påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'R_',    array(array('Blå påle', 0), array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'B_',    array(array('Blå påle', 0), array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'L_',    array(array('Blå påle', 0), array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'T_',    array(array('Blå påle', 0), array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'C21_',  array(array('Blå påle', 0), array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'C60_',  array(array('Blå påle', 0), array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('Blå påle', 0), array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('Blå påle', 0), array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('Blå påle', 0), array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'TU21_', array(array('Blå påle', 0), array('Blå påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'RU16_', array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'CU16_', array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'BU16_', array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'LU16_', array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'TU16_', array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'R21_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'R50_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'R60_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'B21_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'B50_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'B60_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'L21_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'L50_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'L60_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'T21_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'T50_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, 'T60_',  array(array('Svart påle', 0), array('Svart påle', 0)));
        CreateDistanceNew($TourId, $TourType, '_U13_', array(array('Vit påle', 0), array('Vit påle', 0)));
        break;
}

// Default Target
$i=1;
switch($TourType) {
    case 11:    // WA 3D, 1 distance
        CreateTargetFace($TourId, $i++, 'Röd påle',   'REG-^[C](U21|50)?[MW]{1,1}', '1', 8, 0);
        CreateTargetFace($TourId, $i++, 'Blå påle',   'REG-^[RBLT](U21)?[MW]{1,1}', '1', 8, 0);
        CreateTargetFace($TourId, $i++, 'Blå påle',   'REG-^[C](21|60)[MW]{1,1}', '1', 8, 0);
        CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[RBLT](21|50|60)[MW]{1,1}', '1', 8, 0);
        CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[RCBLT](U16){1,1}[MW]{1,1}', '1', 8, 0);
        CreateTargetFace($TourId, $i++, 'Vit påle',   'REG-^[RCBLT](U13){1,1}[MW]{1,1}', '1', 8, 0);
        break;

    case 13:    // WA 3D, 2 distances
        CreateTargetFace($TourId, $i++, 'Röd påle',   'REG-^[C](U21|50)?[MW]{1,1}', '1', 8, 0, 8, 0);
        CreateTargetFace($TourId, $i++, 'Blå påle',   'REG-^[RBLT](U21)?[MW]{1,1}', '1', 8, 0, 8, 0);
        CreateTargetFace($TourId, $i++, 'Blå påle',   'REG-^[C](21|60)[MW]{1,1}', '1', 8, 0, 8, 0);
        CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[RBLT](21|50|60)[MW]{1,1}', '1', 8, 0, 8, 0);
        CreateTargetFace($TourId, $i++, 'Svart påle', 'REG-^[RCBLT](U16){1,1}[MW]{1,1}', '1', 8, 0, 8, 0);
        CreateTargetFace($TourId, $i++, 'Vit påle',   'REG-^[RCBLT](U13){1,1}[MW]{1,1}', '1', 8, 0, 8, 0);
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 20, 4);

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
