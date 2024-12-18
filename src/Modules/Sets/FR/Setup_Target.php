<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default Classes
CreateStandardClasses($TourId, $TourType, $SubRule);

// default Distances
switch($TourType) {
	case 3:
		switch($SubRule) {
            case 2: // TNJ Tournoi National Jeune
            case 3: // Championnat de France Jeune
				CreateDistanceNew($TourId, $TourType, 'CLU13%', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'CLU15%', array(array('40m-1',40), array('40m-2',40)));
				CreateDistanceNew($TourId, $TourType, 'CLU18%', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'CLU21%', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CO%', array(array('50m-1',50), array('50m-2',50)));
                break;
            case 15: // Championnat de France Jeune Equipe
				CreateDistanceNew($TourId, $TourType, 'CLU13%', array(array('30m',30)));
				CreateDistanceNew($TourId, $TourType, 'CLU15%', array(array('30m',30)));
				CreateDistanceNew($TourId, $TourType, 'CLU18%', array(array('60m',60)));
				CreateDistanceNew($TourId, $TourType, 'CLU21%', array(array('60m',60)));

                // only 1 distance so change the settings
                $DistanceInfoArray=array(array(6, 6));
                $tourDetNumDist			= '1';
                break;
			case 9: // Finales des DR
			case 10: // Championnat de France Elite
				CreateDistanceNew($TourId, $TourType, 'CO%', array(array('50m-1',50), array('50m-2',50)));
			case 16: // Finales D2
				CreateDistanceNew($TourId, $TourType, 'CL%', array(array('70m-1',70), array('70m-2',70)));
				break;
			case 11:
				// Championnat de France Adultes
				CreateDistanceNew($TourId, $TourType, 'CLS1H', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLS2H', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLS3H', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'CLS1F', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLS2F', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLS3F', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'CL%W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CL%M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CO%', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'BB%', array(array('50m-1',50), array('50m-2',50)));
				break;
            case 13: // selectif
            case 14: // selectif + para
                CreateDistanceNew($TourId, $TourType, 'BBS%', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'BBU2%', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'BBU1%', array(array('30m-1',30), array('30m-2',30)));
                CreateDistanceNew($TourId, $TourType, 'CLD%', array(array('dist-1',30), array('dist-2',30)));
                CreateDistanceNew($TourId, $TourType, 'CLS1F', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'CLS1H', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'CLS2F', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'CLS2H', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'CLS3F', array(array('60m-1',60), array('60m-2',60)));
                CreateDistanceNew($TourId, $TourType, 'CLS3H', array(array('60m-1',60), array('60m-2',60)));
                CreateDistanceNew($TourId, $TourType, 'CLS_W', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'CLS_M', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'CLU11_', array(array('20m-1',20), array('20m-2',20)));
                CreateDistanceNew($TourId, $TourType, 'CLU13H', array(array('30m-1',30), array('30m-2',30)));
                CreateDistanceNew($TourId, $TourType, 'CLU13F', array(array('30m-1',30), array('30m-2',30)));
                CreateDistanceNew($TourId, $TourType, 'CLU13W', array(array('20m-1',20), array('20m-2',20)));
                CreateDistanceNew($TourId, $TourType, 'CLU13M', array(array('20m-1',20), array('20m-2',20)));
                CreateDistanceNew($TourId, $TourType, 'CLU15H', array(array('40m-1',40), array('40m-2',40)));
                CreateDistanceNew($TourId, $TourType, 'CLU15F', array(array('40m-1',40), array('40m-2',40)));
                CreateDistanceNew($TourId, $TourType, 'CLU15M', array(array('30m-1',30), array('30m-2',30)));
                CreateDistanceNew($TourId, $TourType, 'CLU15W', array(array('30m-1',30), array('30m-2',30)));
                CreateDistanceNew($TourId, $TourType, 'COS%', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'COU21_', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'COU18_', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'COU13_', array(array('30m-1',50), array('30m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'COU15_', array(array('30m-1',50), array('30m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'CLU21F', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'CLU21H', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'CLU18F', array(array('60m-1',60), array('60m-2',60)));
                CreateDistanceNew($TourId, $TourType, 'CLU18H', array(array('60m-1',60), array('60m-2',60)));
                CreateDistanceNew($TourId, $TourType, 'CLU21W', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'CLU21M', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'CLU18W', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'CLU18M', array(array('50m-1',50), array('50m-2',50)));
                if($SubRule==14) {
                    // PARA
                    CreateDistanceNew($TourId, $TourType, 'H%', array(array('30m-1',30), array('30m-2',30)));
                    CreateDistanceNew($TourId, $TourType, 'W%', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'S%', array(array('20m-1',20), array('20m-2',20)));
                    CreateDistanceNew($TourId, $TourType, '_J%', array(array('30m-1',30), array('30m-2',30)));
                    CreateDistanceNew($TourId, $TourType, 'CH%', array(array('20m-1',20), array('20m-2',20)));
                    CreateDistanceNew($TourId, $TourType, 'CRCL%', array(array('15m-1',15), array('15m-2',15)));
                    CreateDistanceNew($TourId, $TourType, 'CRCO%', array(array('20m-1',20), array('20m-2',20)));
                    CreateDistanceNew($TourId, $TourType, 'OPCOS%', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'OPCOU2%', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'OPCOU1%', array(array('30m-1',30), array('30m-2',30)));
                    CreateDistanceNew($TourId, $TourType, 'FECOS%', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'FECOU2%', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'FECOU1%', array(array('30m-1',30), array('30m-2',30)));
                    CreateDistanceNew($TourId, $TourType, 'OPCL%F', array(array('70m-1',70), array('70m-2',70)));
                    CreateDistanceNew($TourId, $TourType, 'OPCL%H', array(array('70m-1',70), array('70m-2',70)));
                    CreateDistanceNew($TourId, $TourType, 'OPCLS%M', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'OPCLU2%M', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'OPCLU1%M', array(array('30m-1',30), array('30m-2',30)));
                    CreateDistanceNew($TourId, $TourType, 'OPCLS%W', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'OPCLU2%W', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'OPCLU1%W', array(array('30m-1',30), array('30m-2',30)));
                    CreateDistanceNew($TourId, $TourType, 'FECL%F', array(array('70m-1',70), array('70m-2',70)));
                    CreateDistanceNew($TourId, $TourType, 'FECL%H', array(array('70m-1',70), array('70m-2',70)));
                    CreateDistanceNew($TourId, $TourType, 'FECLS%M', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'FECLU2%M', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'FECLU1%M', array(array('30m-1',30), array('30m-2',30)));
                    CreateDistanceNew($TourId, $TourType, 'FECLS%W', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'FECLU2%W', array(array('50m-1',50), array('50m-2',50)));
                    CreateDistanceNew($TourId, $TourType, 'FECLU1%W', array(array('30m-1',30), array('30m-2',30)));
                }
                break;
			default:
				CreateDistanceNew($TourId, $TourType, 'BBS%', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'BBU%', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'CLD%', array(array('dist-1',30), array('dist-2',30)));
				CreateDistanceNew($TourId, $TourType, 'CRCL%', array(array('15m-1',15), array('15m-2',15)));
				CreateDistanceNew($TourId, $TourType, 'CRCO%', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'CH%', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'FCO%', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'FCL%F', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'FCL%H', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'FCL%W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'FCL%M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'OCO%', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'OCL%W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'OCL%M', array(array('50m-1',50), array('50m-2',50)));
                CreateDistanceNew($TourId, $TourType, 'OCL%F', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'OCL%H', array(array('70m-1',70), array('70m-2',70)));
                CreateDistanceNew($TourId, $TourType, 'HV%', array(array('30m-1',30), array('30m-2',30)));
                CreateDistanceNew($TourId, $TourType, '_J%', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'S%', array(array('20m-1',20), array('20m-2',20)));
                CreateDistanceNew($TourId, $TourType, 'W%', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CLS_F', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLS_H', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLU21F', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLU21H', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CLU18F', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'CLU18H', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'C_U15H', array(array('40m-1',40), array('40m-2',40)));
				CreateDistanceNew($TourId, $TourType, 'C_U15F', array(array('40m-1',40), array('40m-2',40)));
				CreateDistanceNew($TourId, $TourType, 'CLU13H', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'CLU13F', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'CLU11_', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'C_U21W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_U21M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_U18W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_U18M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_U15M', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'C_U15W', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'CLU13W', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'CLU13M', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'COS%', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CLS%W', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CLS%M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'COU13F', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'COU13H', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'COU18F', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'COU18H', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'COU21F', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'COU21H', array(array('50m-1',50), array('50m-2',50)));
		}
		break;
	case 6:
        if($SubRule==4) {
            // PARA
            CreateDistanceNew($TourId, $TourType, 'CLU%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'CLS%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'CO%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'BB%', array(array('18m-1',18), array('18m-2',18)));
			CreateDistanceNew($TourId, $TourType, 'CLD%', array(array('dist-1',18), array('dist-2',18)));
            CreateDistanceNew($TourId, $TourType, 'OP%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'FE%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'W1%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'CHC%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'H%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'SU%', array(array('15m-1',15), array('15m-2',15)));
            CreateDistanceNew($TourId, $TourType, 'CRCL%', array(array('10m-1',10), array('10m-2',10)));
            CreateDistanceNew($TourId, $TourType, 'CRCO%', array(array('15m-1',15), array('15m-2',15)));
        } else {
            CreateDistanceNew($TourId, $TourType, 'CLU%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'CLS%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'CO%', array(array('18m-1',18), array('18m-2',18)));
            CreateDistanceNew($TourId, $TourType, 'BB%', array(array('18m-1',18), array('18m-2',18)));
			CreateDistanceNew($TourId, $TourType, 'CLD%', array(array('dist-1',18), array('dist-2',18)));
        }
		break;
	case 7:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25)));
		break;
	case 8:
		CreateDistanceNew($TourId, $TourType, '%', array(array('25m-1',25), array('25m-2',25), array('18m-1',18), array('18m-2',18)));
		break;
    case 50:
		CreateDistanceNew($TourId, $TourType, '%U13%', array(array('30m',30)));
		CreateDistanceNew($TourId, $TourType, '%U15%', array(array('30m',30)));
		CreateDistanceNew($TourId, $TourType, '%U18%', array(array('50m',50)));
		CreateDistanceNew($TourId, $TourType, '%U21%', array(array('50m',50)));
		CreateDistanceNew($TourId, $TourType, '%S%', array(array('50m',50)));
        break;
}

if(in_array($TourType, array(3, 6, 7, 8, 50))) {
	// default Events
	CreateStandardEvents($TourId, $TourType, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $TourType, $SubRule);

	// Finals & TeamFinals
	switch("{$TourType}|{$SubRule}") {
		case '3|7':
			// if D1 before 2023 creates individual finals MatchNo a little differently
			CreateFinals_FR_3_SetFRChampsD1DNAP($TourId);
			break;
		// case '3|12':
		// 	// if D1 before 2023 creates individual finals MatchNo a little differently
		// 	CreateFinalsTeam_FR_3_SetFRD12023($TourId);
		// 	break;
		default:
			CreateFinals($TourId);
	}
}

// Default Target
$TgtId=1;
switch($TourType) {
	case 3:
		switch($SubRule) {
            case 2: // TNJ Tournoi National Jeune  
            case 3: // Championnat de France Jeune
				CreateTargetFace($TourId, 1, 'Blason Complet 80', 'REG-^CLU1[0-7]', '1', 5, 80, 5, 80);
				CreateTargetFace($TourId, 2, 'Blason Classique 122', 'REG-(^CLU18)|(^CLU21)', '1', 5, 122, 5, 122);
                if($SubRule!=15) {
                    CreateTargetFace($TourId, 3, 'Blason Poulies 80', 'CO%', '1',  9, 80, 9, 80);
                }
                break;
            case 15: // Championnat de France Jeune Equipe
				CreateTargetFace($TourId, 1, 'Blason Complet 80', 'REG-^CLU1[0-7]', '1', 5, 80);
				CreateTargetFace($TourId, 2, 'Blason Classique 122', 'REG-(^CLU18)|(^CLU21)', '1', 5, 122);
                break;
			case 9: // Finales des DR
			case 10: // Championnat France Elite
                CreateTargetFace($TourId, 2, 'Blason Poulies 80', 'CO%', '1',  9, 80, 9, 80);
			case 16: // Finales D2
                CreateTargetFace($TourId, 1, 'Blason Classique 122', 'CL%', '1', 5, 122, 5, 122);
                break;
			case 11: // Championnat de France
				CreateTargetFace($TourId, 1, 'Blason Classique 122', 'REG-(^CL)|(^BB)|(^CO.+[WM])', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, 2, 'Blason Poulies 80', 'REG-^CO.+[FH]', '1',  9, 80, 9, 80);
				break;
			case 13: // selectif
			case 14: // selectif + para
				CreateTargetFace($TourId, 1, 'Blason Complet 80', 'REG-(^CLU1[0-7])|(^COU1[0-7])|(^BBU1)|(^C[HR])|(^H)|(^SU)|(^(OPC[LO]|FEC[LO])U1)|(^W1.+[FH])', '1', 5, 80, 5, 80);
				CreateTargetFace($TourId, 2, 'Blason Classique 122', 'REG-(^BBS)|(^BBU21)|(^CL(S|(U18)|(U21)))|(^OPCL(U2|S))|(^OPCO(U2|S).+[WM])|(^FECL(U2|S))|(^FECO(U2|S).+[WM])|(^W1.+[WM]|(^CO.+[WM]))', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, 3, 'Blason Poulies 80', 'REG-(^CO((S.)|(U21)|(U18))[FH])|(^OPCO.+[FH])|(^FECO.+[FH])', '1',  9, 80, 9, 80);
				CreateTargetFace($TourId, 4, 'Blason Débutant/Découverte', 'CLD%', '1',  1, 122, 1, 122);
				break;
			default:
				CreateTargetFace($TourId, 1, 'Blason Classique 80', 'REG-(^CLU1[0-7])|(^BBU)|(^C[HR]CL)|(^[FO]CLU)|(^S)|(^HV)|(^.J)', '1', 5, 80, 5, 80);
				CreateTargetFace($TourId, 2, 'Blason Classique 122', 'REG-(^BBS)|(^CL([DS]|(U18)|(U21)))|(^[FO]CLS)', '1', 5, 122, 5, 122);
				CreateTargetFace($TourId, 3, 'Blason Poulies 80', 'REG-((^CO((S)|(U2)|(U18)).*)|(^C.CO.*)|(^FCO.*)|(^OCO.*)|(^W1.*))[FH]', '1',  9, 80, 9, 80);
				CreateTargetFace($TourId, 4, 'Blason Poulies 122', 'REG-((^CO((S)|(U2)|(U18)).*)|(^C.CO.*)|(^FCO.*)|(^OCO.*)|(^W1.*))[MW]', '1',  5, 122, 5, 122);
				CreateTargetFace($TourId, 5, 'Blason Poulies 80 Complet', 'REG-(^COU13)|(^COU15)', '1',  5, 80, 5, 80);
		}
		break;
	case 6:
		switch($SubRule) {
			case '1': // selectif
			case '4': // selectif + para
				// All classes
				CreateTargetFace($TourId, $TgtId++, 'Blason Unique 40cm', 'REG-(^CLU18)|(^CLU21)|(^CLS)|(^BBS)|(^BBU2)'.($SubRule==4 ? '|(^OPCLS)|(^OPCLU2)|(^FECLS)|(^FECLU2)|(^W1)' : ''), '1', 1, 40, 1, 40);
				CreateTargetFace($TourId, $TgtId++, 'Blason Unique 60cm', 'REG-(^CLU1[35])|(^BBU1)'.($SubRule==4 ? '|(^CH)|(^CR)|(^H)|(^OPC.U1)|(^FEC.U1)' : ''), '1', 1, 60, 1, 60);
				CreateTargetFace($TourId, $TgtId++, 'Blason Unique 80cm', $SubRule==4 ? 'REG-(^CLU11)|(^SU)' : 'CLU11%', '1', 1, 80, 1, 80);
				CreateTargetFace($TourId, $TgtId++, 'Trispot Poulie 6-10 40cm', $SubRule==4 ? 'REG-(^CO)|(^OPCOU2)|(^OPCOS)|(^FECOU2)|(^FECOS)' : 'CO%', '1', 4, 40, 4, 40);
				CreateTargetFace($TourId, $TgtId++, 'Blason Débutant/Découverte', 'CLD%', '1',  1, 80, 1, 80);

				// optional target faces
				CreateTargetFace($TourId, $TgtId++, 'Trispot Classique 6-10 40cm', 'REG-(^CLU18)|(^CLU21)|(^CLS)|(^BBS)|(^BBU2)'.($SubRule==4 ? '|(^OPCLS)|(^OPCLU2)|(^FECLS)|(^FECLU2)|(^W1)' : ''), '',  2, 40, 2, 40);
				CreateTargetFace($TourId, $TgtId++, 'Trispot Classique 6-10 60cm', 'REG-(^CLU1(3|5))|(^BBU1)', '',  2, 60, 2, 60);
				break;
			case '2':
				// Championships Adults
				CreateTargetFace($TourId, $TgtId++, 'Trispot Classique 6-10', 'CL%', '1', 2, 40, 2, 40);
				CreateTargetFace($TourId, $TgtId++, 'Trispot Poulie 6-10', 'CO%', '1', 4, 40, 4, 40);
				CreateTargetFace($TourId, $TgtId++, 'Blason Unique', 'BB%', '1', 1, 40, 1, 40);
				break;
			case '3':
				// Championships Youth
				CreateTargetFace($TourId, $TgtId++, 'Trispot 40cm 6-10', 'REG-^CL(([^U])|(U2)|(U18))', '1', 2, 40, 2, 40);
				CreateTargetFace($TourId, $TgtId++, 'Trispot 60cm 6-10', 'REG-^CLU1[0-7]', '1', 2, 60, 2, 60);
				CreateTargetFace($TourId, $TgtId++, 'Trispot Poulie 6-10', 'CO%', '1', 4, 40, 4, 40);
				CreateTargetFace($TourId, $TgtId++, 'Blason Unique 60cm', 'BB%', '1', 1, 60, 1, 60);
				break;
		}
		break;
	case 7:
		CreateTargetFace($TourId, $TgtId++, 'Blason Unique 60', 'REG-(^CLU18)|(^CLU21)|(^CLS)|(^BBS)|(^BBU21)', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $TgtId++, 'Blason Unique 80', 'REG-(^CLU1[35])|(^BBU18)', '1', 1, 80, 1, 80);
		CreateTargetFace($TourId, $TgtId++, 'Trispot Poulie 6-10', 'CO%', '1', 4, 60, 4, 60);
		// optional target faces
		CreateTargetFace($TourId, $TgtId++, 'Trispot Classique 60', 'REG-(^CLU18)|(^CLU21)|(^CLS)|(^BBS)|(^BBU21)', '', 2, 60, 2, 60);
		break;
	case 8:
		CreateTargetFace($TourId, $TgtId++, 'Blason Unique 60/40', 'REG-(^CLU18)|(^CLU21)|(^CLS)|(^BBS)|(^BBU21)', '1', 1, 60, 1, 60,  1, 40, 1, 40);
		CreateTargetFace($TourId, $TgtId++, 'Blason Unique 80/60', 'REG-(^CLU1[35])|(^BBU18)', '1', 1, 80, 1, 80, 1, 60, 1, 60);
		CreateTargetFace($TourId, $TgtId++, 'Trispot Poulie 6-10', 'CO%', '1', 4, 60, 4, 60, 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, $TgtId++, 'Trispot Classique 60/40', 'REG-(^CLU18)|(^CLU21)|(^CLS)|(^BBS)|(^BBU21)', '',  2, 60, 2, 60, 2, 40, 2, 40);
		break;
    case 50: // Beursault
        switch($SubRule) {
            case 1: // Bouquet
                CreateTargetFace($TourId, $TgtId++, 'Bouquet', '%', '1', 28, 45);
                break;
            case 2: // Beursault
                CreateTargetFace($TourId, $TgtId++, 'Beursault', '%', '1', 27, 45);
                break;
        }
        break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 24, 4);

$tourDetIocCode         = 'FRA';

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

