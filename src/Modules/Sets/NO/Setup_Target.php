<?php
/*

COMMON SETUP FOR TARGET

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default Classes
CreateStandardClasses($TourId, $SubRule, $TourType);

// default Subclasses
CreateStandardSubClasses($TourId);

// default Distances
switch($TourType) {
	case 1: // FITA
	case 4: // 1/2 FITA
		// only ordinary tournaments
        CreateDistanceNew($TourId, $TourType, 'F6', array(array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10)));
        CreateDistanceNew($TourId, $TourType, 'R1', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
        CreateDistanceNew($TourId, $TourType, 'R2', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
        CreateDistanceNew($TourId, $TourType, 'R3', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
        CreateDistanceNew($TourId, $TourType, 'R4', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType, 'R5', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'C1', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
        CreateDistanceNew($TourId, $TourType, 'C2', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
        CreateDistanceNew($TourId, $TourType, 'C3', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
        CreateDistanceNew($TourId, $TourType, 'C4', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType, 'C5', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'B1', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType, 'B2', array(array('40 m',40), array('30 m',30), array('25 m',25), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType, 'B3', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'B4', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'B5', array(array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10)));
        CreateDistanceNew($TourId, $TourType, 'T1', array(array('40 m',40), array('30 m',30), array('25 m',25), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType, 'T2', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'T3', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'T4', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'T5', array(array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10)));
        CreateDistanceNew($TourId, $TourType, 'LB1', array(array('40 m',40), array('30 m',30), array('25 m',25), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType, 'LB2', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'LB3', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'LB4', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'LB5', array(array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10)));
        CreateDistanceNew($TourId, $TourType, 'OC1', array(array('20 m',20), array('20 m',20), array('20 m',20), array('20 m',20)));
		break;
	case 2: // 2xFITA
		// only ordinary tournaments
		CreateDistanceNew($TourId, $TourType,  'F6', array(array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10), array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10)));
		CreateDistanceNew($TourId, $TourType,  'R1', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType,  'R2', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType,  'R3', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType,  'R4', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType,  'R5', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
		CreateDistanceNew($TourId, $TourType,  'C1', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30), array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType,  'C2', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30), array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType,  'C3', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30), array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType,  'C4', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
        CreateDistanceNew($TourId, $TourType,  'C5', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType,  'B1', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20), array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType,  'B2', array(array('40 m',40), array('30 m',30), array('25 m',25), array('20 m',20), array('40 m',40), array('30 m',30), array('25 m',25), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType,  'B3', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
		CreateDistanceNew($TourId, $TourType,  'B4', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType,  'B5', array(array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10), array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10)));
		CreateDistanceNew($TourId, $TourType,  'T1', array(array('40 m',40), array('30 m',30), array('25 m',25), array('20 m',20), array('40 m',40), array('30 m',30), array('25 m',25), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType,  'T2', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType,  'T3', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType,  'T4', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType,  'T5', array(array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10), array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10)));
		CreateDistanceNew($TourId, $TourType, 'LB1', array(array('40 m',40), array('30 m',30), array('25 m',25), array('20 m',20), array('40 m',40), array('30 m',30), array('25 m',25), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, 'LB2', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'LB3', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
        CreateDistanceNew($TourId, $TourType, 'LB4', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15), array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
		CreateDistanceNew($TourId, $TourType, 'LB5', array(array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10), array('25 m',25), array('20 m',20), array('15 m',15), array('10 m',10)));
        CreateDistanceNew($TourId, $TourType, 'OC1', array(array('20 m',20), array('20 m',20), array('20 m',20), array('20 m',20), array('20 m',20), array('20 m',20), array('20 m',20), array('20 m',20)));
		break;
	case 3: // 72 Round
		switch($SubRule) {
			case 1:
			case 2:
				// standard 70m
				CreateDistanceNew($TourId, $TourType,  'F6', array(array('20 m',20), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType,  'R1', array(array('70 m',70), array('70 m',70)));
				CreateDistanceNew($TourId, $TourType,  'R2', array(array('60 m',60), array('60 m',60)));
				CreateDistanceNew($TourId, $TourType,  'R3', array(array('50 m',50), array('50 m',50)));
				CreateDistanceNew($TourId, $TourType,  'R4', array(array('40 m',40), array('40 m',40)));
				CreateDistanceNew($TourId, $TourType,  'R5', array(array('25 m',25), array('25 m',25)));
				CreateDistanceNew($TourId, $TourType,  'C1', array(array('50 m',50), array('50 m',50)));
				CreateDistanceNew($TourId, $TourType,  'C2', array(array('50 m',50), array('50 m',50)));
				CreateDistanceNew($TourId, $TourType,  'C3', array(array('50 m',50), array('50 m',50)));
				CreateDistanceNew($TourId, $TourType,  'C4', array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType,  'C5', array(array('25 m',25), array('25 m',25)));
				CreateDistanceNew($TourId, $TourType,  'B1', array(array('50 m',40), array('50 m',40)));
				CreateDistanceNew($TourId, $TourType,  'B2', array(array('40 m',40), array('40 m',40)));
				CreateDistanceNew($TourId, $TourType,  'B3', array(array('25 m',25), array('25 m',25)));
				CreateDistanceNew($TourId, $TourType,  'B4', array(array('25 m',25), array('25 m',25)));
                CreateDistanceNew($TourId, $TourType,  'B5', array(array('20 m',20), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType,  'T1', array(array('30 m',30), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType,  'T2', array(array('25 m',25), array('25 m',25)));
                CreateDistanceNew($TourId, $TourType,  'T3', array(array('25 m',25), array('25 m',25)));
				CreateDistanceNew($TourId, $TourType,  'T4', array(array('25 m',25), array('25 m',25)));
                CreateDistanceNew($TourId, $TourType,  'T5', array(array('20 m',20), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType, 'LB1', array(array('30 m',30), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType, 'LB2', array(array('25 m',25), array('25 m',25)));
                CreateDistanceNew($TourId, $TourType, 'LB3', array(array('25 m',25), array('25 m',25)));
				CreateDistanceNew($TourId, $TourType, 'LB4', array(array('25 m',25), array('25 m',25)));
                CreateDistanceNew($TourId, $TourType, 'LB5', array(array('20 m',20), array('20 m',20)));
                CreateDistanceNew($TourId, $TourType, 'OC1', array(array('20 m',20), array('20 m',20)));
				break;
			case 3:
				// Norges Runden
				CreateDistanceNew($TourId, $TourType,  'F6', array(array('15 m',15), array('15 m',15)));
				CreateDistanceNew($TourId, $TourType,  'R1', array(array('50 m',50), array('50 m',50)));
				CreateDistanceNew($TourId, $TourType,  'R2', array(array('50 m',50), array('50 m',50)));
				CreateDistanceNew($TourId, $TourType,  'R3', array(array('50 m',50), array('50 m',50)));
				CreateDistanceNew($TourId, $TourType,  'R4', array(array('30 m',30), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType,  'R5', array(array('20 m',20), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType,  'C1', array(array('50 m',50), array('50 m',50)));
				CreateDistanceNew($TourId, $TourType,  'C2', array(array('50 m',50), array('50 m',50)));
				CreateDistanceNew($TourId, $TourType,  'C3', array(array('50 m',50), array('50 m',50)));
				CreateDistanceNew($TourId, $TourType,  'C4', array(array('30 m',30), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType,  'C5', array(array('20 m',20), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType,  'B1', array(array('30 m',30), array('30 m',30)));
				CreateDistanceNew($TourId, $TourType,  'B2', array(array('25 m',25), array('25 m',25)));
				CreateDistanceNew($TourId, $TourType,  'B3', array(array('25 m',25), array('25 m',25)));
				CreateDistanceNew($TourId, $TourType,  'B4', array(array('20 m',20), array('20 m',20)));
				CreateDistanceNew($TourId, $TourType,  'B5', array(array('15 m',15), array('15 m',15)));
                CreateDistanceNew($TourId, $TourType, 'LB1', array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'LB2', array(array('20 m',20), array('20 m',20)));
                CreateDistanceNew($TourId, $TourType, 'LB3', array(array('20 m',20), array('20 m',20)));
                CreateDistanceNew($TourId, $TourType, 'LB4', array(array('20 m',20), array('20 m',20)));
                CreateDistanceNew($TourId, $TourType, 'LB5', array(array('15 m',15), array('15 m',15)));
                CreateDistanceNew($TourId, $TourType,  'T1', array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType,  'T2', array(array('20 m',20), array('20 m',20)));
                CreateDistanceNew($TourId, $TourType,  'T3', array(array('20 m',20), array('20 m',20)));
                CreateDistanceNew($TourId, $TourType,  'T4', array(array('20 m',20), array('20 m',20)));
                CreateDistanceNew($TourId, $TourType,  'T5', array(array('15 m',15), array('15 m',15)));
				CreateDistanceNew($TourId, $TourType, 'OC1', array(array('20 m',20), array('20 m',20)));
				break;
			case 4:
				// Champs
                CreateDistanceNew($TourId, $TourType, 'R_U16',array(array('40 m',40), array('40 m',40)));
                CreateDistanceNew($TourId, $TourType, 'C_U16',array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'T_U16',array(array('25 m',25), array('25 m',25)));
                CreateDistanceNew($TourId, $TourType, 'LB_U16',array(array('25 m',25), array('25 m',25)));
                CreateDistanceNew($TourId, $TourType, 'B_U16',array(array('25 m',25), array('25 m',25)));

                CreateDistanceNew($TourId, $TourType, 'R_U18',array(array('60 m',60), array('60 m',60)));
                CreateDistanceNew($TourId, $TourType, 'C_U18',array(array('50 m',50), array('50 m',50)));
                CreateDistanceNew($TourId, $TourType, 'T_U18',array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'LB_U18',array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'B_U18',array(array('40 m',40), array('40 m',40)));

                CreateDistanceNew($TourId, $TourType, 'R_U21',array(array('70 m',70), array('70 m',70)));
                CreateDistanceNew($TourId, $TourType, 'C_U21',array(array('50 m',50), array('50 m',50)));
                CreateDistanceNew($TourId, $TourType, 'T_U21',array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'LB_U21',array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'B_U21',array(array('50 m',50), array('50 m',50)));

				CreateDistanceNew($TourId, $TourType, 'R_5',array(array('60 m',60), array('60 m',60)));
                CreateDistanceNew($TourId, $TourType, 'C_5',array(array('50 m',50), array('50 m',50)));
                CreateDistanceNew($TourId, $TourType, 'T_5',array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'LB_5',array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'B_5',array(array('50 m',50), array('50 m',50)));

                CreateDistanceNew($TourId, $TourType, 'R_',array(array('70 m',70), array('70 m',70)));
                CreateDistanceNew($TourId, $TourType, 'C_',array(array('50 m',50), array('50 m',50)));
                CreateDistanceNew($TourId, $TourType, 'T_',array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'LB_',array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'B_',array(array('50 m',50), array('50 m',50)));

                CreateDistanceNew($TourId, $TourType, 'VI%',array(array('30 m',30), array('30 m',30)));
                CreateDistanceNew($TourId, $TourType, 'OC%',array(array('20 m',20), array('20 m',20)));

				break;
		}
		break;
	case 5: // 900 round
		if($SubRule==1) {
			// 900 Round
			CreateDistanceNew($TourId, $TourType, 'R1', array(array('60 m',60), array('50 m',50), array('40 m',40)));
			CreateDistanceNew($TourId, $TourType, 'R2', array(array('60 m',60), array('50 m',50), array('40 m',40)));
			CreateDistanceNew($TourId, $TourType, 'R3', array(array('60 m',60), array('50 m',50), array('40 m',40)));
			CreateDistanceNew($TourId, $TourType, 'R4', array(array('40 m',40), array('30 m',30), array('20 m',20)));
			CreateDistanceNew($TourId, $TourType, 'R5', array(array('25 m',25), array('20 m',20), array('15 m',15)));
			CreateDistanceNew($TourId, $TourType, 'C1', array(array('60 m',60), array('50 m',50), array('40 m',40)));
			CreateDistanceNew($TourId, $TourType, 'C2', array(array('60 m',60), array('50 m',50), array('40 m',40)));
			CreateDistanceNew($TourId, $TourType, 'C3', array(array('60 m',60), array('50 m',50), array('40 m',40)));
			CreateDistanceNew($TourId, $TourType, 'C4', array(array('40 m',40), array('30 m',30), array('20 m',20)));
			CreateDistanceNew($TourId, $TourType, 'C5', array(array('25 m',25), array('20 m',20), array('15 m',15)));
			CreateDistanceNew($TourId, $TourType, 'B1', array(array('40 m',40), array('30 m',30), array('20 m',20)));
			CreateDistanceNew($TourId, $TourType, 'B2', array(array('30 m',30), array('25 m',25), array('20 m',20)));
			CreateDistanceNew($TourId, $TourType, 'B3', array(array('25 m',25), array('20 m',20), array('15 m',15)));
			CreateDistanceNew($TourId, $TourType, 'B4', array(array('25 m',25), array('20 m',20), array('15 m',15)));
			CreateDistanceNew($TourId, $TourType, 'B5', array(array('20 m',20), array('15 m',15), array('10 m',10)));
            CreateDistanceNew($TourId, $TourType, 'T1', array(array('30 m',30), array('25 m',25), array('20 m',20)));
            CreateDistanceNew($TourId, $TourType, 'T2', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType, 'T3', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType, 'T4', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType, 'T5', array(array('20 m',20), array('15 m',15), array('10 m',10)));
            CreateDistanceNew($TourId, $TourType,'LB1', array(array('30 m',30), array('25 m',25), array('20 m',20)));
            CreateDistanceNew($TourId, $TourType,'LB2', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType,'LB3', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType,'LB4', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType,'LB5', array(array('20 m',20), array('15 m',15), array('10 m',10)));
            CreateDistanceNew($TourId, $TourType,'OC1', array(array('20 m',20), array('20 m',20), array('20 m',20)));
			CreateDistanceNew($TourId, $TourType, 'F6', array(array('20 m',20), array('15 m',15), array('10 m',10)));
		} else {
			// Norsk kortrunde
			CreateDistanceNew($TourId, $TourType, 'F6', array(array('20 m',20), array('15 m',15), array('10 m',10)));
			CreateDistanceNew($TourId, $TourType, 'B5', array(array('20 m',20), array('15 m',15), array('10 m',10)));
			CreateDistanceNew($TourId, $TourType, 'B4', array(array('25 m',25), array('20 m',20), array('15 m',15)));
			CreateDistanceNew($TourId, $TourType, 'B3', array(array('25 m',25), array('20 m',20), array('15 m',15)));
			CreateDistanceNew($TourId, $TourType, 'B2', array(array('30 m',30), array('25 m',25), array('20 m',20)));
			CreateDistanceNew($TourId, $TourType, 'B1', array(array('35 m',35), array('25 m',25), array('20 m',20)));
			CreateDistanceNew($TourId, $TourType, 'C5', array(array('25 m',25), array('20 m',20), array('15 m',15)));
			CreateDistanceNew($TourId, $TourType, 'C4', array(array('35 m',35), array('25 m',25), array('20 m',20)));
			CreateDistanceNew($TourId, $TourType, 'C3', array(array('50 m',50), array('35 m',35), array('25 m',25)));
			CreateDistanceNew($TourId, $TourType, 'C2', array(array('50 m',50), array('35 m',35), array('25 m',25)));
			CreateDistanceNew($TourId, $TourType, 'C1', array(array('50 m',50), array('35 m',35), array('25 m',25)));
			CreateDistanceNew($TourId, $TourType, 'R5', array(array('25 m',25), array('20 m',20), array('15 m',15)));
			CreateDistanceNew($TourId, $TourType, 'R4', array(array('35 m',35), array('25 m',25), array('20 m',20)));
			CreateDistanceNew($TourId, $TourType, 'R3', array(array('50 m',50), array('35 m',35), array('25 m',25)));
			CreateDistanceNew($TourId, $TourType, 'R2', array(array('50 m',50), array('35 m',35), array('25 m',25)));
			CreateDistanceNew($TourId, $TourType, 'R1', array(array('50 m',50), array('35 m',35), array('25 m',25)));
            CreateDistanceNew($TourId, $TourType,'LB5', array(array('20 m',20), array('15 m',15), array('10 m',10)));
            CreateDistanceNew($TourId, $TourType,'LB4', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType,'LB3', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType,'LB2', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType,'LB1', array(array('30 m',30), array('25 m',25), array('20 m',20)));
            CreateDistanceNew($TourId, $TourType, 'T5', array(array('20 m',20), array('15 m',15), array('10 m',10)));
            CreateDistanceNew($TourId, $TourType, 'T4', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType, 'T3', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType, 'T2', array(array('25 m',25), array('20 m',20), array('15 m',15)));
            CreateDistanceNew($TourId, $TourType, 'T1', array(array('30 m',30), array('25 m',25), array('20 m',20)));
            CreateDistanceNew($TourId, $TourType,'OC1', array(array('20 m',20), array('20 m',20), array('20 m',20)));
		}
		break;
	case 6:
		if($SubRule<3) {
			// ordinary tournaments
			CreateDistanceNew($TourId, $TourType, 'F6', array(array('12m-1',12), array('12m-2',12)));
			CreateDistanceNew($TourId, $TourType, '%5', array(array('12m-1',12), array('12m-2',12)));
			CreateDistanceNew($TourId, $TourType, '%4', array(array('18m-1',18), array('18m-2',18)));
			CreateDistanceNew($TourId, $TourType, '%3', array(array('18m-1',18), array('18m-2',18)));
			CreateDistanceNew($TourId, $TourType, '%2', array(array('18m-1',18), array('18m-2',18)));
			CreateDistanceNew($TourId, $TourType, '_1', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'LB1', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'VI1', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'OC1', array(array('12m-1',12), array('12m-2',18)));
		} else {
			// Champs
			CreateDistanceNew($TourId, $TourType, 'T%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'LB%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'B%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'R%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'C%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'VI%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'OC%', array(array('12m-1',12), array('12m-2',18)));
		}
		break;
	case 7:
		// ordinary tournaments
		CreateDistanceNew($TourId, $TourType, 'F6', array(array('16m-1',16), array('16m-2',16)));
		CreateDistanceNew($TourId, $TourType, '%5', array(array('16m-1',16), array('16m-2',16)));
		CreateDistanceNew($TourId, $TourType, '%4', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '%3', array(array('25m-1',25), array('25m-2',25)));
		CreateDistanceNew($TourId, $TourType, '%2', array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, '_1', array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, 'LB1', array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, 'VI1', array(array('25m-1',25), array('25m-2',25)));
        CreateDistanceNew($TourId, $TourType, 'OC1', array(array('16m-1',16), array('16m-2',16)));
        break;
	case 8:
		// ordinary tournaments
		CreateDistanceNew($TourId, $TourType, 'F6', array(array('16m-1',16), array('16m-2',16), array('12m-1',12), array('12m-2',12)));
		CreateDistanceNew($TourId, $TourType, '%5', array(array('16m-1',16), array('16m-2',16), array('12m-1',12), array('12m-2',12)));
		CreateDistanceNew($TourId, $TourType, '%4', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '%3', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
		CreateDistanceNew($TourId, $TourType, '%2', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, '_1', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'LB1', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'VI1', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'OC1', array(array('16m-1',16), array('16m-2',16), array('12m-1',12), array('12m-2',12)));
        break;
	case 18:
		// only with Finals
		CreateDistanceNew($TourId, $TourType, 'C5', array(array('25m-1',25), array('25m-2',25), array('-',0), array('-',0)));
		CreateDistanceNew($TourId, $TourType, 'C4', array(array('40m-1',40), array('40m-2',40), array('-',0), array('-',0)));
		CreateDistanceNew($TourId, $TourType, 'C3', array(array('50m-1',50), array('50m-2',50), array('-',0), array('-',0)));
		CreateDistanceNew($TourId, $TourType, 'C2', array(array('50m-1',50), array('50m-2',50), array('-',0), array('-',0)));
		CreateDistanceNew($TourId, $TourType, 'C1', array(array('50m-1',50), array('50m-2',50), array('-',0), array('-',0)));
		CreateDistanceNew($TourId, $TourType, 'F6', array(array('25 m',25), array('20 m',20), array('25 m',25), array('10 m',10)));
		CreateDistanceNew($TourId, $TourType, 'R5', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
		CreateDistanceNew($TourId, $TourType, 'B1', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, 'B4', array(array('30 m',30), array('25 m',25), array('20 m',20), array('15 m',15)));
		CreateDistanceNew($TourId, $TourType, 'R4', array(array('50 m',50), array('40 m',40), array('30 m',30), array('20 m',20)));
		CreateDistanceNew($TourId, $TourType, 'R3', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'R2', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, 'R1', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		break;
	case 22:
		if($SubRule<3) {
            // ordinary tournaments
            CreateDistanceNew($TourId, $TourType, 'F6', array(array('12m-1',12)));
            CreateDistanceNew($TourId, $TourType, '%5', array(array('12m-1',12)));
            CreateDistanceNew($TourId, $TourType, '%4', array(array('18m-1',18)));
            CreateDistanceNew($TourId, $TourType, '%3', array(array('18m-1',18)));
            CreateDistanceNew($TourId, $TourType, '%2', array(array('18m-1',18)));
            CreateDistanceNew($TourId, $TourType, '_1', array(array('18m-1',18)));
            CreateDistanceNew($TourId, $TourType, 'LB1', array(array('18m-1',18)));
            CreateDistanceNew($TourId, $TourType, 'VI1', array(array('18m-1',18)));
            CreateDistanceNew($TourId, $TourType, 'OC2', array(array('12m-1',12)));

		} else {
			// Champs
			CreateDistanceNew($TourId, $TourType, '%', array(array('18m',18)));
		}
		break;
}

// rules 1, 2 and 4 do not have finals!
if(in_array($TourType, array(3, 6, 7, 8, 22))) {
	$Champ=false;
	if($SubRule==2 or $Champ=(($TourType==3 and $SubRule==4) or ($TourType==6 and $SubRule==3))) {
		// default Events
		CreateStandardEvents($TourId, $TourType, $SubRule, $tourDetCategory=='1');

		// Classes in Events
		InsertStandardEvents($TourId, $TourType, $SubRule, $tourDetCategory=='1');

		// Finals & TeamFinals
		CreateFinals($TourId);
	}
}

// Default Target
$i=1;
switch($TourType) {
	case 1:
	case 4:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~30: 5-X', 'REG-R2', '1',  5, 122, 5, 122, 5, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 5-X', 'REG-R1|C1|C2', '1',  5, 122, 5, 122, 9, 80, 9, 80);
		break;
	case 2:
		CreateTargetFace($TourId, $i++, '~Default', '%', '1', 5, 122, 5, 122, 5, 80, 5, 80, 5, 122, 5, 122, 5, 80, 5, 80);
		// optional target faces
		CreateTargetFace($TourId, $i++, '~30: 5-X', 'REG-R2', '1',  5, 122, 5, 122, 5, 80, 9, 80,  5, 122, 5, 122, 5, 80, 9, 80);
		CreateTargetFace($TourId, $i++, '~50: 5-X/30: 5-X', 'REG-R1|C1|C2', '1',  5, 122, 5, 122, 9, 80, 9, 80,  5, 122, 5, 122, 9, 80, 9, 80);
		break;
	case 3:
		if($SubRule==3) {
			// Norgesrunde
            CreateTargetFace($TourId, $i++, '~5-X', 'REG-R1|C1|C2', '1',  TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, $i++, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
		} elseif($SubRule==4) {
			// Championship
			CreateTargetFace($TourId, $i++, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
			CreateTargetFace($TourId, $i++, '~5-X', 'C%', '1',  TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, $i++, '80cm', 'VI%', '1',  TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
//			CreateTargetFace($TourId, $i++, '~5-X', 'CKJ', '1',  9, 80, 9, 80);
//			CreateTargetFace($TourId, $i++, '~5-X', 'CKG', '1',  9, 80, 9, 80);
		} else {
			// ordinary 70m
			CreateTargetFace($TourId, $i++, '~5-X', 'REG-^C[124]', '1',  9, 80, 9, 80);
            CreateTargetFace($TourId, $i++, '~1-X', 'REG-^C[35]', '1',  TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
            CreateTargetFace($TourId, $i++, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
		}
		break;
	case 5:
		if($SubRule==2) {
			// Norsk Kortrunde
            CreateTargetFace($TourId, $i++, '~5-X', 'REG-^C[12]|^R[1]', '1',  TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
            CreateTargetFace($TourId, $i++, '~5-X', 'REG-^OC', '1',  TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
            CreateTargetFace($TourId, $i++, '~Default', '%', '1',  TGT_OUT_FULL, 80, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
//			// optional target faces

		} else {
			CreateTargetFace($TourId, $i++, '~Default', '%', '1',  5, 122, 5, 122, 5, 122);
		}
		break;
	case 6:
	case 22:
		if($SubRule==3) {
			// Champs
			CreateTargetFace($TourId, $i++, 'Tripple 40cm', 'REG-(^R[D|H](?!U16)|^B[D|H]$)', '1', TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, $i++, 'Tripple 40cm CO', 'REG-(^C[D|H](|5|U21|U18)$)', '1', TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
            CreateTargetFace($TourId, $i++, '40cm (1-10)', 'REG-(^B[D|H](U21|U18|5)$)|((^T|^LB)[D|H](5){0,1})$', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, $i++, '40cm (1-10)', 'REG-(^R[D|H](|5|U21|U18)$|^B[D|H]$)', '', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
			CreateTargetFace($TourId, $i++, '60cm (1-10)', 'REG-((^R|^T|^B|^LB)[D|H]U16)$|((^T|^LB)[D|H](U21|U18))$|^VI|^OC', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, $i++, 'Tripple 60cm', 'REG-^C[D|H]U16$', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
		} else {
			CreateTargetFace($TourId, $i++, '~Standard 60', '%', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60); // most of the "small" class use big targets!
			CreateTargetFace($TourId, $i++, '~Standard 40', 'REG-^(R2|T1|LB1)', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
			CreateTargetFace($TourId, $i++, '~Trippel 40 CO', 'REG-^C[12]', '1', TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
			CreateTargetFace($TourId, $i++, '~Trippel 60 CO', 'REG-^C[345]', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
            //CreateTargetFace($TourId, $i++, '~Trippel 60', 'REG-^(LB1|IN1)', '1',  2, 60, 2, 60);
            CreateTargetFace($TourId, $i++, '~Trippel 40', 'REG-^R1', '1',  TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, $i++, '~Standard 40', 'REG-(^B1|^B2)', '1',  TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, $i++, '~Trippel 40', 'REG-^B1', '0',  TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
		}
		break;
	case 7:
		CreateTargetFace($TourId, $i++, '~Standard 80', '%', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80); // most of the "small" class use big targets!
		CreateTargetFace($TourId, $i++, '~Standard 60', 'REG-^(R2|B1|T1|LB1)', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
		CreateTargetFace($TourId, $i++, '~Trippel 60 CO', 'REG-^C[12]', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
		CreateTargetFace($TourId, $i++, '~80 6-10', 'REG-^C[345]', '1', TGT_IND_6_small10, 80, TGT_IND_6_small10, 80);
		CreateTargetFace($TourId, $i++, '~Trippel 60', 'REG-^R1', '1',  TGT_IND_6_big10, 60, TGT_IND_6_big10, 60);
		break;
	case 8:
		CreateTargetFace($TourId, $i++, '~Standard 80/60', '%', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80, TGT_IND_1_big10, 60, TGT_IND_1_big10, 60); // most of the "small" class use big targets!
		CreateTargetFace($TourId, $i++, '~Standard 60/40', 'REG-^(R2|B1|T1|LB1)', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60, TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
		CreateTargetFace($TourId, $i++, '~Trippel 60/40 CO', 'REG-^C[12]', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60, TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
		CreateTargetFace($TourId, $i++, '~80 6-10/Trippel 60 CO', 'REG-^C[345]', '1', TGT_IND_6_small10, 80, TGT_IND_6_small10, 80, TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
        CreateTargetFace($TourId, $i++, '~Trippel 60/40', 'REG-^R1', '1',  TGT_IND_6_big10, 60, TGT_IND_6_big10, 60,  TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 32, (in_array($TourType, array(1,2,3,4,5)) ? 3 : 4));

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

