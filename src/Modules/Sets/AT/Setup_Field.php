<?php
/*

Common setup for Field

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, 'FIELD', $SubRule);

// default Classes
CreateStandardClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 9:
		CreateDistanceNew($TourId, $TourType, '%', array(array('12+12',0)));
		break;
	case 10:
	case 12:
		CreateDistanceNew($TourId, $TourType, '%', array(array('Unbekannt',0), array('Bekannt',0)));
		break;
}

// default Events
CreateStandardFieldEvents($TourId, $SubRule);

// insert class in events
InsertStandardFieldEvents($TourId, $SubRule);

// Elimination rounds
InsertStandardFieldEliminations($TourId, $SubRule);

// Finals & TeamFinals
CreateFinals($TourId);

// create Groups finals
switch($SubRule) {
	case 3:
		$query = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) 
			SELECT EvCode,GrMatchNo, $TourId, " . StrSafe_DB(date('Y-m-d H:i')) . " 
			FROM Events 
			INNER JOIN Grids ON GrMatchNo in (".implode(',', getPoolMatchNos()).") AND EvTeamEvent='0' AND EvTournament=$TourId
			where EvElimType=3";
		$rs=safe_w_sql($query);
		break;
	case 4:
		$query = "INSERT INTO Finals (FinEvent,FinMatchNo,FinTournament,FinDateTime) 
			SELECT EvCode,GrMatchNo, $TourId, " . StrSafe_DB(date('Y-m-d H:i')) . " 
			FROM Events 
			INNER JOIN Grids ON GrMatchNo in (".implode(',', getPoolMatchNosWA()).") AND EvTeamEvent='0' AND EvTournament=$TourId
			where EvElimType=4";
		$rs=safe_w_sql($query);
		break;
}

// Default Target
switch($TourType) {
    case 9:
        switch ($SubRule) {
            case 1:
            case 3:
                CreateTargetFace($TourId, 1, 'Weiß', 'REG-^RU13|^RU15|^CU13|^CU15|^BU13|^BU15|^LU13|^LU15|^TU13|^TU15', '1', TGT_FIELD, 0);
                CreateTargetFace($TourId, 2, 'Gelb', 'REG-^BU18|^LU18|^TU18|^LU21|^L60|^L[M|W]$', '1', TGT_FIELD, 0);
                CreateTargetFace($TourId, 3, 'Blau', 'REG-^RU18|^CU18|^BU21|^B60|^B[M|W]$|^TU21|^T60|^T[M|W]$', '1', TGT_FIELD, 0);
                CreateTargetFace($TourId, 4, 'Rot', 'REG-^RU21|^R60|^R[M|W]$|^CU21|^C60|^C[M|W]$', '1', TGT_FIELD, 0);
                break;
            case 2:
            case 4:
                CreateTargetFace($TourId, 1, 'Gelb', 'REG-^L', '1', TGT_FIELD, 0);
                CreateTargetFace($TourId, 2, 'Blau', 'REG-^[BT]', '1', TGT_FIELD, 0);
                CreateTargetFace($TourId, 3, 'Rot', 'REG-^[RC]', '1', TGT_FIELD, 0);
                break;
        }
        break;
    case 10:
    case 12:
        switch ($SubRule) {
            case 1:
            case 3:
                CreateTargetFace($TourId, 1, 'Weiß', 'REG-^RU13|^RU15|^CU13|^CU15|^BU13|^BU15|^LU13|^LU15|^TU13|^TU15', '1', TGT_FIELD, 0, TGT_FIELD, 0);
                CreateTargetFace($TourId, 2, 'Gelb', 'REG-^BU18|^LU18|^TU18|^LU21|^L60|^L[M|W]$', '1', TGT_FIELD, 0, TGT_FIELD, 0);
                CreateTargetFace($TourId, 3, 'Blau', 'REG-^RU18|^CU18|^BU21|^B60|^B[M|W]$|^TU21|^T60|^T[M|W]$', '1', TGT_FIELD, 0, TGT_FIELD, 0);
                CreateTargetFace($TourId, 4, 'Rot', 'REG-^RU21|^R60|^R[M|W]$|^CU21|^C60|^C[M|W]$', '1', TGT_FIELD, 0, TGT_FIELD, 0);
                break;
            case 2:
            case 4:
                CreateTargetFace($TourId, 1, 'Gelb', 'REG-^L', '1', TGT_FIELD, 0, TGT_FIELD, 0);
                CreateTargetFace($TourId, 2, 'Blau', 'REG-^[BT]', '1', TGT_FIELD, 0, TGT_FIELD, 0);
                CreateTargetFace($TourId, 3, 'Rot', 'REG-^[RC]', '1', TGT_FIELD, 0, TGT_FIELD, 0);
                break;
        }
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

