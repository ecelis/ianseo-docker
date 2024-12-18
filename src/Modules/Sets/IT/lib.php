<?php

/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'ITA';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) {
	$i=1;
	if($Type==11 or $Type==13) {
		CreateDivision($TourId, $i++, 'AI', 'Arco Istintivo', 1, 'I', 'I');
	} else {
		CreateDivision($TourId, $i++, 'OL', 'Arco Olimpico', 1, 'R', 'R');
	}
	CreateDivision($TourId, $i++, 'CO', 'Arco Compound', 1, 'C', 'C');
	if(!in_array($Type, array(1,2,4,18))) {
		CreateDivision($TourId, $i++, 'AN', 'Arco Nudo', 1, 'B', 'B');
	}
	if($Type>=9 and $Type<=13) {
		CreateDivision($TourId, $i++, 'LB', 'Long Bow', 1, 'L', 'L');
	}
	if(in_array($Type, array(1,2,3,4,5,6,7,8,18,37,39))) {
		CreateDivision($TourId, $i++, 'W1', 'Compound W1',1,'W1','W1',1);
		CreateDivision($TourId, $i++, 'V1', 'Visually Impaired 1',1,'V1','V1',1);
		CreateDivision($TourId, $i++, 'V2', 'Visually Impaired 2/3',1,'V2','V2',1);
	}
	if(in_array($Type, array(9,10,12))) {
		CreateDivision($TourId, $i++, 'AI', 'Arco Istintivo', 1, 'I', 'I');
	}
}

function CreateStandardClasses($TourId, $SubRule, $Field='', $Type=0) {
	$i=1;
	$Fita=in_array($Type, array(1,2,4,5,18));
	$FitaWithAN = in_array($Type, array(3,5,37,39));
	$Indoor=in_array($Type, array(6,7,8));
	$divs=(($Indoor or $Field=='FIELD' or $FitaWithAN) ? 'AN,CO,OL' : ($Fita ? 'CO,OL' : ''));
	switch($SubRule) {
		case '1':
			if($Field=='3D') {
				CreateClass($TourId, $i++, 21, 100, 0, 'SM', 'SM', 'Over 20 Maschile');
				CreateClass($TourId, $i++, 21, 100, 1, 'SF', 'SF', 'Over 20 Femminile');
				CreateClass($TourId, $i++,  1, 20, 0, 'JM', 'JM,SM', 'Under 20 Maschile');
				CreateClass($TourId, $i++,  1, 20, 1, 'JF', 'JF,SF', 'Under 20 Femminile');
			} else {
				CreateClass($TourId, $i++, 21, 49, 0, 'SM', 'SM', 'Senior Maschile', 1, $divs);
				CreateClass($TourId, $i++, 21, 49, 1, 'SF', 'SF', 'Senior Femminile', 1, $divs);
				CreateClass($TourId, $i++, 50,100, 0, 'MM', 'MM,SM', 'Master Maschile', 1, $divs);
				CreateClass($TourId, $i++, 50,100, 1, 'MF', 'MF,SF', 'Master Femminile', 1, $divs);
				CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,SM', 'Junior Maschile', 1, $divs);
				CreateClass($TourId, $i++, 18, 20, 1, 'JF', 'JF,SF', 'Junior Femminile', 1, $divs);
				CreateClass($TourId, $i++, 15, 17, 0, 'AM', 'AM,JM', 'Allievi Maschile', 1, $divs);
				CreateClass($TourId, $i++, 15, 17, 1, 'AF', 'AF,JF', 'Allieve Femminile', 1, $divs);
				CreateClass($TourId, $i++, 13, 14, 0, 'RM', 'RM,AM,JM', 'Ragazzi Maschile', 1, $divs);
				CreateClass($TourId, $i++, 13, 14, 1, 'RF', 'RF,AF,JF', 'Ragazzi Femminile', 1, $divs);
				CreateClass($TourId, $i++,  9, 12, 0, 'GM', 'GM,RM', 'Giovanissimi Maschile', 1, $divs);
				CreateClass($TourId, $i++,  9, 12, 1, 'GF', 'GF,RF', 'Giovanissimi Femminile', 1, $divs);
				if($Fita or $FitaWithAN or $Indoor) {
					CreateClass($TourId, $i++,  1, 100, 0, 'M', 'M', 'Maschile', 1, 'W1','','',1);
					CreateClass($TourId, $i++,  1, 100, 1, 'F', 'F', 'Femminile', 1, 'W1','','',1);
					CreateClass($TourId, $i++,  1, 100, -1, 'U', 'U', 'Unica', 1, 'V1,V2','','',1);
				}
				if($Field=='FIELD') {
					CreateClass($TourId, $i++,  1, 100, 0, 'M', 'M', 'Maschile', 1, 'AI,LB');
					CreateClass($TourId, $i++,  1, 100, 1, 'F', 'F', 'Femminile', 1, 'AI,LB');
				}
			}
			break;
		case '2':
			if($Field=='3D') {
				CreateClass($TourId, $i++, 21, 100, 0, 'SM', 'SM', 'Over 20 Maschile');
				CreateClass($TourId, $i++, 21, 100, 1, 'SF', 'SF', 'Over 20 Femminile');
				CreateClass($TourId, $i++,  1, 20, 0, 'JM', 'JM,SM', 'Under 20 Maschile');
				CreateClass($TourId, $i++,  1, 20, 1, 'JF', 'JF,SF', 'Under 20 Femminile');
			} else {
				CreateClass($TourId, $i++, 1, 100, 0, 'SM', 'SM', 'Senior Maschile',1, $divs);
				CreateClass($TourId, $i++, 1, 100, 1, 'SF', 'SF', 'Senior Femminile',1, $divs);
                if($Fita or $FitaWithAN or $Indoor) {
                    CreateClass($TourId, $i++,  1, 100, 0, 'M', 'M', 'Maschile', 1, 'W1','','',1);
                    CreateClass($TourId, $i++,  1, 100, 1, 'F', 'F', 'Femminile', 1, 'W1','','',1);
                    CreateClass($TourId, $i++,  1, 100, -1, 'U', 'U', 'Unica', 1, 'V1,V2','','',1);
                }
			}
			break;
		case '3':
			CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,SM', 'Junior Maschile', 1, $divs);
			CreateClass($TourId, $i++, 18, 20, 1, 'JF', 'JF,SF', 'Junior Femminile', 1, $divs);
			CreateClass($TourId, $i++, 15, 17, 0, 'AM', 'AM,JM', 'Allievi Maschile', 1, $divs);
			CreateClass($TourId, $i++, 15, 17, 1, 'AF', 'AF,JF', 'Allieve Femminile', 1, $divs);
			CreateClass($TourId, $i++, 13, 14, 0, 'RM', 'RM,AM,JM', 'Ragazzi Maschile', 1, $divs);
			CreateClass($TourId, $i++, 13, 14, 1, 'RF', 'RF,AF,JF', 'Ragazzi Femminile', 1, $divs);
			CreateClass($TourId, $i++,  9, 12, 0, 'GM', 'GM,RM', 'Giovanissimi Maschile', 1, $divs);
			CreateClass($TourId, $i++,  9, 12, 1, 'GF', 'GF,RF', 'Giovanissimi Femminile', 1, $divs);
            if($Fita or $FitaWithAN or $Indoor) {
                CreateClass($TourId, $i++,  1, 100, 0, 'M', 'M', 'Maschile', 1, 'W1','','',1);
                CreateClass($TourId, $i++,  1, 100, 1, 'F', 'F', 'Femminile', 1, 'W1','','',1);
                CreateClass($TourId, $i++,  1, 100, -1, 'U', 'U', 'Unica', 1, 'V1,V2','','',1);
            }
			break;
		case '4':
			CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'SM', 'Junior Maschile', 1, $divs);
			CreateClass($TourId, $i++, 18, 20, 1, 'JF', 'SF', 'Junior Femminile', 1, $divs);
			CreateClass($TourId, $i++, 21, 49, 0, 'SM', 'SM', 'Senior Maschile', 1, $divs);
			CreateClass($TourId, $i++, 21, 49, 1, 'SF', 'SF', 'Senior Femminile', 1, $divs);
			CreateClass($TourId, $i++, 50,100, 0, 'MM', 'MM,SM', 'Master Maschile', 1, $divs);
			CreateClass($TourId, $i++, 50,100, 1, 'MF', 'MF,SF', 'Master Femminile', 1, $divs);
            if($Fita or $FitaWithAN or $Indoor) {
                CreateClass($TourId, $i++,  1, 100, 0, 'M', 'M', 'Maschile', 1, 'W1','','',1);
                CreateClass($TourId, $i++,  1, 100, 1, 'F', 'F', 'Femminile', 1, 'W1','','',1);
                CreateClass($TourId, $i++,  1, 100, -1, 'U', 'U', 'Unica', 1, 'V1,V2','','',1);
            }
			break;
	}
}

function CreateStandardSubClasses($TourId) {
	$i=1;
	CreateSubClass($TourId, $i++, '01', '01');
	CreateSubClass($TourId, $i++, '02', '02');
	CreateSubClass($TourId, $i++, '03', '03');
	CreateSubClass($TourId, $i++, '04', '04');
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
	$TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC_RG=(TGT_IND_1_small10);

	$TargetSizeR=($Outdoor ? 122 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
	$TargetSizeRg=($Outdoor ? 122 : 60);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
	$DistanceRam=($Outdoor ? 60 : 18);
    $DistanceBam=($Outdoor ? 40 : 18);
	$DistanceRr=($Outdoor ? 40 : 18);
	$DistanceRg=($Outdoor ? 25 : 18);
    $DistanceBrg=($Outdoor ? 25 : 18);
	$DistanceC=($Outdoor ? 50 : 18);


	switch($SubRule) {
		case '1':
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLM',  'Assoluti Arco Olimpico Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLF',  'Assoluti Arco Olimpico Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COM',  'Assoluti Arco Compound Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COF',  'Assoluti Arco Compound Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if(!$Outdoor OR $TourType == 3 OR $TourType == 37 OR $TourType ==39) {
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'ANM',  'Assoluti Arco Nudo Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'ANF',  'Assoluti Arco Nudo Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                if($Outdoor AND $SubRule==1) {
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'ANAM',  'Assoluti Arco Nudo Allievi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBam);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'ANAF',  'Assoluti Arco Nudo Allievi Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBam);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'ANBM',  'Assoluti Arco Nudo Ragazzi e Giovanissimi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBrg);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'ANBF',  'Assoluti Arco Nudo Ragazze e Giovanissime Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBrg);
                }
            }
			if($Outdoor AND $SubRule==1) {
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLNM',  'Assoluti Arco Olimpico Allievi e Master Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLNF',  'Assoluti Arco Olimpico Allievi e Master Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLRM',  'Assoluti Arco Olimpico Ragazzi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLRF',  'Assoluti Arco Olimpico Ragazze Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLGM',  'Assoluti Arco Olimpico Giovanissimi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeRg, $DistanceRg);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLGF',  'Assoluti Arco Olimpico Giovanissime Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeRg, $DistanceRg);
                CreateEvent($TourId, $i++, 0, 0, 16, $TargetC_RG, 5, 3, 1, 5, 3, 1, 'CORM',  'Assoluti Arco Compound Ragazzi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
                CreateEvent($TourId, $i++, 0, 0, 16, $TargetC_RG, 5, 3, 1, 5, 3, 1, 'CORF',  'Assoluti Arco Compound Ragazze Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
                CreateEvent($TourId, $i++, 0, 0, 16, $TargetC_RG, 5, 3, 1, 5, 3, 1, 'COGM',  'Assoluti Arco Compound Giovanissimi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeRg, $DistanceRg);
                CreateEvent($TourId, $i++, 0, 0, 16, $TargetC_RG, 5, 3, 1, 5, 3, 1, 'COGF',  'Assoluti Arco Compound Giovanissime Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeRg, $DistanceRg);
			}
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLMT',  'Squadre Arco Olimpico Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLFT',  'Squadre Arco Olimpico Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLXT',  'Arco Olimpico Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COMT',  'Squadre Arco Compound Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COFT',  'Squadre Arco Compound Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COXT',  'Arco Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
            if(!$Outdoor OR $TourType == 3 OR $TourType == 37 OR $TourType ==39) {
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANMT',  'Squadre Arco Nudo Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANFT',  'Squadre Arco Nudo Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                if($Outdoor) {
                    CreateEvent($TourId, $i++, 1, 1, 4, $TargetB, 4, 4, 2, 4, 4, 2, 'ANXT',  'Arco Nudo Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                }
				if($Outdoor AND $SubRule==1) {
                    CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANAM',  'Squadre Arco Nudo Allievi Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBam);
                    CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANAF',  'Squadre Arco Nudo Allievi Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBam);
                    CreateEvent($TourId, $i++, 1, 1, 4, $TargetB, 4, 4, 2, 4, 4, 2, 'ANAX',  'Arco Nudo Allievi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBam);
                    CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANBM',  'Squadre Arco Nudo Ragazzi e Giovanissimi Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBrg);
                    CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANBF',  'Squadre Arco Nudo Ragazze e Giovanissime Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBrg);
                    CreateEvent($TourId, $i++, 1, 1, 4, $TargetB, 4, 4, 2, 4, 4, 2, 'ANBX',  'Arco Nudo Ragazzi e Giovanissimi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBrg);
                }
			}
			if($Outdoor AND $SubRule==1) {
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLNM',  'Squadre Arco Olimpico Allievi e Master Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLNF',  'Squadre Arco Olimpico Allievi e Master Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
                CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLNX',  'Arco Olimpico Allievi e Master Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLRM',  'Squadre Arco Olimpico Ragazzi Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLRF',  'Squadre Arco Olimpico Ragazze Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
                CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLRX',  'Arco Olimpico Ragazzi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLGM',  'Squadre Arco Olimpico Giovanissimi Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLGF',  'Squadre Arco Olimpico Giovanissime Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
                CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLGX',  'Olimpico Giovanissime Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
			}
			break;
		case '3':
			if($Outdoor) {
				$i=1;
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLJM',  'Assoluti Arco Olimpico Junior Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLJF',  'Assoluti Arco Olimpico Junior Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLAM',  'Assoluti Arco Olimpico Allievi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLAF',  'Assoluti Arco Olimpico Allievi Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLRM',  'Assoluti Arco Olimpico Ragazzi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLRF',  'Assoluti Arco Olimpico Ragazzi Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLGM',  'Assoluti Arco Olimpico Giovanissimi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLGF',  'Assoluti Arco Olimpico Giovanissimi Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRg);
                CreateEvent($TourId, $i++, 0, 0, 16, $TargetC_RG, 5, 3, 1, 5, 3, 1, 'CORM',  'Assoluti Arco Compound Ragazzi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
                CreateEvent($TourId, $i++, 0, 0, 16, $TargetC_RG, 5, 3, 1, 5, 3, 1, 'CORF',  'Assoluti Arco Compound Ragazze Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRr);
                CreateEvent($TourId, $i++, 0, 0, 16, $TargetC_RG, 5, 3, 1, 5, 3, 1, 'COGM',  'Assoluti Arco Compound Giovanissimi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeRg, $DistanceRg);
                CreateEvent($TourId, $i++, 0, 0, 16, $TargetC_RG, 5, 3, 1, 5, 3, 1, 'COGF',  'Assoluti Arco Compound Giovanissime Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeRg, $DistanceRg);
				if($TourType==3 OR $TourType==18 OR $TourType==37 OR $TourType==39) {
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COM',  'Assoluti Arco Compound Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COF',  'Assoluti Arco Compound Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
                    if($TourType==3 OR $TourType==37 OR $TourType==39) {
                        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'ANJM', 'Assoluti Arco Nudo Junior Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'ANJF', 'Assoluti Arco Nudo Junior Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                        CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'ANAM',  'Assoluti Arco Nudo Allievi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBam);
                        CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'ANAF',  'Assoluti Arco Nudo Allievi Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBam);
                        CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'ANBM',  'Assoluti Arco Nudo Ragazzi e Giovanissimi Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBrg);
                        CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'ANBF',  'Assoluti Arco Nudo Ragazze e Giovanissime Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBrg);
                    }
                } else {
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COJM',  'Assoluti Arco Compound Junior Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COJF',  'Assoluti Arco Compound Junior Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COAM',  'Assoluti Arco Compound Allievi Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COAF',  'Assoluti Arco Compound Allievi Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CORM',  'Assoluti Arco Compound Ragazzi Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CORF',  'Assoluti Arco Compound Ragazzi Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				}
				$i=1;
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLJM',  'Squadre Arco Olimpico Junior Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLJF',  'Squadre Arco Olimpico Junior Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLJX',  'Arco Olimpico Junior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLAM',  'Squadre Arco Olimpico Allievi Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLAF',  'Squadre Arco Olimpico Allievi Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLAX',  'Arco Olimpico Allievi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLRM',  'Squadre Arco Olimpico Ragazzi Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLRF',  'Squadre Arco Olimpico Ragazzi Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLRX',  'Arco Olimpico Ragazzi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRr);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLGM',  'Squadre Arco Olimpico Giovanissimi Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLGF',  'Squadre Arco Olimpico Giovanissime Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLGX',  'Arco Olimpico Giovanissimi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRg);
				if($TourType==3 OR $TourType==18 OR $TourType==37 OR $TourType==39) {
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COM',  'Squadre Arco Compound Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COF',  'Squadre Arco Compound Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COX',  'Arco Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
                    if($TourType==3 OR $TourType==37 OR $TourType==39) {
                        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANJM', 'Squadre Arco Nudo Junior Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANJF', 'Squadre Arco Nudo Junior Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                        CreateEvent($TourId, $i++, 1, 1, 4, $TargetB, 4, 4, 2, 4, 4, 2, 'ANJX', 'Arco Nudo Junior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANAM',  'Squadre Arco Nudo Allievi Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBam);
                        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANAF',  'Squadre Arco Nudo Allievi Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBam);
                        CreateEvent($TourId, $i++, 1, 1, 4, $TargetB, 4, 4, 2, 4, 4, 2, 'ANAX',  'Arco Nudo Allievi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBam);
                        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANBM',  'Squadre Arco Nudo Ragazzi e Giovanissimi Maschile', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBrg);
                        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANBF',  'Squadre Arco Nudo Ragazze e Giovanissime Femminile', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBrg);
                        CreateEvent($TourId, $i++, 1, 1, 4, $TargetB, 4, 4, 2, 4, 4, 2, 'ANBX',  'Arco Nudo Ragazzi e Giovanissimi Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBrg);
                    }
				} else {
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COJM',  'Squadre Arco Compound Junior Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COJF',  'Squadre Arco Compound Junior Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COJX',  'Arco Compound Junior Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COAM',  'Squadre Arco Compound Allievi Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COAF',  'Squadre Arco Compound Allievi Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COAX',  'Arco Compound Allievi Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CORM',  'Squadre Arco Compound Ragazzi Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CORF',  'Squadre Arco Compound Ragazzi Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'CORX',  'Arco Compound Ragazzi Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				}
			} else {
				$i=1;
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLM',  'Assoluti Arco Olimpico Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLF',  'Assoluti Arco Olimpico Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COM',  'Assoluti Arco Compound Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COF',  'Assoluti Arco Compound Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 0, 0, 16, 1, 5, 3, 1, 5, 3, 1, 'ANM',  'Assoluti Arco Nudo Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, 1, 5, 3, 1, 5, 3, 1, 'ANF',  'Assoluti Arco Nudo Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				$i=1;
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLMT',  'Squadre Arco Olimpico Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLFT',  'Squadre Arco Olimpico Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COMT',  'Squadre Arco Compound Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COFT',  'Squadre Arco Compound Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 0, 4, 1, 4, 6, 3, 4, 6, 3, 'ANMT',  'Squadre Arco Nudo Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, 1, 4, 6, 3, 4, 6, 3, 'ANFT',  'Squadre Arco Nudo Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			break;
		case '4':
			if($Outdoor) {
				$i=1;
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLSM',  'Assoluti Arco Olimpico Senior Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLSF',  'Assoluti Arco Olimpico Senior Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLMM',  'Assoluti Arco Olimpico Master Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLMF',  'Assoluti Arco Olimpico Master Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				if($TourType==3 OR $TourType==18 OR $TourType==37 OR $TourType==39) {
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COM',  'Assoluti Arco Compound Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COF',  'Assoluti Arco Compound Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
                    if($TourType==3 OR $TourType==37 OR $TourType==39) {
                        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'ANM', 'Assoluti Arco Nudo Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
                        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'ANF', 'Assoluti Arco Nudo Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
                    }
                } else {
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COSM',  'Assoluti Arco Compound Senior Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COSF',  'Assoluti Arco Compound Senior Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COMM',  'Assoluti Arco Compound Master Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COMF',  'Assoluti Arco Compound Master Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				}
				$i=1;
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLSM',  'Squadre Arco Olimpico Senior Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLSF',  'Squadre Arco Olimpico Senior Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLSX',  'Arco Olimpico Senior Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLMM',  'Squadre Arco Olimpico Master Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLMF',  'Squadre Arco Olimpico Master Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'OLMX',  'Arco Olimpico Master Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRam);
                if($TourType==3 OR $TourType==18 OR $TourType==37 OR $TourType==39) {
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COM',  'Squadre Arco Compound Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COF',  'Squadre Arco Compound Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COX',  'Arco Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
                    if($TourType==3 OR $TourType==37 OR $TourType==39) {
                        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANM', 'Squadre Arco Nudo Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'ANF', 'Squadre Arco Nudo Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                        CreateEvent($TourId, $i++, 1, 1, 4, $TargetB, 4, 4, 2, 4, 4, 2, 'ANX', 'Arco Nudo Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                    }
				} else {
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COSM',  'Squadre Arco Compound Senior Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COSF',  'Squadre Arco Compound Senior Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COSX',  'Arco Compound Senior Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COMM',  'Squadre Arco Compound Master Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COMF',  'Squadre Arco Compound Master Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
					CreateEvent($TourId, $i++, 1, 1, 4, $TargetC, 4, 4, 2, 4, 4, 2, 'COMX',  'Arco Compound Master Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				}
			} else {
				$i=1;
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLM',  'Assoluti Arco Olimpico Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'OLF',  'Assoluti Arco Olimpico Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COM',  'Assoluti Arco Compound Maschile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'COF',  'Assoluti Arco Compound Femminile', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 0, 0, 16, 1, 5, 3, 1, 5, 3, 1, 'ANM',  'Assoluti Arco Nudo Maschile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 0, 0, 16, 1, 5, 3, 1, 5, 3, 1, 'ANF',  'Assoluti Arco Nudo Femminile', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
				$i=1;
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLMT',  'Squadre Arco Olimpico Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'OLFT',  'Squadre Arco Olimpico Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COMT',  'Squadre Arco Compound Maschili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'COFT',  'Squadre Arco Compound Femminili', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 0, 4, 1, 4, 6, 3, 4, 6, 3, 'ANMT',  'Squadre Arco Nudo Maschili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 0, 4, 1, 4, 6, 3, 4, 6, 3, 'ANFT',  'Squadre Arco Nudo Femminili', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			break;
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	if($TourType==3 OR $TourType==18 OR $TourType==37 OR $TourType==39) {
		switch($SubRule) {
			case '1':
				$ds=array(
					'OL' => array('S', 'J'),
					'OLN' => array('M', 'A'),
					'OLR' => array('R'),
					'OLG' => array('G'),
					'CO' => array('S', 'J', 'M', 'A'),
                    'COR' => array('R'),
                    'COG' => array('G'),
                    'AN' => array('S', 'J', 'M'),
                    'ANA' => array('A'),
                    'ANB' => array('R', 'G'),
					);
			case '2':
				if(empty($ds)) {
					$ds=array(
						'OL' => array('S'),
						'CO' => array('S'),
                        'AN' => array('S'),
						);
				}
				foreach($ds as $d => $cs) {
					foreach($cs as $c) {
						InsertClassEvent($TourId, 0, 1, $d.'M', substr($d,0,2), $c.'M');
						InsertClassEvent($TourId, 0, 1, $d.'F', substr($d,0,2), $c.'F');

						InsertClassEvent($TourId, 1, 3, substr($d.'MT',0,4), substr($d,0,2), $c.'M');
						InsertClassEvent($TourId, 1, 3, substr($d.'FT',0,4), substr($d,0,2), $c.'F');
						InsertClassEvent($TourId, 1, 1, substr($d.'XT',0,4), substr($d,0,2), $c.'M');
						InsertClassEvent($TourId, 2, 1, substr($d.'XT',0,4), substr($d,0,2), $c.'F');
					}
				}
				break;
			case '3':
				$ds=array(
					'OL' => array('J', 'A', 'R', 'G'),
					'CO' => array('J', 'A'),
                    'COR' => array('R'),
                    'COG' => array('G'),
                    'AN' => array('J', 'A'),
                    'ANB' => array('R', 'G'),
					);
                foreach($ds as $d => $cs) {
                    foreach($cs as $c) {
                        InsertClassEvent($TourId, 0, 1, $d.(($d!='OL' AND $d!='AN') ? '' : $c).'M', substr($d,0,2), $c.'M');
                        InsertClassEvent($TourId, 0, 1, $d.(($d!='OL' AND $d!='AN') ? '' : $c).'F', substr($d,0,2), $c.'F');

                        InsertClassEvent($TourId, 1, 3, $d.(($d!='OL' AND $d!='AN') ? '' : $c).'M', substr($d,0,2), $c.'M');
                        InsertClassEvent($TourId, 1, 3, $d.(($d!='OL' AND $d!='AN') ? '' : $c).'F', substr($d,0,2), $c.'F');
                        InsertClassEvent($TourId, 1, 1, $d.(($d!='OL' AND $d!='AN') ? '' : $c).'X', substr($d,0,2), $c.'M');
                        InsertClassEvent($TourId, 2, 1, $d.(($d!='OL' AND $d!='AN') ? '' : $c).'X', substr($d,0,2), $c.'F');
                    }
                }
                break;
			case '4':
                $ds=array(
                    'OL' => array('S', 'M'),
                    'CO' => array('S', 'M'),
                    'AN' => array('S', 'M'),
                    );
				foreach($ds as $d => $cs) {
					foreach($cs as $c) {
						InsertClassEvent($TourId, 0, 1, $d.($d!='OL' ? '' : $c).'M', $d, $c.'M');
						InsertClassEvent($TourId, 0, 1, $d.($d!='OL' ? '' : $c).'F', $d, $c.'F');

						InsertClassEvent($TourId, 1, 3, $d.($d!='OL' ? '' : $c).'M', $d, $c.'M');
						InsertClassEvent($TourId, 1, 3, $d.($d!='OL' ? '' : $c).'F', $d, $c.'F');
						InsertClassEvent($TourId, 1, 1, $d.($d!='OL' ? '' : $c).'X', $d, $c.'M');
						InsertClassEvent($TourId, 2, 1, $d.($d!='OL' ? '' : $c).'X', $d, $c.'F');
					}
				}
				break;
		}
	} else {
		switch($SubRule) {
			case '1':
				$ds=array('OL', 'CO');
				$cs=array('S', 'J');
				if(!$Outdoor) {
					$ds[]='AN';
					$cs[]='M';
					$cs[]='A';
					$cs[]='R';
                    if(!in_array($TourType,array(6,7,8))) {
                        $cs[]='G';
                    }
				}

				foreach($ds as $d) {
					foreach($cs as $c) {
						InsertClassEvent($TourId, 0, 1, $d.'M', $d, $c.'M');
						InsertClassEvent($TourId, 0, 1, $d.'F', $d, $c.'F');

						InsertClassEvent($TourId, 1, 3, $d.'MT', $d, $c.'M');
						InsertClassEvent($TourId, 1, 3, $d.'FT', $d, $c.'F');
						if($Outdoor) {
							InsertClassEvent($TourId, 1, 1, $d.'XT', $d, $c.'M');
							InsertClassEvent($TourId, 2, 1, $d.'XT', $d, $c.'F');
						}
					}
				}
				if($Outdoor) {
					InsertClassEvent($TourId, 0, 1, 'OLNM', 'OL', 'MM');
					InsertClassEvent($TourId, 0, 1, 'OLNM', 'OL', 'AM');
					InsertClassEvent($TourId, 0, 1, 'OLNF', 'OL', 'MF');
					InsertClassEvent($TourId, 0, 1, 'OLNF', 'OL', 'AF');
				}
				break;
			case '2':
				$ds=array('OL', 'CO');
				$cs=array('S');
				if(!$Outdoor) {
					$ds[]='AN';
				}
				foreach($ds as $d) {
					foreach($cs as $c) {
						InsertClassEvent($TourId, 0, 1, $d.'M', $d, $c.'M');
						InsertClassEvent($TourId, 0, 1, $d.'F', $d, $c.'F');

						InsertClassEvent($TourId, 1, 3, $d.'MT', $d, $c.'M');
						InsertClassEvent($TourId, 1, 3, $d.'FT', $d, $c.'F');
						if($Outdoor) {
							InsertClassEvent($TourId, 1, 1, $d.'XT', $d, $c.'M');
							InsertClassEvent($TourId, 2, 1, $d.'XT', $d, $c.'F');
						}
					}
				}
				break;
			case '3':
				if($Outdoor) {
					foreach(array('J','A','R','G') as $c) {
						foreach(array('OL','CO') as $d) {
							InsertClassEvent($TourId, 0, 1, $d.$c.'M', $d, $c.'M');
							InsertClassEvent($TourId, 0, 1, $d.$c.'F', $d, $c.'F');

							InsertClassEvent($TourId, 1, 3, $d.$c.'M', $d, $c.'M');
							InsertClassEvent($TourId, 1, 3, $d.$c.'F', $d, $c.'F');
							InsertClassEvent($TourId, 1, 1, $d.$c.'X', $d, $c.'M');
							InsertClassEvent($TourId, 2, 1, $d.$c.'X', $d, $c.'F');
						}
					}
				} else {
					foreach(array('J','A','R') as $c) {
						foreach(array('OL','CO','AN') as $d) {
							InsertClassEvent($TourId, 0, 1, $d.'M', $d, $c.'M');
							InsertClassEvent($TourId, 0, 1, $d.'F', $d, $c.'F');

							InsertClassEvent($TourId, 1, 3, $d.'MT', $d, $c.'M');
							InsertClassEvent($TourId, 1, 3, $d.'FT', $d, $c.'F');
						}
					}
				}
				break;
			case '4':
				if($Outdoor) {
					foreach(array('S','M') as $c) {
						foreach(array('OL','CO') as $d) {
							InsertClassEvent($TourId, 0, 1, $d.$c.'M', $d, $c.'M');
							InsertClassEvent($TourId, 0, 1, $d.$c.'F', $d, $c.'F');

							InsertClassEvent($TourId, 1, 3, $d.$c.'M', $d, $c.'M');
							InsertClassEvent($TourId, 1, 3, $d.$c.'F', $d, $c.'F');
							InsertClassEvent($TourId, 1, 1, $d.$c.'X', $d, $c.'M');
							InsertClassEvent($TourId, 2, 1, $d.$c.'X', $d, $c.'F');
						}
					}
				} else {
					foreach(array('S','M') as $c) {
						foreach(array('OL','CO','AN') as $d) {
							InsertClassEvent($TourId, 0, 1, $d.'M', $d, $c.'M');
							InsertClassEvent($TourId, 0, 1, $d.'F', $d, $c.'F');

							InsertClassEvent($TourId, 1, 3, $d.'MT', $d, $c.'M');
							InsertClassEvent($TourId, 1, 3, $d.'FT', $d, $c.'F');
						}
					}
				}
				break;
		}
	}
}

/*

FIELD ONLY THINGS

*/

function CreateStandardFieldEvents($TourId, $SubRule) {
	$Elim1=array(
		'Archers' => ($SubRule==3 ? 16 : 0),
		'Ends' => 12,
		'Arrows' => 3,
		'SO' => 1
	);
	$Elim2=array(
		'Archers' => ($SubRule==4 ? 0 : 8),
		'Ends' => 8,
		'Arrows' => 3,
		'SO' => 1
	);
	$i=1;
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'OLM', 'Assoluti Arco Olimpico Maschile',  0, 0, 0, $Elim1, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'OLF', 'Assoluti Arco Olimpico Femminile', 0, 0, 0, $Elim1, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'COM', 'Assoluti Arco Compound Maschile',  0, 0, 0, $Elim1, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'COF', 'Assoluti Arco Compound Femminile', 0, 0, 0, $Elim1, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'ANM', 'Assoluti Arco Nudo Maschile',     0, 0, 0, $Elim1, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'ANF', 'Assoluti Arco Nudo Femminile',   0, 0, 0, $Elim1, $Elim2);
	if($SubRule==2) {
		CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'LBM', 'Assoluti Arco LongBow Maschile',     0, 0, 0, $Elim1, $Elim2);
		CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'LBF', 'Assoluti Arco LongBow Femminile',   0, 0, 0, $Elim1, $Elim2);
	}
	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'SQM',  'Squadre Assolute Maschili',0,248,15);
	CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'SQF',  'Squadre Assolute Femminili',0,248,15);
}

function InsertStandardFieldEvents($TourId, $SubRule) {
	InsertClassEvent($TourId, 0, 1, 'OLM', 'OL', 'SM');
	InsertClassEvent($TourId, 0, 1, 'OLM', 'OL', 'JM');
	InsertClassEvent($TourId, 0, 1, 'OLM', 'OL', 'MM');
	InsertClassEvent($TourId, 0, 1, 'OLM', 'OL', 'VM');
	InsertClassEvent($TourId, 0, 1, 'OLF', 'OL', 'SF');
	InsertClassEvent($TourId, 0, 1, 'OLF', 'OL', 'JF');
	InsertClassEvent($TourId, 0, 1, 'OLF', 'OL', 'MF');
	InsertClassEvent($TourId, 0, 1, 'OLF', 'OL', 'VF');
	InsertClassEvent($TourId, 0, 1, 'COM', 'CO', 'SM');
	InsertClassEvent($TourId, 0, 1, 'COM', 'CO', 'MM');
	InsertClassEvent($TourId, 0, 1, 'COM', 'CO', 'JM');
	InsertClassEvent($TourId, 0, 1, 'COM', 'CO', 'VM');
	InsertClassEvent($TourId, 0, 1, 'COF', 'CO', 'JF');
	InsertClassEvent($TourId, 0, 1, 'COF', 'CO', 'SF');
	InsertClassEvent($TourId, 0, 1, 'COF', 'CO', 'MF');
	InsertClassEvent($TourId, 0, 1, 'COF', 'CO', 'VF');
	InsertClassEvent($TourId, 0, 1, 'ANM', 'AN', 'SM');
	InsertClassEvent($TourId, 0, 1, 'ANM', 'AN', 'JM');
	InsertClassEvent($TourId, 0, 1, 'ANM', 'AN', 'MM');
	InsertClassEvent($TourId, 0, 1, 'ANM', 'AN', 'VM');
	InsertClassEvent($TourId, 0, 1, 'ANF', 'AN', 'MF');
	InsertClassEvent($TourId, 0, 1, 'ANF', 'AN', 'SF');
	InsertClassEvent($TourId, 0, 1, 'ANF', 'AN', 'JF');
	InsertClassEvent($TourId, 0, 1, 'ANF', 'AN', 'VF');
	if($SubRule==2) {
		InsertClassEvent($TourId, 0, 1, 'LBM', 'LB', 'SM');
		InsertClassEvent($TourId, 0, 1, 'LBM', 'LB', 'JM');
		InsertClassEvent($TourId, 0, 1, 'LBM', 'LB', 'MM');
		InsertClassEvent($TourId, 0, 1, 'LBM', 'LB', 'VM');
		InsertClassEvent($TourId, 0, 1, 'LBF', 'LB', 'MF');
		InsertClassEvent($TourId, 0, 1, 'LBF', 'LB', 'SF');
		InsertClassEvent($TourId, 0, 1, 'LBF', 'LB', 'JF');
		InsertClassEvent($TourId, 0, 1, 'LBF', 'LB', 'VF');
	}

	InsertClassEvent($TourId, 1, 1, 'SQM', 'OL', 'SM');
	InsertClassEvent($TourId, 1, 1, 'SQM', 'OL', 'JM');
	InsertClassEvent($TourId, 1, 1, 'SQM', 'OL', 'MM');
	InsertClassEvent($TourId, 1, 1, 'SQM', 'OL', 'VM');
	InsertClassEvent($TourId, 2, 1, 'SQM', 'CO', 'SM');
	InsertClassEvent($TourId, 2, 1, 'SQM', 'CO', 'JM');
	InsertClassEvent($TourId, 2, 1, 'SQM', 'CO', 'MM');
	InsertClassEvent($TourId, 2, 1, 'SQM', 'CO', 'VM');
	InsertClassEvent($TourId, 3, 1, 'SQM', 'AN', 'SM');
	InsertClassEvent($TourId, 3, 1, 'SQM', 'AN', 'JM');
	InsertClassEvent($TourId, 3, 1, 'SQM', 'AN', 'MM');
	InsertClassEvent($TourId, 3, 1, 'SQM', 'AN', 'VM');
	InsertClassEvent($TourId, 1, 1, 'SQF', 'OL', 'SF');
	InsertClassEvent($TourId, 1, 1, 'SQF', 'OL', 'JF');
	InsertClassEvent($TourId, 1, 1, 'SQF', 'OL', 'MF');
	InsertClassEvent($TourId, 1, 1, 'SQF', 'OL', 'VF');
	InsertClassEvent($TourId, 2, 1, 'SQF', 'CO', 'SF');
	InsertClassEvent($TourId, 2, 1, 'SQF', 'CO', 'JF');
	InsertClassEvent($TourId, 2, 1, 'SQF', 'CO', 'MF');
	InsertClassEvent($TourId, 2, 1, 'SQF', 'CO', 'VF');
	InsertClassEvent($TourId, 3, 1, 'SQF', 'AN', 'SF');
	InsertClassEvent($TourId, 3, 1, 'SQF', 'AN', 'JF');
	InsertClassEvent($TourId, 3, 1, 'SQF', 'AN', 'MF');
	InsertClassEvent($TourId, 3, 1, 'SQF', 'AN', 'VF');
}


function InsertStandardFieldEliminations($TourId, $SubRule){
	if($SubRule==4) return;
	$cls=array('M', 'F');
	$divs=array('OL', 'CO', 'AN');
	if($SubRule==2) $divs[]='AI';
	foreach($divs as $div) {
		foreach($cls as $cl) {
			if($SubRule==3) {
				for($n=1; $n<=16; $n++) {
					safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=0, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
				}
			}
			for($n=1; $n<=8; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
		}
	}
}

/*

3D ONLY THINGS

*/

function CreateStandard3DEvents($TourId, $SubRule) {
	$Elim1=array(
		'Archers' => 16,
		'Ends' => 12,
		'Arrows' => 1,
		'SO' => 1
	);
	$Elim2=array(
		'Archers' => 8,
		'Ends' => 8,
		'Arrows' => 1,
		'SO' => 1
	);
	$i=1;
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'COSM', 'Arco Compound Senior Maschile',   0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'COSF', 'Arco Compound Senior Femminile',  0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'AISM', 'Arco Istintivo Senior Maschile',  0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'AISF', 'Arco Istintivo Senior Femminile', 0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ANSM', 'Arco Nudo Senior Maschile',       0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ANSF', 'Arco Nudo Senior Femminile',      0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LBSM', 'Longbow Senior Maschile',         0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LBSF', 'Longbow Senior Femminile',        0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'COJM', 'Arco Compound Junior Maschile',   0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'COJF', 'Arco Compound Junior Femminile',  0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'AIJM', 'Arco Istintivo Junior Maschile',  0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'AIJF', 'Arco Istintivo Junior Femminile', 0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ANJM', 'Arco Nudo Junior Maschile',       0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ANJF', 'Arco Nudo Junior Femminile',      0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LBJM', 'Longbow Junior Maschile',         0, 0, 0, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LBJF', 'Longbow Junior Femminile',        0, 0, 0, 0, $Elim2);
	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 4, 8, 8, 3, 3, 4, 3, 3, 'SQSM',  'Squadre Senior Maschili',0,248,15);
	CreateEvent($TourId, $i++, 1, 0, 4, 8, 8, 3, 3, 4, 3, 3, 'SQSF',  'Squadre Senior Femminili',0,248,15);
	CreateEvent($TourId, $i++, 1, 0, 4, 8, 8, 3, 3, 4, 3, 3, 'SQJM',  'Squadre Junior Maschili',0,248,15);
	CreateEvent($TourId, $i++, 1, 0, 4, 8, 8, 3, 3, 4, 3, 3, 'SQJF',  'Squadre Junior Femminili',0,248,15);
}

function InsertStandard3DEvents($TourId, $SubRule) {
	InsertClassEvent($TourId, 0, 1, 'COSM', 'CO', 'SM');
	InsertClassEvent($TourId, 0, 1, 'COJM', 'CO', 'JM');
	InsertClassEvent($TourId, 0, 1, 'COSF', 'CO', 'SF');
	InsertClassEvent($TourId, 0, 1, 'COJF', 'CO', 'JF');
	InsertClassEvent($TourId, 0, 1, 'AISM', 'AI', 'SM');
	InsertClassEvent($TourId, 0, 1, 'AIJM', 'AI', 'JM');
	InsertClassEvent($TourId, 0, 1, 'AISF', 'AI', 'SF');
	InsertClassEvent($TourId, 0, 1, 'AIJF', 'AI', 'JF');
	InsertClassEvent($TourId, 0, 1, 'ANSM', 'AN', 'SM');
	InsertClassEvent($TourId, 0, 1, 'ANJM', 'AN', 'JM');
	InsertClassEvent($TourId, 0, 1, 'ANSF', 'AN', 'SF');
	InsertClassEvent($TourId, 0, 1, 'ANJF', 'AN', 'JF');
	InsertClassEvent($TourId, 0, 1, 'LBSM', 'LB', 'SM');
	InsertClassEvent($TourId, 0, 1, 'LBJM', 'LB', 'JM');
	InsertClassEvent($TourId, 0, 1, 'LBSF', 'LB', 'SF');
	InsertClassEvent($TourId, 0, 1, 'LBJF', 'LB', 'JF');

	InsertClassEvent($TourId, 1, 1, 'SQSM',  'CO',  'SM');
	InsertClassEvent($TourId, 2, 1, 'SQSM',  'LB',  'SM');
	InsertClassEvent($TourId, 3, 1, 'SQSM',  'AN',  'SM');
	InsertClassEvent($TourId, 3, 1, 'SQSM',  'AI',  'SM');
	InsertClassEvent($TourId, 1, 1, 'SQJM',  'CO',  'JM');
	InsertClassEvent($TourId, 2, 1, 'SQJM',  'LB',  'JM');
	InsertClassEvent($TourId, 3, 1, 'SQJM',  'AN',  'JM');
	InsertClassEvent($TourId, 3, 1, 'SQJM',  'AI',  'JM');
	InsertClassEvent($TourId, 1, 1, 'SQSF',  'CO',  'SF');
	InsertClassEvent($TourId, 2, 1, 'SQSF',  'LB',  'SF');
	InsertClassEvent($TourId, 3, 1, 'SQSF',  'AN',  'SF');
	InsertClassEvent($TourId, 3, 1, 'SQSF',  'AI',  'SF');
	InsertClassEvent($TourId, 1, 1, 'SQJF',  'CO',  'JF');
	InsertClassEvent($TourId, 2, 1, 'SQJF',  'LB',  'JF');
	InsertClassEvent($TourId, 3, 1, 'SQJF',  'AN',  'JF');
	InsertClassEvent($TourId, 3, 1, 'SQJF',  'AI',  'JF');
}

function InsertStandard3DEliminations($TourId, $SubRule){
	$cls=array('SM', 'JM', 'SF', 'JF');
	foreach(array('CO', 'LB', 'AN', 'AI') as $div) {
		foreach($cls as $cl) {
			for($n=1; $n<=8; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
		}
	}
}

/*

GdG THINGS

*/


function CreateStandardStudClasses($TourId, $TourType) {
	$i=1;
	if($TourType==33) {
		CreateClass($TourId, $i++, 9, 14, 0, 'M', 'M', 'Maschi');
		CreateClass($TourId, $i++, 9, 14, 1, 'F', 'F', 'Femmine');
	} else {
		CreateClass($TourId, $i++, 13, 21, 0, 'CM', 'CM', 'Cadetti Maschile');
		CreateClass($TourId, $i++, 13, 21, 1, 'CF', 'CF', 'Cadette Femminile');
		CreateClass($TourId, $i++, 13, 21, 0, 'AM', 'AM', 'Allievi Maschile');
		CreateClass($TourId, $i++, 13, 21, 1, 'AF', 'AF', 'Allieve Femminile');
	}
}

function CreateStandardGdGClasses($TourId, $SubRule, $Field='') {
	$i=1;
    CreateClass($TourId, $i++, 8, 8, 0, 'GM', 'GM', 'Ragazzi 2016',1, 'OL');
    CreateClass($TourId, $i++, 8, 8, 1, 'GF', 'GF', 'Ragazze 2016',1, 'OL');
    CreateClass($TourId, $i++, 9, 11, 0, 'M0', 'M0', 'Ragazzi 2013-2014-2015',1, 'OL');
    CreateClass($TourId, $i++, 9, 11, 1, 'F0', 'F0', 'Ragazze 2013-2014-2015',1, 'OL');
    CreateClass($TourId, $i++, 12, 12, 0, 'M3', 'M3', 'Ragazzi 2012',1, 'OL');
    CreateClass($TourId, $i++, 12, 12, 1, 'F3', 'F3', 'Ragazze 2012',1, 'OL');
    CreateClass($TourId, $i++, 13, 13, 0, 'M4', 'M4', 'Ragazzi 2011',1, 'OL');
    CreateClass($TourId, $i++, 13, 13, 1, 'F4', 'F4', 'Ragazze 2011',1, 'OL');
    CreateClass($TourId, $i++, 12, 13, -1, 'X', 'X', 'Ragazzi/e 2011-2012',1, 'CO');
}

function CreateStandardSperimClasses($TourId) {
	$i=1;
	CreateClass($TourId, $i++, 0, 1, 0, 'M1', 'M1,M2', 'Esordienti 1 Maschile');
	CreateClass($TourId, $i++, 0, 1, 1, 'F1', 'F1,F2', 'Esordienti 1 Femminile');
	CreateClass($TourId, $i++, 2, 2, 0, 'M2', 'M2', 'Esordienti 2 Maschile');
	CreateClass($TourId, $i++, 2, 2, 1, 'F2', 'F2', 'Esordienti 2 Femminile');
}
