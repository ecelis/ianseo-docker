<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default SubClasses
//CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardClasses($TourId, $TourType, $SubRule);

// default Distances

switch($TourType) {
    case 1: // 1440 round
        $dist90m = array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30));
        $dist70m = array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30));
        $dist60m = array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30));
        $dist50m = array(array('50m-1',50), array('40m-2',40), array('30m-3',30), array('20m-4',20));
        $dist40m = array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20));
        switch($SubRule) {
            case '1':
                CreateDistanceNew($TourId, $TourType, 'RM', $dist90m);
                CreateDistanceNew($TourId, $TourType, 'RW', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'R50M', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'R50W', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'R60M', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'R60W', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'R70M', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'R70W', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'RU21M', $dist90m);
                CreateDistanceNew($TourId, $TourType, 'RU21W', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'RU18M', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'RU18W', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'CM', $dist90m);
                CreateDistanceNew($TourId, $TourType, 'CW', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'C50M', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'C50W', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'C60M', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'C60W', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'C70M', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'C70W', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'CU21M', $dist90m);
                CreateDistanceNew($TourId, $TourType, 'CU21W', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'CU18M', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'CU18W', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'B_M', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'B_W', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'B_50%', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'B_60M', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'B_60W', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'B_70M', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'B_70W', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'B_U21M', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'B_U21W', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'B_U18M', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'B_U18W', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'LM', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'LU21M', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'L%W', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'L_0M', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'LU18M', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'RU16%', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'CU16%', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'B_U16%', $dist40m);
                CreateDistanceNew($TourId, $TourType, 'LU16%', $dist40m);
                CreateDistanceNew($TourId, $TourType, '%U14%', $dist40m);
            break;
            case '2':
                CreateDistanceNew($TourId, $TourType, 'RM', $dist90m);
                CreateDistanceNew($TourId, $TourType, 'RW', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'CM', $dist90m);
                CreateDistanceNew($TourId, $TourType, 'CW', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'B_M', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'B_W', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'LM', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'LW', $dist50m);
            break;
        }
    break;
    case 3: // 720 round based on Archery Australia QRE Policy
        $dist70m = array(array('70m-1',70), array('70m-2',70));
        $dist60m = array(array('60m-1',60), array('60m-2',60));
        $dist50m = array(array('50m-1',50), array('50m-2',50));
        $dist40m = array(array('40m-1',40), array('40m-2',40));
        $dist30m = array(array('30m-1',30), array('30m-2',30));
        switch($SubRule) {
            case '1':
                CreateDistanceNew($TourId, $TourType, 'R_', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'R50%', $dist60m);
                CreateDistanceNew($TourId, $TourType, '%60_', $dist50m);
                CreateDistanceNew($TourId, $TourType, '%70_', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'C_', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'C_0_', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'B_M', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'B_W', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'B__0_', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'RU21%', $dist70m);
                CreateDistanceNew($TourId, $TourType, 'CU21%', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'CU18%', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'B_U21M', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'B_U21W', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'B_U18M', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'B_U18W', $dist40m);
                CreateDistanceNew($TourId, $TourType, 'RU18%', $dist60m);
                CreateDistanceNew($TourId, $TourType, 'L_', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'L50_', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'LU21_', $dist50m);
                CreateDistanceNew($TourId, $TourType, 'LU18_', $dist40m);
                CreateDistanceNew($TourId, $TourType, 'RU16_', $dist40m);
                CreateDistanceNew($TourId, $TourType, 'CU16_', $dist40m);
                CreateDistanceNew($TourId, $TourType, 'B_U16M', $dist30m);
                CreateDistanceNew($TourId, $TourType, 'B_U16W', $dist30m);
                CreateDistanceNew($TourId, $TourType, 'LU16_', $dist30m);
                CreateDistanceNew($TourId, $TourType, '_U14_', $dist30m);
                CreateDistanceNew($TourId, $TourType, 'B_U14_', $dist30m);

            break;
            case '2':
            case '3':
                CreateDistanceNew($TourId, $TourType, 'R_', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'C_', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'B%', array(array('50m-1',50), array('50m-2',50)));
            break;
            // case '3':
            //     CreateDistanceNew($TourId, $TourType, 'R_', array(array('70m-1',70), array('70m-2',70)));
            //     CreateDistanceNew($TourId, $TourType, 'C_', array(array('50m-1',50), array('50m-2',50)));
            // break;
        }
    break;
    case 5: // WA900
        switch($SubRule) {
            case '1':
                CreateDistanceNew($TourId, $TourType, '_M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'RW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'CW', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'C_0M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'B%M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'B_W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'R50W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'C50W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'B_50W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'R_0M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'L_0%', array(array('50m-1',50), array('40m-2',40), array('30m-3',30)));
                CreateDistanceNew($TourId, $TourType, 'LW', array(array('50m-1',50), array('40m-2',40), array('30m-3',30)));
                CreateDistanceNew($TourId, $TourType, '%60W', array(array('50m-1',50), array('40m-2',40), array('30m-3',30)));
                CreateDistanceNew($TourId, $TourType, '%70W', array(array('50m-1',50), array('40m-2',40), array('30m-3',30)));
                CreateDistanceNew($TourId, $TourType, '%U21M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'RU21W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'CU21W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'B_U21W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'LU21W', array(array('50m-1',50), array('40m-2',40), array('30m-3',30)));
                CreateDistanceNew($TourId, $TourType, 'RU18M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'CU18M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'B_U18M', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'RU18W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'CU18W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
                CreateDistanceNew($TourId, $TourType, 'B_U18W', array(array('50m-1',50), array('40m-2',40), array('30m-3',30)));
                CreateDistanceNew($TourId, $TourType, 'LU18%', array(array('50m-1',50), array('40m-2',40), array('30m-3',30)));
                CreateDistanceNew($TourId, $TourType, 'RU16%', array(array('50m-1',50), array('40m-2',40), array('30m-3',30)));
                CreateDistanceNew($TourId, $TourType, 'CU16%', array(array('50m-1',50), array('40m-2',40), array('30m-3',30)));
                CreateDistanceNew($TourId, $TourType, 'B_U16%', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
                CreateDistanceNew($TourId, $TourType, 'LU16%', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
                CreateDistanceNew($TourId, $TourType, '%U14%', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
            break;
            case'2':
                CreateDistanceNew($TourId, $TourType, '%', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
            break;
        }
    break;    
}


if($TourType==3 or $TourType==6) {
	// default Events
	CreateStandardEvents($TourId, $SubRule, $TourType!=6, in_array($TourType,array(3,6,7,8)));

	// Classes in Events
	InsertStandardEvents($TourId, $SubRule, $TourType!=6);

	// Finals & TeamFinals
    if($SubRule==3) { // only create finals for Australian Open format
	    CreateFinals($TourId);
    }
}


// Default Target
switch($TourType) {
    case 1: // 1440 rounds
        CreateTargetFace($TourId, 1, 'Full Face Default-Recurve, Barebow, Longbow', 'REG-^[RLB].*$', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        CreateTargetFace($TourId, 2, 'Full Face Option', 'REG-^(?![RLB]).*$', '', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        CreateTargetFace($TourId, 3, '10 Ring / 6 Ring', 'REG-^(?!C).*$', '',TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        CreateTargetFace($TourId, 4, '5-X Default-Compound', 'REG-^[C].*$', '1',TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
    break;
    case 3: // 720 rounds
        CreateTargetFace($TourId, 1, 'Full Face Default', 'REG-^[^!C].*$', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        CreateTargetFace($TourId, 2, '5-X Default-Compound', 'REG-^[C].*$', '1',TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
    break;
    case 5:
        CreateTargetFace($TourId, 1, 'Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
    break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 24, 4);

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
