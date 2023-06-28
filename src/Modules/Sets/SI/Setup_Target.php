<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId,$TourType, $SubRule);

// default SubClasses
//CreateSubClass($TourId, 1, '00', '00');

// default Classes
CreateStandardClasses($TourId, $TourType, $SubRule);

// default Distances

switch($TourType) {
    case 1:
        CreateDistanceNew($TourId, $TourType, '__U13_', array(array('30m',30), array('25m',25), array('20m',20), array('15m',15)));

        CreateDistanceNew($TourId, $TourType, 'ULU15_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'ULU18W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULU18M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULU21W', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULU21M', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULW', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULM', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'UL50W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'UL50M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'SLU15_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'SLU18W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLU18M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLU21W', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLU21M', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLW', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLM', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SL50W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SL50M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'GLU15_', array(array('40m',40), array('30m',30), array('25m',25), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'GLU18_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'GLU21_', array(array('60m',60), array('50m',40), array('40m',30), array('30m',20)));
        CreateDistanceNew($TourId, $TourType, 'GL_', array(array('60m',60), array('50m',40), array('40m',30), array('30m',20)));
        CreateDistanceNew($TourId, $TourType, 'GL50_', array(array('60m',60), array('50m',40), array('40m',30), array('30m',20)));

        break;

    case 2:
        CreateDistanceNew($TourId, $TourType, '__U13_', array(array('30m',30), array('25m',25), array('20m',20), array('15m',15), array('30m',30), array('25m',25), array('20m',20), array('15m',15)));

        CreateDistanceNew($TourId, $TourType, 'ULU15_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'ULU18W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULU18M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULU21W', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULU21M', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULW', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULM', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'UL50W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'UL50M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'SLU15_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'SLU18W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLU18M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLU21W', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLU21M', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLW', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLM', array(array('90m',90), array('70m',70), array('50m',50), array('30m',30), array('90m',90), array('70m',70), array('50m',50), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SL50W', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SL50M', array(array('70m',70), array('60m',60), array('50m',50), array('30m',30), array('70m',70), array('60m',60), array('50m',50), array('30m',30)));

        CreateDistanceNew($TourId, $TourType, 'GLU15_', array(array('40m',40), array('30m',30), array('25m',25), array('20m',20), array('40m',40), array('30m',30), array('25m',25), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'GLU18_', array(array('50m',50), array('40m',40), array('30m',30), array('20m',20), array('50m',50), array('40m',40), array('30m',30), array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'GLU21_', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'GL_', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'GL50_', array(array('60m',60), array('50m',50), array('40m',40), array('30m',30), array('60m',60), array('50m',50), array('40m',40), array('30m',30)));

        break;
	case 3:
        CreateDistanceNew($TourId, $TourType, 'ULU13%', array(array('30m-1', 30), array('30m-2', 30)));
        CreateDistanceNew($TourId, $TourType, 'ULU15%', array(array('40m-1', 40), array('40m-2', 40)));
        CreateDistanceNew($TourId, $TourType, 'ULU18%', array(array('60m-1', 60), array('60m-2', 60)));
        CreateDistanceNew($TourId, $TourType, 'ULU21%', array(array('70m-1', 70), array('70m-2', 70)));
        CreateDistanceNew($TourId, $TourType, 'UL50%', array(array('60m-1', 60), array('60m-2', 60)));
        CreateDistanceNew($TourId, $TourType, 'UL_', array(array('70m-1', 70), array('70m-2', 70)));

        CreateDistanceNew($TourId, $TourType, 'SLU13%', array(array('30m-1', 30), array('30m-2', 30)));
        CreateDistanceNew($TourId, $TourType, 'SLU15%', array(array('40m-1', 40), array('40m-2', 40)));
        CreateDistanceNew($TourId, $TourType, 'SLU18%', array(array('50m-1', 50), array('50m-2', 50)));
        CreateDistanceNew($TourId, $TourType, 'SLU21%', array(array('50m-1', 50), array('50m-2', 50)));
        CreateDistanceNew($TourId, $TourType, 'SL50%', array(array('50m-1', 50), array('50m-2', 50)));
        CreateDistanceNew($TourId, $TourType, 'SL_', array(array('50m-1', 50), array('50m-2', 50)));

        CreateDistanceNew($TourId, $TourType, 'GLU13%', array(array('20m-1', 20), array('20m-2', 20)));
        CreateDistanceNew($TourId, $TourType, 'GLU15%', array(array('30m-1', 30), array('30m-2', 30)));
        CreateDistanceNew($TourId, $TourType, 'GLU18%', array(array('40m-1', 40), array('40m-2', 40)));
        CreateDistanceNew($TourId, $TourType, 'GLU21%', array(array('50m-1', 50), array('50m-2', 50)));
        CreateDistanceNew($TourId, $TourType, 'GL50%', array(array('50m-1', 50), array('50m-2', 50)));
        CreateDistanceNew($TourId, $TourType, 'GL_', array(array('50m-1', 40), array('50m-2', 50)));

		break;
    case 37:
        CreateDistanceNew($TourId, $TourType, 'ULU13%', array(array('30m-1', 30), array('30m-2', 30), array('30m-3', 30), array('30m-4', 30)));
        CreateDistanceNew($TourId, $TourType, 'ULU15%', array(array('40m-1', 40), array('40m-2', 40), array('40m-3', 40), array('40m-4', 40)));
        CreateDistanceNew($TourId, $TourType, 'ULU18%', array(array('60m-1', 60), array('60m-2', 60), array('60m-3', 60), array('60m-4', 60)));
        CreateDistanceNew($TourId, $TourType, 'ULU21%', array(array('70m-1', 70), array('70m-2', 70), array('70m-3', 70), array('70m-4', 70)));
        CreateDistanceNew($TourId, $TourType, 'UL50%', array(array('60m-1', 60), array('60m-2', 60), array('60m-3', 60), array('60m-4', 60)));
        CreateDistanceNew($TourId, $TourType, 'UL_', array(array('70m-1', 70), array('70m-2', 70), array('70m-3', 70), array('70m-4', 70)));

        CreateDistanceNew($TourId, $TourType, 'SLU13%', array(array('30m-1', 30), array('30m-2', 30), array('30m-3', 30), array('30m-4', 30)));
        CreateDistanceNew($TourId, $TourType, 'SLU15%', array(array('40m-1', 40), array('40m-2', 40), array('40m-3', 40), array('40m-4', 40)));
        CreateDistanceNew($TourId, $TourType, 'SLU18%', array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4', 50)));
        CreateDistanceNew($TourId, $TourType, 'SLU21%', array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4', 50)));
        CreateDistanceNew($TourId, $TourType, 'SL50%', array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4', 50)));
        CreateDistanceNew($TourId, $TourType, 'SL_', array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4', 50)));

        CreateDistanceNew($TourId, $TourType, 'GLU13%',  array(array('20m-1', 20), array('20m-2', 20), array('20m-3', 20), array('20m-4', 20)));
        CreateDistanceNew($TourId, $TourType, 'GLU15%',  array(array('30m-1', 30), array('30m-2', 30), array('30m-3', 30), array('30m-4', 30)));
        CreateDistanceNew($TourId, $TourType, 'GLU18%',  array(array('40m-1', 40), array('40m-2', 40), array('40m-3', 40), array('40m-4', 40)));
        CreateDistanceNew($TourId, $TourType, 'GLU21%',  array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4', 50)));
        CreateDistanceNew($TourId, $TourType, 'GL50%',  array(array('50m-1', 50), array('50m-2', 50), array('50m-3', 50), array('50m-4', 50)));
        CreateDistanceNew($TourId, $TourType, 'GL_',  array(array('50m-1', 40), array('50m-2', 50), array('50m-3', 50), array('50m-4', 50)));
        break;
    case 5:
    case 5:
        CreateDistanceNew($TourId, $TourType, '%U13%', array(array('20m',20), array('15m',15),array('10m',10)));

        CreateDistanceNew($TourId, $TourType, 'GLU15%', array(array('25m',25), array('20m',20),array('15m',15)));
        CreateDistanceNew($TourId, $TourType, 'GLU18%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'GLU21%', array(array('40m',40), array('30m',30),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'GL50%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'GL_', array(array('40m',40), array('30m',30),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'DLU15%', array(array('25m',25), array('20m',20),array('15m',15)));
        CreateDistanceNew($TourId, $TourType, 'DLU18%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'DLU21%', array(array('40m',40), array('30m',30),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'DL50%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'DL_', array(array('40m',40), array('30m',30),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'TLU15%', array(array('25m',25), array('20m',20),array('15m',15)));
        CreateDistanceNew($TourId, $TourType, 'TLU18%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'TLU21%', array(array('40m',40), array('30m',30),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'TL50%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'TL_', array(array('40m',40), array('30m',30),array('20m',20)));

        CreateDistanceNew($TourId, $TourType, 'ULU15%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'ULU18%', array(array('50m',50), array('40m',40),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'ULU21%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'UL50%', array(array('50m',50), array('40m',40),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'UL_', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'SLU15%', array(array('30m',30), array('25m',25),array('20m',20)));
        CreateDistanceNew($TourId, $TourType, 'SLU18%', array(array('50m',50), array('40m',40),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SLU21%', array(array('60m',60), array('50m',50),array('40m',40)));
        CreateDistanceNew($TourId, $TourType, 'SL50%', array(array('50m',50), array('40m',40),array('30m',30)));
        CreateDistanceNew($TourId, $TourType, 'SL_', array(array('60m',60), array('50m',50),array('40m',40)));
        break;
	case 6:
		CreateDistanceNew($TourId, $TourType, '%',   array(array('18m-1',18), array('18m-2',18)));
		break;
	case 7:
        CreateDistanceNew($TourId, $TourType, '%',   array(array('25m-1',25), array('25m-2',25)));
		break;
	/*
        case 8:
        CreateDistanceNew($TourId, $TourType, 'R%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'C%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'W1_',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'B%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'T%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        CreateDistanceNew($TourId, $TourType, 'L%',   array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        if($SubRule==1) {
            CreateDistanceNew($TourId, $TourType, 'W1U13%', array(array('20m-1',20), array('20m-2',20), array('10m-1',10), array('10m-2',10)));
            CreateDistanceNew($TourId, $TourType, 'W1U15%', array(array('20m-1',20), array('20m-2',20), array('10m-1',10), array('10m-2',10)));
            CreateDistanceNew($TourId, $TourType, 'W1U18%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W1U21%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W165%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W150%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
        }
		break;
    */
}


if($TourType==3 or $TourType==6 or $TourType==37) {
	// default Events
	CreateStandardEvents($TourId, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}


// Default Target
switch($TourType) {

    case 1:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        CreateTargetFace($TourId, 2, '~50: 5-X/30: 5-X', 'REG-^UL|^SL', '',TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        break;
    case 2:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        CreateTargetFace($TourId, 2, '~50: 5-X/30: 5-X', 'REG-^UL|^SL', '',TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80,TGT_OUT_FULL, 122, TGT_OUT_FULL, 122,TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        break;
    case 3:
        CreateTargetFace($TourId, 1, '~Default', 'REG-^UL|^GL', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^SLU18|^SLU21|^SL50|^SL[M|W]$', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        CreateTargetFace($TourId, 3, '~Default', 'REG-^SLU13|^SLU15', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
		break;
    case 5:
        CreateTargetFace($TourId, 1, '~Default', '%', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        break;
    case 37:
        CreateTargetFace($TourId, 1, '~Default', 'REG-^UL|^GL', '1', TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122, TGT_OUT_FULL, 122);
        CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^SLU18|^SLU21|^SL50|^SL[M|W]$', '1', TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80, TGT_OUT_5_big10, 80);
        CreateTargetFace($TourId, 3, '~Default', 'REG-^SLU13|^SLU15', '1', TGT_OUT_FULL, 80, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80, TGT_OUT_FULL, 80);
        break;
	case 6:
        CreateTargetFace($TourId, 1, '~Default', 'REG-^DL|^TL', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
        CreateTargetFace($TourId, 2, '~Default', 'REG-^GLU21|^GL50|^GL[M|W]$', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
        CreateTargetFace($TourId, 3, '~Default', 'REG-^SLU18|^SLU21|^SL50|^SL[M|W]$', '1', TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
        CreateTargetFace($TourId, 4, '~Default', 'REG-^SLU15', '1', TGT_IND_1_small10, 40, TGT_IND_1_small10, 40);
        CreateTargetFace($TourId, 5, '~Default', 'REG-^SLU13', '1', TGT_IND_1_small10, 60, TGT_IND_1_small10, 60);
        CreateTargetFace($TourId, 6, '~Default', 'REG-^ULU21|^UL50|^UL[M|W]$', '1', TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
        CreateTargetFace($TourId, 7, '~Default', 'REG-^ULU18|^ULU15|^GLU18|^GLU15', '1', TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
        CreateTargetFace($TourId, 8, '~Default', 'REG-^ULU13|^GLU13', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);

		break;
    case 7:
        CreateTargetFace($TourId, 1, '~Default', 'REG-^DL|^TL', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80);
        CreateTargetFace($TourId, 2, '~Default', 'REG-^GLU21|^GL50|^GL[M|W]', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
        CreateTargetFace($TourId, 3, '~Default', 'REG-^SLU18|^SLU21|^SL50|^SL[M|W]$', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60);
        CreateTargetFace($TourId, 4, '~Default', 'REG-^SLU15', '1', TGT_IND_1_small10, 60, TGT_IND_1_small10, 60);
        CreateTargetFace($TourId, 5, '~Default', 'REG-^SLU13', '1', TGT_IND_1_small10, 80, TGT_IND_1_small10, 80);
        CreateTargetFace($TourId, 6, '~Default', 'REG-^ULU21|^UL50|^UL[M|W]$', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60);
        CreateTargetFace($TourId, 7, '~Default', 'REG-^ULU18|^ULU15|^GLU18|^GLU15', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
        CreateTargetFace($TourId, 8, '~Default', 'REG-^ULU13|^GLU13', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80);
        break;
        /*
	case 8:
        if($SubRule==1) {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^RU18|^RU21|^R50|^R65|^R[M|W]$|^ROU18|^ROU21|^RO50|^RO65|^RO[M|W]$', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60, TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^CU18|^CU21|^C50|^C65|^C[M|W]$|^COU18|^COU21|^CO50|^CO65|^CO[M|W]$|^W1U21|^W150|^W165|^W1[M|W]$', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60, TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^RU13|^RU15|^CU13|^CU15|^ROU13|^ROU15|^COU13|^COU15', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60, TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^B', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60, TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 5, '~Default', 'REG-^T|^L', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80, TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);
            CreateTargetFace($TourId, 6, '~Default', 'REG-^W1U13|^W1U15', '1', TGT_IND_1_big10, 122, TGT_IND_1_big10, 122, TGT_IND_1_big10, 80, TGT_IND_1_big10, 80);
            CreateTargetFace($TourId, 7, '~Default', 'REG-^W1U18', '1', TGT_IND_1_small10, 80, TGT_IND_1_small10, 80, TGT_IND_1_small10, 60, TGT_IND_1_small10, 60);
        } else {
            CreateTargetFace($TourId, 1, '~Default', 'REG-^R', '1', TGT_IND_6_big10, 60, TGT_IND_6_big10, 60, TGT_IND_6_big10, 40, TGT_IND_6_big10, 40);
            CreateTargetFace($TourId, 2, '~DefaultCO', 'REG-^C|^W1', '1', TGT_IND_6_small10, 60, TGT_IND_6_small10, 60, TGT_IND_6_small10, 40, TGT_IND_6_small10, 40);
            CreateTargetFace($TourId, 3, '~Default', 'REG-^B', '1', TGT_IND_1_big10, 60, TGT_IND_1_big10, 60, TGT_IND_1_big10, 40, TGT_IND_1_big10, 40);
            CreateTargetFace($TourId, 4, '~Default', 'REG-^T|^L', '1', TGT_IND_1_big10, 80, TGT_IND_1_big10, 80, TGT_IND_1_big10, 60, TGT_IND_1_big10, 60);

        }
		break;
    */
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
    'ToPrintChars' => 1
//	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);
