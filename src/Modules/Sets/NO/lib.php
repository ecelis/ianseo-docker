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
        CreateDivision($TourId, $i++, 'OC', 'Open Class');
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
			CreateClass($TourId, $i++, 13, 15, 1, 'RJ', 'RJ,DU18,DU21,D', 'Damer U16', 1, 'C,R');
			CreateClass($TourId, $i++, 13, 15, 0, 'RG', 'RG,HU18,HU21,H', 'Herrer U16', 1, 'C,R');
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
                CreateClass($TourId, $i++, 13, 99, 0, 'F', 'F', 'Felles', 1, 'OC');
            }
		} else {
			// Outdoor and Indoor champs
            $Indoor = ($Type==6 OR $Type==7 OR $Type==8 OR $Type==22);
            CreateClass($TourId, $i++, 1, 15, 1, 'DU16', 'DU16,DU18,DU21,D', 'Damer Under 16', 1, 'C,R,B,LB,T');
            CreateClass($TourId, $i++, 1, 15, 0, 'HU16', 'HU16,HU18,HU21,H', 'Herrer Under 16', 1, 'C,R,B,LB,T');

            CreateClass($TourId, $i++, 16, 17, 1, 'DU18', 'DU18,DU21,D', 'Damer Under 18', 1, 'C,R,B,LB,T');
            CreateClass($TourId, $i++, 16, 17, 0, 'HU18', 'HU18,HU21,H', 'Herrer Under 18', 1, 'C,R,B,LB,T');
            CreateClass($TourId, $i++, 18, 20, 1, 'DU21', 'DU21,D', 'Damer Under 21', 1, 'C,R,B,LB,T');
            CreateClass($TourId, $i++, 18, 20, 0, 'HU21', 'HU21,H', 'Herrer Under 21', 1, 'C,R,B,LB,T');
            CreateClass($TourId, $i++, 50, 99, 1, 'D5', 'D5,D', 'Damer 50', 1, 'C,R,B,LB,T');
            CreateClass($TourId, $i++, 50, 99, 0, 'H5', 'H5,H', 'Herrer 50', 1, 'C,R,B,LB,T');
            CreateClass($TourId, $i++, 21, 49, 1, 'D', 'D', 'Damer', 1, 'C,R,B,LB,T');
            CreateClass($TourId, $i++, 21, 49, 0, 'H', 'H', 'Herrer', 1, 'C,R,B,LB,T');
            CreateClass($TourId, $i++, 1, 99, -1, 'VI', 'VI', 'Visually Impaired', 1, 'VI','','',true);
            CreateClass($TourId, $i++, 1, 99, -1, 'OC', 'OC', 'Open Class', 1, 'OC','','',true);
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
			CreateClass($TourId, $i++, 16, 100, -1, '1', '1', '1', 1, 'B,C,T,LB,R,OC' . ($Indoor ? ',VI' : '' ));
		}
	}
}

function CreateStandardSubClasses($TourId) {
	$i=1;
	CreateSubClass($TourId, $i++, '16', 'U16');
	CreateSubClass($TourId, $i++, '18', 'U18');
	CreateSubClass($TourId, $i++, '21', 'U21');
	CreateSubClass($TourId, $i++, 'Sr', 'Sr');
	CreateSubClass($TourId, $i++, '50', '50');
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
    $TargetF= ($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetRY=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);

	$SetC=($Outdoor?0:1);
	$Phase=16;
	$Champs=(($SubRule==3 and $TourType==6)
			or ($SubRule==4 and $TourType==3)
// 			or ($SubRule==9 and $Type==9)
// 			or ($SubRule==7 and $Type==11)
// 			or ($SubRule==9 and $Type==17)
			);


	$DistanceRW=($Outdoor ? 60 : 18);

	$DistanceB=($Outdoor ? 40 : 18);
    $DistanceB50=($Outdoor ? 50 : 18);
	$DistanceF=($Outdoor ? 25 : 18);
    $DistanceOCF=($Outdoor ? 20 : 12);


    $TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
    $TargetSizeR=($Outdoor ? 122 : 40);
    $TargetSizeR16=($Outdoor ? 122 : 60);
    $DistanceR=($Outdoor ? 70 : 18);
    $DistanceR50=($Outdoor ? 60 : 18);
    $DistanceR16=($Outdoor ? 40 : 18);
    $TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
    $TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeC16=($Outdoor ? 80 : 60);
    $DistanceC=($Outdoor ? 50 : 18);
    $DistanceC16=($Outdoor ? 30 : 18);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
    $TargetLBT=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeBLBT=($Outdoor ? 122 : 40);
    $TargetSizeBLBT16=($Outdoor ? 122 : 60);
    $DistanceB=($Outdoor ? 50 : 18);
    $DistanceB18=($Outdoor ? 40 : 18);
    $DistanceLBT=($Outdoor ? 30 : 18);
    $DistanceBLBT16=($Outdoor ? 25 : 18);

    $TargetVI=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeVI=($Outdoor ? 80 : 60);
    $DistanceVI=($Outdoor ? 30 : 18);
    $TargetOC=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeOC=($Outdoor ? 122 : 60);
    $DistanceOC=($Outdoor ? 20 : 12);

	if($Champs) {
        // only Indoor and 70m Round
        $i=1;
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'LD',  'Langbue Damer', 1, 240, 255, 0, 0, '', 'LW', $TargetSizeBLBT, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'TD',  'Tradisjonell Damer', 1, 240, 255, 0, 0, '', 'TW', $TargetSizeBLBT, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BD',  'Barebow Damer', 1, 240, 255, 0, 0, 'BW', 'BW', $TargetSizeBLBT, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CD',  'Compound Damer', 0, 240, 255, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RD',  'Recurve Damer', 1, 240, 255, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'LH',  'Langbue Herrer', 1, 240, 255, 0, 0, '', 'LM', $TargetSizeBLBT, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'TH',  'Tradisjonell Herrer', 1, 240, 255, 0, 0, '', 'TM', $TargetSizeBLBT, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BH',  'Barebow Herrer', 1, 240, 255, 0, 0, 'BM', 'BM', $TargetSizeBLBT, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CH',  'Compound Herrer', 0, 240, 255, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RH',  'Recurve Herrer', 1, 240, 255, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);

        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'LD21',  'Langbue Damer Under 21', 1, 240, 255, 0, 0, '', 'LU21W', $TargetSizeBLBT16, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'TD21',  'Tradisjonell Damer Under 21', 1, 240, 255, 0, 0, '', 'U21', $TargetSizeBLBT16, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BD21',  'Barebow Damer Under 21', 1, 240, 255, 0, 0, 'BU21W', 'BU21W', $TargetSizeBLBT, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CD21',  'Compound Damer Under 21', 0, 240, 255, 0, 0, 'CU21W', 'CU21W', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RD21',  'Recurve Damer Under 21', 1, 240, 255, 0, 0, 'RU21W', 'RU21W', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'LH21',  'Langbue Herrer Under 21', 1, 240, 255, 0, 0, '', 'LU21M', $TargetSizeBLBT16, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'TH21',  'Tradisjonell Herrer Under 21', 1, 240, 255, 0, 0, '', 'TU21M', $TargetSizeBLBT16, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BH21',  'Barebow Herrer Under 21', 1, 240, 255, 0, 0, 'BU21M', 'BU21M', $TargetSizeBLBT, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CH21',  'Compound Herrer Under 21', 0, 240, 255, 0, 0, 'CU21M', 'CU21M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RH21',  'Recurve Herrer Under 21', 1, 240, 255, 0, 0, 'RU21M', 'RU21M', $TargetSizeR, $DistanceR);

        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'LD18',  'Langbue Damer Under 18', 1, 240, 255, 0, 0, '', 'LU18W', $TargetSizeBLBT16, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'TD18',  'Tradisjonell Damer Under 18', 1, 240, 255, 0, 0, '', 'TU18W', $TargetSizeBLBT16, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BD18',  'Barebow Damer Under 18', 1, 240, 255, 0, 0, 'BU18W', 'BU18W', $TargetSizeBLBT, $DistanceB18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CD18',  'Compound Damer Under 18', 0, 240, 255, 0, 0, 'CU18W', 'CU18W', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RD18',  'Recurve Damer Under 18', 1, 240, 255, 0, 0, 'RU18W', 'RU18W', $TargetSizeR, $DistanceR50);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'LH18',  'Langbue Herrer Under 18', 1, 240, 255, 0, 0, '', 'LU18M', $TargetSizeBLBT16, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'TH18',  'Tradisjonell Herrer Under 18', 1, 240, 255, 0, 0, '', 'TU18M', $TargetSizeBLBT16, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BH18',  'Barebow Herrer Under 18', 1, 240, 255, 0, 0, 'BU18M', 'BU18M', $TargetSizeBLBT, $DistanceB18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CH18',  'Compound Herrer Under 18', 0, 240, 255, 0, 0, 'CU18M', 'CU18M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RH18',  'Recurve Herrer Under 18', 1, 240, 255, 0, 0, 'RU18M', 'RU18M', $TargetSizeR, $DistanceR50);

        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'LD16',  'Langbue Damer Under 16', 1, 240, 255, 0, 0, '', '', $TargetSizeBLBT16, $DistanceBLBT16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'TD16',  'Tradisjonell Damer Under 16', 1, 240, 255, 0, 0, '', '', $TargetSizeBLBT16, $DistanceBLBT16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BD16',  'Barebow Damer Under 16', 1, 240, 255, 0, 0, '', '', $TargetSizeBLBT16, $DistanceBLBT16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CD16',  'Compound Damer Under 16', 0, 240, 255, 0, 0, '', '', $TargetSizeC16, $DistanceC16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RD16',  'Recurve Damer Under 16', 1, 240, 255, 0, 0, '', '', $TargetSizeR16, $DistanceR16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'LH16',  'Langbue Herrer Under 16', 1, 240, 255, 0, 0, '', '', $TargetSizeBLBT16, $DistanceBLBT16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'TH16',  'Tradisjonell Herrer Under 16', 1, 240, 255, 0, 0, '', '', $TargetSizeBLBT16, $DistanceBLBT16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BH16',  'Barebow Herrer Under 16', 1, 240, 255, 0, 0, '', '', $TargetSizeBLBT16, $DistanceBLBT16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CH16',  'Compound Herrer Under 16', 0, 240, 255, 0, 0, '', '', $TargetSizeC16, $DistanceC16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RH16',  'Recurve Herrer Under 16', 1, 240, 255, 0, 0, '', '', $TargetSizeR16, $DistanceR16);

        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'LD5',  'Langbue Damer 50', 1, 240, 255, 0, 0, '', 'L50W', $TargetSizeBLBT, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'TD5',  'Tradisjonell Damer 50', 1, 240, 255, 0, 0, '', 'T50W', $TargetSizeBLBT, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BD5',  'Barebow Damer 50', 1, 240, 255, 0, 0, 'B50W', 'B50W', $TargetSizeBLBT, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CD5',  'Compound Damer 50', 0, 240, 255, 0, 0, 'C50W', 'C50W', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RD5',  'Recurve Damer 50', 1, 240, 255, 0, 0, 'R50W', 'R50W', $TargetSizeR, $DistanceR50);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'LH5',  'Langbue Herrer 50', 1, 240, 255, 0, 0, '', '50LM', $TargetSizeBLBT, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLBT, 5, 3, 1, 5, 3, 1, 'TH5',  'Tradisjonell Herrer 50', 1, 240, 255, 0, 0, '', 'T50M', $TargetSizeBLBT, $DistanceLBT);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BH5',  'Barebow Herrer 50', 1, 240, 255, 0, 0, 'B50M', 'B50M', $TargetSizeBLBT, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CH5',  'Compound Herrer 50', 0, 240, 255, 0, 0, 'C50M', 'C50M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RH5',  'Recurve Herrer 50', 1, 240, 255, 0, 0, 'R50M', 'R50M', $TargetSizeR, $DistanceR50);

        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetVI, 5, 3, 1, 5, 3, 1, 'VI', 'Visually Impaired', 1, 240, 255, 0, 0, '', '', $TargetSizeVI, $DistanceVI);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetOC, 5, 3, 1, 5, 3, 1, 'OC', 'Open Class', 1, 240, 255, 0, 0, '', '', $TargetSizeOC, $DistanceOC);

        //teams
        $Phase=4;
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetLBT, 4, 6, 3, 4, 6, 3, 'L',  'Langbue', 1, 0, 0, 0, 0, '', '', $TargetSizeBLBT, $DistanceLBT, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetLBT, 4, 6, 3, 4, 6, 3, 'T',  'Tradisjonell', 1, 0, 0, 0, 0, '', '', $TargetSizeBLBT, $DistanceLBT, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'B',  'Barebow', 1, 0, 0, 0, 0, '', '', $TargetSizeBLBT, $DistanceB, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'C',  'Compound',0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
        CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'R',  'Recurve',1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);

        if($Outdoor) {
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'R60M', 'Recurve 60m', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR50, '', 1);
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU18', 'Barebow Under 18', 1, 0, 0, 0, 0, '', '', $TargetSizeBLBT, $DistanceB18, '', 1);
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetLBT, 4, 6, 3, 4, 6, 3, 'LU16', 'Langbue Under 16', 1, 0, 0, 0, 0, '', '', $TargetSizeBLBT, $DistanceBLBT16, '', 1);
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetLBT, 4, 6, 3, 4, 6, 3, 'TU16', 'Tradisjonell Under 16', 1, 0, 0, 0, 0, '', '', $TargetSizeBLBT, $DistanceBLBT16, '', 1);
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU16', 'Barebow Under 16', 1, 0, 0, 0, 0, '', '', $TargetSizeBLBT, $DistanceBLBT16, '', 1);
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU16', 'Compound Under 16', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC16, '', 1);
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU16', 'Recurve Under 16', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR16, '', 1);
        } else {
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetLBT, 4, 6, 3, 4, 6, 3, 'L60',  'Langbue 60cm', 1, 0, 0, 0, 0, '', '', $TargetSizeBLBT16, $DistanceLBT, '', 1);
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetLBT, 4, 6, 3, 4, 6, 3, 'T60',  'Tradisjonell 60cm', 1, 0, 0, 0, 0, '', '', $TargetSizeBLBT16, $DistanceLBT, '', 1);
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'B60',  'Barebow 60cm', 1, 0, 0, 0, 0, '', '', $TargetSizeBLBT16, $DistanceB, '', 1);
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'C60',  'Compound 60cm',0, 0, 0, 0, 0, '', '', $TargetSizeC16, $DistanceC, '', 1);
            CreateEvent($TourId, $i++, 1, 0, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'R60',  'Recurve 60cm',1, 0, 0, 0, 0, '', '', $TargetSizeR16, $DistanceR, '', 1);

        }

	} elseif($SubRule=='2' or $SubRule=='4') {
		// always individual finals if subrule==2, but NO teams
		$i=1;
        $TargetSizeB=($Outdoor ? 122 : 60);
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

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'OC1', 'Open Class', 1, 240, 255, 0, 0, '', '', $TargetSizeR, 20);
        } else {
            $TargetSizeC = ($TourType == 7 ? 60 : 40);
            $TargetSizeB2 = ($TourType == 7 ? 80 : 40);
            $TargetSizeR = ($TourType == 7 ? 80 : 60);
            $DistanceF   = ($TourType == 7 ? 16 : 12);
            $DistanceR   = ($TourType == 7 ? 25 : 18);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'T5', 'Tradisjonell 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'T4', 'Tradisjonell 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'T3', 'Tradisjonell 3', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'T2', 'Tradisjonell 2', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'T1', 'Tradisjonell 1', 1, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'LB5', 'Langbue 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'LB4', 'Langbue 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'LB3', 'Langbue 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'LB2', 'Langbue 2', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetRY, 5, 3, 1, 5, 3, 1, 'LB1', 'Langbue 1', 1, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B5', 'Barebow 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B4', 'Barebow 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B3', 'Barebow 3', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B2', 'Barebow 2', 1, 240, 255, 0, 0, '', '', $TargetSizeB2, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'B1', 'Barebow 1', 1, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C5', 'Compound 5', 0, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C4', 'Compound 4', 0, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C3', 'Compound 3', 0, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C2', 'Compound 2', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C1', 'Compound 1', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R5', 'Recurve 5', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R4', 'Recurve 4', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R3', 'Recurve 3', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R2', 'Recurve 2', 1, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);
            CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R1', 'Recurve 1', 1, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceR);

            CreateEvent($TourId, $i++, 0, 0, 8, $TargetF, 5, 3, 1, 5, 3, 1, 'OC1', 'Open Class', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceF);
        }
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$inds=array();
	$team=array();
	$Champs=(($SubRule==3 and $TourType==6) or ($SubRule==4 and $TourType==3));

	if($Champs) {
        // 70m Round and Indoor Champs
        foreach (array('R'=>'R','C'=>'C','B'=>'B','L'=>'LB','T'=>'T') as $kDiv=>$vDiv) {
            $clsTmpArr = array(
                'D'=>'D','D21'=>'DU21','D18'=>'DU18','D16'=>'DU16','D5'=>'D5',
                'H'=>'H','H21'=>'HU21','H18'=>'HU18','H16'=>'HU16','H5'=>'H5'
            );
            foreach($clsTmpArr as $kClass=>$vClass) {
                InsertClassEvent($TourId, 0, 1, $kDiv.$kClass, $vDiv, $vClass);
            }
        }
        InsertClassEvent($TourId, 0, 1, 'VI', 'VI', 'VI');
        InsertClassEvent($TourId, 0, 1, 'OC', 'OC', 'OC');

        $team=array();
        if($Outdoor) {
            $team = array(
                'R' => array('R' => array('D', 'DU21', 'H', 'HU21')),
                'C' => array('C' => array('D', 'D5', 'DU21', 'DU18', 'H', 'H5', 'HU21', 'HU18')),
                'B' => array('B' => array('D', 'D5', 'DU21', 'H', 'H5', 'HU21')),
                'T' => array('T' => array('D', 'D5', 'DU21', 'DU18', 'H', 'H5', 'HU21', 'HU18')),
                'L' => array('LB' => array('D', 'D5', 'DU21', 'DU18', 'H', 'H5', 'HU21', 'HU18')),
                'R60M' => array('C' => array('D5', 'DU18', 'H5', 'HU18')),
                'BU18' => array('B' => array('DU18', 'HU18')),
                'LU16' => array('LB' => array('DU16', 'HU16')),
                'TU16' => array('T' => array('DU16', 'HU16')),
                'BU16' => array('B' => array('DU16', 'HU16')),
                'CU16' => array('C' => array('DU16', 'HU16')),
                'RU16' => array('R' => array('DU16', 'HU16')),
            );
        } else {
            $team = array(
                'R' => array('R' => array('D', 'DU21', 'H', 'HU21', 'D5', 'DU18', 'H5', 'HU18')),
                'R60' => array('R' => array('DU16', 'HU16')),
                'C' => array('C' => array('D', 'D5', 'DU21', 'DU18', 'H', 'H5', 'HU21', 'HU18')),
                'C60' => array('C' => array('DU16', 'HU16')),
                'B' => array('B' => array('D', 'D5', 'DU21', 'DU18', 'H', 'H5', 'HU21', 'HU18')),
                'B60' => array('B' => array('DU16', 'HU16')),
                'T' => array('T' => array('D', 'D5', 'H', 'H5')),
                'T60' => array('T' => array('DU21', 'DU18', 'HU21', 'HU18', 'DU16', 'HU16')),
                'L' => array('LB' => array('D', 'D5', 'H', 'H5')),
                'L60' => array('LB' => array('DU21', 'DU18', 'HU21', 'HU18', 'DU16', 'HU16')),
            );
        }
        foreach($team as $n => $divs) {
            foreach($divs as $d => $cs) {
                foreach($cs as $c) {
                    InsertClassEvent($TourId, 1, 3, $n, $d, $c);
                }
            }
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
        InsertClassEvent($TourId, 0, 1, 'OC1', 'OC', '1');
        InsertClassEvent($TourId, 0, 1, 'B1', 'B', '1');
        InsertClassEvent($TourId, 0, 1, 'T1', 'T', '1');
        InsertClassEvent($TourId, 0, 1, 'LB1', 'LB', '1');
		InsertClassEvent($TourId, 0, 1, 'C1', 'C', '1');
		InsertClassEvent($TourId, 0, 1, 'R1', 'R', '1');
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
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RRJ',  'Recurve Damer U16',    0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CRJ',  'Compound Damer U16',   0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'RRG',  'Recurve Herrer U16',    0, 240, 255);
	CreateEvent($TourId, $i++, 0, 0, 0, $Target, 5, 3, 1, 5, 3, 1, 'CRG',  'Compound Herrer U16',   0, 240, 255);
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
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'RRJ',  'Recurve Damer U16',    0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'CRJ',  'Compound Damer U16',   0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'RRG',  'Recurve Herrer U16',    0, 240, 255, 0, $Elim2);
	CreateEvent($TourId, $i++, 0, 0, 2, $Target, 5, 3, 1, 5, 3, 1, 'CRG',  'Compound Herrer U16',   0, 240, 255, 0, $Elim2);
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
