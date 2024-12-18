<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/
require_once(dirname(dirname(__FILE__)).'/lib.php');

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'FRA';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $TourType, $SubRule) {
    $isTAE=in_array($TourType, [1,2,3,4, 50]);
    $IsIndoor=in_array($TourType, [6,7,8]);
    $isTAENorm=($TourType==3 and $SubRule==13);
    $isTAEPara=(($TourType==3 and $SubRule==14) or ($TourType==6 and $SubRule==4) or ($TourType==7 and $SubRule==4) or ($TourType==8 and $SubRule==4));
    $HasOfficials=(($TourType==3 and in_array($SubRule, [2, 3, 9, 10, 11, 15, 16])) or ($TourType==6 and in_array($SubRule, [2, 3])));
	$i=1;
    CreateDivision($TourId, $i++, 'CL', 'Arc Classique', 1, 'R', 'R');
    if(!in_array($SubRule, [15, 16])) {
        // Finales D2 and Championnats de france Jeunes par Equipes do not have Compounds, Recurve only
        CreateDivision($TourId, $i++, 'CO', 'Arc à Poulies', 1, 'C', 'C');
    }
	if($isTAE or $IsIndoor) {
        if($isTAENorm or $IsIndoor or $isTAEPara or ($TourType==3 and $SubRule==11) or $TourType==50) {
            CreateDivision($TourId, $i++, 'BB', 'Arc Nu', 1, 'B', 'B');
        }
        if($TourType==50) {
            // Beursault
            CreateDivision($TourId, $i++, 'AD', 'Arc Droit', 1, '', '');
        }
        if($isTAEPara) {
            CreateDivision($TourId, $i++, 'OPCL', 'Open Arc Classique', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'OPCO', 'Open Arc à Poulies', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'FECL', 'Fédéral Classique', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'FECO', 'Fédéral Arc à Poulies', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'HV1', 'HV1', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'HV2', 'HV23', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'W1', 'W1', 1, 'W1', 'W1', 1);
            CreateDivision($TourId, $i++, 'CHCL', 'Challenge Arc Classique', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'CHCO', 'Challenge Arc à Poulies', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'CRCL', 'Critérium Arc CLassique', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'CRCO', 'Critérium Arc à Poulies', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'HLCL', 'HV Libre Arc Classique', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'HLCO', 'HV Libre Arc à Poulies', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'SU1', 'Support 1 Arc Classique', 1, '', '', 1);
            CreateDivision($TourId, $i++, 'SU2', 'Support 2 Arc Classique', 1, '', '', 1);
        }
	}
    if($HasOfficials) {
        CreateDivision($TourId, $i++, 'OF', 'Officiels', 0, '', '');
    }
}

function CreateStandardClasses($TourId, $TourType, $SubRule) {
    $NextYearClass=intval(substr($_SESSION['TourRealWhenFrom'], 5,2))>=9 ? -1 : 0;
	$SubYouth=array(1,2,3);
	$SubJuniors=array(1,2,3,9,10,12);
	$SubAdults=array(1,9,10,11,12);
    $isTAESel=($TourType==3 and in_array($SubRule, [13,14]));
    $isTAEPara=(($TourType==3 and $SubRule==14) or ($TourType==6 and $SubRule==4));
    $HasOfficials=(($TourType==3 and in_array($SubRule, [2, 3, 9, 10, 11, 15, 16])) or ($TourType==6 and in_array($SubRule, [2, 3])));
	$i=1;
	switch($TourType) {
		case '6': // INDOORS 18
		case '7': // INDOORS 25
		case '8': // INDOORS 25+18
			switch($SubRule) {
				case '1':
				case '4':
					// All classes...
                    if($TourType==6) {
                        CreateClass($TourId, $i++,  1, 10+$NextYearClass, 1, 'U11F', 'U11F,U13F', 'U11 Femme', '1', 'CL', '', '');
                        CreateClass($TourId, $i++,  1, 10+$NextYearClass, 0, 'U11H', 'U11H,U13H', 'U11 Homme', '1', 'CL', '', '');
                    }
					CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 1, 'U13F', 'U13F,U15F', 'U13 Femme', '1', 'CL,CO'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), '', '');
					CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 0, 'U13H', 'U13H,U15H', 'U13 Homme', '1', 'CL,CO'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), '', '');
					CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 1, 'U15F', 'U15F,U18F', 'U15 Femme', '1', 'BB,CL,CO'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), '', '');
					CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 0, 'U15H', 'U15H,U18H', 'U15 Homme', '1', 'BB,CL,CO'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), '', '');
					CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 1, 'U18F', 'U18F,U21F,S1F', 'U18 Femme', '1', 'BB,CL,CO'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), 'U18W', 'U18W');
					CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 0, 'U18H', 'U18H,U21H,S1H', 'U18 Homme', '1', 'BB,CL,CO'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), 'U18M', 'U18M');
					CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 1, 'U21F', 'U21F,S1F', 'U21 Femme', '1', 'BB,CL,CO'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), 'U21W', 'U21W');
					CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 0, 'U21H', 'U21H,S1H', 'U21 Homme', '1', 'BB,CL,CO'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), 'U21M', 'U21M');
					CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 1, 'S1F', 'S1F', 'Senior 1 Femme', '1', 'BB,CO,CL'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), 'W', 'W');
					CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 0, 'S1H', 'S1H', 'Senior 1 Homme', '1', 'BB,CO,CL'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), 'M', 'M');
					CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 1, 'S2F', 'S2F,S1F', 'Senior 2 Femme', '1', 'BB,CO,CL'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), '', '');
					CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 0, 'S2H', 'S2H,S1H', 'Senior 2 Homme', '1', 'BB,CO,CL'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), '', '');
					CreateClass($TourId, $i++, 60+$NextYearClass,100, 1, 'S3F', 'S3F,S2F,S1F', 'Senior 3 Femme', '1', 'BB,CO,CL'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), '50W', '50W');
					CreateClass($TourId, $i++, 60+$NextYearClass,100, 0, 'S3H', 'S3H,S2H,S1H', 'Senior 3 Homme', '1', 'BB,CO,CL'.($isTAEPara?',OPCL,FECL,OPCO,FECO,W1,CHCL,CRCL,CHCO,CRCO,HV1,HV2,HLCL,HLCO,SU1,SU2':''), '50M', '50M');
                    CreateClass($TourId, $i++, 1,127, -1, 'DEC', 'DEC', 'Découverte', '1', 'CL', '', '');
                    CreateClass($TourId, $i++, 1,127, -1, 'DEB', 'DEB', 'Débutant', '1', 'CL', '', '');
					break;
				case '2':
					// Championships Adults and Elite...
					CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 1, 'U21F', 'U21F,S1F', 'U21 Femme', '1', 'BB', 'U21W', 'U21W');
					CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 0, 'U21H', 'U21H,S1H', 'U21 Homme', '1', 'BB', 'U21M', 'U21M');
					CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 1, 'S1F', 'S1F', 'Senior 1 Femme', '1', 'BB,CL,CO', 'M', 'W');
					CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 0, 'S1H', 'S1H', 'Senior 1 Homme', '1', 'BB,CL,CO', 'W', 'M');
					CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 1, 'S2F', 'S2F,S1F', 'Senior 2 Femme', '1', 'BB,CL,CO', '', '');
					CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 0, 'S2H', 'S2H,S1H', 'Senior 2 Homme', '1', 'BB,CL,CO', '', '');
					CreateClass($TourId, $i++, 60+$NextYearClass,100, 1, 'S3F', 'S3F,S2F,S1F', 'Senior 3 Femme', '1', 'BB,CL,CO', '50W', '50W');
					CreateClass($TourId, $i++, 60+$NextYearClass,100, 0, 'S3H', 'S3H,S2H,S1H', 'Senior 3 Homme', '1', 'BB,CL,CO', '50M', '50M');

                    CreateSubClass($TourId, '1','A','Adulte');
                    CreateSubClass($TourId, '2','E','Elite');
                    break;
				case '3':
					// Championships Youth...
					CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 1, 'U13F', 'U13F,U15F', 'U13 Femme', '1', 'CL', '', '');
					CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 0, 'U13H', 'U13H,U15H', 'U13 Homme', '1', 'CL', '', '');
					CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 1, 'U15F', 'U15F,U18F', 'U15 Femme', '1', 'BB,CL', '', '');
					CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 0, 'U15H', 'U15H,U18H', 'U15 Homme', '1', 'BB,CL', '', '');
					CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 1, 'U18F', 'U18F,U21F', 'U18 Femme', '1', 'BB,CL,CO', 'U18W', 'U18W');
					CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 0, 'U18H', 'U18H,U21H', 'U18 Homme', '1', 'BB,CL,CO', 'U18M', 'U18M');
					CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 1, 'U21F', 'U21F,1F', 'U21 Femme', '1', 'CL,CO', 'U21W', 'U21W');
					CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 0, 'U21H', 'U21H,1H', 'U21 Homme', '1', 'CL,CO', 'U21M', 'U21M');
					break;
			}
			break;
		case '50': // Beursault
            CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 1, 'U13F', 'U13F,U15F', 'U13 Femme', '1', '', '', '');
            CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 0, 'U13H', 'U13H,U15H', 'U13 Homme', '1', '', '', '');
            CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 1, 'U15F', 'U15F,U18F', 'U15 Femme', '1', '', '', '');
            CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 0, 'U15H', 'U15H,U18H', 'U15 Homme', '1', '', '', '');
            CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 1, 'U18F', 'U18F,U21F,S1F', 'U18 Femme', '1', '', '', '');
            CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 0, 'U18H', 'U18H,U21H,S1H', 'U18 Homme', '1', '', '', '');
            CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 1, 'U21F', 'U21F,S1F', 'U21 Femme', '1', '', '', '');
            CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 0, 'U21H', 'U21H,S1H', 'U21 Homme', '1', '', '', '');
            CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 1, 'S1F', 'S1F', 'Senior 1 Femme', '1', '', '', '');
            CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 0, 'S1H', 'S1H', 'Senior 1 Homme', '1', '', '', '');
            CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 1, 'S2F', 'S2F,S1F', 'Senior 2 Femme', '1', '', '', '');
            CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 0, 'S2H', 'S2H,S1H', 'Senior 2 Homme', '1', '', '', '');
            CreateClass($TourId, $i++, 60+$NextYearClass,100, 1, 'S3F', 'S3F,S2F,S1F', 'Senior 3 Femme', '1', '', '', '');
            CreateClass($TourId, $i++, 60+$NextYearClass,100, 0, 'S3H', 'S3H,S2H,S1H', 'Senior 3 Homme', '1', '', '', '');
			break;
		case '3': // 72 arrows round
            switch($SubRule) {
                case 2: // TNJ Tournoi National Jeune
                case 3: // Championnat de France Jeune
                case 15: // Championnat de France Jeune Equipes
                    CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 1, 'U13F', 'U13F,U15F', 'U13 Femmes', '1', 'CL', '', '');
                    CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 0, 'U13H', 'U13H,U15H', 'U13 Hommes', '1', 'CL', '', '');
                    CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 1, 'U15F', 'U15F,U18F', 'U15 Femmes', '1', 'CL', '', '');
                    CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 0, 'U15H', 'U15H,U18H', 'U15 Hommes', '1', 'CL', '', '');
                    CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 1, 'U18F', 'U18F,U21F,S1F', 'U18 Femmes', '1', 'CL,CO', 'U18W', 'U18W');
                    CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 0, 'U18H', 'U18H,U21H,S1H', 'U18 Hommes', '1', 'CL,CO', 'U18M', 'U18M');
                    CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 1, 'U21F', 'U21F,S1F', 'U21 Femmes', '1', 'CL,CO', 'U21W', 'U21W');
                    CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 0, 'U21H', 'U21H,S1H', 'U21 Hommes', '1', 'CL,CO', 'U21M', 'U21M');
                    break;
                case 9: // Finales DR
                case 10: // Championnats France Elite
                case 16: // Finales D2
                    CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 1, 'U18F', 'U18F,U21F,S1F', 'U18 Femmes', '1', 'CL,CO', 'U18W', 'U18W');
                    CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 0, 'U18H', 'U18H,U21H,S1H', 'U18 Hommes', '1', 'CL,CO', 'U18M', 'U18M');
                    CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 1, 'U21F', 'U21F,S1F', 'U21 Femmes', '1', 'CL,CO', 'U21W', 'U21W');
                    CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 0, 'U21H', 'U21H,S1H', 'U21 Hommes', '1', 'CL,CO', 'U21M', 'U21M');
                    CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 1, 'S1F', 'S1F', 'Senior 1 Femmes', '1', 'CL,CO', 'W', 'W');
                    CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 0, 'S1H', 'S1H', 'Senior 1 Hommes', '1', 'CL,CO', 'M', 'M');
                    CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 1, 'S2F', 'S2F,S1F', 'Senior 2 Femmes', '1', 'CL,CO', '', '');
                    CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 0, 'S2H', 'S2H,S1H', 'Senior 2 Hommes', '1', 'CL,CO', '', '');
                    CreateClass($TourId, $i++, 60+$NextYearClass,127, 1, 'S3F', 'S3F,S2F,S1F', 'Senior 3 Femmes', '1', 'CL,CO', '50W', '50W');
                    CreateClass($TourId, $i++, 60+$NextYearClass,127, 0, 'S3H', 'S3H,S2H,S1H', 'Senior 3 Hommes', '1', 'CL,CO', '50M', '50M');
                    break;
                case 11: // Championnat de France Adulte
                    CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 1, 'S1F', 'S1F', 'Senior 1 Femmes', '1', 'CL,CO', 'W', 'W');
                    CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 0, 'S1H', 'S1H', 'Senior 1 Hommes', '1', 'CL,CO', 'M', 'M');
                    CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 1, 'S2F', 'S2F,S1F', 'Senior 2 Femmes', '1', 'CL,CO', '', '');
                    CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 0, 'S2H', 'S2H,S1H', 'Senior 2 Hommes', '1', 'CL,CO', '', '');
                    CreateClass($TourId, $i++, 60+$NextYearClass,127, 1, 'S3F', 'S3F,S2F,S1F', 'Senior 3 Femmes', '1', 'CL,CO', '50W', '50W');
                    CreateClass($TourId, $i++, 60+$NextYearClass,127, 0, 'S3H', 'S3H,S2H,S1H', 'Senior 3 Hommes', '1', 'CL,CO', '50M', '50M');
                    CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 1, 'S1W', 'S1W', 'Senior 1 Femmes National', '1', 'CL,CO,BB', '', '');
                    CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 0, 'S1M', 'S1M', 'Senior 1 Hommes National', '1', 'CL,CO,BB', '', '');
                    CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 1, 'S2W', 'S2W,S1W', 'Senior 2 Femmes National', '1', 'CL,CO,BB', '', '');
                    CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 0, 'S2M', 'S2M,S1M', 'Senior 2 Hommes National', '1', 'CL,CO,BB', '', '');
                    CreateClass($TourId, $i++, 60+$NextYearClass,127, 1, 'S3W', 'S3W,S2W,S1W', 'Senior 3 Femmes National', '1', 'CL,CO,BB', '', '');
                    CreateClass($TourId, $i++, 60+$NextYearClass,127, 0, 'S3M', 'S3M,S2M,S1M', 'Senior 3 Hommes National', '1', 'CL,CO,BB', '', '');
                    break;
                case 13:
                case 14:
                    CreateClass($TourId, $i++,  1, 10+$NextYearClass, 1, 'U11F', 'U11F,U13F', 'U11 Femmes', '1', 'CL', '', '');
                    CreateClass($TourId, $i++,  1, 10+$NextYearClass, 0, 'U11H', 'U11H,U13H', 'U11 Hommes', '1', 'CL', '', '');
                    CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 1, 'U13F', 'U13F,U15F', 'U13 Femmes', '1', 'CL'.($isTAEPara?',HV1,HV2,W1':''), '', '');
                    CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 0, 'U13H', 'U13H,U15H', 'U13 Hommes', '1', 'CL'.($isTAEPara?',HV1,HV2,W1':''), '', '');
                    CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 1, 'U15F', 'U15F,U18F', 'U15 Femmes', '1', 'CL'.($isTAEPara?',HV1,HV2,W1':''), '', '');
                    CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 0, 'U15H', 'U15H,U18H', 'U15 Hommes', '1', 'CL'.($isTAEPara?',HV1,HV2,W1':''), '', '');
                    CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 1, 'U18F', 'U18F,U21F,S1F', 'U18 Femmes', '1', 'CL,CO'.(($isTAEPara)?',HV1,HV2,W1':''), 'U18W', 'U18W');
                    CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 0, 'U18H', 'U18H,U21H,S1H', 'U18 Hommes', '1', 'CL,CO'.($isTAEPara?',HV1,HV2,W1':''), 'U18M', 'U18M');
                    CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 1, 'U21F', 'U21F,S1F', 'U21 Femmes', '1', 'CL,CO'.($isTAEPara?',FECL,FECO,OPCL,OPCO,HV1,HV2,W1':''), 'U21W', 'U21W');
                    CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 0, 'U21H', 'U21H,S1H', 'U21 Hommes', '1', 'CL,CO'.($isTAEPara?',FECL,FECO,OPCL,OPCO,HV1,HV2,W1':''), 'U21M', 'U21M');
                    CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 1, 'S1F', 'S1F', 'Senior 1 Femmes', '1', 'CL,CO'.($isTAEPara?',FECL,FECO,OPCL,OPCO,HV1,HV2,W1':''), 'W', 'W');
                    CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 0, 'S1H', 'S1H', 'Senior 1 Hommes', '1', 'CL,CO'.($isTAEPara?',FECL,FECO,OPCL,OPCO,HV1,HV2,W1':''), 'M', 'M');
                    CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 1, 'S2F', 'S2F,S1F', 'Senior 2 Femmes', '1', 'CL,CO'.($isTAEPara?',FECL,FECO,OPCL,OPCO,HV1,HV2,W1':''), '', '');
                    CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 0, 'S2H', 'S2H,S1H', 'Senior 2 Hommes', '1', 'CL,CO'.($isTAEPara?',FECL,FECO,OPCL,OPCO,HV1,HV2,W1':''), '', '');
                    CreateClass($TourId, $i++, 60+$NextYearClass,127, 1, 'S3F', 'S3F,S2F,S1F', 'Senior 3 Femmes', '1', 'CL,CO'.($isTAEPara?',FECL,FECO,OPCL,OPCO,HV1,HV2,W1':''), '50W', '50W');
                    CreateClass($TourId, $i++, 60+$NextYearClass,127, 0, 'S3H', 'S3H,S2H,S1H', 'Senior 3 Hommes', '1', 'CL,CO'.($isTAEPara?',FECL,FECO,OPCL,OPCO,HV1,HV2,W1':''), '50M', '50M');
                    CreateClass($TourId, $i++, 1,127, -1, 'DEC', 'DEC', 'Découverte', '1', 'CL', '', '');
                    CreateClass($TourId, $i++, 1,127, -1, 'DEB', 'DEB', 'Débutant', '1', 'CL', '', '');
                    CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 1, 'U13W', 'U13W,U15W', 'U13 Femmes National', '1', 'CL,CO'.($isTAEPara?',CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,OPCL,OPCO,FECL,FECO,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 11+$NextYearClass, 12+$NextYearClass, 0, 'U13M', 'U13M,U15M', 'U13 Hommes National', '1', 'CL,CO'.($isTAEPara?',CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,OPCL,OPCO,FECL,FECO,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 1, 'U15W', 'U15W,U18W', 'U15 Femmes National', '1', 'CL,CO,BB'.($isTAEPara?',CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,OPCL,OPCO,FECL,FECO,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 13+$NextYearClass, 14+$NextYearClass, 0, 'U15M', 'U15M,U18M', 'U15 Hommes National', '1', 'CL,CO,BB'.($isTAEPara?',CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,OPCL,OPCO,FECL,FECO,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 1, 'U18W', 'U18W,U21W,S1W', 'U18 Femmes National', '1', 'CL,CO,BB'.($isTAEPara?',CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,OPCL,OPCO,FECL,FECO,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 15+$NextYearClass, 17+$NextYearClass, 0, 'U18M', 'U18M,U21M,S1M', 'U18 Hommes National', '1', 'CL,CO,BB'.($isTAEPara?',CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,OPCL,OPCO,FECL,FECO,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 1, 'U21W', 'U21W,S1W', 'U21 Femmes National', '1', 'CL,CO,BB'.($isTAEPara?',OPCL,OPCO,FECL,FECO,CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 18+$NextYearClass, 20+$NextYearClass, 0, 'U21M', 'U21M,S1M', 'U21 Hommes National', '1', 'CL,CO,BB'.($isTAEPara?',OPCL,OPCO,FECL,FECO,CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 1, 'S1W', 'S1W', 'Senior 1 Femmes National', '1', 'CL,CO,BB'.($isTAEPara?',OPCL,OPCO,FECL,FECO,CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 21+$NextYearClass, 39+$NextYearClass, 0, 'S1M', 'S1M', 'Senior 1 Hommes National', '1', 'CL,CO,BB'.($isTAEPara?',OPCL,OPCO,FECL,FECO,CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 1, 'S2W', 'S2W,S1W', 'Senior 2 Femmes National', '1', 'CL,CO,BB'.($isTAEPara?',OPCL,OPCO,FECL,FECO,CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 40+$NextYearClass, 59+$NextYearClass, 0, 'S2M', 'S2M,S1M', 'Senior 2 Hommes National', '1', 'CL,CO,BB'.($isTAEPara?',OPCL,OPCO,FECL,FECO,CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 60+$NextYearClass,127, 1, 'S3W', 'S3W,S2W,S1W', 'Senior 3 Femmes National', '1', 'CL,CO,BB'.($isTAEPara?',OPCL,OPCO,FECL,FECO,CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,SU1,SU2':''), '', '');
                    CreateClass($TourId, $i++, 60+$NextYearClass,127, 0, 'S3M', 'S3M,S2M,S1M', 'Senior 3 Hommes National', '1', 'CL,CO,BB'.($isTAEPara?',OPCL,OPCO,FECL,FECO,CHCL,CHCO,CRCL,CRCO,HLCL,HLCO,W1,SU1,SU2':''), '', '');
                break;
            }
			break;
	}
    if($HasOfficials) {
        CreateClass($TourId, $i++, 1,127, -1, 'EN', 'EN', 'Entraîneur', '0', 'OF', '', '');
        CreateClass($TourId, $i++, 1,127, -1, 'ENP', 'ENP', 'Entraîneur de Pôle', '0', 'OF', '', '');
        CreateClass($TourId, $i++, 1,127, -1, 'CT', 'CT', 'Cadre Technique FFTA', '0', 'OF', '', '');
        CreateClass($TourId, $i++, 1,127, -1, 'PR', 'PR', 'Presse', '0', 'OF', '', '');
        CreateClass($TourId, $i++, 1,127, -1, 'VIP', 'VIP', 'VIP', '0', 'OF', '', '');
    }
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=false) {
	$i=1;
	switch($TourType) {
		case 3: // Outdoor championships
			switch($SubRule) {
				case 15: // Championnat France Equipe
                    CreateEvent($TourId, $i++, 1, 0, 8, 5, 4, 6, 3, 4, 6, 3, 'U15M', 'U15 Mixte',   1, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 40);
                    CreateEvent($TourId, $i++, 1, 0, 2, 5, 4, 6, 3, 4, 6, 3, 'U15M2', 'U15 Mixte (5-8)',   1, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 40, 'U15M', '0', '0', 5);
                    CreateEvent($TourId, $i++, 1, 0, 4, 5, 4, 6, 3, 4, 6, 3, 'U15M3', 'U15 Mixte (9-12)',   1, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 40, 'U15M', '0', '0', 9);
                    CreateEvent($TourId, $i++, 1, 0, 2, 5, 4, 6, 3, 4, 6, 3, 'U15M4', 'U15 Mixte (13-16)',   1, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 40, 'U15M3', '0', '0', 13);
                    CreateEvent($TourId, $i++, 1, 0, 8, 5, 4, 6, 3, 4, 6, 3, 'U21F', 'U21 Femme',    1, 0, MATCH_ALL_SEP, 0, 0, '', '', 122, 60);
                    CreateEvent($TourId, $i++, 1, 0, 2, 5, 4, 6, 3, 4, 6, 3, 'U21F2', 'U21 Femme (5-8)',    1, 0, MATCH_ALL_SEP, 0, 0, '', '', 122, 60, 'U21F', '0', '0', 5);
                    CreateEvent($TourId, $i++, 1, 0, 4, 5, 4, 6, 3, 4, 6, 3, 'U21F3', 'U21 Femme (9-12)',    1, 0, MATCH_ALL_SEP, 0, 0, '', '', 122, 60, 'U21F', '0', '0', 9);
                    CreateEvent($TourId, $i++, 1, 0, 2, 5, 4, 6, 3, 4, 6, 3, 'U21F4', 'U21 Femme (13-16)',    1, 0, MATCH_ALL_SEP, 0, 0, '', '', 122, 60, 'U21F3', '0', '0', 13);
                    CreateEvent($TourId, $i++, 1, 0, 8, 5, 4, 6, 3, 4, 6, 3, 'U21H', 'U21 Homme',    1, 0, MATCH_ALL_SEP, 0, 0, '', '', 122, 60);
                    CreateEvent($TourId, $i++, 1, 0, 2, 5, 4, 6, 3, 4, 6, 3, 'U21H2', 'U21 Homme (5-8)',    1, 0, MATCH_ALL_SEP, 0, 0, '', '', 122, 60, 'U21H', '0', '0', 5);
                    CreateEvent($TourId, $i++, 1, 0, 4, 5, 4, 6, 3, 4, 6, 3, 'U21H3', 'U21 Homme (9-12)',    1, 0, MATCH_ALL_SEP, 0, 0, '', '', 122, 60, 'U21H', '0', '0', 9);
                    CreateEvent($TourId, $i++, 1, 0, 2, 5, 4, 6, 3, 4, 6, 3, 'U21H4', 'U21 Homme (13-16)',    1, 0, MATCH_ALL_SEP, 0, 0, '', '', 122, 60, 'U21H3', '0', '0', 13);
                    break;
				case 2: // TNJ
					// Individuals
					CreateEvent($TourId, $i++, 0, 0, 7, 5, 5, 3, 1, 5, 3, 1, 'U13FCL', 'U13 Femme Arc Classique', 1, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 30);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U13FCL2', 'U13 Femme Arc Classique (5-8)', 1, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 30, 'U13FCL', '0', '0', 5);
                    CreateEvent($TourId, $i++, 0, 0, 7, 5, 5, 3, 1, 5, 3, 1, 'U13HCL', 'U13 Homme Arc Classique', 1, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 30);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U13HCL2', 'U13 Homme Arc Classique (5-8)', 1, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 30, 'U13HCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U15FCL', 'U15 Femme Arc Classique',   1, 0, MATCH_SEP_GOLD, 0, 0, '', '',  80, 40);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U15FCL2', 'U15 Femme Arc Classique (5-8)',   1, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 40, 'U15FCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U15FCL3', 'U15 Femme Arc Classique (9-12)',   1, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 40, 'U15FCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U15FCL4', 'U15 Femme Arc Classique (13-16)',   1, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 40, 'U15FCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U15HCL', 'U15 Homme Arc Classique',   1, 0, MATCH_SEP_GOLD, 0, 0, '', '',  80, 40);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U15HCL2', 'U15 Homme Arc Classique (5-8)',   1, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 40, 'U15HCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U15HCL3', 'U15 Homme Arc Classique (9-12)',   1, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 40, 'U15HCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U15HCL4', 'U15 Homme Arc Classique (13-16)',   1, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 40, 'U15HCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U18FCL', 'U18 Femme Arc Classique',    1, 0, MATCH_SEP_GOLD, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U18FCL2', 'U18 Femme Arc Classique (5-8)',    1, 0, MATCH_NO_SEP, 0, 0, '', '', 122, 60, 'U18FCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U18FCL3', 'U18 Femme Arc Classique (9-12)',    1, 0, MATCH_NO_SEP, 0, 0, '', '', 122, 60, 'U18FCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U18FCL4', 'U18 Femme Arc Classique (13-16)',    1, 0, MATCH_NO_SEP, 0, 0, '', '', 122, 60, 'U18FCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U18HCL', 'U18 Homme Arc Classique',    1, 0, MATCH_SEP_GOLD, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U18HCL2', 'U18 Homme Arc Classique (5-8)',    1, 0, MATCH_NO_SEP, 0, 0, '', '', 122, 60, 'U18HCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U18HCL3', 'U18 Homme Arc Classique (9-12)',    1, 0, MATCH_NO_SEP, 0, 0, '', '', 122, 60, 'U18HCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U18HCL4', 'U18 Homme Arc Classique (13-16)',    1, 0, MATCH_NO_SEP, 0, 0, '', '', 122, 60, 'U18HCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0, 7, 5, 5, 3, 1, 5, 3, 1, 'U21FCL', 'U21 Femme Arc Classique',   1, 0, MATCH_SEP_GOLD, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U21FCL2', 'U21 Femme Arc Classique (5-8)',   1, 0, MATCH_NO_SEP, 0, 0, '', '', 122, 70, 'U21FCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U21HCL', 'U21 Homme Arc Classique',   1, 0, MATCH_SEP_GOLD, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U21HCL2', 'U21 Homme Arc Classique (5-8)',   1, 0, MATCH_NO_SEP, 0, 0, '', '', 122, 70, 'U21HCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U21HCL3', 'U21 Homme Arc Classique (9-12)',   1, 0, MATCH_NO_SEP, 0, 0, '', '', 122, 70, 'U21HCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U21HCL4', 'U21 Homme Arc Classique (13-16)',   1, 0, MATCH_NO_SEP, 0, 0, '', '', 122, 70, 'U21HCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0, 7, 9, 5, 3, 1, 5, 3, 1, 'U21FCO', 'U21 Femme Arc à Poulies',     0, 0, MATCH_SEP_GOLD, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'U21FCO2', 'U21 Femme Arc à Poulies (5-8)',     0, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 50, 'YFCO', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0, 7, 9, 5, 3, 1, 5, 3, 1, 'U21HCO', 'U21 Homme Arc à Poulies',     0, 0, MATCH_SEP_GOLD, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'U21HCO2', 'U21 Homme Arc à Poulies (5-8)',     0, 0, MATCH_NO_SEP, 0, 0, '', '',  80, 50, 'YHCO', '0', '0', 5);
					// Team
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  4, 5, 4, 4, 2, 4, 4, 2, 'DMCLU21', 'Double Mixte U21 Arc Classique',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 1,  4, 5, 4, 4, 2, 4, 4, 2, 'DMCLU18', 'Double Mixte U18 Arc Classique',   1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60);
					CreateEvent($TourId, $i++, 1, 1,  4, 9, 4, 4, 2, 4, 4, 2, 'DMCOU21', 'Double Mixte U21 Arc à Poulies',    0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					// always second team!
					safe_w_sql("update Events set EvTeamCreationMode=2 where EvTeamEvent=1 and EvTournament=$TourId");
					break;
				case 3: // Championships Youth
                    $Options=[
                        'EvFinalFirstPhase' => 0,
                        'EvNumQualified'=>0,
                        'EvFinalTargetType'=>0,
                        'EvTargetSize'=>40,
                        'EvDistance'=>0,
                        'EvFinalAthTarget'=>MATCH_SEP_FROM_2,
                        'EvMatchMode'=>0,
                        'EvMatchArrowsNo'=>0,
                        'EvElimEnds'=>5,
                        'EvElimArrows'=>3,
                        'EvElimSO'=>1,
                        'EvFinEnds'=>5,
                        'EvFinArrows'=>3,
                        'EvFinSO'=>1,
                        'EvRecCategory'=>'',
                        'EvWaCategory'=>'',
                    ];
                    $Events=[
                        'U13FCL'=>['U13 Femme Arc Classique', 7, 5, 80, 30, 1],
                        'U13HCL'=>['U13 Homme Arc Classique', 14, 5, 80, 30, 1],
                        'U15FCL'=>['U15 Femme Arc Classique', 14, 5, 80, 40, 1],
                        'U15HCL'=>['U15 Homme Arc Classique', 14, 5, 80, 40, 1],
                        'U18FCL'=>['U18 Femme Arc Classique', 14, 5, 122, 60, 1],
                        'U18HCL'=>['U18 Homme Arc Classique', 14, 5, 122, 60, 1],
                        'U21FCL'=>['U21 Femme Arc Classique', 7, 5, 122, 70, 1],
                        'U21HCL'=>['U21 Homme Arc Classique', 14, 5, 122, 70, 1],
                        'U21FCO'=>['U21 Femme Arc à Poulies', 4, 9, 80, 50, 0],
                        'U21HCO'=>['U21 Homme Arc à Poulies', 7, 9, 80, 50, 0],
                        'DMJ'=>['Double Mixte Jeunes', 8, 5, 122, 60, 0],
                    ];

                    foreach($Events as $EvCode=>$EvName) {
                        $Options['EvFinalFirstPhase']=$EvName[1];
                        $Options['EvNumQualified']=$EvName[1]*2;
                        $Options['EvFinalTargetType']=$EvName[2];
                        $Options['EvTargetSize']=$EvName[3];
                        $Options['EvDistance']=$EvName[4];
                        $Options['EvMatchMode']=$EvName[5];
                        if($EvCode=='DMJ') {
                            $Options['EvTeamEvent']=1;
                            $Options['EvMixedTeam']=1;
                            $Options['EvMaxTeamPerson']=2;
                            $Options['EvElimEnds']=4;
                            $Options['EvElimArrows']=4;
                            $Options['EvElimSO']=2;
                            $Options['EvFinEnds']=4;
                            $Options['EvFinArrows']=4;
                            $Options['EvFinSO']=2;
                        }
                        CreateEventNew($TourId, $EvCode, $EvName[0], $i++, $Options);
                    }
					break;
				case 9: // Finales des DR
                    // Only Teams with losers brackets, start with Compound Women
                    CreateEvent($TourId, $i++, 1, 0,  8, 9, 4, 6, 3, 4, 6, 3, 'DRCF', 'Equipes DR Poulies Femme',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
                    CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CF05', 'Equipes DR Poulies Femme (5-8)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCF', '0', '0', 5);
                    CreateEvent($TourId, $i++, 1, 0,  4, 9, 4, 6, 3, 4, 6, 3, 'CF09', 'Equipes DR Poulies Femme (9-12)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCF', '0', '0', 9);
                    CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CF13', 'Equipes DR Poulies Femme (13-16)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'CF09', '0', '0', 13);

                    // Compound Men
                    CreateEvent($TourId, $i++, 1, 0,  12, 9, 4, 6, 3, 4, 6, 3, 'DRCH', 'Equipes DR Poulies Homme',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
                    CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CH05', 'Equipes DR Poulies Homme (5-8)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCH', '0', '0', 5);
                    CreateEvent($TourId, $i++, 1, 0,  4, 9, 4, 6, 3, 4, 6, 3, 'CH09', 'Equipes DR Poulies Homme (9-12)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCH', '0', '0', 9);
                    CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CH13', 'Equipes DR Poulies Homme (13-16)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'CH09', '0', '0', 13);
                    CreateEvent($TourId, $i++, 1, 0,  8, 9, 4, 6, 3, 4, 6, 3, 'CH17', 'Equipes DR Poulies Homme (17-20)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCH', '0', '8', 17);
                    CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CH21', 'Equipes DR Poulies Homme (21-24)',   0, 0, MATCH_ALL_SEP, 0, 0, '', '',   80, 50, 'CH17', '0', '0', 21);

                    // Recurve Women
                    CreateEvent($TourId, $i++, 1, 0,  12, 5, 4, 6, 3, 4, 6, 3, 'DRRF', 'Equipes DR Classique Femme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
                    CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RF05', 'Equipes DR Classique Femme (5-8)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRF', '0', '0', 5);
                    CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'RF09', 'Equipes DR Classique Femme (9-12)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRF', '0', '0', 9);
                    CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RF13', 'Equipes DR Classique Femme (13-16)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RF09', '0', '0', 13);
                    CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'RF17', 'Equipes DR Classique Femme (17-20)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRF', '0', '8', 17);
                    CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RF21', 'Equipes DR Classique Femme (21-24)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RF17', '0', '0', 21);

                    // Recurve Men
                    CreateEvent($TourId, $i++, 1, 0,  12, 5, 4, 6, 3, 4, 6, 3, 'DRRH', 'Equipes DR Classique Homme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
                    CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RH05', 'Equipes DR Classique Homme (5-8)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRH', '0', '0', 5);
                    CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'RH09', 'Equipes DR Classique Homme (9-12)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRH', '0', '0', 9);
                    CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RH13', 'Equipes DR Classique Homme (13-16)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RH09', '0', '0', 13);
                    CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'RH17', 'Equipes DR Classique Homme (17-20)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRH', '0', '8', 17);
                    CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RH21', 'Equipes DR Classique Homme (21-24)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RH17', '0', '0', 21);
                    break;
				case 16: // Finales D2
                    // Only Teams with losers brackets, start with Recurve Women
                    CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'D2F', 'Equipes D2 Femme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
                    CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DF05', 'Equipes D2 Femme (5-8)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2F', '0', '0', 5);
                    CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'DF09', 'Equipes D2 Femme (9-12)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2F', '0', '0', 9);
                    CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DF13', 'Equipes D2 Femme (13-16)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DF09', '0', '0', 13);

                    // Recruve Men
                    CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'D2H', 'Equipes D2 Homme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
                    CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DH05', 'Equipes D2 Homme (5-8)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2H', '0', '0', 5);
                    CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'DH09', 'Equipes D2 Homme (9-12)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2H', '0', '0', 9);
                    CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DH13', 'Equipes D2 Homme (13-16)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DH09', '0', '0', 13);
                    break;
				case 10: // Championnats France Elite
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'EFCL', 'Elite Femme Arc Classique',   1, 0, MATCH_SEP_FROM_4, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'EHCL', 'Elite Homme Arc Classique',   1, 0, MATCH_SEP_FROM_4, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, 'EFCO', 'Elite Femme Arc à Poulies',   0, 0, MATCH_SEP_FROM_4, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 16, 9, 5, 3, 1, 5, 3, 1, 'EHCO', 'Elite Homme Arc à Poulies',   0, 0, MATCH_SEP_FROM_4, 0, 0, '', '',  80, 50);

					// MIXED TEAMS
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  8, 5, 4, 4, 2, 4, 4, 2, 'DMCL', 'Double Mixte Classique',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 1,  8, 9, 4, 4, 2, 4, 4, 2, 'DMCO', 'Double Mixte Poulie',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
                    break;
				case 11: // Championnat de France Adulte
                    $Options=[
                        'EvFinalFirstPhase' => 2,
                        'EvNumQualified'=>4,
                        'EvFinalTargetType'=>0,
                        'EvTargetSize'=>40,
                        'EvDistance'=>0,
                        'EvFinalAthTarget'=>MATCH_SEP_FROM_2,
                        'EvMatchMode'=>0,
                        'EvMatchArrowsNo'=>0,
                        'EvElimEnds'=>5,
                        'EvElimArrows'=>3,
                        'EvElimSO'=>1,
                        'EvFinEnds'=>5,
                        'EvFinArrows'=>3,
                        'EvFinSO'=>1,
                        'EvRecCategory'=>'',
                        'EvWaCategory'=>'',
                    ];
                    $Events=[
                        'S1FCL'=>['Senior 1 Femme Arc Classique', 8, 5, 122, 70, 1],
                        'S1HCL'=>['Senior 1 Homme Arc Classique', 8, 5, 122, 70, 1],
                        'S2FCL'=>['Senior 2 Femme Arc Classique', 8, 5, 122, 70, 1],
                        'S2HCL'=>['Senior 2 Homme Arc Classique', 8, 5, 122, 70, 1],
                        'S3FCL'=>['Senior 3 Femme Arc Classique', 4, 5, 122, 60, 1],
                        'S3HCL'=>['Senior 3 Homme Arc Classique', 8, 5, 122, 60, 1],
                        'S1FCO'=>['Senior 1 Femme Arc à Poulies', 4, 9, 80, 50, 0],
                        'S1HCO'=>['Senior 1 Homme Arc à Poulies', 8, 9, 80, 50, 0],
                        'S2FCO'=>['Senior 2 Femme Arc à Poulies', 4, 9, 80, 50, 0],
                        'S2HCO'=>['Senior 2 Homme Arc à Poulies', 8, 9, 80, 50, 0],
                        'S3FCO'=>['Senior 3 Femme Arc à Poulies', 4, 9, 80, 50, 0],
                        'S3HCO'=>['Senior 3 Homme Arc à Poulies', 8, 9, 80, 50, 0],
                        'NS1FCL'=>['Senior 1 Femme Arc Classique National', 0, 5, 122, 50, 1],
                        'NS1HCL'=>['Senior 1 Homme Arc Classique National', 0, 5, 122, 50, 1],
                        'NS2FCL'=>['Senior 2 Femme Arc Classique National', 0, 5, 122, 50, 1],
                        'NS2HCL'=>['Senior 2 Homme Arc Classique National', 0, 5, 122, 50, 1],
                        'NS3FCL'=>['Senior 3 Femme Arc Classique National', 0, 5, 122, 50, 1],
                        'NS3HCL'=>['Senior 3 Homme Arc Classique National', 0, 5, 122, 50, 1],
                        'NS1FCO'=>['Senior 1 Femme Arc à Poulies National', 0, 5, 122, 50, 0],
                        'NS1HCO'=>['Senior 1 Homme Arc à Poulies National', 0, 5, 122, 50, 0],
                        'NS2FCO'=>['Senior 2 Femme Arc à Poulies National', 0, 5, 122, 50, 0],
                        'NS2HCO'=>['Senior 2 Homme Arc à Poulies National', 0, 5, 122, 50, 0],
                        'NS3FCO'=>['Senior 3 Femme Arc à Poulies National', 0, 5, 122, 50, 0],
                        'NS3HCO'=>['Senior 3 Homme Arc à Poulies National', 0, 5, 122, 50, 0],
                        'NFBB'=>['Femme Arc Nu National', 0, 5, 122, 50, 0],
                        'NHBB'=>['Homme Arc Nu National', 0, 5, 122, 50, 0],
                    ];

                    foreach($Events as $EvCode=>$EvName) {
                        $Options['EvFinalFirstPhase']=$EvName[1];
                        $Options['EvNumQualified']=$EvName[1]*2;
                        $Options['EvFinalTargetType']=$EvName[2];
                        $Options['EvTargetSize']=$EvName[3];
                        $Options['EvDistance']=$EvName[4];
                        $Options['EvMatchMode']=$EvName[5];
                        CreateEventNew($TourId, $EvCode, $EvName[0], $i++, $Options);
                    }
					break;
                case 12: // D1/DNAP 2023 formula!
                    $i=1;
                    // start with FCL
                    $Options=[
                        'EvTeamEvent' => 1,
                        'EvNumQualified'=>16,
                        'EvFirstQualified'=>1,
                        'EvFinalTargetType'=>5,
                        'EvTargetSize'=>122,
                        'EvDistance'=>70,
                        'EvElimType'=>5,
                        'EvElim1'=>2,
                        'EvMatchMode'=>1,
                        'EvElimEnds'=>4,
                        'EvElimArrows'=>6,
                        'EvElimSO'=>3,
                        'EvFinEnds'=>4,
                        'EvFinArrows'=>6,
                        'EvFinSO'=>3,
                        'EvRecCategory'=>'RW',
                        'EvWaCategory'=>'RW',
                        'EvTourRules'=>'SetFRD12023',
                        'EvGolds' => '10+X',
                        'EvXNine' => 'X',
                        'EvGoldsChars' => 'KL',
                        'EvXNineChars' => 'K',
                        'EvArrowPenalty' => 0,
                        'EvLoopPenalty' => 0,

                    ];
                    CreateEventNew($TourId, 'FCL', 'Equipe Arc Classique Femme', 1, $Options);
                    $Options['EvRecCategory']='RM';
                    $Options['EvWaCategory']='RM';
                    CreateEventNew($TourId, 'HCL', 'Equipe Arc Classique Homme', 2, $Options);
                    $Options['EvMatchMode']=0;
                    $Options['EvDistance']=50;
                    $Options['EvFinalTargetType']=9;
                    $Options['EvRecCategory']='CM';
                    $Options['EvWaCategory']='CM';
                    CreateEventNew($TourId, 'HCO', 'Equipe Arc à Poulies Homme', 4, $Options);
                    $Options['EvRecCategory']='CW';
                    $Options['EvWaCategory']='CW';
                    $Options['EvNumQualified']=8;
                    $Options['EvElim1']=4;
                    CreateEventNew($TourId, 'FCO', 'Equipe Arc à Poulies Femme', 3, $Options);

                    // create all what is needed for round robin!
                    require_once('Modules/RoundRobin/Lib.php');
                    safe_w_sql("delete from RoundRobinGrids where RrGridTournament=$TourId");
                    safe_w_sql("delete from RoundRobinGroup where RrGrTournament=$TourId");
                    safe_w_sql("delete from RoundRobinLevel where RrLevTournament=$TourId");
                    safe_w_sql("delete from RoundRobinMatches where RrMatchTournament=$TourId");
                    safe_w_sql("delete from RoundRobinParticipants where RrPartTournament=$TourId");
                    $SQL="insert into RoundRobinLevel set RrLevTournament=$TourId, RrLevTeam=1, RrLevEvent='%s',
							RrLevLevel=%s, RrLevMatchMode=%s, RrLevBestRankMode=0, RrLevName='%s', RrLevGroups=1, RrLevGroupArchers=%s,
							RrLevArrows=6, RrLevEnds=4, RrLevSO=3, RrLevTieAllowed=0, RrLevWinPoints=1, RrLevTiePoints=0, RrLevTieBreakSystem=%s, RrLevTieBreakSystem2=%s";
                    foreach(['FCL'=>[16,2,1,5,2],'HCL'=>[16,2,1,5,2],'HCO'=>[16,2,0,3,0],'FCO'=>[8,4,0,3,0]] as $Event=>$Items) {

                        for($i=1;$i<=$Items[1];$i++) {
                            // Levels
                            safe_w_sql(sprintf($SQL, $Event, $i, $Items[2], $i%2 ? 'Aller'.($i>2?'-2':'') : 'Retour'.($i>2?'-2':''), $Items[0], $Items[3], $Items[4]));
                            createRound(1, $Event, $i);

                            // Participants selection update
                            safe_w_SQL("update RoundRobinParticipants set RrPartSourceLevel=".($i-1).", RrPartSourceGroup=".($i==1 ? 0 : 1).", RrPartSourceRank=RrPartDestItem
								where RrPartTournament=$TourId and RrPartTeam=1 and  RrPartEvent='$Event' and RrPartLevel=$i and RrPartGroup=1");
                        }

                        // Participants Brackets
                        $sqlPart=array();
                        for($n=1;$n<=4;$n++) {
                            $sqlPart[]="($TourId, 1, '$Event', 0, 0, $n, ".($i-1).", 1, $n)";
                        }
                        safe_w_SQL("insert ignore into RoundRobinParticipants (RrPartTournament, RrPartTeam, RrPartEvent, RrPartLevel, RrPartGroup, RrPartDestItem, RrPartSourceLevel, RrPartSourceGroup, RrPartSourceRank)
							values ".implode(', ', $sqlPart));
                    }

                    // adjust Groups
                    safe_w_sql("update RoundRobinGroup set RrGrTargetArchers=0 where RrGrTournament=$TourId");

                    // creates all the grids and matches
                    break;
                case 13: // TAE Selectif
                case 14: // TAE Para
                    $isTAESel=($TourType==3 and in_array($SubRule, [13,14]));
                    $isTAEPara=($TourType==3 and $SubRule==14);
                    $Options=[
                        'EvFinalFirstPhase' => 0,
                        'EvFinalTargetType'=>0,
                        'EvRecCategory'=>'',
                        'EvWaCategory'=>'',
                        'EvMedals'=>1,
                        'EvIsPara' => 0,
                    ];
                    // CL and CO have all their classes as events

                    $i=1;
                    $EventsInt=[
                        'U11FCL'=>['U11 Femme Arc Classique', 0, 5, 80, 20],
                        'U11HCL'=>['U11 Homme Arc Classique', 0, 5, 80, 20],
                        'U13FCL'=>['U13 Femme Arc Classique', 0, 5, 80, 30],
                        'U13HCL'=>['U13 Homme Arc Classique', 0, 5, 80, 30],
                        'U15FCL'=>['U15 Femme Arc Classique', 0, 5, 80, 40],
                        'U15HCL'=>['U15 Homme Arc Classique', 0, 5, 80, 40],
                        'U18FCL'=>['U18 Femme Arc Classique', 0, 5, 122, 60],
                        'U18HCL'=>['U18 Homme Arc Classique', 0, 5, 122, 60],
                        'U21FCL'=>['U21 Femme Arc Classique', 0, 5, 122, 70],
                        'U21HCL'=>['U21 Homme Arc Classique', 0, 5, 122, 70],
                        'S1FCL'=>['S1 Femme Arc Classique', 0, 5, 122, 70],
                        'S1HCL'=>['S1 Homme Arc Classique', 0, 5, 122, 70],
                        'S2FCL'=>['S2 Femme Arc Classique', 0, 5, 122, 70],
                        'S2HCL'=>['S2 Homme Arc Classique', 0, 5, 122, 70],
                        'S3FCL'=>['S3 Femme Arc Classique', 0, 5, 122, 60],
                        'S3HCL'=>['S3 Homme Arc Classique', 0, 5, 122, 60],
                        'U18FCO'=>['U18 Femme Arc à Poulies', 0, 9, 80, 50],
                        'U18HCO'=>['U18 Homme Arc à Poulies', 0, 9, 80, 50],
                        'U21FCO'=>['U21 Femme Arc à Poulies', 0, 9, 80, 50],
                        'U21HCO'=>['U21 Homme Arc à Poulies', 0, 9, 80, 50],
                        'S1FCO'=>['S1 Femme Arc à Poulies', 0, 9, 80, 50],
                        'S1HCO'=>['S1 Homme Arc à Poulies', 0, 9, 80, 50],
                        'S2FCO'=>['S2 Femme Arc à Poulies', 0, 9, 80, 50],
                        'S2HCO'=>['S2 Homme Arc à Poulies', 0, 9, 80, 50],
                        'S3FCO'=>['S3 Femme Arc à Poulies', 0, 9, 80, 50],
                        'S3HCO'=>['S3 Homme Arc à Poulies', 0, 9, 80, 50],
                    ];
                    $EventsNat=[
                        'NU13FCL'=>['U13 Femme Arc Classique National', 0, 5, 80, 20],
                        'NU13HCL'=>['U13 Homme Arc Classique National', 0, 5, 80, 20],
                        'NU15FCL'=>['U15 Femme Arc Classique National', 0, 5, 80, 30],
                        'NU15HCL'=>['U15 Homme Arc Classique National', 0, 5, 80, 30],
                        'NU18FCL'=>['U18 Femme Arc Classique National', 0, 5, 122, 50],
                        'NU18HCL'=>['U18 Homme Arc Classique National', 0, 5, 122, 50],
                        'NU21FCL'=>['U21 Femme Arc Classique National', 0, 5, 122, 50],
                        'NU21HCL'=>['U21 Homme Arc Classique National', 0, 5, 122, 50],
                        'NS1FCL'=>['S1 Femme Arc Classique National', 0, 5, 122, 50],
                        'NS1HCL'=>['S1 Homme Arc Classique National', 0, 5, 122, 50],
                        'NS2FCL'=>['S2 Femme Arc Classique National', 0, 5, 122, 50],
                        'NS2HCL'=>['S2 Homme Arc Classique National', 0, 5, 122, 50],
                        'NS3FCL'=>['S3 Femme Arc Classique National', 0, 5, 122, 50],
                        'NS3HCL'=>['S3 Homme Arc Classique National', 0, 5, 122, 50],
                        'NU15FCO'=>['U15 Femme Arc à Poulies National', 0, 5, 80, 30],
                        'NU15HCO'=>['U15 Homme Arc à Poulies National', 0, 5, 80, 30],
                        'NU18FCO'=>['U18 Femme Arc à Poulies National', 0, 5, 122, 50],
                        'NU18HCO'=>['U18 Homme Arc à Poulies National', 0, 5, 122, 50],
                        'NU21FCO'=>['U21 Femme Arc à Poulies National', 0, 5, 122, 50],
                        'NU21HCO'=>['U21 Homme Arc à Poulies National', 0, 5, 122, 50],
                        'NS1FCO'=>['S1 Femme Arc à Poulies National', 0, 5, 122, 50],
                        'NS1HCO'=>['S1 Homme Arc à Poulies National', 0, 5, 122, 50],
                        'NS2FCO'=>['S2 Femme Arc à Poulies National', 0, 5, 122, 50],
                        'NS2HCO'=>['S2 Homme Arc à Poulies National', 0, 5, 122, 50],
                        'NS3FCO'=>['S3 Femme Arc à Poulies National', 0, 5, 122, 50],
                        'NS3HCO'=>['S3 Homme Arc à Poulies National', 0, 5, 122, 50],
                        'NU18FBB'=>['U18 Femme Arc Nu National', 0, 5, 80, 30],
                        'NU18HBB'=>['U18 Homme Arc Nu National', 0, 5, 80, 30],
                        'NSFBB'=>['Scratch Femme Arc Nu National', 0, 5, 122, 50],
                        'NSHBB'=>['Scratch Homme Arc Nu National', 0, 5, 122, 50],
                        'NDEC'=>['Découverte', 0, 5, 122, 30],
                        'NDEB'=>['Débutant', 0, 5, 122, 30],
                    ];

                    if($SubRule==14) {
                        // Para
                        $EventsInt['OPFCL']=['Open Femme Arc Classique', 1, 5, 122, 70];
                        $EventsInt['OPHCL']=['Open Homme Arc Classique', 1, 5, 122, 70];
                        $EventsInt['OPFCO']=['Open Femme Arc à Poulies', 1, 9, 80, 50];
                        $EventsInt['OPHCO']=['Open Homme Arc à Poulies', 1, 9, 80, 50];
                        $EventsInt['FEFCL']=['Fédéral Femme Arc Classique', 1, 5, 122, 70];
                        $EventsInt['FEHCL']=['Fédéral Homme Arc Classique', 1, 5, 122, 70];
                        $EventsInt['FEFCO']=['Fédéral Femme Arc à Poulies', 1, 9, 80, 50];
                        $EventsInt['FEHCO']=['Fédéral Homme Arc à Poulies', 1, 9, 80, 50];
                        $EventsInt['HV1']=['HV1', 1, 5, 80, 30];
                        $EventsInt['HV2']=['HV2-3', 1, 5, 80, 30];
                        $EventsInt['W1']=['W1', 1, 5, 80, 50];

                        $EventsNat['NOPFCL']=['Open Femme Arc Classique National', 1, 5, 122, 50];
                        $EventsNat['NOPHCL']=['Open Homme Arc Classique National', 1, 5, 122, 50];
                        $EventsNat['NOPFCO']=['Open Femme Arc à Poulies National', 1, 9, 122, 50];
                        $EventsNat['NOPHCO']=['Open Homme Arc à Poulies National', 1, 9, 122, 50];
                        $EventsNat['NFEFCL']=['Fédéral Femme Arc Classique National', 1, 5, 122, 50];
                        $EventsNat['NFEHCL']=['Fédéral Homme Arc Classique National', 1, 5, 122, 50];
                        $EventsNat['NFEFCO']=['Fédéral Femme Arc à Poulies National', 1, 9, 122, 50];
                        $EventsNat['NFEHCO']=['Fédéral Homme Arc à Poulies National', 1, 9, 122, 50];
                        $EventsNat['NCHFCL']=['Challenge Femme Arc Classique National', 1, 5, 80, 20];
                        $EventsNat['NCHHCL']=['Challenge Homme Arc Classique National', 1, 5, 80, 20];
                        $EventsNat['NCHCO']=['Challenge Arc à Poulies National', 1, 5, 80, 20];
                        $EventsNat['NCRFCL']=['Critérium Femme Arc Classique National', 1, 5, 80, 15];
                        $EventsNat['NCRHCL']=['Critérium Homme Arc Classique National', 1, 5, 80, 15];
                        $EventsNat['NCRCO']=['Critérium Arc à Poulies National', 1, 5, 80, 20];
                        $EventsNat['NHLCL']=['HV Libre Arc Classique National', 1, 5, 80, 30];
                        $EventsNat['NHLCO']=['HV Libre Arc à Poulies National', 1, 5, 80, 30];
                        $EventsNat['NW1']=['W1 National', 1, 5, 122, 50];
                        $EventsNat['NOPFEJ']=['Open et Fédéral Jeune National', 1, 5, 80, 30];
                        $EventsNat['NSU1']=['Support 1 Arc Classique National', 1, 5, 80, 20];
                        $EventsNat['NSU2']=['Support 2 Arc Classique National', 1, 5, 80, 20];
                    }

                    foreach($EventsInt as $EvCode=>$EvName) {
                        $Options['EvIsPara']=$EvName[1];
                        $Options['EvFinalTargetType']=$EvName[2];
                        $Options['EvTargetSize']=$EvName[3];
                        $Options['EvDistance']=$EvName[4];
                        CreateEventNew($TourId, $EvCode, $EvName[0], $i++, $Options);
                    }
                    foreach($EventsNat as $EvCode=>$EvName) {
                        $Options['EvIsPara']=$EvName[1];
                        $Options['EvFinalTargetType']=$EvName[2];
                        $Options['EvTargetSize']=$EvName[3];
                        $Options['EvDistance']=$EvName[4];
                        CreateEventNew($TourId, $EvCode, $EvName[0], $i++, $Options);
                    }

                    safe_w_SQL("update Events set EvMatchMode=1 where (EvEventName like '%Classique%' or EvEventName like '%Arc Nu%') and EvTournament={$_SESSION['TourId']}");
                    break;
			}
			break;
        case 6: // INDOOR 18m
            $TargetR=2;
            $TargetC=4;
            $Distance=18;
            $TargetSize4=40;
            $TargetSize6=60;

            // NEVER as Team
            switch($SubRule) {
                case '1': // Selectif
                case '4': // Selectif + Para
                    $Options=[
                        'EvFinalFirstPhase' => 2,
                        'EvNumQualified'=>4,
                        'EvFinalTargetType'=>0,
                        'EvTargetSize'=>40,
                        'EvDistance'=>0,
                        'EvFinalAthTarget'=>MATCH_SEP_MEDALS,
                        'EvMatchMode'=>0,
                        'EvMatchArrowsNo'=>0,
                        'EvElimEnds'=>5,
                        'EvElimArrows'=>3,
                        'EvElimSO'=>1,
                        'EvFinEnds'=>5,
                        'EvFinArrows'=>3,
                        'EvFinSO'=>1,
                        'EvRecCategory'=>'',
                        'EvWaCategory'=>'',
                    ];
                    $Events=[
                        'U11FCL'=>['U11 Femme Arc Classique', 0, 1, 80, 18, 1],
                        'U11HCL'=>['U11 Homme Arc Classique', 0, 1, 80, 18, 1],
                        'U13FCL'=>['U13 Femme Arc Classique', 0, 2, 60, 18, 1],
                        'U13HCL'=>['U13 Homme Arc Classique', 0, 2, 60, 18, 1],
                        'U15FCL'=>['U15 Femme Arc Classique', 0, 2, 60, 18, 1],
                        'U15HCL'=>['U15 Homme Arc Classique', 0, 2, 60, 18, 1],
                        'U18FCL'=>['U18 Femme Arc Classique', 0, 2, 40, 18, 1],
                        'U18HCL'=>['U18 Homme Arc Classique', 0, 2, 40, 18, 1],
                        'U21FCL'=>['U21 Femme Arc Classique', 0, 2, 40, 18, 1],
                        'U21HCL'=>['U21 Homme Arc Classique', 0, 2, 40, 18, 1],
                        'S1FCL'=>['S1 Femme Arc Classique', 0, 2, 40, 18, 1],
                        'S1HCL'=>['S1 Homme Arc Classique', 0, 2, 40, 18, 1],
                        'S2FCL'=>['S2 Femme Arc Classique', 0, 2, 40, 18, 1],
                        'S2HCL'=>['S2 Homme Arc Classique', 0, 2, 40, 18, 1],
                        'S3FCL'=>['S3 Femme Arc Classique', 0, 2, 40, 18, 1],
                        'S3HCL'=>['S3 Homme Arc Classique', 0, 2, 40, 18, 1],
                        'U15FCO'=>['U15 Femme Arc à Poulies', 0, 4, 40, 18, 0],
                        'U15HCO'=>['U15 Homme Arc à Poulies', 0, 4, 40, 18, 0],
                        'U18FCO'=>['U18 Femme Arc à Poulies', 0, 4, 40, 18, 0],
                        'U18HCO'=>['U18 Homme Arc à Poulies', 0, 4, 40, 18, 0],
                        'U21FCO'=>['U21 Femme Arc à Poulies', 0, 4, 40, 18, 0],
                        'U21HCO'=>['U21 Homme Arc à Poulies', 0, 4, 40, 18, 0],
                        'S1FCO'=>['S1 Femme Arc à Poulies', 0, 4, 40, 18, 0],
                        'S1HCO'=>['S1 Homme Arc à Poulies', 0, 4, 40, 18, 0],
                        'S2FCO'=>['S2 Femme Arc à Poulies', 0, 4, 40, 18, 0],
                        'S2HCO'=>['S2 Homme Arc à Poulies', 0, 4, 40, 18, 0],
                        'S3FCO'=>['S3 Femme Arc à Poulies', 0, 4, 40, 18, 0],
                        'S3HCO'=>['S3 Homme Arc à Poulies', 0, 4, 40, 18, 0],
                        'U18FBB'=>['U18 Femme Arc Nu', 0, 2, 60, 18, 1],
                        'U18HBB'=>['U18 Homme Arc Nu', 0, 2, 60, 18, 1],
                        'SFBB'=>['Scratch Femme Arc Nu', 0, 2, 40, 18, 1],
                        'SHBB'=>['Scratch Homme Arc Nu', 0, 2, 40, 18, 1],
                        'DEC'=>['Découverte', 0, 1, 80, 18, 1],
                        'DEB'=>['Débutant', 0, 1, 80, 18, 1],
                    ];
                    if($SubRule==4) {
                        // Para Events
                        $Events['OPFCL']=['Open Femme Arc Classique', 0, 2, 40, 18, 1, 1];
                        $Events['OPHCL']=['Open Homme Arc Classique', 0, 2, 40, 18, 1, 1];
                        $Events['OPFCO']=['Open Femme Arc à Poulies', 0, 4, 40, 18, 0, 1];
                        $Events['OPHCO']=['Open Homme Arc à Poulies', 0, 4, 40, 18, 0, 1];
                        $Events['FEFCL']=['Fédéral Femme Arc Classique', 0, 2, 40, 18, 1, 1];
                        $Events['FEHCL']=['Fédéral Homme Arc Classique', 0, 2, 40, 18, 1, 1];
                        $Events['FEFCO']=['Fédéral Femme Arc à Poulies', 0, 4, 40, 18, 0, 1];
                        $Events['FEHCO']=['Fédéral Homme Arc à Poulies', 0, 4, 40, 18, 0, 1];
                        $Events['CHFCL']=['Challenge Femme Arc Classique', 0, 1, 60, 18, 1, 1];
                        $Events['CHHCL']=['Challenge Homme Arc Classique', 0, 1, 60, 18, 1, 1];
                        $Events['CHCO']=['Challenge Arc à Poulies', 0, 1, 60, 18, 1, 1];
                        $Events['CRFCL']=['Critérium Femme Arc Classique', 0, 1, 60, 10, 1, 1];
                        $Events['CRHCL']=['Critérium Homme Arc Classique', 0, 1, 60, 10, 1, 1];
                        $Events['CRCO']=['Critérium Arc à Poulies', 0, 1, 60, 15, 1, 1];
                        $Events['HV1']=['HV1', 0, 1, 60, 18, 1, 1];
                        $Events['HV2']=['HV2-3', 0, 1, 60, 18, 1, 1];
                        $Events['HLCL']=['HV Libre Arc Classique', 0, 1, 60, 18, 1, 1];
                        $Events['HLCO']=['HV Libre Arc à Poulies', 0, 1, 60, 18, 1, 1];
                        $Events['W1']=['W1', 0, 2, 40, 18, 1, 1];
                        $Events['OPJ']=['Open Jeune', 0, 1, 60, 18, 1, 1];
                        $Events['OFEJ']=['Fédéral Jeune', 0, 1, 60, 18, 1, 1];
                        $Events['SU1']=['Support 1 Arc Classique', 0, 1, 80, 15, 1, 1];
                        $Events['SU2']=['Support 2 Arc Classique', 0, 1, 80, 15, 1, 1];
                    }

                    foreach($Events as $EvCode=>$EvName) {
                        $Options['EvFinalFirstPhase']=$EvName[1];
                        $Options['EvNumQualified']=$EvName[1]*2;
                        $Options['EvFinalTargetType']=$EvName[2];
                        $Options['EvTargetSize']=$EvName[3];
                        $Options['EvDistance']=$EvName[4];
                        $Options['EvMatchMode']=$EvName[5];
                        $Options['EvIsPara']=$EvName[6];
                        CreateEventNew($TourId, $EvCode, $EvName[0], $i++, $Options);
                    }
                    break;
                case '2': // Championships Adults and Elite
                    $Options=[
                        'EvFinalFirstPhase' => 2,
                        'EvNumQualified'=>4,
                        'EvFinalTargetType'=>0,
                        'EvTargetSize'=>40,
                        'EvDistance'=>0,
                        'EvFinalAthTarget'=>MATCH_SEP_FROM_2,
                        'EvMatchMode'=>0,
                        'EvMatchArrowsNo'=>0,
                        'EvElimEnds'=>5,
                        'EvElimArrows'=>3,
                        'EvElimSO'=>1,
                        'EvFinEnds'=>5,
                        'EvFinArrows'=>3,
                        'EvFinSO'=>1,
                        'EvRecCategory'=>'',
                        'EvWaCategory'=>'',
                    ];
                    $Events=[
                        'EFBB'=>['Elite Femme Arc Nu', 2, 2, 40, 18, 1],
                        'EHBB'=>['Elite Homme Arc Nu', 4, 2, 40, 18, 1],
                        'EFCL'=>['Elite Femme Arc Classique', 8, 2, 40, 18, 1],
                        'EHCL'=>['Elite Homme Arc Classique', 8, 2, 40, 18, 1],
                        'EFCO'=>['Elite Femme Arc à Poulies', 4, 4, 40, 18, 0],
                        'EHCO'=>['Elite Homme Arc à Poulies', 8, 4, 40, 18, 0],
                        'SFBB'=>['Femme Arc Nu', 4, 2, 40, 18, 1],
                        'SHBB'=>['Homme Arc Nu', 8, 2, 40, 18, 1],
                        'S1FCL'=>['Senior 1 Femme Arc Classique', 8, 2, 40, 18, 1],
                        'S1HCL'=>['Senior 1 Homme Arc Classique', 16, 2, 40, 18, 1],
                        'S1FCO'=>['Senior 1 Femme Arc à Poulies', 4, 4, 40, 18, 0],
                        'S1HCO'=>['Senior 1 Homme Arc à Poulies', 8, 4, 40, 18, 0],
                        'S2FCL'=>['Senior 2 Femme Arc Classique', 8, 2, 40, 18, 1],
                        'S2HCL'=>['Senior 2 Homme Arc Classique', 16, 2, 40, 18, 1],
                        'S2FCO'=>['Senior 2 Femme Arc à Poulies', 4, 4, 40, 18, 0],
                        'S2HCO'=>['Senior 2 Homme Arc à Poulies', 8, 4, 40, 18, 0],
                        'S3FCL'=>['Senior 3 Femme Arc Classique', 4, 2, 40, 18, 1],
                        'S3HCL'=>['Senior 3 Homme Arc Classique', 16, 2, 40, 18, 1],
                        'S3FCO'=>['Senior 3 Femme Arc à Poulies', 4, 4, 40, 18, 0],
                        'S3HCO'=>['Senior 3 Homme Arc à Poulies', 8, 4, 40, 18, 0],
                    ];

                    foreach($Events as $EvCode=>$EvName) {
                        $Options['EvFinalFirstPhase']=$EvName[1];
                        $Options['EvNumQualified']=$EvName[1]*2;
                        $Options['EvFinalTargetType']=$EvName[2];
                        $Options['EvTargetSize']=$EvName[3];
                        $Options['EvDistance']=$EvName[4];
                        $Options['EvMatchMode']=$EvName[5];
                        $Options['EvFinalAthTarget']=($EvCode[0]=='E' ? MATCH_SEP_FROM_2 : MATCH_SEP_MEDALS);
                        CreateEventNew($TourId, $EvCode, $EvName[0], $i++, $Options);
                    }
                    break;
                case '3': // Championships YOUTH
                    CreateEvent($TourId, $i++, 0, 0,  2, $TargetR, 5, 3, 1, 5, 3, 1, 'U18FBB', 'U18 Femme Arc Nu',       1, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, '', '', $TargetSize6, $Distance);
                    CreateEvent($TourId, $i++, 0, 0,  4, $TargetR, 5, 3, 1, 5, 3, 1, 'U18HBB', 'U18 Homme Arc Nu',       1, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, '', '', $TargetSize6, $Distance);
                    CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'U13FCL', 'U13 Femme Arc Classique', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, '', '', $TargetSize6, $Distance);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'U13HCL', 'U13 Homme Arc Classique', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, '', '', $TargetSize6, $Distance);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'U15FCL', 'U15 Femme Arc Classique',   1, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, '', '', $TargetSize6, $Distance);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'U15HCL', 'U15 Homme Arc Classique',   1, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, '', '', $TargetSize6, $Distance);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'U18FCL', 'U18 Femme Arc Classique',    1, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, 'RU18W', 'RU18W', $TargetSize4, $Distance);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'U18HCL', 'U18 Homme Arc Classique',    1, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, 'RU18M', 'RU18M', $TargetSize4, $Distance);
                    CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'U21FCL', 'U21 Femme Arc Classique',   1, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, 'RU21W', 'RU21W', $TargetSize4, $Distance);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'U21HCL', 'U21 Homme Arc Classique',   1, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, 'RU21M', 'RU21M', $TargetSize4, $Distance);
                    CreateEvent($TourId, $i++, 0, 0,  4, $TargetC, 5, 3, 1, 5, 3, 1, 'U21FCO', 'U18-U21 Femme Arc à Poulies',     0, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, 'CU21W', 'CU21W', $TargetSize4, $Distance);
                    CreateEvent($TourId, $i++, 0, 0,  8, $TargetC, 5, 3, 1, 5, 3, 1, 'U21HCO', 'U18-U21 Homme Arc à Poulies',     0, FINAL_NO_ELIM, MATCH_SEP_FROM_2, 0, 0, 'CU21M', 'CU21M', $TargetSize4, $Distance);
                    break;
            }
            break;
        case 7: // INDOOR 25m
        case 8: // INDOOR 25m + 18 (eventually matches at 18m)
            $Distance=25;
            $TargetSize4=60;
            $TargetSize6=80;
            if($TourType==8) {
                $Distance=18;
                $TargetSize4=40;
                $TargetSize6=60;
            }
            // NEVER as Team
            switch($SubRule) {
                case '1': // Selectif
                case '4': // Selectif + Para
                    $Options=[
                        'EvFinalFirstPhase' => 2,
                        'EvNumQualified'=>4,
                        'EvFinalTargetType'=>0,
                        'EvTargetSize'=>40,
                        'EvDistance'=>0,
                        'EvFinalAthTarget'=>MATCH_SEP_MEDALS,
                        'EvMatchMode'=>0,
                        'EvMatchArrowsNo'=>0,
                        'EvElimEnds'=>5,
                        'EvElimArrows'=>3,
                        'EvElimSO'=>1,
                        'EvFinEnds'=>5,
                        'EvFinArrows'=>3,
                        'EvFinSO'=>1,
                        'EvRecCategory'=>'',
                        'EvWaCategory'=>'',
                    ];
                    $Events=[
                        'U18FBB'=>['U18 Femme Arc Nu', 0, 2, $TargetSize6, $Distance, 1],
                        'U18HBB'=>['U18 Homme Arc Nu', 0, 2, $TargetSize6, $Distance, 1],
                        'U15FCL'=>['U13-U15 Femme Arc Classique', 0, 2, $TargetSize6, $Distance, 1],
                        'U15HCL'=>['U13-U15 Homme Arc Classique', 0, 2, $TargetSize6, $Distance, 1],
                        'U15FCO'=>['U13-U15 Femme Arc à Poulies', 0, 4, $TargetSize4, $Distance, 0],
                        'U15HCO'=>['U13-U15 Homme Arc à Poulies', 0, 4, $TargetSize4, $Distance, 0],
                        'SFBB'=>['Senior Femme Arc Nu', 0, 2, $TargetSize4, $Distance, 1],
                        'SHBB'=>['Senior Homme Arc Nu', 0, 2, $TargetSize4, $Distance, 1],
                        'SFCL'=>['Senior Femme Arc Classique', 0, 2, $TargetSize4, $Distance, 1],
                        'SHCL'=>['Senior Homme Arc Classique', 0, 2, $TargetSize4, $Distance, 1],
                        'SFCO'=>['Senior Femme Arc à Poulies', 0, 4, $TargetSize4, $Distance, 0],
                        'SHCO'=>['Senior Homme Arc à Poulies', 0, 4, $TargetSize4, $Distance, 0],
                    ];

                    foreach($Events as $EvCode=>$EvName) {
                        $Options['EvFinalFirstPhase']=$EvName[1];
                        $Options['EvNumQualified']=$EvName[1]*2;
                        $Options['EvFinalTargetType']=$EvName[2];
                        $Options['EvTargetSize']=$EvName[3];
                        $Options['EvDistance']=$EvName[4];
                        $Options['EvMatchMode']=$EvName[5];
                        CreateEventNew($TourId, $EvCode, $EvName[0], $i++, $Options);
                    }
                    break;
            }
            break;
        case 50: // Beursault
            // NEVER as Team
            $Options=[
                'EvFinalFirstPhase' => 0,
                'EvNumQualified'=>0,
                'EvFinalTargetType'=>$SubRule==1 ? 28 : 27,
                'EvTargetSize'=>45,
                'EvDistance'=>50,
            ];
            $Events=[
                'U13FCL'=>['U13 Femme Arc Classique', 30],
                'U13HCL'=>['U13 Homme Arc Classique', 30],
                'U15FCL'=>['U15 Femme Arc Classique', 30],
                'U15HCL'=>['U15 Homme Arc Classique', 30],
                'U15FBB'=>['U13-U15 Femme Arc Nu', 30],
                'U15HBB'=>['U13-U15 Homme Arc Nu', 30],
                'U15FAD'=>['U13-U15 Femme Arc Droit', 30],
                'U15HAD'=>['U13-U15 Homme Arc Droit', 30],
                'U15FCO'=>['U13-U15 Femme Arc à Poulies', 30],
                'U15HCO'=>['U13-U15 Homme Arc à Poulies', 30],
                'U18FCL'=>['U18 Femme Arc Classique', 50],
                'U18HCL'=>['U18 Homme Arc Classique', 50],
                'U18FCO'=>['U18 Femme Arc à Poulies', 50],
                'U18HCO'=>['U18 Homme Arc à Poulies', 50],
                'U21FCL'=>['U18 Femme Arc Classique', 50],
                'U21HCL'=>['U18 Homme Arc Classique', 50],
                'U21FCO'=>['U18 Femme Arc à Poulies', 50],
                'U21HCO'=>['U18 Homme Arc à Poulies', 50],
                'US1FCL'=>['U18 Femme Arc Classique', 50],
                'US1HCL'=>['U18 Homme Arc Classique', 50],
                'US1FCO'=>['U18 Femme Arc à Poulies', 50],
                'US1HCO'=>['U18 Homme Arc à Poulies', 50],
                'US2FCL'=>['U18 Femme Arc Classique', 50],
                'US2HCL'=>['U18 Homme Arc Classique', 50],
                'US2FCO'=>['U18 Femme Arc à Poulies', 50],
                'US2HCO'=>['U18 Homme Arc à Poulies', 50],
                'US3FCL'=>['U18 Femme Arc Classique', 50],
                'US3HCL'=>['U18 Homme Arc Classique', 50],
                'US3FCO'=>['U18 Femme Arc à Poulies', 50],
                'US3HCO'=>['U18 Homme Arc à Poulies', 50],
                'SFBB'=>['Senior Femme Arc Nu', 50],
                'SHBB'=>['Senior Homme Arc Nu', 50],
                'SFAD'=>['Senior Femme Arc Droit', 50],
                'SHAD'=>['Senior Homme Arc Droit', 50],
            ];

            foreach($Events as $EvCode=>$EvName) {
                $Options['EvDistance']=$EvName[1];
                CreateEventNew($TourId, $EvCode, $EvName[0], $i++, $Options);
            }
            break;
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule) {
	switch($TourType) {
        case 3:
            switch($SubRule) {
                case 15: // TNJ
                    $Events['U15M']=['CL'=>['U13F','U13H', 'U15F','U15H']];
                    $Events['U21F']=['CL'=>['U21F', 'U18F']];
                    $Events['U21H']=['CL'=>['U21H', 'U18H']];

                    foreach($Events as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Class) {
                                InsertClassEvent($TourId, 1, 3, $EvCode, $Div,$Class);
                            }
                        }
                    }
                    break;
                case 2: // TNJ
                    $EventsInd['U13FCL']=['CL'=>['U13F']];
                    $EventsInd['U13HCL']=['CL'=>['U13H']];
                    $EventsInd['U15FCL']=['CL'=>['U15F']];
                    $EventsInd['U15HCL']=['CL'=>['U15H']];
                    $EventsInd['U18FCL']=['CL'=>['U18F']];
                    $EventsInd['U18HCL']=['CL'=>['U18H']];
                    $EventsInd['U21FCL']=['CL'=>['U21F']];
                    $EventsInd['U21HCL']=['CL'=>['U21H']];
                    $EventsInd['U21FCO']=['CO'=>['U15F','U18F','U21F']];
                    $EventsInd['U21HCO']=['CO'=>['U15H','U18H','U21H']];

                    $EventsMxTeam['DMCLU21']=['CL'=>[['U21F'],['U21H']]];
                    $EventsMxTeam['DMCLU18']=['CL'=>[['U18F'],['U18H']]];
                    $EventsMxTeam['DMCOU21']=['CO'=>[['U15F','U18F','U21F'],['U15H','U18H','U21H']]];

                    foreach($EventsInd as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Class) {
                                InsertClassEvent($TourId, 0, 1, $EvCode, $Div,$Class);
                            }
                        }
                    }

                    foreach($EventsMxTeam as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Num => $Class) {
                                foreach($Class as $Cl) {
                                    InsertClassEvent($TourId, $Num+1, 1, $EvCode, $Div,$Cl);
                                }
                            }
                        }
                    }

                    break;
                case 3: // Championship Youth
                    InsertClassEvent($TourId, 0, 1, 'U13HCL', 'CL','U13H');
                    InsertClassEvent($TourId, 0, 1, 'U13FCL', 'CL','U13F');
                    InsertClassEvent($TourId, 0, 1, 'U15HCL', 'CL','U15H');
                    InsertClassEvent($TourId, 0, 1, 'U15FCL', 'CL','U15F');
                    InsertClassEvent($TourId, 0, 1, 'U18HCL', 'CL','U18H');
                    InsertClassEvent($TourId, 0, 1, 'U18FCL', 'CL','U18F');
                    InsertClassEvent($TourId, 0, 1, 'U21HCL', 'CL','U21H');
                    InsertClassEvent($TourId, 0, 1, 'U21FCL', 'CL','U21F');
                    InsertClassEvent($TourId, 0, 1, 'U21HCO', 'CO','U18H');
                    InsertClassEvent($TourId, 0, 1, 'U21FCO', 'CO','U18F');
                    InsertClassEvent($TourId, 0, 1, 'U21HCO', 'CO','U21H');
                    InsertClassEvent($TourId, 0, 1, 'U21FCO', 'CO','U21F');
                    // Mixed Team
                    InsertClassEvent($TourId, 1, 1, 'DMJ', 'CL','U18F');
                    InsertClassEvent($TourId, 1, 1, 'DMJ', 'CL','U21F');
                    InsertClassEvent($TourId, 2, 1, 'DMJ', 'CL','U18H');
                    InsertClassEvent($TourId, 2, 1, 'DMJ', 'CL','U21H');
                    // Teams
//					InsertClassEvent($TourId, 1, 3, 'CJH', 'CL','U21H');
//					InsertClassEvent($TourId, 1, 3, 'CJH', 'CL','U18H');
//					InsertClassEvent($TourId, 1, 3, 'CJF', 'CL','U21F');
//					InsertClassEvent($TourId, 1, 3, 'CJF', 'CL','U18F');
//					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','U13F');
//					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','U13H');
//					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','U15F');
//					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','U15H');
                    break;
                case 9: // Finales des DR
                    $Events=[
                        'DRCF'=>['CO'=>['U18F','U21F','S1F','S2F','S3F']],
                        'DRCH'=>['CO'=>['U18H','U21H','S1H','S2H','S3H']],
                        'DRRF'=>['CL'=>['U18F','U21F','S1F','S2F','S3F']],
                        'DRRH'=>['CL'=>['U18H','U21H','S1H','S2H','S3H']],
                    ];
                    foreach($Events as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Class) {
                                InsertClassEvent($TourId, 1, 3, $EvCode, $Div,$Class);
                            }
                        }
                    }
                    break;
                case 16: // Finales DR 2
                    $Events['D2F']=['CL'=>['U18F','U21F','S1F','S2F','S3F']];
                    $Events['D2H']=['CL'=>['U18H','U21H','S1H','S2H','S3H']];

                    foreach($Events as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Class) {
                                InsertClassEvent($TourId, 1, 3, $EvCode, $Div,$Class);
                            }
                        }
                    }
                    break;
                case 10: // Championnat de France Elite
                    $EventsInd['EFCL']=['CL'=>['U18F','U21F','S1F','S2F','S3F']];
                    $EventsInd['EHCL']=['CL'=>['U18H','U21H','S1H','S2H','S3H']];
                    $EventsInd['EFCO']=['CO'=>['U18F','U21F','S1F','S2F','S3F']];
                    $EventsInd['EHCO']=['CO'=>['U18H','U21H','S1H','S2H','S3H']];

                    $EventsMxTeam['DMCL']=['CL'=>[['U18F','U21F','S1F','S2F','S3F'],['U18H','U21H','S1H','S2H','S3H']]];
                    $EventsMxTeam['DMCO']=['CO'=>[['U18F','U21F','S1F','S2F','S3F'],['U18H','U21H','S1H','S2H','S3H']]];

                    foreach($EventsInd as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Class) {
                                InsertClassEvent($TourId, 0, 1, $EvCode, $Div,$Class);
                            }
                        }
                    }

                    foreach($EventsMxTeam as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Num => $Class) {
                                foreach($Class as $Cl) {
                                    InsertClassEvent($TourId, $Num+1, 1, $EvCode, $Div,$Cl);
                                }
                            }
                        }
                    }
                    break;
                case 11: // Championnat de France Adulte
                    $Events=[
                        'S1FCL'=>['CL'=>['S1F']],
                        'S1HCL'=>['CL'=>['S1H']],
                        'S2FCL'=>['CL'=>['S2F']],
                        'S2HCL'=>['CL'=>['S2H']],
                        'S3FCL'=>['CL'=>['S3F']],
                        'S3HCL'=>['CL'=>['S3H']],
                        'S1FCO'=>['CO'=>['S1F']],
                        'S1HCO'=>['CO'=>['S1H']],
                        'S2FCO'=>['CO'=>['S2F']],
                        'S2HCO'=>['CO'=>['S2H']],
                        'S3FCO'=>['CO'=>['S3F']],
                        'S3HCO'=>['CO'=>['S3H']],
                        'NS1FCL'=>['CL'=>['S1W']],
                        'NS1HCL'=>['CL'=>['S1M']],
                        'NS2FCL'=>['CL'=>['S2W']],
                        'NS2HCL'=>['CL'=>['S2M']],
                        'NS3FCL'=>['CL'=>['S3W']],
                        'NS3HCL'=>['CL'=>['S3M']],
                        'NS1FCO'=>['CO'=>['S1W']],
                        'NS1HCO'=>['CO'=>['S1M']],
                        'NS2FCO'=>['CO'=>['S2W']],
                        'NS2HCO'=>['CO'=>['S2M']],
                        'NS3FCO'=>['CO'=>['S3W']],
                        'NS3HCO'=>['CO'=>['S3M']],
                        'NFBB'=>['BB'=> ['S1W','S2W','S3W']],
                        'NHBB'=>['BB'=> ['S1M','S2M','S3M']],
                    ];

                    foreach($Events as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Class) {
                                InsertClassEvent($TourId, 0, 1, $EvCode, $Div,$Class);
                            }
                        }
                    }
                    break;
                case 12: // D1/DNAP... team events selection are as usual, indivudal no
                    InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','U18F');
                    InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','U21F');
                    InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','S1F');
                    InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','S2F');
                    InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','S3F');
                    InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','U18H');
                    InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','U21H');
                    InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','S1H');
                    InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','S2H');
                    InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','S3H');
                    InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','U18F');
                    InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','U21F');
                    InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','S1F');
                    InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','S2F');
                    InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','S3F');
                    InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','U18H');
                    InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','U21H');
                    InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','S1H');
                    InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','S2H');
                    InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','S3H');
                    break;
                case 13: // TAE-Selectif
                case 14: // TAE-Para
                    // International
                    $Events=[
                        'U11FCL'=>['CL'=>['U11F']],
                        'U11HCL'=>['CL'=>['U11H']],
                        'U13FCL'=>['CL'=>['U13F']],
                        'U13HCL'=>['CL'=>['U13H']],
                        'U15FCL'=>['CL'=>['U15F']],
                        'U15HCL'=>['CL'=>['U15H']],
                        'U18FCL'=>['CL'=>['U18F']],
                        'U18HCL'=>['CL'=>['U18H']],
                        'U21FCL'=>['CL'=>['U21F']],
                        'U21HCL'=>['CL'=>['U21H']],
                        'S1FCL'=>['CL'=>['S1F']],
                        'S1HCL'=>['CL'=>['S1H']],
                        'S2FCL'=>['CL'=>['S2F']],
                        'S2HCL'=>['CL'=>['S2H']],
                        'S3FCL'=>['CL'=>['S3F']],
                        'S3HCL'=>['CL'=>['S3H']],
                        'U18FCO'=>['CO'=>['U18F']],
                        'U18HCO'=>['CO'=>['U18H']],
                        'U21FCO'=>['CO'=>['U21F']],
                        'U21HCO'=>['CO'=>['U21H']],
                        'S1FCO'=>['CO'=>['S1F']],
                        'S1HCO'=>['CO'=>['S1H']],
                        'S2FCO'=>['CO'=>['S2F']],
                        'S2HCO'=>['CO'=>['S2H']],
                        'S3FCO'=>['CO'=>['S3F']],
                        'S3HCO'=>['CO'=>['S3H']],
                        'NU13FCL'=>['CL'=>['U13W']],
                        'NU13HCL'=>['CL'=>['U13M']],
                        'NU15FCL'=>['CL'=>['U15W']],
                        'NU15HCL'=>['CL'=>['U15M']],
                        'NU18FCL'=>['CL'=>['U18W']],
                        'NU18HCL'=>['CL'=>['U18M']],
                        'NU21FCL'=>['CL'=>['U21W']],
                        'NU21HCL'=>['CL'=>['U21M']],
                        'NS1FCL'=>['CL'=>['S1W']],
                        'NS1HCL'=>['CL'=>['S1M']],
                        'NS2FCL'=>['CL'=>['S2W']],
                        'NS2HCL'=>['CL'=>['S2M']],
                        'NS3FCL'=>['CL'=>['S3W']],
                        'NS3HCL'=>['CL'=>['S3M']],
                        'NU15FCO'=>['CO'=>['U13W','U15W']],
                        'NU15HCO'=>['CO'=>['U13M','U15M']],
                        'NU18FCO'=>['CO'=>['U18W']],
                        'NU18HCO'=>['CO'=>['U18M']],
                        'NU21FCO'=>['CO'=>['U21W']],
                        'NU21HCO'=>['CO'=>['U21M']],
                        'NS1FCO'=>['CO'=>['S1W']],
                        'NS1HCO'=>['CO'=>['S1M']],
                        'NS2FCO'=>['CO'=>['S2W']],
                        'NS2HCO'=>['CO'=>['S2M']],
                        'NS3FCO'=>['CO'=>['S3W']],
                        'NS3HCO'=>['CO'=>['S3M']],
                        'NU18FBB'=>['BB'=>['U15W','U18W']],
                        'NU18HBB'=>['BB'=>['U15M','U18M']],
                        'NSFBB'=>['BB'=>['U21W','S1W','S2W','S3W']],
                        'NSHBB'=>['BB'=>['U21M','S1M','S2M','S3M']],
                        'NDEC'=>['CL'=>['DEC']],
                        'NDEB'=>['CL'=>['DEB']],
                    ];

                    if($SubRule==14) {
                        // Para
                        $Events['OPFCL']=['OPCL'=>['U21F','S1F','S2F','S3F']];
                        $Events['OPHCL']=['OPCL'=>['U21H','S1H','S2H','S3H']];
                        $Events['OPFCO']=['OPCO'=>['U21F','S1F','S2F','S3F']];
                        $Events['OPHCO']=['OPCO'=>['U21H','S1H','S2H','S3H']];
                        $Events['FEFCL']=['FECL'=>['U21F','S1F','S2F','S3F']];
                        $Events['FEHCL']=['FECL'=>['U21H','S1H','S2H','S3H']];
                        $Events['FEFCO']=['FECO'=>['U21F','S1F','S2F','S3F']];
                        $Events['FEHCO']=['FECO'=>['U21H','S1H','S2H','S3H']];
                        $Events['HV1']=['HV1'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['HV2']=['HV2'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['W1']=['W1'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];

                        $Events['NOPFCL']=['OPCL'=>['U21W','S1W','S2W','S3W']];
                        $Events['NOPHCL']=['OPCL'=>['U21M','S1M','S2M','S3M']];
                        $Events['NOPFCO']=['OPCO'=>['U21W','S1W','S2W','S3W']];
                        $Events['NOPHCO']=['OPCO'=>['U21M','S1M','S2M','S3M']];
                        $Events['NFEFCL']=['FECL'=>['U21W','S1W','S2W','S3W']];
                        $Events['NFEHCL']=['FECL'=>['U21M','S1M','S2M','S3M']];
                        $Events['NFEFCO']=['FECO'=>['U21W','S1W','S2W','S3W']];
                        $Events['NFEHCO']=['FECO'=>['U21M','S1M','S2M','S3M']];
                        $Events['NCHFCL']=['CHCL'=>['U13W','U15W','U18W','U21W','S1W','S2W','S3W']];
                        $Events['NCHHCL']=['CHCL'=>['U13M','U15M','U18M','U21M','S1M','S2M','S3M']];
                        $Events['NCHCO']=['CHCL'=>['U13W','U15W','U18W','U21W','S1W','S2W','S3W','U13M','U15M','U18M','U21M','S1M','S2M','S3M']];
                        $Events['NCRFCL']=['CRCL'=>['U13W','U15W','U18W','U21W','S1W','S2W','S3W']];
                        $Events['NCRHCL']=['CRCL'=>['U13M','U15M','U18M','U21M','S1M','S2M','S3M']];
                        $Events['NCRCO']=['CHCO'=>['U13W','U15W','U18W','U21W','S1W','S2W','S3W','U13M','U15M','U18M','U21M','S1M','S2M','S3M']];
                        $Events['NHLCL']=['HLCL'=>['U13W','U15W','U18W','U21W','S1W','S2W','S3W','U13M','U15M','U18M','U21M','S1M','S2M','S3M']];
                        $Events['NHLCO']=['HLCO'=>['U13W','U15W','U18W','U21W','S1W','S2W','S3W','U13M','U15M','U18M','U21M','S1M','S2M','S3M']];
                        $Events['NW1']=['W1'=>['U13W','U15W','U18W','U21W','S1W','S2W','S3W','U13M','U15M','U18M','U21M','S1M','S2M','S3M']];
                        $Events['NOPFEJ']=['OPCL'=>['U13W','U15W','U18W','U13M','U15M','U18M'],
                            'OPCO'=>['U13W','U15W','U18W','U13M','U15M','U18M'],
                            'FECL'=>['U13W','U15W','U18W','U13M','U15M','U18M'],
                            'FECO'=>['U13W','U15W','U18W','U13M','U15M','U18M'],
                        ];
                        $Events['NSU1']=['SU1'=>['U13W','U15W','U18W','U21W','S1W','S2W','S3W','U13M','U15M','U18M','U21M','S1M','S2M','S3M']];
                        $Events['NSU2']=['SU2'=>['U13W','U15W','U18W','U21W','S1W','S2W','S3W','U13M','U15M','U18M','U21M','S1M','S2M','S3M']];
                    }

                    // inserts all the events!!!
                    foreach($Events as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Class) {
                                InsertClassEvent($TourId, 0, 1, $EvCode, $Div,$Class);
                            }
                        }
                    }
                    break;
            }
            break;
		case 6:
			switch($SubRule) {
                case 1: // Selectif
                case 4: // Selectif + Para
                    // International
                    $Events=[
                        'U11FCL'=>['CL'=>['U11F']],
                        'U11HCL'=>['CL'=>['U11H']],
                        'U13FCL'=>['CL'=>['U13F']],
                        'U13HCL'=>['CL'=>['U13H']],
                        'U15FCL'=>['CL'=>['U15F']],
                        'U15HCL'=>['CL'=>['U15H']],
                        'U18FCL'=>['CL'=>['U18F']],
                        'U18HCL'=>['CL'=>['U18H']],
                        'U21FCL'=>['CL'=>['U21F']],
                        'U21HCL'=>['CL'=>['U21H']],
                        'S1FCL'=>['CL'=>['S1F']],
                        'S1HCL'=>['CL'=>['S1H']],
                        'S2FCL'=>['CL'=>['S2F']],
                        'S2HCL'=>['CL'=>['S2H']],
                        'S3FCL'=>['CL'=>['S3F']],
                        'S3HCL'=>['CL'=>['S3H']],
                        'U15FCO'=>['CO'=>['U13F','U15F']],
                        'U15HCO'=>['CO'=>['U13H','U15H']],
                        'U18FCO'=>['CO'=>['U18F']],
                        'U18HCO'=>['CO'=>['U18H']],
                        'U21FCO'=>['CO'=>['U21F']],
                        'U21HCO'=>['CO'=>['U21H']],
                        'S1FCO'=>['CO'=>['S1F']],
                        'S1HCO'=>['CO'=>['S1H']],
                        'S2FCO'=>['CO'=>['S2F']],
                        'S2HCO'=>['CO'=>['S2H']],
                        'S3FCO'=>['CO'=>['S3F']],
                        'S3HCO'=>['CO'=>['S3H']],
                        'NU13FCL'=>['CL'=>['U13W']],
                        'NU13HCL'=>['CL'=>['U13M']],
                        'NU15FCL'=>['CL'=>['U15W']],
                        'NU15HCL'=>['CL'=>['U15M']],
                        'NU18FCL'=>['CL'=>['U18W']],
                        'NU18HCL'=>['CL'=>['U18M']],
                        'NU21FCL'=>['CL'=>['U21W']],
                        'NU21HCL'=>['CL'=>['U21M']],
                        'NS1FCL'=>['CL'=>['S1W']],
                        'NS1HCL'=>['CL'=>['S1M']],
                        'NS2FCL'=>['CL'=>['S2W']],
                        'NS2HCL'=>['CL'=>['S2M']],
                        'NS3FCL'=>['CL'=>['S3W']],
                        'NS3HCL'=>['CL'=>['S3M']],
                        'NU15FCO'=>['CO'=>['U13W','U15W']],
                        'NU15HCO'=>['CO'=>['U13M','U15M']],
                        'NU18FCO'=>['CO'=>['U18W']],
                        'NU18HCO'=>['CO'=>['U18M']],
                        'NU21FCO'=>['CO'=>['U21W']],
                        'NU21HCO'=>['CO'=>['U21M']],
                        'NS1FCO'=>['CO'=>['S1W']],
                        'NS1HCO'=>['CO'=>['S1M']],
                        'NS2FCO'=>['CO'=>['S2W']],
                        'NS2HCO'=>['CO'=>['S2M']],
                        'NS3FCO'=>['CO'=>['S3W']],
                        'NS3HCO'=>['CO'=>['S3M']],
                        'U18FBB'=>['BB'=>['U15F','U18F']],
                        'U18HBB'=>['BB'=>['U15H','U18H']],
                        'SFBB'=>['BB'=>['U21F','S1F','S2F','S3F']],
                        'SHBB'=>['BB'=>['U21H','S1H','S2H','S3H']],
                        'DEC'=>['CL'=>['DEC']],
                        'DEB'=>['CL'=>['DEB']],
                    ];

                    if($SubRule==4) {
                        // Para
                        $Events['OPFCL']=['OPCL'=>['U21F','S1F','S2F','S3F']];
                        $Events['OPHCL']=['OPCL'=>['U21H','S1H','S2H','S3H']];
                        $Events['OPFCO']=['OPCO'=>['U21F','S1F','S2F','S3F']];
                        $Events['OPHCO']=['OPCO'=>['U21H','S1H','S2H','S3H']];
                        $Events['FEFCL']=['FECL'=>['U21F','S1F','S2F','S3F']];
                        $Events['FEHCL']=['FECL'=>['U21H','S1H','S2H','S3H']];
                        $Events['FEFCO']=['FECO'=>['U21F','S1F','S2F','S3F']];
                        $Events['FEHCO']=['FECO'=>['U21H','S1H','S2H','S3H']];
                        $Events['CHFCL']=['CHCL'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F']];
                        $Events['CHHCL']=['CHCL'=>['U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['CHCO']=['CHCL'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['CRFCL']=['CRCL'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F']];
                        $Events['CRHCL']=['CRCL'=>['U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['CRCO']=['CHCO'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['HV1']=['HV1'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['HV2']=['HV2'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['HLCL']=['HLCL'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['HLCO']=['HLCO'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['W1']=['W1'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['OPJ']=[
                            'OPCL'=>['U13F','U15F','U18F','U13H','U15H','U18H'],
                            'OPCO'=>['U13F','U15F','U18F','U13H','U15H','U18H'],
                        ];
                        $Events['OFEJ']=[
                            'FECL'=>['U13F','U15F','U18F','U13H','U15H','U18H'],
                            'FECO'=>['U13F','U15F','U18F','U13H','U15H','U18H'],
                        ];
                        $Events['SU1']=['SU1'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                        $Events['SU2']=['SU2'=>['U13F','U15F','U18F','U21F','S1F','S2F','S3F','U13H','U15H','U18H','U21H','S1H','S2H','S3H']];
                    }
                    // inserts all the events!!!
                    foreach($Events as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Class) {
                                InsertClassEvent($TourId, 0, 1, $EvCode, $Div,$Class);
                            }
                        }
                    }
                    break;
				case '2':
                    $Events=[
                        'EFBB'=>['BB'=>['SC'=>'E','CL'=>['U21F','S1F','S2F','S3F']]],
                        'EHBB'=>['BB'=>['SC'=>'E','CL'=>['U21H','S1H','S2H','S3H']]],
                        'EFCL'=>['CL'=>['SC'=>'E','CL'=>['S1F','S2F','S3F']]],
                        'EHCL'=>['CL'=>['SC'=>'E','CL'=>['S1H','S2H','S3H']]],
                        'EFCO'=>['CO'=>['SC'=>'E','CL'=>['S1F','S2F','S3F']]],
                        'EHCO'=>['CO'=>['SC'=>'E','CL'=>['S1H','S2H','S3H']]],
                        'SFBB'=>['BB'=>['SC'=>'E','CL'=>['U21F','S1F','S2F','S3F']]],
                        'SHBB'=>['BB'=>['SC'=>'E','CL'=>['U21H','S1H','S2H','S3H']]],
                        'S1FCL'=>['CL'=>['SC'=>'A','CL'=>['S1F']]],
                        'S1HCL'=>['CL'=>['SC'=>'A','CL'=>['S1H']]],
                        'S1FCO'=>['CO'=>['SC'=>'A','CL'=>['S1F']]],
                        'S1HCO'=>['CO'=>['SC'=>'A','CL'=>['S1H']]],
                        'S2FCL'=>['CL'=>['SC'=>'A','CL'=>['S2F']]],
                        'S2HCL'=>['CL'=>['SC'=>'A','CL'=>['S2H']]],
                        'S2FCO'=>['CO'=>['SC'=>'A','CL'=>['S2F']]],
                        'S2HCO'=>['CO'=>['SC'=>'A','CL'=>['S2H']]],
                        'S3FCL'=>['CL'=>['SC'=>'A','CL'=>['S3F']]],
                        'S3HCL'=>['CL'=>['SC'=>'A','CL'=>['S3H']]],
                        'S3FCO'=>['CO'=>['SC'=>'A','CL'=>['S3F']]],
                        'S3HCO'=>['CO'=>['SC'=>'A','CL'=>['S3H']]],
                    ];
                    foreach($Events as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes['CL'] as $Class) {
                                InsertClassEvent($TourId, 0, 1, $EvCode, $Div, $Class, $Classes['SC']);
                            }
                        }
                    }
					break;
				case '3': // Championships YOUTH
                    $Events['U18FBB']=['BB'=>['U15F','U18F']];
                    $Events['U18HBB']=['BB'=>['U15H','U18H']];
                    $Events['U13FCL']=['CL'=>['U13F']];
                    $Events['U13HCL']=['CL'=>['U13H']];
                    $Events['U15FCL']=['CL'=>['U15F']];
                    $Events['U15HCL']=['CL'=>['U15H']];
                    $Events['U18FCL']=['CL'=>['U18F']];
                    $Events['U18HCL']=['CL'=>['U18H']];
                    $Events['U21FCL']=['CL'=>['U21F']];
                    $Events['U21HCL']=['CL'=>['U21H']];
                    $Events['U21FCO']=['CO'=>['U21F', 'U18F']];
                    $Events['U21HCO']=['CO'=>['U21H', 'U18H']];

                    foreach($Events as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Class) {
                                InsertClassEvent($TourId, 0, 1, $EvCode, $Div,$Class);
                            }
                        }
                    }
					break;
			}
			break;
		case 7:
			switch($SubRule) {
				case '1':
				case '4':
                    $Events=[
                        'U18FBB'=>['BB'=>['U18F']],
                        'U18HBB'=>['BB'=>['U18H']],
                        'U15FCL'=>['CL'=>['U13F','U15F']],
                        'U15HCL'=>['CL'=>['U13H','U15H']],
                        'U15FCO'=>['CO'=>['U13F','U15F']],
                        'U15HCO'=>['CO'=>['U13H','U15H']],
                        'SFBB'=>['BB'=>['U21F','S1F','S2F','S3F']],
                        'SHBB'=>['BB'=>['U21H','S1H','S2H','S3H']],
                        'SFCL'=>['CL'=>['U18F','U21F','S1F','S2F','S3F']],
                        'SHCL'=>['CL'=>['U18H','U21H','S1H','S2H','S3H']],
                        'SFCO'=>['CO'=>['U18F','U21F','S1F','S2F','S3F']],
                        'SHCO'=>['CO'=>['U18H','U21H','S1H','S2H','S3H']],
                    ];
                    foreach($Events as $EvCode => $Divs) {
                        foreach($Divs as $Div=>$Classes) {
                            foreach($Classes as $Class) {
                                InsertClassEvent($TourId, 0, 1, $EvCode, $Div, $Class);
                            }
                        }
                    }
					break;
			}
			break;
        case 50:
            $Events=[
                'U13FCL'=>['CL'=>['U13F']],
                'U13HCL'=>['CL'=>['U13H']],
                'U15FCL'=>['CL'=>['U15F']],
                'U15HCL'=>['CL'=>['U15H']],
                'U15FBB'=>['BB'=>['U13F','U15F']],
                'U15HBB'=>['BB'=>['U13H','U15H']],
                'U15FAD'=>['AD'=>['U13F','U15F']],
                'U15HAD'=>['AD'=>['U13H','U15H']],
                'U15FCO'=>['CO'=>['U13F','U15F']],
                'U15HCO'=>['CO'=>['U13H','U15H']],
                'U18FCL'=>['CL'=>['U18F']],
                'U18HCL'=>['CL'=>['U18H']],
                'U18FCO'=>['CO'=>['U18F']],
                'U18HCO'=>['CO'=>['U18H']],
                'U21FCL'=>['CL'=>['U21F']],
                'U21HCL'=>['CL'=>['U21H']],
                'U21FCO'=>['CO'=>['U21F']],
                'U21HCO'=>['CO'=>['U21H']],
                'US1FCL'=>['CL'=>['S1F']],
                'US1HCL'=>['CL'=>['S1H']],
                'US1FCO'=>['CO'=>['S1F']],
                'US1HCO'=>['CO'=>['S1H']],
                'US2FCL'=>['CL'=>['S2F']],
                'US2HCL'=>['CL'=>['S2H']],
                'US2FCO'=>['CO'=>['S2F']],
                'US2HCO'=>['CO'=>['S2H']],
                'US3FCL'=>['CL'=>['S3F']],
                'US3HCL'=>['CL'=>['S3H']],
                'US3FCO'=>['CO'=>['S3F']],
                'US3HCO'=>['CO'=>['S3H']],
                'SFBB'=>['BB'=>['U18F','U21F','S1F','S2F','S3F']],
                'SHBB'=>['BB'=>['U18H','U21H','S1H','S2H','S3H']],
                'SFAD'=>['AD'=>['U18F','U21F','S1F','S2F','S3F']],
                'SHAD'=>['AD'=>['U18H','U21H','S1H','S2H','S3H']],
            ];
            foreach($Events as $EvCode => $Divs) {
                foreach($Divs as $Div=>$Classes) {
                    foreach($Classes as $Class) {
                        InsertClassEvent($TourId, 0, 1, $EvCode, $Div, $Class);
                    }
                }
            }
            break;
	}
}

function CreateFinals_FR_3_SetFRChampsD1DNAP($TourId) {
	CreateFinalsInd_FR_3_SetFRChampsD1DNAP($TourId);
	CreateFinalsTeam_FR_3_SetFRChampsD1DNAP($TourId);
}

/**
 * @param $TourId
 * @param string $StrEv2Delete [optional] SQL-escaped string that goes in the IN () statement
 */
function CreateFinalsInd_FR_3_SetFRChampsD1DNAP($TourId, $StrEv2Delete='') {
	safe_w_sql("INSERT INTO Finals (FinEvent, FinMatchNo, FinTournament, FinDateTime) 
		SELECT EvCode, GrMatchNo, EvTournament, " . StrSafe_DB(date('Y-m-d H:i:s')) . "
		FROM Events 
		INNER JOIN Grids ON GrMatchNo between 128 and 207
		WHERE EvTournament=$TourId AND EvTeamEvent='0' and right(EvCode, 1) in (1,2,3,4)".($StrEv2Delete ? " AND EvCode IN ($StrEv2Delete)" : ""));
}

/**
 * @param $TourId
 * @param string $StrEv2Delete [optional] SQL-escaped string that goes in the IN () statement
 */
function CreateFinalsTeam_FR_3_SetFRChampsD1DNAP($TourId, $StrEv2Delete='') {
	safe_w_sql("INSERT INTO TeamFinals (TfEvent, TfMatchNo, TfTournament, TfDateTime) 
		SELECT EvCode, GrMatchNo, EvTournament, " . StrSafe_DB(date('Y-m-d H:i:s')) . " 
		FROM Events 
		INNER JOIN Grids ON GrMatchNo between 128 and 207
		WHERE EvTournament=$TourId AND EvTeamEvent='1'".($StrEv2Delete ? " AND EvCode IN ($StrEv2Delete)" : ""));
}

function CreateFinalsTeam_FR_3_SetFRD12023($TourId, $StrEv2Delete='') {
	safe_w_sql("INSERT INTO TeamFinals (TfEvent, TfMatchNo, TfTournament, TfDateTime) 
		SELECT EvCode, GrMatchNo, EvTournament, " . StrSafe_DB(date('Y-m-d H:i:s')) . " 
		FROM Events 
		INNER JOIN Grids ON GrMatchNo between 128 and 207
		WHERE EvTournament=$TourId AND EvTeamEvent='1'".($StrEv2Delete ? " AND EvCode IN ($StrEv2Delete)" : ""));
}

/*

FIELD DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-Field.php');

/*

3D DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-3D.php');

