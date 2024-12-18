<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'AU';
if(empty($SubRule)) {
    $SubRule='1';
}

function CreateStandardDivisions($TourId, $Type, $SubRule) {
	$i=1;
    switch($SubRule) {
        case '1':
        case '2':
            $optionDivs = array(
                'R' => 'Recurve',
                'C' => 'Compound',
                'BR' => 'Barebow Recurve',
                'BC' => 'Barebow Compound',
                'L' => 'Longbow'
            );
            break;
        case '3':
            $optionDivs = array(
                'R' => 'Recurve',
                'BR' => 'Barebow Recurve',
                'C' => 'Compound'
            );
            break;
    }

    foreach ($optionDivs as $k => $v){
		CreateDivision($TourId, $i++, $k, $v);
	}
}

// subRules are specific to tournament types arnd are defined in sets.php
// so the subrules need to be interpreted in the context of the tournament type
function CreateStandardClasses($TourId, $Type, $SubRule) {
    $i=1;
	switch($SubRule) {
		case '1': // All AU Classes
            CreateClass($TourId, $i++, 1, 13, 0, 'U14M', 'U14M,U16M,U18M,U21M,M', 'U14 Men');
            CreateClass($TourId, $i++, 14, 15, 0, 'U16M', 'U16M,U18M,U21M,M', 'U16 Men');
            CreateClass($TourId, $i++, 16, 17, 0, 'U18M', 'U18M,U21M,M', 'U18 Men');
            CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M,M', 'U21 Men');
            CreateClass($TourId, $i++, 21, 49, 0, 'M', 'M', 'Men');
            CreateClass($TourId, $i++, 50, 59, 0, '50M', '50M,M', '50+ Men');
            CreateClass($TourId, $i++, 60, 69, 0, '60M', '60M,50M,M', '60+ Men');
            CreateClass($TourId, $i++, 70, 100, 0, '70M', '70M,60M,50M,M', '70+ Men');
            CreateClass($TourId, $i++, 1, 13, 0, 'U14W', 'U14W,U16W,U18W,U21W,W', 'U14 Women');
            CreateClass($TourId, $i++, 14, 15, 0, 'U16W', 'U16W,U18W,U21W,W', 'U16 Women');
            CreateClass($TourId, $i++, 1, 17, 1, 'U18W', 'U18W,U21W,W', 'U18 Women');
            CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W,W', 'U21 Women');
            CreateClass($TourId, $i++, 21, 49, 1, 'W', 'W', 'Women');
            CreateClass($TourId, $i++, 50, 59, 1, '50W', '50W, W', '50+ Women');
            CreateClass($TourId, $i++, 60, 69, 1, '60W', '60W,50W,W', '60+ Women');
            CreateClass($TourId, $i++, 70, 100, 1, '70W', '70W,60W,50W,W', '70+ Women');
            break;
        case '2': // M/F open classes
        case '3': // M/F open classes for AUS Open style event
            CreateClass($TourId, $i++, 1, 100, 0, 'M', 'M', 'Men');
            CreateClass($TourId, $i++, 1, 100, 1, 'W', 'W', 'Women');
            break;
    
	}
}


function CreateStandardEvents($TourId, $SubRule, $Outdoor=true, $allowBB=true) {
	$TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
	$TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
	$FirstPhase = ($Outdoor ? 48 : 16);
	$TeamFirstPhase = ($Outdoor ? 12 : 8);

    // function CreateEvent($TourId, $Order, $Team, $MixTeam, $FirstPhase, $TargetType, $ElimEnds, $ElimArrows, $ElimSO, $FinEnds, $FinArrows, $FinSO, $Code, $Description, $SetMode=0, $MatchArrows=0, $AthTarget=0, $ElimRound1=array(), $ElimRound2=array(), $RecCategory='', $WaCategory='', $tgtSize=0, $shootingDist=0, $parentEvent='', $MultipleTeam=0, $Selected=0, $EvWinnerFinalRank=1, $CreationMode=0, $MultipleTeamNo=0, $PartialTeam=0) {
    
    $i=1;
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW', 'Compound Women', 0, 240, 240, 0, 0, '', 'CW', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', 'Compound Men', 0, 240, 240, 0, 0, '', 'CM', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW', 'Recurve Women', 1, 240, 240, 0, 0, '', 'RW', $TargetSizeR, $DistanceR);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', 'Recurve Men', 1, 240, 240, 0, 0, '', 'RM', $TargetSizeR, $DistanceR);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'BRW', 'Barebow Recurve Women', 1, 240, 240, 0, 0, '', 'BW', $TargetSizeB, $DistanceB);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'BRM', 'Barebow Recurve Men', 1, 240, 240, 0, 0, '', 'BM', $TargetSizeB, $DistanceB);
}

// need to insert event here for all the events created above
function InsertStandardEvents($TourId, $SubRule, $Outdoor=true) {
    foreach (array('R'=>'R_','C'=>'C_', 'BR' => 'BR_') as $kDiv=>$vDiv) {
        $clsTmpArr = array('W','U21W','U18W','50W','60W');

        //function InsertClassEvent($TourId, $Team, $Number, $Code, $Division, $Class, $SubClass='')
        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, str_replace('_','W',$vDiv), $kDiv,  $vClass);
            InsertClassEvent($TourId, 1, 3, str_replace('_','W',$vDiv),  $kDiv,  $vClass);
        }
        $clsTmpArr = array('M','U21M','U18M','50M','60M');

        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, str_replace('_','M',$vDiv), $kDiv,  $vClass);
            InsertClassEvent($TourId, 1, 3, str_replace('_','M',$vDiv),  $kDiv,  $vClass);
        }
    }
}

/*

FIELD DEFINITIONS (Target Tournaments)

*/

// require_once(dirname(__FILE__).'/lib-Field.php');

/*

3D DEFINITIONS (Target Tournaments)

*/

// require_once(dirname(__FILE__).'/lib-3D.php');

