<?php
/*

Common setup for Field

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId,$TourType, $SubRule);

// default Classes
CreateStandardClasses($TourId, $TourType, $SubRule);

// default Distances
switch($TourType) {
	case 9:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Proga',0)));
		break;
	case 10:
	case 12:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Neznane',0), array('Znane',0)));
		break;
}

// default Events
CreateStandardFieldEvents($TourId, $SubRule);

// insert class in events
InsertStandardFieldEvents($TourId, $SubRule);

// Finals & TeamFinals
CreateFinals($TourId);

// create Groups finals
$query = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) 
    SELECT EvCode,GrMatchNo, $TourId, " . StrSafe_DB(date('Y-m-d H:i')) . " 
    FROM Events 
    INNER JOIN Grids ON GrMatchNo in (".implode(',', getPoolMatchNosWA()).") AND EvTeamEvent='0' AND EvTournament=$TourId
    where EvElimType=4";
$rs=safe_w_sql($query);

// Default Target
switch($TourType) {
    case 9:
        CreateTargetFace($TourId, 1, 'Bel', 'REG-^ULU13|^ULU15|^SLU13|^SLU15|^GLU13|^GLU15|^DLU13|^DLU15|^TLU13|^TLU15', '1', TGT_FIELD, 0);
        CreateTargetFace($TourId, 2, 'Rumen', 'REG-^GLU18|^DLU18|^TLU18|^DLU21|^DL50|^DL[M|W]$', '1', TGT_FIELD, 0);
        CreateTargetFace($TourId, 3, 'Moder', 'REG-^ULU18|^SLU18|^GLU21|^GL50|^GL[M|W]$|^TLU21|^TL50|^TL[M|W]$', '1', TGT_FIELD, 0);
        CreateTargetFace($TourId, 4, 'Rdeč', 'REG-^ULU21|^UL50|^UL[M|W]$|^SLU21|^SL50|^SL[M|W]$', '1', TGT_FIELD, 0);
        break;
    case 10:
    case 12:
        CreateTargetFace($TourId, 1, 'Bel', 'REG-^ULU13|^ULU15|^SLU13|^SLU15|^GLU13|^GLU15|^DLU13|^DLU15|^TLU13|^TLU15', '1', TGT_FIELD, 0, TGT_FIELD, 0);
        CreateTargetFace($TourId, 2, 'Rumen', 'REG-^GLU18|^DLU18|^TLU18|^DLU21|^DL50|^DL[M|W]$', '1', TGT_FIELD, 0, TGT_FIELD, 0);
        CreateTargetFace($TourId, 3, 'Moder', 'REG-^ULU18|^SLU18|^GLU21|^GL50|^GL[M|W]$|^TLU21|^TL50|^TL[M|W]$', '1', TGT_FIELD, 0, TGT_FIELD, 0);
        CreateTargetFace($TourId, 4, 'Rdeč', 'REG-^ULU21|^UL50|^UL[M|W]$|^SLU21|^SL50|^SL[M|W]$', '1', TGT_FIELD, 0, TGT_FIELD, 0);
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, $tourDetNumEnds, 4);

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

