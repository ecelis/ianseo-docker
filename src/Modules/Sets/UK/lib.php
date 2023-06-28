<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'UK';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type, $SubRule) {
	$i=1;
    $optionDivs = array(
        'R'=>'Recurve',
        'C'=>'Compound',
        'B'=>'Barebow',
        'L'=>'Longbow',
    );
    if ($Type == 21) {
        $optionDivs = array('C' => 'Compound','R' => 'Recurve');
    } else if (($Type!=40) && ($SubRule == 1)) {
        $optionDivs = array('R'=>'Recurve/Barebow','C'=>'Compound','L'=>'Longbow');
    }

    foreach ($optionDivs as $k => $v){
        CreateDivision($TourId, $i++, $k, $v);
    }

}

function CreateStandardClasses($TourId, $SubRule,$TourType) {
    $i=1;
	switch($TourType) {
        case 40:
            CreateClass($TourId, $i++, 21, 110, 0, 'M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men');
            CreateClass($TourId, $i++, 21, 110, 1, 'W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women');
            CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U21');
            CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U21');
            CreateClass($TourId, $i++, 16, 17, 0, 'U18M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U18');
            CreateClass($TourId, $i++, 16, 17, 1, 'U18W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U18');
            CreateClass($TourId, $i++, 14, 15, 0, 'U16M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U16');
            CreateClass($TourId, $i++, 14, 15, 1, 'U16W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U16');
            CreateClass($TourId, $i++, 14, 14, 0, 'U15M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U15');
            CreateClass($TourId, $i++, 14, 14, 1, 'U15W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U15');
            CreateClass($TourId, $i++, 12, 13, 0, 'U14M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U14');
            CreateClass($TourId, $i++, 12, 13, 1, 'U14W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U14');
            CreateClass($TourId, $i++, 1, 12, 0, 'U12M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U12');
            CreateClass($TourId, $i++, 1, 12, 1, 'U12W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U12');
            CreateClass($TourId, $i++, 50, 110, 0, '50M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', '50+ Men');
            CreateClass($TourId, $i++, 50, 110, 1, '50W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', '50+ Women');
            break;
        default:
            switch ($SubRule) {
                case '1': // National Championships - F2F
                    CreateClass($TourId, $i++, 1, 99, 0, 'M', 'M', 'Men');
                    CreateClass($TourId, $i++, 1, 99, 1, 'W', 'W', 'Women');
                    break;
                case '2': // Junior National Championships
                    CreateClass($TourId, $i++, 17, 18, 0, 'S1', 'S1', 'Section 1 - Junior Men U18');
                    CreateClass($TourId, $i++, 17, 18, 1, 'S2', 'S2', 'Section 2 - Junior Women U18');
                    CreateClass($TourId, $i++, 15, 16, 0, 'S3', 'S3', 'Section 3 - Junior Men U16');
                    CreateClass($TourId, $i++, 15, 16, 1, 'S4', 'S4', 'Section 4 - Junior Women U16');
                    CreateClass($TourId, $i++, 13, 14, 0, 'S5', 'S5', 'Section 5 - Junior Men U14');
                    CreateClass($TourId, $i++, 13, 14, 1, 'S6', 'S6', 'Section 6 - Junior Women U14');
                    CreateClass($TourId, $i++, 1, 12, 0, 'S7', 'S7', 'Section 7 - Junior Men U12');
                    CreateClass($TourId, $i++, 1, 12, 1, 'S8', 'S8', 'Section 8 - Junior Women U12');
                    break;
                case 3:
                    CreateClass($TourId, $i++, 21, 110, 0, 'M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men');
                    CreateClass($TourId, $i++, 21, 110, 1, 'W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women');
                    CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U21');
                    CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U21');
                    CreateClass($TourId, $i++, 16, 17, 0, 'U18M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U18');
                    CreateClass($TourId, $i++, 16, 17, 1, 'U18W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U18');
                    CreateClass($TourId, $i++, 14, 15, 0, 'U16M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U16');
                    CreateClass($TourId, $i++, 14, 15, 1, 'U16W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U16');
                    CreateClass($TourId, $i++, 14, 14, 0, 'U15M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U15');
                    CreateClass($TourId, $i++, 14, 14, 1, 'U15W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U15');
                    CreateClass($TourId, $i++, 12, 13, 0, 'U14M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U14');
                    CreateClass($TourId, $i++, 12, 13, 1, 'U14W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U14');
                    CreateClass($TourId, $i++, 1, 12, 0, 'U12M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', 'Men U12');
                    CreateClass($TourId, $i++, 1, 12, 1, 'U12W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', 'Women U12');
                    CreateClass($TourId, $i++, 50, 110, 0, '50M', 'U21M,U18M,U16M,U15M,U14M,U12M,50M,M', '50+ Men');
                    CreateClass($TourId, $i++, 50, 110, 1, '50W', 'U21W,U18W,U16W,U15W,U14W,U12W,50W,W', '50+ Women');
                    break;

            }
            break;
	}

}


function CreateStandardEvents($TourId, $SubRule, $Outdoor=true,$TourType) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
	$SetC=($Outdoor?0:1);
	switch($TourType) {
        case 40:
            switch ($SubRule) {
                case 1:
                    $M = "York";
                    $W = "Hereford";
                    $B1 = "Bristol 1";
                    $B2 = "Bristol 2";
                    $B3 = "Bristol 3";
                    $B4 = "Bristol 4";
                    $B5 = "Bristol 5";
                    break;
                case 2:
                    $M = "St George";
                    $W = "Albion";
                    $B1 = "Albion";
                    $B2 = "Windsor";
                    $B3 = "Short Windsor";
                    $B4 = "Junior Windsor";
                    $B5 = "Short Junior Windsor";
                    break;
                case 3:
                    $M = "American";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 4:
                    $M = "New National";
                    $W = "Long National";
                    $B1 = "Long National";
                    $B2 = "National";
                    $B3 = "Short National";
                    $B4 = "Junior National";
                    $B5 = "Short Junior National";
                    break;
                case 5:
                    $M = "New Western";
                    $W = "Long Western";
                    $B1 = "Long Western";
                    $B2 = "Western";
                    $B3 = "Short Western";
                    $B4 = "Junior Western";
                    $B5 = "Short Junior Western";
                    break;
                case 6:
                    $M = "New Warwick";
                    $W = "Long Warwick";
                    $B1 = "Long Warwick";
                    $B2 = "Warwick";
                    $B3 = "Short Warwick";
                    $B4 = "Junior Warwick";
                    $B5 = "Short Junior Warwick";
                    break;
                case 7:
                    $M = "St Nicholas";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 8:
                    $M = "ontarget";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 9:
                    $M = "Short Metric";
                    $W = "Short Metric";
                    $B1 = "Short Metric 1";
                    $B2 = "Short Metric 2";
                    $B3 = "Short Metric 3";
                    $B4 = "Short Metric 4";
                    $B5 = "Short Metric 5";
                    break;
                case 10:
                    $M = "Long Metric";
                    $W = "Long Metric";
                    $B1 = "Long Metric 1";
                    $B2 = "Long Metric 2";
                    $B3 = "Long Metric 3";
                    $B4 = "Long Metric 4";
                    $B5 = "Long Metric 5";
                    break;
                case 11:
                    $M = "Worcester";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 12:
                    $M = "Bray 1";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 13:
                    $M = "Bray 2";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 14:
                    $M = "Stafford";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
                case 15:
                    $M = "Portsmouth";
                    $W = $M;
                    $B1 = $M;
                    $B2 = $M;
                    $B3 = $M;
                    $B4 = $M;
                    $B5 = $M;
                    break;
            }
            $i = 1;
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', $M . ' Recurve Men', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', $M . ' Compound Men', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM', $M . ' Longbow Men', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM', $M . ' Barebow Men', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', $W . ' Recurve Women', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', $W . ' Compound Women', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW', $W . ' Longbow Women', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW', $W . ' Barebow Women', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21M', $M . ' Men Recurve Under 21', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21M', $M . ' Men Compound Under 21', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU21M', $M . ' Men Longbow Under 21', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU21M', $M . ' Men Barebow Under 21', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21W', $W . ' Women Recurve Under 21', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21W', $W . ' Women Compound Under 21', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU21W', $W . ' Women Longbow Under 21', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU21W', $W . ' Women Barebow Under 21', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18M', $B1 . ' Men Recurve Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18M', $B1 . ' Men Compound Under 18', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU18M', $B1 . ' Men Longbow Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU18M', $B1 . ' Men Barebow Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18W', $B2 . ' Women Recurve Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18W', $B2 . ' Women Compound Under 18', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU18W', $B2 . ' Women Longbow Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU18W', $B2 . ' Women Barebow Under 18', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU16M', $B2 . ' Men Recurve Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU16M', $B2 . ' Men Compound Under 16', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU16M', $B2 . ' Men Longbow Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU16M', $B2 . ' Men Barebow Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU16W', $B3 . ' Women Recurve Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU16W', $B3 . ' Women Compound Under 16', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU16W', $B3 . ' Women Longbow Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU16W', $B3 . ' Women Barebow Under 16', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU15M', $B3 . ' Men Recurve Under 15', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU15M', $B3 . ' Men Compound Under 15', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU15M', $B3 . ' Men Longbow Under 15', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU15M', $B3 . ' Men Barebow Under 15', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU15W', $B3 . ' Women Recurve Under 15', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU15W', $B3 . ' Women Compound Under 15', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU15W', $B3 . ' Women Longbow Under 15', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU15W', $B3 . ' Women Barebow Under 15', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU14M', $B4 . ' Men Recurve Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU14M', $B4 . ' Men Compound Under 14', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU14M', $B4 . ' Men Longbow Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU14M', $B4 . ' Men Barebow Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU14W', $B4 . ' Women Recurve Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU14W', $B4 . ' Women Compound Under 14', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU14W', $B4 . ' Women Longbow Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU14W', $B4 . ' Women Barebow Under 14', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU12M', $B5 . ' Men Recurve Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU12M', $B5 . ' Men Compound Under 12', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU12M', $B5 . ' Men Longbow Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU12M', $B5 . ' Men Barebow Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU12W', $B5 . ' Women Recurve Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU12W', $B5 . ' Women Compound Under 12', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU12W', $B5 . ' Women Longbow Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU12W', $B5 . ' Women Barebow Under 12', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'R50M', $W . ' Recurve Men 50+', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'C50M', $W . ' Compound Men 50+', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'L50M', $W . ' Longbow Men 50+', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'B50M', $W . ' Barebow Men 50+', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'R50W', $B2 . ' Recurve Women 50+', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'C50W', $B2 . ' Compound Women 50+', $SetC, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'L50W', $B2 . ' Longbow Women 50+', 1, 240);
            CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'B50W', $B2 . ' Barebow Women 50+', 1, 240);
            break;
        default:
            switch ($SubRule) {
                case 1:// National Championships
                    $i = 1;
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', 'Men Recurve', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', 'Women Recurve', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', 'Men Compound', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', 'Women Compound', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'LM', 'Men Longbow', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'LW', 'Women Longbow', 1, 240);
                    break;
                case 2:
                    break;
                case 3:
                    if ($TourType == 1) {
                        $appAdult='1440';
                        $app1 = 'Metric 1';
                        $app2 = 'Metric 2';
                        $app3 = 'Metric 3';
                        $app4 = 'Metric 4';
                        $app5 = 'Metric 5';
                    }
                    elseif ($TourType == 2) {
                        $appAdult = 'Double 1440';
                        $app1 = 'Double Metric 1';
                        $app2 = 'Double Metric 2';
                        $app3 = 'Double Metric 3';
                        $app4 = 'Double Metric 4';
                        $app5 = 'Double Metric 5';
                    }
                    else {
                        $app1 = '';
                        $app2 = '';
                        $app3 = '';
                        $app4 = '';
                        $app5 = '';
                        $appAdult = '';

                    }

                    $i=1;
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', $appAdult.' Recurve Men', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', $appAdult.' Recurve Women', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', $appAdult.' Compound Men', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', $appAdult.' Compound Women', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LM', $appAdult.' Longbow Men', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LW', $appAdult.' Longbow Women', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BM', $appAdult.' Barebow Men', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BW', $appAdult.' Barebow Women', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21M', $appAdult.' Men Recurve Under 21', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21M', $appAdult.' Men Compound Under 21', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU21M', $appAdult.' Men Longbow Under 21', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU21M', $appAdult.' Men Barebow Under 21', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21W', $appAdult.' Women Recurve Under 21', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21W', $appAdult.' Women Compound Under 21', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU21W', $appAdult.' Women Longbow Under 21', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU21W', $appAdult.' Women Barebow Under 21', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18M', $app1.' Men Recurve Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18M', $app1.' Men Compound Under 18', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU18M', $app1.' Men Longbow Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU18M', $app1.' Men Barebow Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18W', $app2.' Women Recurve Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18W', $app2.' Women Compound Under 18', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU18W', $app2.' Women Longbow Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU18W', $app2.' Women Barebow Under 18', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU16M', $app2.' Men Recurve Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU16M', $app2.' Men Compound Under 16', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU16M', $app2.' Men Longbow Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU16M', $app2.' Men Barebow Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU16W', $app3.' Women Recurve Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU16W', $app3.' Women Compound Under 16', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU16W', $app3.' Women Longbow Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU16W', $app3.' Women Barebow Under 16', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU15M', $app3.' Men Recurve Under 15', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU15M', $app3.' Men Compound Under 15', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU15M', $app3.' Men Longbow Under 15', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU15M', $app3.' Men Barebow Under 15', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU15W', $app3.' Women Recurve Under 15', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU15W', $app3.' Women Compound Under 15', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU15W', $app3.' Women Longbow Under 15', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU15W', $app3.' Women Barebow Under 15', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU14M', $app4. ' Men Recurve Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU14M', $app4. ' Men Compound Under 14', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU14M', $app4. ' Men Longbow Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU14M', $app4. ' Men Barebow Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU14W', $app4. ' Women Recurve Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU14W', $app4. ' Women Compound Under 14', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU14W', $app4. ' Women Longbow Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU14W', $app4. ' Women Barebow Under 14', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU12M', $app5. ' Men Recurve Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU12M', $app5. ' Men Compound Under 12', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU12M', $app5. ' Men Longbow Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU12M', $app5. ' Men Barebow Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RU12W', $app5. ' Women Recurve Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CU12W', $app5. ' Women Compound Under 12', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'LU12W', $app5. ' Women Longbow Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'BU12W', $app5. ' Women Barebow Under 12', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'R50M', $appAdult . ' Recurve Men 50+', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'C50M', $appAdult . ' Compound Men 50+', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'L50M', $appAdult . ' Longbow Men 50+', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'B50M', $appAdult . ' Barebow Men 50+', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'R50W', $appAdult . ' Recurve Women 50+', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'C50W', $appAdult . ' Compound Women 50+', $SetC, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'L50W', $appAdult . ' Longbow Women 50+', 1, 240);
                    CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'B50W', $appAdult . ' Barebow Women 50+', 1, 240);
                    break;

            }
            break;

            }
}

function InsertStandardEvents($TourId, $SubRule,$TourType){

    switch ($TourType) {
        case 40:
            EventInserts($TourId);
            break;
        default:
            switch($SubRule){
                case 3:
                   EventInserts($TourId);
                break;


            }
    }
}

function EventInserts($TourId){
    foreach (array('R' => 'R', 'C' => 'C', 'B' => 'B', 'L' => 'L') as $kDiv => $vDiv) {
        $clsTmpArr = array('W', 'U21W', 'U18W', 'U16W', 'U15W','U14W','U12W','50W');

        foreach ($clsTmpArr as $kClass => $vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv . $vClass, $kDiv, $vClass);
        }
        $clsTmpArr = array('M', 'U21M', 'U18M', 'U16M', 'U15M', 'U14M','U12M','50M');
        foreach ($clsTmpArr as $kClass => $vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv . $vClass, $kDiv, $vClass);

        }
    }

}

/*

FIELD DEFINITIONS (Target Tournaments)

*/

function CreateStandardFieldClasses($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			CreateClass($TourId, 1, 21, 49, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21, 49, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 18, 20, 0, 'JM', 'JM,M', 'Junior Men');
			CreateClass($TourId, 4, 18, 20, 1, 'JW', 'JW,W', 'Junior Women');
			CreateClass($TourId, 5,  1, 17, 0, 'CM', 'CM,JM,M', 'Cadet Men');
			CreateClass($TourId, 6,  1, 17, 1, 'CW', 'CW,JW,W', 'Cadet Women');
			CreateClass($TourId, 7, 50,100, 0, 'MM', 'MM,M', 'Master Men');
			CreateClass($TourId, 8, 50,100, 1, 'MW', 'MW,W', 'Master Women');
			break;
		case '2':
			CreateClass($TourId, 1, 21,100, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21,100, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 1, 20, 0, 'JM', 'JM,M', 'Junior Men');
			CreateClass($TourId, 4, 1, 20, 1, 'JW', 'JW,W', 'Junior Women');
			break;
	}
}

function CreateStandardFieldEvents($TourId, $SubRule) {
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
	switch($SubRule) {
		case '1':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RM',  'Recurve Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RW',  'Recurve Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJM', 'Recurve Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJW', 'Recurve Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RCM', 'Recurve Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RCW', 'Recurve Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RMM', 'Recurve Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RMW', 'Recurve Master Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJM', 'Compound Junior Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJW', 'Compound Junior Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CCM', 'Compound Cadet Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CCW', 'Compound Cadet Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CMM', 'Compound Master Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CMW', 'Compound Master Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJM', 'Barebow Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJW', 'Barebow Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BCM', 'Barebow Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BCW', 'Barebow Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BMM', 'Barebow Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BMW', 'Barebow Master Women',  0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MJT',  'Men Junior Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WJT',  'Women Junior Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MCT',  'Men Cadet Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WCT',  'Women Cadet Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MMT',  'Men Master Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WMT',  'Women Master Team',0,248,15);
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RM',  'Recurve Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RW',  'Recurve Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJM', 'Recurve Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'RJW', 'Recurve Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJM', 'Compound Junior Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'CJW', 'Compound Junior Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJM', 'Barebow Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 6, 12, 3, 3, 4, 3, 3, 'BJW', 'Barebow Junior Women',  0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'MJT',  'Men Junior Team',0,248,15);
			CreateEvent($TourId, $i++, 1, 0, 4, 6, 8, 3, 3, 4, 3, 3, 'WJT',  'Women Junior Team',0,248,15);
			break;
	}
}

function InsertStandardFieldEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RCM', 'R', 'CM');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'RCW', 'R', 'CW');
			InsertClassEvent($TourId, 0, 1, 'RMW', 'R', 'MW');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
			InsertClassEvent($TourId, 0, 1, 'BCM', 'B', 'CM');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'MM');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');
			InsertClassEvent($TourId, 0, 1, 'BCW', 'B', 'CW');
			InsertClassEvent($TourId, 0, 1, 'BMW', 'B', 'MW');

			InsertClassEvent($TourId, 1, 1, 'MT',  'R',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 1, 1, 'MJT', 'R', 'JM');
			InsertClassEvent($TourId, 2, 1, 'MJT', 'C', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'B', 'JM');
			InsertClassEvent($TourId, 1, 1, 'MCT', 'R', 'CM');
			InsertClassEvent($TourId, 2, 1, 'MCT', 'C', 'CM');
			InsertClassEvent($TourId, 3, 1, 'MCT', 'B', 'CM');
			InsertClassEvent($TourId, 1, 1, 'MMT', 'R', 'MM');
			InsertClassEvent($TourId, 2, 1, 'MMT', 'C', 'MM');
			InsertClassEvent($TourId, 3, 1, 'MMT', 'B', 'MM');
			InsertClassEvent($TourId, 1, 1, 'WT',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 1, 1, 'WJT', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'WJT', 'C', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'B', 'JW');
			InsertClassEvent($TourId, 1, 1, 'WCT', 'R', 'CW');
			InsertClassEvent($TourId, 2, 1, 'WCT', 'C', 'CW');
			InsertClassEvent($TourId, 3, 1, 'WCT', 'B', 'CW');
			InsertClassEvent($TourId, 1, 1, 'WMT', 'R', 'MW');
			InsertClassEvent($TourId, 2, 1, 'WMT', 'C', 'MW');
			InsertClassEvent($TourId, 3, 1, 'WMT', 'B', 'MW');
			break;
		case '2':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RJM', 'R', 'JM');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'RJW', 'R', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');

			InsertClassEvent($TourId, 1, 1, 'MT',  'R',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 1, 1, 'MJT', 'R', 'JM');
			InsertClassEvent($TourId, 2, 1, 'MJT', 'C', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'B', 'JM');
			InsertClassEvent($TourId, 1, 1, 'WT',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 1, 1, 'WJT', 'R', 'JW');
			InsertClassEvent($TourId, 2, 1, 'WJT', 'C', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'B', 'JW');
			break;
	}
}

function InsertStandardFieldEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
			$cls=array('M', 'W', 'JM', 'JW', 'CM', 'CW', 'MM', 'MW');
			break;
		case '2':
			$cls=array('M', 'W', 'JM', 'JW');
			break;
	}
	foreach(array('R', 'C', 'B') as $div) {
		foreach($cls as $cl) {
			for($n=1; $n<=16; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=0, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
			for($n=1; $n<=8; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
		}
	}
}

/*

3D DEFINITIONS (Target Tournaments)

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
	switch($SubRule) {
		case '1':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CJM', 'Compound Junior Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CJW', 'Compound Junior Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CCM', 'Compound Cadet Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CCW', 'Compound Cadet Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CMM', 'Compound Master Men',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CMW', 'Compound Master Women', 0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BJM', 'Barebow Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BJW', 'Barebow Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BCM', 'Barebow Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BCW', 'Barebow Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BMM', 'Barebow Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BMW', 'Barebow Master Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LM',  'Longbow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LW',  'Longbow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LJM', 'Longbow Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LJW', 'Longbow Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LCM', 'Longbow Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LCW', 'Longbow Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LMM', 'Longbow Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LMW', 'Longbow Master Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IM',  'Instinctive Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IW',  'Instinctive Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IJM', 'Instinctive Junior Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IJW', 'Instinctive Junior Women',  0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICM', 'Instinctive Cadet Men',     0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICW', 'Instinctive Cadet Women',   0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICM', 'Instinctive Master Men',    0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'ICW', 'Instinctive Master Women',  0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MJT',  'Men Junior Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WJT',  'Women Junior Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MCT',  'Men Cadet Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WCT',  'Women Cadet Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MMT',  'Men Master Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WMT',  'Women Master Team');
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CM',  'Compound Men',          0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'CW',  'Compound Women',        0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BM',  'Barebow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'BW',  'Barebow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LM',  'Longbow Men',           0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'LW',  'Longbow Women',         0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IM',  'Instinctive Men',       0, 0, 0, $Elim1, $Elim2);
			CreateEvent($TourId, $i++, 0, 0, 2, 8, 12, 1, 1, 4, 1, 1, 'IW',  'Instinctive Women',     0, 0, 0, $Elim1, $Elim2);
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'MT',  'Men Team');
			CreateEvent($TourId, $i++, 1, 0, 2, 8, 8, 3, 3, 4, 3, 3, 'WT',  'Women Team');
			break;
	}
}

function InsertStandard3DEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CJM', 'C', 'JM');
			InsertClassEvent($TourId, 0, 1, 'CCM', 'C', 'CM');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CJW', 'C', 'JW');
			InsertClassEvent($TourId, 0, 1, 'CCW', 'C', 'CW');
			InsertClassEvent($TourId, 0, 1, 'CMW', 'C', 'MW');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BJM', 'B', 'JM');
			InsertClassEvent($TourId, 0, 1, 'BCM', 'B', 'CM');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'MM');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'BJW', 'B', 'JW');
			InsertClassEvent($TourId, 0, 1, 'BCW', 'B', 'CW');
			InsertClassEvent($TourId, 0, 1, 'BMW', 'B', 'MW');
			InsertClassEvent($TourId, 0, 1, 'LM',  'L',  'M');
			InsertClassEvent($TourId, 0, 1, 'LJM', 'L', 'JM');
			InsertClassEvent($TourId, 0, 1, 'LCM', 'L', 'CM');
			InsertClassEvent($TourId, 0, 1, 'LMM', 'L', 'MM');
			InsertClassEvent($TourId, 0, 1, 'LW',  'L',  'W');
			InsertClassEvent($TourId, 0, 1, 'LJW', 'L', 'JW');
			InsertClassEvent($TourId, 0, 1, 'LCW', 'L', 'CW');
			InsertClassEvent($TourId, 0, 1, 'LMW', 'L', 'MW');
			InsertClassEvent($TourId, 0, 1, 'IM',  'I',  'M');
			InsertClassEvent($TourId, 0, 1, 'IJM', 'I', 'JM');
			InsertClassEvent($TourId, 0, 1, 'ICM', 'I', 'CM');
			InsertClassEvent($TourId, 0, 1, 'IMM', 'I', 'MM');
			InsertClassEvent($TourId, 0, 1, 'IW',  'I',  'W');
			InsertClassEvent($TourId, 0, 1, 'IJW', 'I', 'JW');
			InsertClassEvent($TourId, 0, 1, 'ICW', 'I', 'CW');
			InsertClassEvent($TourId, 0, 1, 'IMW', 'I', 'MW');

			InsertClassEvent($TourId, 1, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'L',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'I',  'M');
			InsertClassEvent($TourId, 1, 1, 'MJT', 'C', 'JM');
			InsertClassEvent($TourId, 2, 1, 'MJT', 'L', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'B', 'JM');
			InsertClassEvent($TourId, 3, 1, 'MJT', 'I', 'JM');
			InsertClassEvent($TourId, 1, 1, 'MCT', 'C', 'CM');
			InsertClassEvent($TourId, 2, 1, 'MCT', 'L', 'CM');
			InsertClassEvent($TourId, 3, 1, 'MCT', 'B', 'CM');
			InsertClassEvent($TourId, 3, 1, 'MCT', 'I', 'CM');
			InsertClassEvent($TourId, 1, 1, 'MMT', 'C', 'MM');
			InsertClassEvent($TourId, 2, 1, 'MMT', 'L', 'MM');
			InsertClassEvent($TourId, 3, 1, 'MMT', 'B', 'MM');
			InsertClassEvent($TourId, 3, 1, 'MMT', 'I', 'MM');
			InsertClassEvent($TourId, 1, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'L',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'I',  'W');
			InsertClassEvent($TourId, 1, 1, 'WJT', 'C', 'JW');
			InsertClassEvent($TourId, 2, 1, 'WJT', 'L', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'B', 'JW');
			InsertClassEvent($TourId, 3, 1, 'WJT', 'I', 'JW');
			InsertClassEvent($TourId, 1, 1, 'WCT', 'C', 'CW');
			InsertClassEvent($TourId, 2, 1, 'WCT', 'L', 'CW');
			InsertClassEvent($TourId, 3, 1, 'WCT', 'B', 'CW');
			InsertClassEvent($TourId, 3, 1, 'WCT', 'I', 'CW');
			InsertClassEvent($TourId, 1, 1, 'WMT', 'C', 'MW');
			InsertClassEvent($TourId, 2, 1, 'WMT', 'L', 'MW');
			InsertClassEvent($TourId, 3, 1, 'WMT', 'B', 'MW');
			InsertClassEvent($TourId, 3, 1, 'WMT', 'I', 'MW');
			break;
		case '2':
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'LM',  'L',  'M');
			InsertClassEvent($TourId, 0, 1, 'LW',  'L',  'W');
			InsertClassEvent($TourId, 0, 1, 'IM',  'I',  'M');
			InsertClassEvent($TourId, 0, 1, 'IW',  'I',  'W');

			InsertClassEvent($TourId, 1, 1, 'MT',  'C',  'M');
			InsertClassEvent($TourId, 2, 1, 'MT',  'L',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'B',  'M');
			InsertClassEvent($TourId, 3, 1, 'MT',  'I',  'M');
			InsertClassEvent($TourId, 1, 1, 'WT',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'WT',  'L',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'B',  'W');
			InsertClassEvent($TourId, 3, 1, 'WT',  'I',  'W');
			break;
	}
}

function InsertStandard3DEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
			$cls=array('M', 'W', 'JM', 'JW', 'CM', 'CW', 'MM', 'MW');
			break;
		case '2':
			$cls=array('M', 'W');
			break;
	}
	foreach(array('C', 'B', 'L', 'I') as $div) {
		foreach($cls as $cl) {
			for($n=1; $n<=16; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=0, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
			for($n=1; $n<=8; $n++) {
				safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='$div$cl', ElTournament=$TourId, ElQualRank=$n");
			}
		}
	}
}
?>