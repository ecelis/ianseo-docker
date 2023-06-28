<?php
/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'NED';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=3, $SubRule=1) {
	$i=1;
	if($Type!=43) {
        CreateDivision($TourId, $i++, 'C', 'Compound');
        CreateDivision($TourId, $i++, 'R', 'Recurve');
        if ($Type != 1 and $Type != 2) {
            CreateDivision($TourId, $i++, 'B', 'Barebow');
        }
        if ($Type == '6' OR $Type == '7') {
            CreateDivision($TourId, $i++, 'L', 'Longbow', 1, 'L', 'L');
            CreateDivision($TourId, $i++, 'T', 'Traditioneel', 1, 'T', 'T');
        }
    } else {
        CreateDivision($TourId, $i++, 'T', 'Traditioneel');
    }
}

function CreateStandardClasses($TourId, $TourType=3, $SubRule=1) {
	$i=1;
	switch ($TourType) {
        case 1:
        case 2:
            CreateClass($TourId, $i++, 0, 17, 0, 'O18H', 'O18H', 'Onder 18 Heren');
            CreateClass($TourId, $i++, 0, 17, 1, 'O18D', 'O18D', 'Onder 18 Dames');
            CreateClass($TourId, $i++, 18, 20, 0, 'O21H', 'CH,O21H', 'Onder 21 Heren');
            CreateClass($TourId, $i++, 18, 20, 1, 'O21D', 'CD,O21D', 'Onder 21 Dames');
            CreateClass($TourId, $i++, 21, 49, 0,  'H', 'O18H,O21H,H,MH', 'Heren');
            CreateClass($TourId, $i++, 21, 49, 1,  'D', 'O18D,O21D,D,MD', 'Dames');
            CreateClass($TourId, $i++, 50, 99, 0, '50H', '50H', '50+ Heren');
            CreateClass($TourId, $i++, 50, 99, 1, '50D', '50D', '50+ Dames');
            break;
        case 3:
        case 37:
            switch ($SubRule) {
                case 1:
                    CreateClass($TourId, $i++, 0,  13, 0, 'O14J', 'O14J', 'Onder 14 Jongens');
                    CreateClass($TourId, $i++, 0,  13, 1, 'O14M', 'O14M', 'Onder 14 Meisjes');
                    CreateClass($TourId, $i++, 14, 17, 0, 'O18H', 'O14J,O18H', 'Onder 18 Heren');
                    CreateClass($TourId, $i++, 14, 17, 1, 'O18D', 'O14M,O18D', 'Onder 18 Dames');
                    CreateClass($TourId, $i++, 18, 20, 0, 'O21H', 'O14J,O18H,O21H', 'Onder 21 Heren');
                    CreateClass($TourId, $i++, 18, 20, 1, 'O21D', 'O14M,O18D,O21D', 'Onder 21 Dames');
                    CreateClass($TourId, $i++, 21, 49, 0,  'H', 'O14J,O18H,O21H,H,50H', 'Heren');
                    CreateClass($TourId, $i++, 21, 49, 1,  'D', 'O14M,O18D,O21D,D,50D', 'Dames');
                    CreateClass($TourId, $i++, 50, 99, 0, '50H', '50H', '50+ Heren');
                    CreateClass($TourId, $i++, 50, 99, 1, '50D', '50D', '50+ Dames');
                    break;
                case 2:
                case 3:
                    CreateClass($TourId, $i++, 0, 100, 0, 'H', 'H', 'Heren');
                    CreateClass($TourId, $i++, 0, 100, 1, 'D', 'D', 'Dames');

            }
            break;
        case 7:
            $SubRule=3;
        case 6:
        case 42:
            switch ($SubRule) {
                case 1:
                case 2:
                    $i=1;
                    CreateClass($TourId, $i++, 1, 11, 0, 'O12J',  'O12J', 'Onder 12 Jongens');
                    CreateClass($TourId, $i++, 1, 11, 1, 'O12M',  'O12M', 'Onder 12 Meisjes');
                    CreateClass($TourId, $i++, 12, 13, 0, 'O14J',  'O14J', 'Onder 14 Jongens');
                    CreateClass($TourId, $i++, 12, 13, 1, 'O14M',  'O14M', 'Onder 14 Meisjes');
                    CreateClass($TourId, $i++, 14, 17, -1, 'C1',  'C1', 'Onder 18 Klasse 1','1','R,C');
                    CreateClass($TourId, $i++, 14, 17, -1, 'C2',  'C2', 'Onder 18 Klasse 2','1','R,C');
                    CreateClass($TourId, $i++, 18, 20, -1, 'J1',  'J1', 'Onder 21 Klasse 1','1','R,C');
                    CreateClass($TourId, $i++, 18, 20, -1, 'J2',  'J2', 'Onder 21 Klasse 2','1','R,C');
                    CreateClass($TourId, $i++, 18, 20, -1, 'J',  'J', 'Onder 21 Klasse 1','1','B,L,T');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S1',  'S1', 'Senioren Klasse 1','1','R,C,B,L,T');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S2',  'S2', 'Senioren Klasse 2','1','R,C,B,L,T');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S3',  'S3', 'Senioren Klasse 3','1','R');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S4',  'S4', 'Senioren Klasse 4','1','R');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S5',  'S5', 'Senioren Klasse 5','1','R');
                    CreateClass($TourId, $i++, 21, 99, -1, 'S6',  'S6', 'Senioren Klasse 6','1','R');
                    break;
                case 3:
                    CreateClass($TourId, $i++, 1, 17, 0, 'O18H', 'O18H', 'Onder 18 Heren');
                    CreateClass($TourId, $i++, 1, 17, 1, 'O18D', 'O18D', 'Onder 18 Dames');
                    CreateClass($TourId, $i++, 18, 20, 0, 'O21H', 'O18H,O21H', 'Onder 21 Heren');
                    CreateClass($TourId, $i++, 18, 20, 1, 'O21D', 'O18D,O21D', 'Onder 21 Dames');
                    CreateClass($TourId, $i++, 21, 49, 0, 'H',  'O18H,O21H,H,50H', 'Heren');
                    CreateClass($TourId, $i++, 21, 49, 1, 'D',  'O18D,O21D,D,50D', 'Dames');
                    CreateClass($TourId, $i++, 50, 99, 0, '50H', '50H', '50+ Heren');
                    CreateClass($TourId, $i++, 50, 99, 1, '50D', '50D', '50+ Dames');
                    break;
                case 4:
                    CreateClass($TourId, $i++, 0, 100, 0, 'H', 'H', 'Heren');
                    CreateClass($TourId, $i++, 0, 100, 1, 'D', 'D', 'Dames');
            }
            break;
        case 41:
            CreateClass($TourId, $i++, 1, 11, 0, 'O12J',  'O12J', 'Onder 12 Jongens');
            CreateClass($TourId, $i++, 1, 11, 1, 'O12M',  'O12J', 'Onder 12 Meisjes');
            CreateClass($TourId, $i++, 12, 13, 0, 'O14J',  'O12J,O14J', 'Onder 14 Jongens');
            CreateClass($TourId, $i++, 12, 13, 1, 'O14M',  'O12M,O14M', 'Onder 14 Meisjes');
            CreateClass($TourId, $i++, 14, 17, 0, 'O18H', 'O12J,O14J,O18H', 'Onder 18 Heren');
            CreateClass($TourId, $i++, 14, 17, 1, 'O18D', 'O12M,O14M,O18D', 'Onder 18 Dames');
            CreateClass($TourId, $i++, 18, 20, 0, 'O21H', 'O12J,O14J,O18H,O21H', 'Onder 21 Heren');
            CreateClass($TourId, $i++, 18, 20, 1, 'O21D', 'O12M,O14M,O18D,O21D', 'Onder 21 Dames');
            break;
        case 43:
            CreateClass($TourId, $i++, 0, 100, 0, 'H', 'H', 'Heren');
            CreateClass($TourId, $i++, 0, 100, 1, 'D', 'D', 'Dames');
    }

}

function CreateStandardSubClasses($TourId, $TourType=3, $SubRule=1) {
    if(($TourType==6 or $TourType==42) and $SubRule<=2) {
        CreateSubClass($TourId, 1, 'E', 'In klasse eren teams');
        CreateSubClass($TourId, 2, 'A', 'In klasse A teams');
        CreateSubClass($TourId, 3, 'B', 'In klasse B teams');
        CreateSubClass($TourId, 4, 'C', 'In klasse C teams');
        CreateSubClass($TourId, 5, 'D', 'In klasse D teams');
    }
}

function CreateStandardEvents($TourId, $SubRule, $Outdoor=true) {
    $TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
	$TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
    $DistanceR_mc=($Outdoor ? 60 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);

    $i=1;
	if(($Outdoor AND $SubRule==1) OR (!$Outdoor AND $SubRule==3)) {
        CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'RH', 'Recurve Heren', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RD', 'Recurve Dames', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RO21H', 'Recurve Onder 21 Heren', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RU21M', 'RU21M', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RO21D', 'Recurve Onder 21 Dames', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RU21W', 'RU21W', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RO18H', 'Recurve Onder 18 Heren', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RU18M', 'RU18M', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RO18D', 'Recurve Onder 18 Dames', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RU18W', 'RU18W', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R50H', 'Recurve 50+ Heren', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'R50M', 'R50M', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'R50D', 'Recurve 50+ Dames', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'R50W', 'R50W', $TargetSizeR, $DistanceR_mc);

        CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CH', 'Compound Heren', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CD', 'Compound Dames', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CO21H', 'Compound Onder 21 Heren', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'CU21M', 'CU21M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CO21D', 'Compound Onder 21 Dames', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'CU21W', 'CU21W', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CO18H', 'Compound Onder 18 Heren', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'CU18M', 'CU18M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CO18D', 'Compound Onder 18 Dames', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'CU18C', 'CU18W', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C50H', 'Compound 50+ Heren', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'C50M', 'M50M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'C50D', 'Compound 50+ Dames', 0, FINAL_NO_ELIM, FINAL_NO_ELIM, 0, 0, 'C50W', 'C50W', $TargetSizeC, $DistanceC);

        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'BH', 'Heren Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BM', 'BM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'BD', 'Dames Barebow', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BW', 'BW', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'BO21H', 'Barebow Onder 21 Heren', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BU21M', 'BU21M', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'BO21D', 'Barebow Onder 21 Dames', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BU21W', 'BU21W', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'BO18H', 'Barebow Onder 18 Heren', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BU18M', 'BU18M', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'BO18D', 'Barebow Onder 21 Dames', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BU18W', 'BU18W', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 16, $TargetB, 5, 3, 1, 5, 3, 1, 'B50H', 'Barebow 50+ Heren', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'B50M', 'B50M', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'B50D', 'Barebow 50+ Dames', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'B50W', 'B50W', $TargetSizeB, $DistanceB);

        $i = 1;

        if ($Outdoor) {
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', 'Recurve Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RX', 'RX', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RO21X', 'Recurve Onder 21 Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RU21X', 'RU21X', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RO18X', 'Recurve Onder 18 Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RU18X', 'RU18X', $TargetSizeR, $DistanceR_mc);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'R50X', 'Recurve 50+ Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'R50X', 'R50X', $TargetSizeR, $DistanceR_mc);
        }
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RH', 'Heren Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RD', 'Dames Recurve', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RO21H', 'Recurve Onder 21 Heren', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RU21M', 'RU21M', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RO21D', 'Recurve Onder 21 Dames', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RU21W', 'RU21W', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RO18H', 'Recurve Onder 18 Heren', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RU18M', 'RU18M', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RO18D', 'Recurve Onder 18 Dames', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RU18W', 'RU18W', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'R50H', 'Recurve 50+ Heren', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'R50M', 'R50M', $TargetSizeR, $DistanceR_mc);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'R50D', 'Recurve 50+ Dames', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'R50M', 'R50M', $TargetSizeR, $DistanceR_mc);

        if ($Outdoor) {
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'CX', 'Compound Mixed Team', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CX', 'CX', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'CO21X', 'Compound Onder 21 Mixed Team', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CU21X', 'CU21X', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'CO18X', 'Compound Onder 18 Mixed Team', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CU18X', 'CU18X', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'C50X', 'Compound 50+ Mixed Team', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'C50X', 'C50X', $TargetSizeC, $DistanceC);
        }
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CH', 'Heren Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CD', 'Dames Compound', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CO21H', 'Compound Onder 21 Heren', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CU21M', 'CU21M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CO21D', 'Compound Onder 21 Dames', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CU21W', 'CU21W', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CO18H', 'Compound Onder 18 Heren', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CU18M', 'CU18M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CO18D', 'Compound Onder 18 Dames', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CU18W', 'CU18W', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'C50H', 'Compound 50+ Heren', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'C50M', 'C50M', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'C50D', 'Compound 50+ Dames', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'C50M', 'C50M', $TargetSizeC, $DistanceC);

        if ($Outdoor) {
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BX', 'BX', $TargetSizeB, $DistanceB);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetB, 4, 4, 2, 4, 4, 2, 'BO21X', 'Barebow Onder 21 Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BU21X', 'BU21X', $TargetSizeB, $DistanceB);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetB, 4, 4, 2, 4, 4, 2, 'BO18X', 'Barebow Onder 18 Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BU18X', 'BU18X', $TargetSizeB, $DistanceB);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetB, 4, 4, 2, 4, 4, 2, 'B50X', 'Barebow 50+ Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'B50X', 'B50X', $TargetSizeB, $DistanceB);
        }
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BH', 'Heren Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BM', 'BM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BD', 'Dames Barebow', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BW', 'BW', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BO21H', 'Barebow Onder 21 Heren', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BU21M', 'BU21M', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BO21D', 'Barebow Onder 21 Dames', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BU21W', 'BU21W', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BO18H', 'Barebow Onder 18 Heren', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BU18M', 'BU18M', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BO18D', 'Barebow Onder 18 Dames', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BU18W', 'BU18W', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'B50H', 'Barebow 50+ Heren', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'B50M', 'B50M', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'B50D', 'Barebow 50+ Dames', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'B50M', 'B50M', $TargetSizeB, $DistanceB);
    } else if(($Outdoor) OR (!$Outdoor AND $SubRule==4)) {
        CreateEvent($TourId, $i++, 0, 0,16, $TargetR, 5, 3, 1, 5, 3, 1, 'RH',  'Recurve Heren', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RD',  'Recurve Dames', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CH',  'Compound Heren', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CD',  'Compound Dames', 0, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0,16, $TargetB, 5, 3, 1, 5, 3, 1, 'BH',  'Barebow Heren', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BM', 'BM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, 8, $TargetB, 5, 3, 1, 5, 3, 1, 'BD',  'Barebow Dames', 1, FINAL_NO_ELIM, MATCH_SEP_FROM_4, 0, 0, 'BW', 'BW', $TargetSizeB, $DistanceB);
        $i=1;
        if($Outdoor) {
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', 'Recurve Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RX', 'RX', $TargetSizeR, $DistanceR);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'CX', 'Compound Mixed Team', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CX', 'CX', $TargetSizeC, $DistanceC);
            CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BX', 'BX', $TargetSizeB, $DistanceB);
        }
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RH',  'Recurve Heren', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RM', 'RM', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetR, 4, 6, 3, 4, 6, 3, 'RD',  'Recurve Dames', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'RW', 'RW', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CH',  'Compound Heren', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CM', 'CM', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetC, 4, 6, 3, 4, 6, 3, 'CD',  'Compound Dames', 0, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'CW', 'CW', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BH',  'Barebow Heren', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BM', 'BM', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BD',  'Barebow Dames', 1, FINAL_NO_ELIM, MATCH_ALL_SEP, 0, 0, 'BW', 'BW', $TargetSizeB, $DistanceB);
	}
}

function InsertStandardEvents($TourId, $SubRule, $Outdoor=true) {
    if(($Outdoor AND $SubRule==1) OR (!$Outdoor AND $SubRule==3)) {
        foreach (array('R','C','B') as $vDiv) {
            foreach(array('X'=>'D','O21X'=>'O21D','O18X'=>'O18D','50X'=>'50D') as $kCl=>$vCl) {
                InsertClassEvent($TourId, 0, 1, $vDiv.$vCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, 1, 3, $vDiv.$vCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, 1, 1, $vDiv.$kCl, $vDiv,  $vCl);
            }
            foreach(array('X'=>'H','O21X'=>'O21H','O18X'=>'O18H','50X'=>'50H') as $kCl=>$vCl) {
                InsertClassEvent($TourId, 0, 1, $vDiv.$vCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, 1, 3, $vDiv.$vCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, 2, 1, $vDiv.$kCl, $vDiv,  $vCl);
            }
        }
    } else if(($Outdoor) OR (!$Outdoor AND $SubRule==4)) {
        foreach (array('R','C','B') as $vDiv) {
            foreach(array('D','H') as $kCl=>$vCl) {
                InsertClassEvent($TourId, 0, 1, $vDiv.$vCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, 1, 3, $vDiv.$vCl, $vDiv,  $vCl);
                InsertClassEvent($TourId, ($kCl+1), 1, $vDiv.'X', $vDiv,  $vCl);
            }
        }
	}
}