<?php

/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = 'danish';
if(empty($SubRule)) $SubRule='1';

$tourDetIocCode = 'NOR_s';
if(($SubRule==3 and $TourType==6) or ($SubRule==5 and $TourType==3) or ($SubRule==9 and $TourType==9) or ($SubRule==7 and $TourType==11) or ($SubRule==9 and $TourType==17)) {
	$tourDetIocCode = 'NOR';
}

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) {
	$Champs=(
			 ($Type==3  and $SubRule==4)
			or ($Type==6  and $SubRule==3)
			or ($Type==9  and $SubRule==9)
			or ($Type==11 and $SubRule==9)
			or ($Type==17 and $SubRule==9)
			);
	$i=1;
	if(!$Champs) CreateDivision($TourId, $i++, 'F', 'Felles');
    CreateDivision($TourId, $i++, 'T', 'Tradisjonell', 1, 'T', 'T');
    CreateDivision($TourId, $i++, 'LB', 'Langbue', 1, 'L', 'L');
    // B is in all types, as is LB  since may 2016, IN changed into T since 2022
    CreateDivision($TourId, $i++, 'B', 'Barebow');
    CreateDivision($TourId, $i++, 'R', 'Recurve');
	CreateDivision($TourId, $i++, 'C', 'Compound');

	// BU is only 3D and Hunter
	if($Type==11 or $Type==17) {
        CreateDivision($TourId, $i++, 'BU', 'Buejeger');
    } else {
        CreateDivision($TourId, $i++, 'PU', 'Psykisk Utviklingshemmet');
    }
    if(($Type==3 AND $SubRule==4) OR $Type==6 OR $Type==7 OR $Type==8 OR $Type==22) {
        CreateDivision($TourId, $i++, 'VI', 'Visual Impaired', 1, 'VI','VI', true);
    }

	// Only in Champs, "non athlete" divisions
	if($Champs) {
		CreateDivision($TourId, $i++, 'D', 'Dommer', 0);
		CreateDivision($TourId, $i++, 'A', 'Arrangør', 0);
		CreateDivision($TourId, $i++, 'V', 'Vip', 0);
	}
}

function CreateStandardClasses($TourId, $SubRule, $Type) {
	$Champs=(
			 ($Type==3  and $SubRule==4)
			or ($Type==6  and $SubRule==3)
			or ($Type==9  and $SubRule==9)
			or ($Type==11 and $SubRule==9)
			or ($Type==17 and $SubRule==9)
			);
	$Field=($Type==9 or $Type==11 or $Type==17);
	$i=1;

	// Champs
	if($Champs) {
		if($Field) {
			// 3D champs
			CreateClass($TourId, $i++, 13, 15, -1, 'R', 'R,K,Di,Hi', 'Rekrutt', 1, 'B,T,LB');
			CreateClass($TourId, $i++, 13, 15, 1, 'RJ', 'RJ,DU18,DU21,D', 'Rekrutt Jenter', 1, 'C,R');
			CreateClass($TourId, $i++, 13, 15, 0, 'RG', 'RG,HU18,HU21,H', 'Rekrutt Gutter', 1, 'C,R');
			CreateClass($TourId, $i++, 16, 17, -1, 'U18', 'U18,Di,Hi', 'Under 18', 1, 'B,T,LB');
			CreateClass($TourId, $i++, 16, 17, 1, 'DU18', 'DU18,DU21,D', 'Damer Under 18', 1, 'C,R');
			CreateClass($TourId, $i++, 16, 17, 0, 'HU18', 'HU18,HU21,H', 'Herrer Under 18', 1, 'C,R');
			CreateClass($TourId, $i++, 18, 20, 1, 'DU21', 'DU21,D', 'Damer Under 21', 1, 'C,R');
			CreateClass($TourId, $i++, 18, 20, 0, 'HU21', 'HU21,H', 'Herrer Under 21', 1, 'C,R');
			CreateClass($TourId, $i++, 18, 99, 1, 'Di', 'Di', 'Damer', 1, 'B,T,LB');
			CreateClass($TourId, $i++, 18, 99, 0, 'Hi', 'Hi', 'Herrer', 1, 'B,T,LB');
			CreateClass($TourId, $i++, 21, 99, 1, 'D', 'D', 'Damer', 1, 'C,R');
			CreateClass($TourId, $i++, 21, 99, 0, 'H', 'H', 'Herrer', 1, 'C,R');
			if($Type==11 or $Type==17) {
                CreateClass($TourId, $i++, 13, 99, -1, 'BU', 'BU', 'Buejeger', 1, 'BU');
            } else {
                CreateClass($TourId, $i++, 13, 99, 0, 'F', 'F', 'Felles', 1, 'PU');
            }
		} else {
			// Outdoor and Indoor champs
            $Indoor = ($Type==6 OR $Type==7 OR $Type==8 OR $Type==22);
            if(!$Indoor) {
                CreateClass($TourId, $i++, 13, 15, -1, 'R', 'R,K,Di,Hi', 'Rekrutt', 1, 'B,T,LB');
            }
            CreateClass($TourId, $i++, 13, 15, 1, 'RJ', 'RJ,HU18,DU21,D', 'Rekrutt Jenter', 1, 'C,R'.($Indoor ? ',B,LB,T':''));
            CreateClass($TourId, $i++, 13, 15, 0, 'RG', 'RG,DU18,HU21,H', 'Rekrutt Gutter', 1, 'C,R'.($Indoor ? ',B,LB,T':''));
            if(!$Indoor) {
                CreateClass($TourId, $i++, 16, 17, -1, 'U18', 'U18,Di,Hi', 'Under 18', 1, 'B,T,LB');
            }
            CreateClass($TourId, $i++, 16, 17, 1, 'DU18', 'DU18,HU21,H', 'Damer Under 18', 1, 'C,R'.($Indoor ? ',B,LB,T':''));
            CreateClass($TourId, $i++, 16, 17, 0, 'HU18', 'HU18,DU21,D', 'Herrer Under 18', 1, 'C,R'.($Indoor ? ',B,LB,T':''));
            CreateClass($TourId, $i++, 18, 20, 1, 'DU21', 'DU21,D', 'Damer Under 21', 1, 'C,R'.($Indoor ? ',B,LB,T':''));
            CreateClass($TourId, $i++, 18, 20, 0, 'HU21', 'HU21,H', 'Herrer Under 21', 1, 'C,R'.($Indoor ? ',B,LB,T':''));
            CreateClass($TourId, $i++, 50, 99, 1, 'D5', 'D5,D', 'Damer 50', 1, 'C,R');
            CreateClass($TourId, $i++, 50, 99, 0, 'H5', 'H5,H', 'Herrer 50', 1, 'C,R');
            CreateClass($TourId, $i++, 21, 99, 1, 'Di', 'Di', 'Damer', 1, 'B,T,LB');
            CreateClass($TourId, $i++, 21, 99, 0, 'Hi', 'Hi', 'Herrer', 1, 'B,T,LB');
            CreateClass($TourId, $i++, 21, 49, 1, 'D', 'D', 'Damer', 1, 'C,R');
            CreateClass($TourId, $i++, 21, 49, 0, 'H', 'H', 'Herrer', 1, 'C,R');
            if($Indoor OR ($Type==3  and $SubRule==4)) {
                CreateClass($TourId, $i++, 13, 99, 0, 'F', 'F', 'Felles', 1, 'VI,PU');
            }
		}

		// non competing classes
		CreateClass($TourId, $i++, 1, 99, -1, 'DO', 'DO', 'Dommer', 0, 'D');
		CreateClass($TourId, $i++, 1, 99, -1, 'AR', 'AR', 'Arrangør', 0, 'A');
		CreateClass($TourId, $i++, 1, 99, -1, 'HE', 'HE', 'Helsepersonell', 0, 'A');
		CreateClass($TourId, $i++, 1, 99, -1, 'ST', 'ST', 'Stab', 0, 'A');
		CreateClass($TourId, $i++, 1, 99, -1, 'LA', 'LA', 'Lagleder', 0, 'V');
		CreateClass($TourId, $i++, 1, 99, -1, 'TR', 'TR', 'Trener', 0, 'V');
		CreateClass($TourId, $i++, 1, 99, -1, 'ME', 'ME', 'Media', 0, 'V');
	} else {
		// ordinary competitions
		// Felles, only 1 class
		CreateClass($TourId, $i++,  8, 10, -1, '6', '6', '6', 1, 'F');

		// C&R have all classes
		// T has 1,2,-,4,5
		if($Field) {
			CreateClass($TourId, $i++, 11, 13, -1, '5', '1,2,4,5', '5', 1, 'C,R,B,LB,T');
			CreateClass($TourId, $i++, 14, 15, -1, '4', '1,2,4', '4', 1, 'C,R,B,LB,T');
			CreateClass($TourId, $i++, 16, 100, -1, '2', '1,2', '2', 1, 'C,R,B,LB,T');
			CreateClass($TourId, $i++, 16, 100, -1, '1', '1', '1', 1, 'C,R,B,LB,T');

			// BueJager has only 1
			if($Type==11 or $Type==17) CreateClass($TourId, $i++, 11, 100, -1, '1u', '1u', '1', 1, 'BU');
		} else {
            $IncludeB3 = !($Type==6 OR $Type==7 OR $Type==8 OR $Type==22);
            $Indoor = ($Type==6 OR $Type==7 OR $Type==8 OR $Type==22);
			CreateClass($TourId, $i++, 11, 13, -1, '5', '1,2,3,4,5', '5', 1, 'B,C,T,LB,R');
			CreateClass($TourId, $i++, 14, 15, -1, '4', '1,2,3,4', '4', 1, 'B,C,T,LB,R');
			CreateClass($TourId, $i++, 16, 100, -1, '3', '1,2,3', '3', 1, 'B,C,T,LB,R');
			CreateClass($TourId, $i++, 16, 100, -1, '2', '1,2', '2', 1, 'B,C,T,LB,R');
			CreateClass($TourId, $i++, 16, 100, -1, '1', '1', '1', 1, 'B,C,T,LB,R,PU' . ($Indoor ? ',VI' : '' ));
		}
	}
}

function CreateStandardSubClasses($TourId) {
	$i=1;
	CreateSubClass($TourId, $i++, 'R', 'R');
	CreateSubClass($TourId, $i++, 'K', 'K');
	CreateSubClass($TourId, $i++, 'Jr', 'Jr');
	CreateSubClass($TourId, $i++, 'Sr', 'Sr');
	CreateSubClass($TourId, $i++, '50', '50');
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
    $TargetF=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetRY=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
	$SetC=($Outdoor?0:1);
	$Phase=16;
	$Champs=(($SubRule==3 and $TourType==6)
			or ($SubRule==4 and $TourType==3)
// 			or ($SubRule==9 and $Type==9)
// 			or ($SubRule==7 and $Type==11)
// 			or ($SubRule==9 and $Type==17)
			);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$TargetSizeR=($Outdoor ? 122 : 40);
    $TargetSizeB=($Outdoor ? 122 : 60);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceRW=($Outdoor ? 60 : 18);
	$DistanceT=($Outdoor ? 30 : 18);
	$DistanceB=($Outdoor ? 40 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
	$DistanceF=($Outdoor ? 25 : 18);
    $DistancePUF=($Outdoor ? 20 : 12);

	if($Champs) {
        // only Indoor and 70m Round
        $i=1;
        if($Outdoor) {
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'LBR', 'Langbue Rekrutt', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'TR', 'Tradisjonell Rekrutt', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BR', 'Barebow Rekrutt', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceF);
        } else {
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'LBRJ', 'Langbue Rekrutt Jenter', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'TRJ', 'Tradisjonell Rekrutt Jenter', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BRJ', 'Barebow Rekrutt Jenter', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceF);
        }
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CRJ',  'Compound Rekrutt Jenter', 0, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'RRJ',  'Recurve Rekrutt Jenter', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        if(!$Outdoor) {
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'LBRG',  'Langbue Rekrutt Gutter', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'TRG',  'Tradisjonell Rekrutt Gutter', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BRG',  'Barebow Rekrutt Gutter', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        }
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CRG',  'Compound Rekrutt Gutter', 0, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'RRG',  'Recurve Rekrutt Gutter', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        if($Outdoor) {
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'LBU18',  'Langbue Under 18', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceT);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'TU18',  'Tradisjonell Under 18', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceT);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BU18',  'Barebow Under 18', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        } else {
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'LBDU18',  'Langbue Damer Under 18 ', 1, 240, 255, 0, 0, '', 'LU18W', $TargetSizeB, $DistanceT);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'TDU18',  'Tradisjonell Damer Under 18 ', 1, 240, 255, 0, 0, '', 'TU18W', $TargetSizeB, $DistanceT);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BDU18',  'Barebow Damer Under 18 ', 1, 240, 255, 0, 0, 'BU18W', 'BU18W', $TargetSizeR, $DistanceB);
        }
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CDU18',  'Compound Damer Under 18 ', 0, 240, 255, 0, 0, 'CU18W', 'CU18W', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RDU18',  'Recurve Damer Under 18 ', 1, 240, 255, 0, 0, 'RU18W', 'RU18W', $TargetSizeR, $DistanceRW);
        if(!$Outdoor) {
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'LBHU18',  'Langbue Herrer Under 18 ', 1, 240, 255, 0, 0, '', 'LU18M', $TargetSizeB, $DistanceT);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'THU18',  'Tradisjonell Herrer Under 18 ', 1, 240, 255, 0, 0, '', 'TU18M', $TargetSizeB, $DistanceT);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BHU18',  'Barebow Herrer Under 18 ', 1, 240, 255, 0, 0, 'BU18M', 'BU18M', $TargetSizeR, $DistanceB);
        }
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CHU18',  'Compound Herrer Under 18 ', 0, 240, 255, 0, 0, 'CU18M', 'CU18M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RHU18',  'Recurve Herrer Under 18 ', 1, 240, 255, 0, 0, 'RU18M', 'RU18M', $TargetSizeR, $DistanceRW);
        if(!$Outdoor) {
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'LBDU21',  'Langbue Damer Under 21', 1, 240, 255, 0, 0, '', 'LU21W', $TargetSizeB, $DistanceT);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'TDU21',  'Tradisjonell Damer Under 21', 1, 240, 255, 0, 0, '', 'TU21W', $TargetSizeB, $DistanceT);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BDU21',  'Barebow Damer Under 21', 1, 240, 255, 0, 0, 'BU21W', 'BU21W', $TargetSizeR, $DistanceB);
        }
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CDU21',  'Compound Damer Under 21', 0, 240, 255, 0, 0, 'CU21W', 'CU21W', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RDU21',  'Recurve Damer Under 21', 1, 240, 255, 0, 0, 'RU21W', 'RU21W', $TargetSizeR, $DistanceR);
        if(!$Outdoor) {
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'LBHU21',  'Langbue Herrer Under 21', 1, 240, 255, 0, 0, '', 'LU21M', $TargetSizeB, $DistanceT);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'THU21',  'Tradisjonell Herrer Under 21', 1, 240, 255, 0, 0, '', 'TU21M', $TargetSizeB, $DistanceT);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BHU21',  'Barebow Herrer Under 21', 1, 240, 255, 0, 0, 'BU21M', 'BU21M', $TargetSizeR, $DistanceB);
        }
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CHU21',  'Compound Herrer Under 21', 0, 240, 255, 0, 0, 'CU21M', 'CU21M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RHU21',  'Recurve Herrer Under 21', 1, 240, 255, 0, 0, 'RU21M', 'RU21M', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CD5',  'Compound Damer 50', 0, 240, 255, 0, 0, 'C50W', 'C50W', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RD5',  'Recurve Damer 50', 1, 240, 255, 0, 0, 'R50W', 'R50W', $TargetSizeR, $DistanceRW);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CH5',  'Compound Herrer 50', 0, 240, 255, 0, 0, 'C50M', 'C50M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RH5',  'Recurve Herrer 50', 1, 240, 255, 0, 0, 'R50M', 'R50M', $TargetSizeR, $DistanceRW);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'LBDi',  'Langbue Damer', 1, 240, 255, 0, 0, '', 'LW', $TargetSizeR, $DistanceT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'TDi',  'Tradisjonell Damer', 1, 240, 255, 0, 0, '', 'TW', $TargetSizeR, $DistanceT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BDi',  'Barebow Damer', 1, 240, 255, 0, 0, 'BW', 'BW', $TargetSizeR, $DistanceRW);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CD',  'Compound Damer', 0, 240, 255, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RD',  'Recurve Damer', 1, 240, 255, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'LBHi',  'Langbue Herrer', 1, 240, 255, 0, 0, '', 'LM', $TargetSizeR, $DistanceT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'THi',  'Tradisjonell Herrer', 1, 240, 255, 0, 0, '', 'TM', $TargetSizeR, $DistanceT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'BHi',  'Barebow Herrer', 1, 240, 255, 0, 0, 'BM', 'BM', $TargetSizeR, $DistanceRW);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CH',  'Compound Herrer', 0, 240, 255, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RH',  'Recurve Herrer', 1, 240, 255, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);
        if(!$Outdoor) {
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetF, 5, 3, 1, 5, 3, 1, 'PUF', 'Psykisk Utviklingshemmet', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistancePUF);
            CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetF, 5, 3, 1, 5, 3, 1, 'VIF', 'Visually Impaired', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceT);
        }
        //teams
        $Phase=4;
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'LBR',  'Langbue Rekrutt', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceF, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'TR',  'Tradisjonell Rekrutt', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceF, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BR',  'Barebow Rekrutt', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceF, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CR',  'Compound Rekrutt', 0, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceT, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'RR',  'Recurve Rekrutt',1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1);
        if($Outdoor) {
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'R60M',  'Recurve II',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRW, '', 1);
        }
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'LB',  'Langbue', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceT, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'T',  'Tradisjonell', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceT, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'B',  'Barebow', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceB, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'C',  'Compound',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'R',  'Recurve',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);
	} elseif($SubRule=='2' or $SubRule=='4') {
		// always individual finals if subrule==2, but NO teams
		$i=1;
        if($Outdoor) {
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B5', 'Barebow 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 20);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'T5', 'Tradisjonell 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 20);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'LB5', 'Langbue 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 20);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C5', 'Compound 5', 0, 240, 255, 0, 0, '', '', $TargetSizeR, 25);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R5', 'Recurve 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 25);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B4', 'Barebow 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 25);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'T4', 'Tradisjonell 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 25);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'LB4', 'Langbue 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 25);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C4', 'Compound 4', 0, 240, 255, 0, 0, '', '', $TargetSizeC, 30);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R4', 'Recurve 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 40);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B3', 'Barebow 3', 1, 240, 255, 0, 0, '', '', $TargetSizeB, 25);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'T3', 'Tradisjonell 3', 1, 240, 255, 0, 0, '', '', $TargetSizeB, 25);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'LB3', 'Langbue 3', 1, 240, 255, 0, 0, '', '', $TargetSizeB, 25);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'C3', 'Compound 3', 0, 240, 255, 0, 0, '', '', $TargetSizeC, 50);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R3', 'Recurve 3', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 50);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B2', 'Barebow 2', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 40);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'T2', 'Tradisjonell 2', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 25);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'LB2', 'Langbue 2', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 25);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C2', 'Compound 2', 0, 240, 255, 0, 0, '', '', $TargetSizeC, 50);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R2', 'Recurve 2', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 60);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B1', 'Barebow 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 50);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'T1', 'Tradisjonell 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 30);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'LB1', 'Langbue 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 30);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C1', 'Compound 1', 0, 240, 255, 0, 0, '', '', $TargetSizeC, 50);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R1', 'Recurve 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 70);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'PU1', 'Psykisk Utviklingshemmet 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 20);
        } else {
            $TargetSizeC = ($TourType == 7 ? 60 : 40);
            $TargetSizeB2 = ($TourType == 7 ? 80 : 40);
            $TargetSizeR = ($TourType == 7 ? 80 : 60);
            $DistanceF   = ($TourType == 7 ? 16 : 12);
            $DistanceR   = ($TourType == 7 ? 25 : 18);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C5', 'Compound 5', 0, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'R5', 'Recurve 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'B5', 'Barebow 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'T5', 'Tradisjonell 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'LB5', 'Langbue 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'B4', 'Barebow 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'T4', 'Tradisjonell 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'LB4', 'Langbue 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C4', 'Compound 4', 0, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'R4', 'Recurve 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'B3', 'Barebow 3', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'T3', 'Tradisjonell 3', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'LB3', 'Langbue 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C3', 'Compound 3', 0, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'R3', 'Recurve 3', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'B2', 'Barebow 2', 1, 240, 255, 0, 0, '', '', $TargetSizeB2, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'T2', 'Tradisjonell 2', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'LB2', 'Langbue 2', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C2', 'Compound 2', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'R2', 'Recurve 2', 1, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetF, 5, 3, 1, 5, 3, 1, 'PU1', 'Psykisk Utviklingshemmet 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'B1', 'Barebow 1', 1, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'T1', 'Tradisjonell 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'LB1', 'Langbue 1', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C1', 'Compound 1', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R1', 'Recurve 1', 1, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);
        }
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$inds=array();
	$team=array();
	$Champs=(($SubRule==3 and $TourType==6) or ($SubRule==4 and $TourType==3));

	if($Champs) {
		// 70m Round and Indoor Champs
		InsertClassEvent($TourId, 0, 1, 'LBR',  'LB', 'R');
		InsertClassEvent($TourId, 0, 1, 'TR',  'T', 'R');
		InsertClassEvent($TourId, 0, 1, 'BR',  'B', 'R');
		InsertClassEvent($TourId, 0, 1, 'RRJ', 'R', 'RJ');
		InsertClassEvent($TourId, 0, 1, 'CRJ', 'C', 'RJ');
		InsertClassEvent($TourId, 0, 1, 'RRG', 'R', 'RG');
		InsertClassEvent($TourId, 0, 1, 'CRG', 'C', 'RG');
		InsertClassEvent($TourId, 0, 1, 'LBK',  'LB', 'K');
		InsertClassEvent($TourId, 0, 1, 'TK',  'T', 'K');
		InsertClassEvent($TourId, 0, 1, 'BK',  'B', 'K');
		InsertClassEvent($TourId, 0, 1, 'RDU18', 'R', 'DU18');
		InsertClassEvent($TourId, 0, 1, 'CDU18', 'C', 'DU18');
		InsertClassEvent($TourId, 0, 1, 'RHU18', 'R', 'HU18');
		InsertClassEvent($TourId, 0, 1, 'CHU18', 'C', 'HU18');
		InsertClassEvent($TourId, 0, 1, 'RDU21', 'R', 'DU21');
		InsertClassEvent($TourId, 0, 1, 'CDU21', 'C', 'DU21');
		InsertClassEvent($TourId, 0, 1, 'RHU21', 'R', 'HU21');
		InsertClassEvent($TourId, 0, 1, 'CHU21', 'C', 'HU21');
		InsertClassEvent($TourId, 0, 1, 'RD5', 'R', 'D5');
		InsertClassEvent($TourId, 0, 1, 'CD5', 'C', 'D5');
		InsertClassEvent($TourId, 0, 1, 'RH5', 'R', 'H5');
		InsertClassEvent($TourId, 0, 1, 'CH5', 'C', 'H5');
        InsertClassEvent($TourId, 0, 1, 'LBDi', 'LB', 'Di');
        InsertClassEvent($TourId, 0, 1, 'TDi', 'T', 'Di');
        InsertClassEvent($TourId, 0, 1, 'BDi', 'B', 'Di');
        if(!$Outdoor) {
            InsertClassEvent($TourId, 0, 1, 'LBRJ', 'LB', 'RJ');
            InsertClassEvent($TourId, 0, 1, 'TRJ', 'T', 'RJ');
            InsertClassEvent($TourId, 0, 1, 'BRJ', 'B', 'RJ');
            InsertClassEvent($TourId, 0, 1, 'LBRG', 'LB', 'RG');
            InsertClassEvent($TourId, 0, 1, 'TRG', 'T', 'RG');
            InsertClassEvent($TourId, 0, 1, 'BRG', 'B', 'RG');
            InsertClassEvent($TourId, 0, 1, 'LBDU18', 'LB', 'DU18');
            InsertClassEvent($TourId, 0, 1, 'TDU18', 'T', 'DU18');
            InsertClassEvent($TourId, 0, 1, 'BDU18', 'B', 'DU18');
            InsertClassEvent($TourId, 0, 1, 'LBHU18', 'LB', 'HU18');
            InsertClassEvent($TourId, 0, 1, 'THU18', 'T', 'HU18');
            InsertClassEvent($TourId, 0, 1, 'BHU18', 'B', 'HU18');
            InsertClassEvent($TourId, 0, 1, 'LBDU21', 'LB', 'DU21');
            InsertClassEvent($TourId, 0, 1, 'TDU21', 'T', 'DU21');
            InsertClassEvent($TourId, 0, 1, 'BDU21', 'B', 'DU21');
            InsertClassEvent($TourId, 0, 1, 'LBHU21', 'LB', 'HU21');
            InsertClassEvent($TourId, 0, 1, 'THU21', 'T', 'HU21');
            InsertClassEvent($TourId, 0, 1, 'BHU21', 'B', 'HU21');
            InsertClassEvent($TourId, 0, 1, 'PUF', 'PU', 'F');
            InsertClassEvent($TourId, 0, 1, 'VIF', 'VI', 'F');
        }
		InsertClassEvent($TourId, 0, 1, 'CD',  'C', 'D');
		InsertClassEvent($TourId, 0, 1, 'RD',  'R', 'D');
		InsertClassEvent($TourId, 0, 1, 'LBHi',  'LB', 'Hi');
		InsertClassEvent($TourId, 0, 1, 'THi',  'T', 'Hi');
		InsertClassEvent($TourId, 0, 1, 'BHi',  'B', 'Hi');
		InsertClassEvent($TourId, 0, 1, 'CH',  'C', 'H');
		InsertClassEvent($TourId, 0, 1, 'RH',  'R', 'H');

		$team=array(
			'CR' => array('C' => array('RJ','RG')),
			'RR' => array('R' => array('RJ','RG')),
			'C' => array('C' => array('D','D5','DU21','H','H5','HU21','HU18','DU18')),
			);
		if($Outdoor) {
			$team=array(
				'LBR' => array('LB' => array('R')),
				'TR' => array('T' => array('R')),
				'BR' => array('B' => array('R')),
				'CR' => array('C' => array('RJ','RG')),
				'RR' => array('R' => array('RJ','RG')),
				'R60M' => array('R' => array('D5','H5','HU18','DU18')),
				'LB' => array('LB' => array('Hi','Di','K')),
				'T' => array('T' => array('Hi','Di','K')),
                'B' => array('B' => array('Hi','Di','K')),
				'C' => array('C' => array('D','D5','DU21','H','H5','HU21','HU18','DU18')),
				'R' => array('R' => array('D','DU21','H','HU21')),
				);
		} else {
            $team['LBR']= array('LB' => array('RJ','RG'));
            $team['TR']= array('T' => array('RJ','RG'));
			$team['BR']= array('B' => array('RJ','RG'));
            $team['LB'] = array('LB' => array('DU18','HU18','DU21','HU21','D','H'));
            $team['T'] = array('LB' => array('DU18','HU18','DU21','HU21','D','H'));
            $team['B'] = array('LB' => array('DU18','HU18','DU21','HU21','D','H'));
			$team['R'] = array('R' => array('D','D5','DU21','H','H5','HU21','HU18','DU18'));
		}
	} else {
// 		InsertClassEvent($TourId, 0, 1, $d.$c, $d, $c);
		InsertClassEvent($TourId, 0, 1, 'C5', 'C', '5');
		InsertClassEvent($TourId, 0, 1, 'R5', 'R', '5');
		InsertClassEvent($TourId, 0, 1, 'B5', 'B', '5');
        InsertClassEvent($TourId, 0, 1, 'T5', 'T', '5');
        InsertClassEvent($TourId, 0, 1, 'LB5', 'LB', '5');
		InsertClassEvent($TourId, 0, 1, 'C4', 'C', '4');
		InsertClassEvent($TourId, 0, 1, 'R4', 'R', '4');
		InsertClassEvent($TourId, 0, 1, 'B4', 'B', '4');
        InsertClassEvent($TourId, 0, 1, 'T4', 'T', '4');
        InsertClassEvent($TourId, 0, 1, 'LB4', 'LB', '4');
        InsertClassEvent($TourId, 0, 1, 'T3', 'T', '3');
        InsertClassEvent($TourId, 0, 1, 'LB3', 'LB', '3');
        InsertClassEvent($TourId, 0, 1, 'B3', 'B', '3');
        InsertClassEvent($TourId, 0, 1, 'C3', 'C', '3');
		InsertClassEvent($TourId, 0, 1, 'R3', 'R', '3');
        InsertClassEvent($TourId, 0, 1, 'B2', 'B', '2');
        InsertClassEvent($TourId, 0, 1, 'T2', 'T', '2');
        InsertClassEvent($TourId, 0, 1, 'LB2', 'LB', '2');
		InsertClassEvent($TourId, 0, 1, 'C2', 'C', '2');
		InsertClassEvent($TourId, 0, 1, 'R2', 'R', '2');
        InsertClassEvent($TourId, 0, 1, 'PU1', 'PU', '1');
        InsertClassEvent($TourId, 0, 1, 'B1', 'B', '1');
        InsertClassEvent($TourId, 0, 1, 'T1', 'T', '1');
        InsertClassEvent($TourId, 0, 1, 'LB1', 'LB', '1');
		InsertClassEvent($TourId, 0, 1, 'C1', 'C', '1');
		InsertClassEvent($TourId, 0, 1, 'R1', 'R', '1');
	}

	foreach($team as $n => $divs) {
		foreach($divs as $d => $cs) {
			foreach($cs as $c) {
				InsertClassEvent($TourId, 1, 3, $n, $d, $c);
			}
		}
	}
}

/*

FIELD ONLY THINGS

*/

function CreateStandardFieldEvents($TourId, $SubRule, $TourType=9) {
	$Elim1=array(
		'Archers' => 16,
		'Ends' => 12,
		'Arrows' => 3,
		'SO' => 1
	);
	$Elim2=array(
		'Archers' => 8,
		'Ends' => 8,
		'Arrows' => 3,
		'SO' => 1
	);
	$Target=($TourType==9 ? 6 : ($TourType==11 ? 8 : 11));
	// Individuals
	$i=1;
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BR',  'Barebow Rekrutt',            0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'IR',  'Tradisjonell Rekrutt',       0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LR',  'Langbue Rekrutt',            0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RRJ',  'Recurve Rekrutt Jenter',    0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CRJ',  'Compound Rekrutt Jenter',   0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RRG',  'Recurve Rekrutt Gutter',    0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CRG',  'Compound Rekrutt Gutter',   0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BU18',  'Barebow Under 18',         0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'TU18',  'Tradisjonell Under 18',    0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LBU18',  'Langbue Under 18',        0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RDU18',  'Recurve Damer Under 18',  0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CDU18',  'Compound Damer Under 18', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RHU18',  'Recurve Herrer Under 18', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CHU18',  'Compound Herrer Under 18',0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RDU21',  'Recurve Damer Under 21',  0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CDU21',  'Compound Damer Under 21', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RHU21',  'Recurve Herrer Under 21', 0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CHU21',  'Compound Herrer Under 21',0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BD',  'Barebow Damer',              0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'ID',  'Tradisjonell Damer',         0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LD',  'Langbue Damer',              0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RD',  'Recurve Damer',              0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CD',  'Compound Damer',             0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'BH',  'Barebow Herrer',             0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'IH',  'Tradisjonell Herrer',        0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'LH',  'Langbue Herrer',             0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RH',  'Recurve Herrer',             0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CH',  'Compound Herrer',            0, 240, 255);

	// Teams
	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 0, $Target, 8, 3, 3, 4, 3, 3, 'Lag',  'Lag', 0, 0, 0, 0, 0, '', '', 0, 0, '', 1);
}

function InsertStandardFieldEvents($TourId, $SubRule) {
	InsertClassEvent($TourId, 0, 1, 'BR', 'B', 'R');
	InsertClassEvent($TourId, 0, 1, 'IR', 'T', 'R');
	InsertClassEvent($TourId, 0, 1, 'LR', 'LB', 'R');
	InsertClassEvent($TourId, 0, 1, 'RRJ', 'R', 'RJ');
	InsertClassEvent($TourId, 0, 1, 'CRJ', 'C', 'RJ');
	InsertClassEvent($TourId, 0, 1, 'RRG', 'R', 'RG');
	InsertClassEvent($TourId, 0, 1, 'CRG', 'C', 'RG');
	InsertClassEvent($TourId, 0, 1, 'BU18', 'B', 'U18');
	InsertClassEvent($TourId, 0, 1, 'TU18', 'T', 'U18');
	InsertClassEvent($TourId, 0, 1, 'LBU18', 'LB', 'U18');
	InsertClassEvent($TourId, 0, 1, 'RDU18', 'R', 'DU18');
	InsertClassEvent($TourId, 0, 1, 'CDU18', 'C', 'DU18');
	InsertClassEvent($TourId, 0, 1, 'RHU18', 'R', 'HU18');
	InsertClassEvent($TourId, 0, 1, 'CHU18', 'C', 'HU18');
	InsertClassEvent($TourId, 0, 1, 'RDU21', 'R', 'DU21');
	InsertClassEvent($TourId, 0, 1, 'CDU21', 'C', 'DU21');
	InsertClassEvent($TourId, 0, 1, 'RHU21', 'R', 'HU21');
	InsertClassEvent($TourId, 0, 1, 'CHU21', 'C', 'HU21');
	InsertClassEvent($TourId, 0, 1, 'BD', 'B', 'Di');
	InsertClassEvent($TourId, 0, 1, 'ID', 'T', 'Di');
	InsertClassEvent($TourId, 0, 1, 'LD', 'LB', 'Di');
	InsertClassEvent($TourId, 0, 1, 'RD', 'R', 'D');
	InsertClassEvent($TourId, 0, 1, 'CD', 'C', 'D');
	InsertClassEvent($TourId, 0, 1, 'BH', 'B', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'IH', 'T', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'LH', 'LB', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'RH', 'R', 'H');
	InsertClassEvent($TourId, 0, 1, 'CH', 'C', 'H');

	foreach(array('R' => array('D','DU21','H','HU21', 'DU18', 'HU18'), 'C' => array('D','DU21','H','HU21', 'DU18', 'HU18')) as $d => $cs) {
		foreach($cs as $c) {
			InsertClassEvent($TourId, 1, 2, 'Lag', $d, $c);
		}
	}
	foreach(array('B' => array('U18', 'Di','Hi'), 'T' => array('U18','Di','Hi'), 'LB' => array('U18', 'Di','Hi'),) as $d => $cs) {
		foreach($cs as $c) {
			InsertClassEvent($TourId, 2, 1, 'Lag', $d, $c);
		}
	}
}

function CreateStandard3DEvents($TourId, $SubRule, $TourType=11) {
	$Elim1=array(
		'Archers' => 16,
		'Ends' => 12,
		'Arrows' => 3,
		'SO' => 1
	);
	$Elim2=array(
		'Archers' => 8,
		'Ends' => 12,
		'Arrows' => 1,
		'SO' => 1
	);
	$Target=8 ;
	// Individuals
	$i=1;
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'BR',  'Barebow Rekrutt',            0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'IR',  'Tradisjonell Rekrutt',         0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'LR',  'Langbue Rekrutt',            0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'RRJ',  'Recurve Rekrutt Jenter',    0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'CRJ',  'Compound Rekrutt Jenter',   0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'RRG',  'Recurve Rekrutt Gutter',    0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'CRG',  'Compound Rekrutt Gutter',   0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'BU18',  'Barebow Under 18',             0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'TU18',  'Tradisjonell Under 18',          0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'LBU18',  'Langbue Under 18',             0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'RDU18',  'Recurve Damer Under 18',     0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'CDU18',  'Compound Damer Under 18',    0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'RHU18',  'Recurve Herrer Under 18',     0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'CHU18',  'Compound Herrer Under 18',    0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'RDU21',  'Recurve Damer Under 21',      0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'CDU21',  'Compound Damer Under 21',     0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'RHU21',  'Recurve Herrer Under 21',     0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'CHU21',  'Compound Herrer Under 21',    0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'BD',  'Barebow Damer',              0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'ID',  'Tradisjonell Damer',           0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'LD',  'Langbue Damer',              0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'RD',  'Recurve Damer',              0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'CD',  'Compound Damer',             0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'BU',  'Buejegere',                  0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'BH',  'Barebow Herrer',             0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'IH',  'Tradisjonell Herrer',          0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'LH',  'Langbue Herrer',             0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'RH',  'Recurve Herrer',             0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'CH',  'Compound Herrer',            0, 240, 255, 0, $Elim2);

	// Teams
	$i=1;
	CreateEvent($TourId, $i++, 1, 0, 4, $Target, 8, 3, 3, 4, 3, 3, 'Lag',  'Lag', 0, 0, 0, 0, 0, '', '', 0, 0, '', 1);
}

function InsertStandard3DEvents($TourId, $SubRule) {
	InsertClassEvent($TourId, 0, 1, 'BR', 'B', 'R');
	InsertClassEvent($TourId, 0, 1, 'IR', 'T', 'R');
	InsertClassEvent($TourId, 0, 1, 'LR', 'LB', 'R');
	InsertClassEvent($TourId, 0, 1, 'RRJ', 'R', 'RJ');
	InsertClassEvent($TourId, 0, 1, 'CRJ', 'C', 'RJ');
	InsertClassEvent($TourId, 0, 1, 'RRG', 'R', 'RG');
	InsertClassEvent($TourId, 0, 1, 'CRG', 'C', 'RG');
	InsertClassEvent($TourId, 0, 1, 'BU18', 'B', 'U18');
	InsertClassEvent($TourId, 0, 1, 'TU18', 'T', 'U18');
    InsertClassEvent($TourId, 0, 1, 'LBU18', 'LB', 'U18');
	InsertClassEvent($TourId, 0, 1, 'RDU18', 'R', 'DU18');
	InsertClassEvent($TourId, 0, 1, 'CDU18', 'C', 'DU18');
	InsertClassEvent($TourId, 0, 1, 'RHU18', 'R', 'HU18');
	InsertClassEvent($TourId, 0, 1, 'CHU18', 'C', 'HU18');
	InsertClassEvent($TourId, 0, 1, 'RDU21', 'R', 'DU21');
	InsertClassEvent($TourId, 0, 1, 'CDU21', 'C', 'DU21');
	InsertClassEvent($TourId, 0, 1, 'RHU21', 'R', 'HU21');
	InsertClassEvent($TourId, 0, 1, 'CHU21', 'C', 'HU21');
	InsertClassEvent($TourId, 0, 1, 'BD', 'B', 'Di');
	InsertClassEvent($TourId, 0, 1, 'ID', 'T', 'Di');
	InsertClassEvent($TourId, 0, 1, 'LD', 'LB', 'Di');
	InsertClassEvent($TourId, 0, 1, 'RD', 'R', 'D');
	InsertClassEvent($TourId, 0, 1, 'CD', 'C', 'D');
	InsertClassEvent($TourId, 0, 1, 'BU', 'BU', 'BU');
	InsertClassEvent($TourId, 0, 1, 'BH', 'B', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'IH', 'T', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'LH', 'LB', 'Hi');
	InsertClassEvent($TourId, 0, 1, 'RH', 'R', 'H');
	InsertClassEvent($TourId, 0, 1, 'CH', 'C', 'H');

	foreach(array('R' => array('D','DU21','H','HU21', 'DU18', 'HU18'), 'C' => array('D','DU21','H','HU21', 'DU18', 'HU18')) as $d => $cs) {
		foreach($cs as $c) {
			InsertClassEvent($TourId, 1, 1, 'Lag', $d, $c);
		}
	}
	foreach(array('LB' => array('U18','U21','Di','Hi'), 'B' => array('U18','U21','Di','Hi'), 'T' => array('U18','U21','Di','Hi')) as $d => $cs) {
		foreach($cs as $c) {
			InsertClassEvent($TourId, 3, 2, 'Lag', $d, $c);
		}
	}
}
