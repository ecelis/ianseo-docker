<?php
/*
11 	3D 	(1 distance)

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (11)

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId,$TourType, $SubRule);

// default Classes
CreateStandardClasses($TourId, $TourType, $SubRule);

// default Distances
switch($TourType) {
	case 11:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Proga',0)));
		break;
	case 13:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Proga 1',0), array('Proga 2',0)));
		break;
}

// default Events
CreateStandard3DEvents($TourId, $SubRule);

// insert class in events
InsertStandard3DEvents($TourId, $SubRule);

// Finals & TeamFinals
CreateFinals($TourId);

// Default Target
switch($TourType) {
	case 11:
        CreateTargetFace($TourId, 1, 'Bel', 'REG-^GLU15|^DLU15|^TLU15|^GLU18|^DLU18|^TLU18', '1', TGT_3D, 0);
        CreateTargetFace($TourId, 2, 'Moder', 'REG-^ULU18|^ULU15|^SLU18|^SLU15|^LLU18|^LLU15', '1', TGT_3D, 0);
        CreateTargetFace($TourId, 3, 'Moder', 'REG-^GLU21|^GL50|^GL[M|W]$|^DLU21|^DL50|^DL[M|W]$|^TLU21|^TL50|^TL[M|W]$', '1', TGT_3D, 0);
        CreateTargetFace($TourId, 4, 'Rdeč', 'REG-^ULU21|^UL50|^UL[M|W]$|^SLU21|^SL50|^SL[M|W]$|^LLU21|^LL50|^LL[M|W]$', '1', TGT_3D, 0);
        CreateTargetFace($TourId, 5, 'Rdeč', 'REG-^S50|^S[M|W]$', '1', TGT_3D, 0);
        break;
    case 13:
        CreateTargetFace($TourId, 1, 'Bel', 'REG-^GLU15|^DLU15|^TLU15|^GLU18|^DLU18|^TLU18', '1', TGT_3D, 0, TGT_3D, 0);
        CreateTargetFace($TourId, 2, 'Moder', 'REG-^ULU18|^ULU15|^SLU18|^SLU15|^LLU18|^LLU15', '1', TGT_3D, 0, TGT_3D, 0);
        CreateTargetFace($TourId, 3, 'Moder', 'REG-^GLU21|^GL50|^GL[M|W]$|^DLU21|^DL50|^DL[M|W]$|^TLU21|^TL50|^TL[M|W]$', '1', TGT_3D, 0, TGT_3D, 0);
        CreateTargetFace($TourId, 4, 'Rdeč', 'REG-^ULU21|^UL50|^UL[M|W]$|^SLU21|^SL50|^SL[M|W]$|^LLU21|^LL50|^LL[M|W]$', '1', TGT_3D, 0, TGT_3D, 0);
        CreateTargetFace($TourId, 5, 'Rdeč', 'REG-^S50|^S[M|W]$', '1', TGT_3D, 0, TGT_3D, 0);
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, $tourDetNumEnds, 6);

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

