<?php

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType);

// default SubClasses
CreateStandardSubClasses($TourId);

// default Classes
CreateStandardClasses($TourId, $SubRule, '', $TourType);

// default Distances
switch($TourType)
{
	case 1:     // WA FITA
		CreateDistanceNew($TourId, $TourType, '_U13_', array(array('20m-1',20), array('20m-2',20), array('20m-3',20), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'BU16_', array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'LU16_', array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'TU16_', array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'RU16_', array(array('50m-1',50), array('40m-2',40), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'CU16_', array(array('50m-1',50), array('40m-2',40), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'TU21_', array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('50m-1',50), array('40m-2',40), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'RU21M', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'RU21W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'CU21M', array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'CU21W', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'B60_',  array(array('50m-1',50), array('40m-2',40), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'L60_',  array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'T60_',  array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'R60_',  array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'C60_',  array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));

		CreateDistanceNew($TourId, $TourType, 'B50_', array(array('50m-1',50), array('40m-2',40), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'L50_', array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'T50_', array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'R50_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'C50_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'B21_', array(array('50m-1',50), array('40m-2',40), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'L21_', array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'T21_', array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'R21_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'C21_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));

		CreateDistanceNew($TourId, $TourType, 'B_',   array(array('60m-1',60), array('50m-2',50), array('40m-3',40), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'L_',   array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'T_',   array(array('40m-1',40), array('30m-2',30), array('30m-3',30), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'RM',   array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'RW',   array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'CM',   array(array('90m-1',90), array('70m-2',70), array('50m-3',50), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'CW',   array(array('70m-1',70), array('60m-2',60), array('50m-3',50), array('30m-4',30)));
		break;

	case 3:     // WA 70m Round
		CreateDistanceNew($TourId, $TourType, '_U13_', array(array('20m-1',20), array('20m-2',20)));
		CreateDistanceNew($TourId, $TourType, 'RU16_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'CU16_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'BU16_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'LU16_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'TU16_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('60m-1',60), array('60m-2',60)));
		CreateDistanceNew($TourId, $TourType, 'CU21_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'TU21_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'R60_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'C60_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'B60_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'L60_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'T60_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'R50_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'C50_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'B50_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'L50_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'T50_', array(array('30m-1',30), array('30m-2',30)));

		CreateDistanceNew($TourId, $TourType, 'R21_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'C21_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'B21_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'L21_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'T21_', array(array('30m-1',30), array('30m-2',30)));
		CreateDistanceNew($TourId, $TourType, 'R_', array(array('70m-1',70), array('70m-2',70)));
		CreateDistanceNew($TourId, $TourType, 'C_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'B_', array(array('50m-1',50), array('50m-2',50)));
		CreateDistanceNew($TourId, $TourType, 'L_', array(array('40m-1',40), array('40m-2',40)));
		CreateDistanceNew($TourId, $TourType, 'T_', array(array('40m-1',40), array('40m-2',40)));
		break;

	case 37:  // Double 70m/50m round
		CreateDistanceNew($TourId, $TourType, '_U13_', array(array('20m-1',20), array('20m-2',20), array('20m-3',20), array('20m-4',20)));
		CreateDistanceNew($TourId, $TourType, 'RU16_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		CreateDistanceNew($TourId, $TourType, 'CU16_', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'BU16_', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'LU16_', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'TU16_', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('60m-1',60), array('60m-2',60), array('60m-3',60), array('60m-4',60)));
		CreateDistanceNew($TourId, $TourType, 'CU21_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
		CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		CreateDistanceNew($TourId, $TourType, 'TU21_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		CreateDistanceNew($TourId, $TourType, 'R60_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
		CreateDistanceNew($TourId, $TourType, 'C60_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		CreateDistanceNew($TourId, $TourType, 'B60_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		CreateDistanceNew($TourId, $TourType, 'L60_', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'T60_', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'R50_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
		CreateDistanceNew($TourId, $TourType, 'C50_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
		CreateDistanceNew($TourId, $TourType, 'B50_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		CreateDistanceNew($TourId, $TourType, 'L50_', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'T50_', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));

		CreateDistanceNew($TourId, $TourType, 'R21_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
		CreateDistanceNew($TourId, $TourType, 'C21_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		CreateDistanceNew($TourId, $TourType, 'B21_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		CreateDistanceNew($TourId, $TourType, 'L21_', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'T21_', array(array('30m-1',30), array('30m-2',30), array('30m-3',30), array('30m-4',30)));
		CreateDistanceNew($TourId, $TourType, 'R_', array(array('70m-1',70), array('70m-2',70), array('70m-3',70), array('70m-4',70)));
		CreateDistanceNew($TourId, $TourType, 'C_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
		CreateDistanceNew($TourId, $TourType, 'B_', array(array('50m-1',50), array('50m-2',50), array('50m-3',50), array('50m-4',50)));
		CreateDistanceNew($TourId, $TourType, 'L_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		CreateDistanceNew($TourId, $TourType, 'T_', array(array('40m-1',40), array('40m-2',40), array('40m-3',40), array('40m-4',40)));
		break;

	case 5:     // WA 900 Round
		CreateDistanceNew($TourId, $TourType, '_U13_', array(array('20m-1',20), array('20m-2',20), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, '_U16_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'L21_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'L50_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'L60_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'TU21_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'T21_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'T50_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'T60_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'CU21_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'R60_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'C60_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'B60_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'R50_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'C50_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'B50_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'R21_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'C21_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'B21_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'R_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'B_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'C_', array(array('60m-1',60), array('50m-2',50), array('40m-3',40)));
		CreateDistanceNew($TourId, $TourType, 'L_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		CreateDistanceNew($TourId, $TourType, 'T_', array(array('40m-1',40), array('30m-2',30), array('20m-3',20)));
		break;

	case 6:     // WA Indoor 18, 2 distances
	case 22:    // WA Indoor 18, 1 distance
		CreateDistanceNew($TourId, $TourType, '_U13_', array(array('12m-1',12), array('12m-2',12)));
		CreateDistanceNew($TourId, $TourType, '_U16_', array(array('12m-1',12), array('12m-2',12)));
		CreateDistanceNew($TourId, $TourType, '_U21_', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '_21_', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '_50_', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '_60_', array(array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '_M', array(array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, '_W', array(array('18m-1',18), array('18m-2',18)));
		break;

	case 7:     // WA Indoor 25
		CreateDistanceNew($TourId, $TourType, '_U13_', array(array('12m-1',12), array('12m-2',12)));
		CreateDistanceNew($TourId, $TourType, '_U16_', array(array('12m-1',12), array('12m-2',12)));
		CreateDistanceNew($TourId, $TourType, '_U21_', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '_21_', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '_50_', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '_60_', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '_M', array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, '_W', array(array('25m-1',25), array('25m-2',25)));
		break;

	case 39:    // 36Arr70mRound
		CreateDistanceNew($TourId, $TourType, 'C_', array(array('50m',50)));
        CreateDistanceNew($TourId, $TourType, 'CU21_', array(array('50m',50)));
		CreateDistanceNew($TourId, $TourType, 'C21_', array(array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'C50_', array(array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'C60_', array(array('40m',40)));
		CreateDistanceNew($TourId, $TourType, 'R_', array(array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'B_', array(array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'R21_', array(array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'R50_', array(array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'R60_', array(array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'CU16_', array(array('30m',30)));
		CreateDistanceNew($TourId, $TourType, 'RU16_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'BU16_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'B21_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'B50_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'B60_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'T_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'TU16_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'TU21_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'T21_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'T50_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'T60_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'L_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LU16_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'L21_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'L50_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, 'L60_', array(array('20m',20)));
		CreateDistanceNew($TourId, $TourType, '_U13_', array(array('20m',20)));
		break;
}

if($TourType==6 || $TourType==3 || $TourType==37 || $TourType==1) {
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
	case 1:  // Full FITA
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '_U13_', '1', 5, 122, 5, 122, 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '10 ring 122/80cm', 'REG-^[RBLT](U16|U21|21|50|60)?[MW]', '1',  5, 122, 5, 122, 5, 80, 5, 80);
		CreateTargetFace($TourId, $i++, '10 ring 122/80cm', 'REG-^[C](U16|U21|21|50|60)?[MW]', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		break;

	case 3:  // 70m/50m round
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '_U13_', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '10 ring 122cm', 'REG-^[RBLT](U16|U21|21|50|60)?[MW]', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[C](U16|U21|21|50|60)?[MW]', '1', 9, 80, 9, 80);
		break;

    case 37: // Double 70m/50m round
        CreateTargetFace($TourId, $i++, '10 ring 122cm', '_U13_', '1', 5, 122, 5, 122, 5, 122, 5, 122);
        CreateTargetFace($TourId, $i++, '10 ring 122cm', 'REG-^[RBLT](U16|U21|21|50|60)?[MW]', '1', 5, 122, 5, 122, 5, 122, 5, 122);
        CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[C](U16|U21|21|50|60)?[MW]', '1', 9, 80, 9, 80, 9, 80, 9, 80);
        break;

	case 5:  // 900 round
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '%', '1', 5, 122, 5, 122, 5, 122);
		break;

	case 6:  // Indoor 18m, 2 Dist - 60 arrows
		CreateTargetFace($TourId, $i++, '10 ring 60cm', '_U13_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-^[R](U16|U21|21|50|60)?[MW]', '1', 2, 40, 2, 40);
        CreateTargetFace($TourId, $i++, '10 ring 40cm', 'REG-^[R](U16|U21|21|50|60)?[MW]', '0', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-^[C](U16|U21|21|50|60)?[MW]', '1', 4, 40, 4, 40);
        CreateTargetFace($TourId, $i++, '10 ring 40cm', 'REG-^[C](U16|U21|21|50|60)?[MW]', '0', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'REG-^[B](U16|U21|21|50|60)?[MW]', '1', 1, 40, 1, 40);
        CreateTargetFace($TourId, $i++, '3 spot', 'REG-^[B](U16|U21|21|50|60)?[MW]', '0', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[LT](U16|U21|21|50|60)?[MW]', '1', 1, 60, 1, 60);
		break;

	case 7:  // Indoor 25m, 2 Dist - 60 arrows
        CreateTargetFace($TourId, $i++, '10 ring 60cm', '_U13_', '1', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '10 ring 40cm', 'REG-^[RB](U16)[MW]', '1', 1, 40, 1, 40);
        CreateTargetFace($TourId, $i++, '3 spot 40cm', 'REG-^[RB](U16)[MW]', '0', 2, 40, 2, 40);
        CreateTargetFace($TourId, $i++, '10 ring 40cm', 'REG-^[C](U16)[MW]', '1', 3, 40, 3, 40);
        CreateTargetFace($TourId, $i++, '3 spot 40cm', 'REG-^[C](U16)[MW]', '0', 4, 40, 4, 40);
        CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[LT](U16)[MW]', '1', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '3 spot 60cm', 'REG-^[R](U21|21|50|60)?[MW]', '1', 2, 60, 2, 60);
        CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[R](U21|21|50|60)?[MW]', '0', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '3 spot 60cm', 'REG-^[C](U21|21|50|60)?[MW]', '1', 4, 60, 4, 60);
        CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[C](U21|21|50|60)[MW]', '0', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[BLT](U21|21|50|60)?[MW]', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot 60cm', 'REG-^[BLT](U21|21|50|60)?[MW]', '0', 2, 60, 2, 60);
		break;

	case 22:  // Indoor 18m, 1 Dist - 30 arrows
		CreateTargetFace($TourId, $i++, '10 ring 60cm', '_U13_', '1', 1, 60);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-^[R](U16|U21|21|50|60)?[MW]', '1', 2, 40);
        CreateTargetFace($TourId, $i++, '10 ring 40cm', 'REG-^[R](U16|U21|21|50|60)?[MW]', '0', 1, 40);
		CreateTargetFace($TourId, $i++, '3 spot', 'REG-^[C](U16|U21|21|50|60)?[MW]', '1', 4, 40);
        CreateTargetFace($TourId, $i++, '10 ring 40cm', 'REG-^[C](U16|U21|21|50|60)?[MW]', '0', 3, 40);
		CreateTargetFace($TourId, $i++, '10 ring 40cm', 'REG-^[B](U16|U21|21|50|60)?[MW]', '1', 1, 40);
        CreateTargetFace($TourId, $i++, '3 spot', 'REG-^[B](U16|U21|21|50|60)?[MW]', '0', 2, 40);
		CreateTargetFace($TourId, $i++, '10 ring 60cm', 'REG-^[LT](U16|U21|21|50|60)?[MW]', '1', 1, 60);
		break;

	case 39:  // SBF36 round, 1 Dist
		CreateTargetFace($TourId, $i++, '10 ring 122cm', '_U13_', '1', 5, 122);
		CreateTargetFace($TourId, $i++, '10 ring 80cm', 'REG-^[RBLT](U16|U21|21|50|60)?[MW]', '1', 5, 80);
		CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[RBLT](U16|U21|21|50|60)?[MW]', '0', 9, 80);
		CreateTargetFace($TourId, $i++, '6 ring 80cm',   'REG-^[C](U16|U21|21|50|60)?[MW]', '1', 9, 80);
		CreateTargetFace($TourId, $i++, '10 ring 80cm', 'REG-^[C](U16|U21|21|50|60)?[MW]', '0', 5, 80);
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
	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);
