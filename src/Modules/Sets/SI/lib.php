<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'SLO';
if(empty($SubRule)) {
    $SubRule='1';
}

function CreateStandardDivisions($TourId, $TourType, $SubRule) {
	$i=1;
	$optionDivs = array(
        'UL'=>array('Ukrivljeni lok','R'),
        'SL'=>array('Sestavljeni lok','C'),
        'GL'=>array('Goli lok','B'),
        'TL'=>array('Tradicionalni lok','T'),
        'DL'=>array('Dolgi lok','L'),
        'LL'=>array('Lovski lok',''),
        'S'=>array('Samostrel',''),
    );

    if(in_array($TourType, array(1,2,3,37,6,7,5,9,10,12,11,13,19))) {
        if($TourType !=11 AND $TourType !=13) {
            unset($optionDivs['LL']);
            unset($optionDivs['S']);
        }
    }
    if(in_array($TourType, array(1,2,3,37,19))){
        unset($optionDivs['DL']);
        unset($optionDivs['TL']);
    }

    /*
        if ($Type == 'FIELD' OR $Type == '3D') {
            $optionDivs = array('R' => 'Recurve', 'C' => 'Compound', 'B' => 'Blankbogen', 'L' => 'Langbogen', 'T' => 'Traditional');
        }
    */
    foreach ($optionDivs as $k => $v){
		CreateDivision($TourId, $i++, $k, $v[0], 1, $v[1], $v[1],false);
	}
}

function CreateStandardClasses($TourId, $TourType, $SubRule) {
    $i=1;
    if($TourType!=11 AND $TourType!=13) {
        CreateClass($TourId, $i++, 21, 49, 1, 'W', 'W,U13W,U15W,U21W,U18W,50W', 'Članice',1, '','W','W');
        CreateClass($TourId, $i++, 21, 49, 0, 'M', 'M,U13M,U15M,U21M,U18M,50M', 'Člani',1, '','M','M');

        CreateClass($TourId, $i++, 1, 12, 1, 'U13W', 'U13W', 'Mlajše od 13 let', 1, (in_array($TourType, array(6, 7)) ? 'GL,UL,SL' : ''));
        CreateClass($TourId, $i++, 1, 12, 0, 'U13M', 'U13M', 'Mlajši od 13 let', 1, (in_array($TourType, array(6, 7)) ? 'GL,UL,SL' : ''));

        CreateClass($TourId, $i++, 13, 14, 1, 'U15W', 'U15W,U13W', 'Mlajše od 15 let',1, (in_array($TourType,array(6,7)) ? 'GL,UL,SL':''));
        CreateClass($TourId, $i++, 13, 14, 0, 'U15M', 'U15M,U13M', 'Mlajši od 15 let',1, (in_array($TourType,array(6,7)) ? 'GL,UL,SL':''));

        CreateClass($TourId, $i++, 15, 17, 1, 'U18W', 'U18W,U15W,U13W', 'Mlajše od 18 let',1, '','U18W','U18W');
        CreateClass($TourId, $i++, 15, 17, 0, 'U18M', 'U18M,U15M,U13M', 'Mlajši od 18 let',1, '','U18M','U18M');

        CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W,U18W,U13W,U15W', 'Mlajše od 21 let',1, '','U21W','U21W');
        CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M,U18M,U15M,U13M', 'Mlajši od 21 let',1, '','U21M','U21M');

        CreateClass($TourId, $i++, 50, 64, 1, '50W', '50W', 'Starejše od 50 let',1, '','50W','50W');
        CreateClass($TourId, $i++, 50, 64, 0, '50M', '50M', 'Starejši od 50 let',1, '','50M','50M');

    } else {
        CreateClass($TourId, $i++, 21, 49, 1, 'W', 'W,U13W,U15W,U21W,U18W,50W', 'Članice', 1, '', 'W', 'W');
        CreateClass($TourId, $i++, 21, 49, 0, 'M', 'M,U13M,U15M,U21M,U18M,50M', 'Člani', 1, '', 'M', 'M');

        CreateClass($TourId, $i++, 1, 14, 1, 'U15W', 'U15W', 'Mlajše od 15 let', 1, 'UL,SL,GL,DL,TL,LL');
        CreateClass($TourId, $i++, 1, 14, 0, 'U15M', 'U15M', 'Mlajši od 15 let', 1, 'UL,SL,GL,DL,TL,LL');

        CreateClass($TourId, $i++, 15, 17, 1, 'U18W', 'U18W,U15W,U13W', 'Mlajše od 18 let', 1, 'UL,SL,GL,DL,TL,LL', 'U18W', 'U18W');
        CreateClass($TourId, $i++, 15, 17, 0, 'U18M', 'U18M,U15M,U13M', 'Mlajši od 18 let', 1, 'UL,SL,GL,DL,TL,LL', 'U18M', 'U18M');

        CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W,U18W,U13W,U15W', 'Mlajše od 21 let', 1, 'UL,SL,GL,DL,TL,LL', 'U21W', 'U21W');
        CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M,U18M,U15M,U13M', 'Mlajši od 21 let', 1, 'UL,SL,GL,DL,TL,LL', 'U21M', 'U21M');

        CreateClass($TourId, $i++, 50, 64, 1, '50W', '50W', 'Starejše od 50 let', 1, '', '50W', '50W');
        CreateClass($TourId, $i++, 50, 64, 0, '50M', '50M', 'Starejši od 50 let', 1, '', '50M', '50M');
    }
}

function CreateStandardEvents($TourId, $Outdoor=true) {
	$TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_6_big10);
    $TargetRU18=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_6_small10);
    $TargetCU15=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_small10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetLT=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetSizeR=($Outdoor ? 122 : 40);
    $TargetSizeU13=($Outdoor ? 122 : 60);
	$TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
    $TargetSizeLT=($Outdoor ? 122 : 60);
	$DistanceR=($Outdoor ? 70 : 18);
    $DistanceRU1850 =($Outdoor ? 60 : 18);
    $DistanceU15 =($Outdoor ? 40 : 18);
    $DistanceU13 =($Outdoor ? 30 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
    $DistanceBU18=($Outdoor ? 40 : 18);
    $DistanceBU15=($Outdoor ? 30 : 18);
    $DistanceBU13=($Outdoor ? 20 : 18);
    $DistanceLT=($Outdoor ? 30 : 18);
	$FirstPhase = ($Outdoor ? 48 : 16);
	$TeamFirstPhase = ($Outdoor ? 12 : 8);

    $i=1;
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'ULM', 'Ukrivljeni lok Člani', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'ULW', 'Ukrivljeni lok Članice', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'ULU21M', 'Ukrivljeni lok Mlajši od 21 let', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'ULU21W', 'Ukrivljeni lok Mlajše od 21 let', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'ULU18M', 'Ukrivljeni lok Mlajši od 18 let', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRU1850);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'ULU18W', 'Ukrivljeni lok Mlajše od 18 let', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRU1850);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'ULU15M', 'Ukrivljeni lok Mlajši od 15 let', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceU15);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'ULU15W', 'Ukrivljeni lok Mlajše od 15 let', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceU15);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'ULU13M', 'Ukrivljeni lok Mlajši od 13 let', 1, 240, 240, 0, 0, '', '', $TargetSizeU13, $DistanceU13);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'ULU13W', 'Ukrivljeni lok Mlajše od 13 let', 1, 240, 240, 0, 0, '', '', $TargetSizeU13, $DistanceU13);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'UL50M', 'Ukrivljeni lok Starejši od 50 let', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRU1850);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'UL50W', 'Ukrivljeni lok Starejše od 50 let', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRU1850);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'SLM', 'Sestavljeni lok  Člani', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'SLW', 'Sestavljeni lok Članice', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'SLU21M', 'Sestavljeni lok Mlajši od 21 let', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'SLU21W', 'Sestavljeni lok Mlajše od 21 let', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'SLU18M', 'Sestavljeni lok Mlajši od 18 let', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'SLU18W', 'Sestavljeni lok Mlajše od 18 let', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetCU15, 5, 3, 1, 5, 3, 1, 'SLU15M', 'Sestavljeni lok Mlajši od 15 let', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceU15);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetCU15, 5, 3, 1, 5, 3, 1, 'SLU15W', 'Sestavljeni lok Mlajše od 15 let', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceU15);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetCU15, 5, 3, 1, 5, 3, 1, 'SLU13M', 'Sestavljeni lok Mlajši od 13 let', 0, 240, 240, 0, 0, '', '', $TargetSizeU13, $DistanceU13);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetCU15, 5, 3, 1, 5, 3, 1, 'SLU13W', 'Sestavljeni lok Mlajše od 13 let', 0, 240, 240, 0, 0, '', '', $TargetSizeU13, $DistanceU13);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'SL50M', 'Sestavljeni lok Starejši od 50 let', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'SL50W', 'Sestavljeni lok Starejše od 50 let', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GLM', 'Goli lok  Člani', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GLW', 'Goli lok Članice', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GLU21M', 'Goli lok Mlajši od 21 let', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GLU21W', 'Goli lok Mlajše od 21 let', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GLU18M', 'Goli lok Mlajši od 18 let', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBU18);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GLU18W', 'Goli lok Mlajše od 18 let', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBU18);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GLU15M', 'Goli lok Mlajši od 15 let', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBU15);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GLU15W', 'Goli lok Mlajše od 15 let', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceBU15);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GLU13M', 'Goli lok Mlajši od 13 let', 1, 240, 240, 0, 0, '', '', $TargetSizeU13, $DistanceBU13);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GLU13W', 'Goli lok Mlajše od 13 let', 1, 240, 240, 0, 0, '', '', $TargetSizeU13, $DistanceBU13);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GL50M', 'Goli lok Starejši od 50 let', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
    CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'GL50W', 'Goli lok Starejše od 50 let', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
    if(!$Outdoor) {
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'DLM', 'Dolgi lok Člani', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'DLW', 'Dolgi lok Članice', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'DLU21M', 'Dolgi lok Mlajši od 21 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'DLU21W', 'Dolgi lok Mlajše od 21 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'DLU18M', 'Dolgi lok Mlajši od 18 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'DLU18W', 'Dolgi lok Mlajše od 18 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'DL50M', 'Dolgi lok Starejši od 50 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'DL50W', 'Dolgi lok Starejše od 50 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'TLM', 'Tradicionalni lok Člani', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'TLW', 'Tradicionalni lok Članice', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'TLU21M', 'Tradicionalni lok Mlajši od 21 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'TLU21W', 'Tradicionalni lok Mlajše od 21 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'TLU18M', 'Tradicionalni lok Mlajši od 18 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'TLU18W', 'Tradicionalni lok Mlajše od 18 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'TL50M', 'Tradicionalni lok Starejši od 50 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
        CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetLT, 5, 3, 1, 5, 3, 1, 'TL50W', 'Tradicionalni lok Starejše od 50 let', 1, 240, 240, 0, 0, '', '', $TargetSizeLT, $DistanceLT);
    }
}

function InsertStandardEvents($TourId, $Outdoor=true) {
    foreach (array('DL','TL','UL','SL','GL') as $vDiv) {
        $clsTmpArr = array('W','U21W','U18W','U15W','U13W','50W');
        foreach($clsTmpArr as $vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv.$vClass, $vDiv,  $vClass);
//            InsertClassEvent($TourId, 1, 3, str_replace('_','W',$vDiv),  $kDiv,  $vClass);
//            InsertClassEvent($TourId, 1, 1, str_replace('_','X',$vDiv),  $kDiv,  $vClass);
        }
        $clsTmpArr = array('M','U21M','U18M','U15M','U13M','50M');
        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv.$vClass, $vDiv,  $vClass);
//            InsertClassEvent($TourId, 1, 3, str_replace('_','M',$vDiv),  $kDiv,  $vClass);
//            InsertClassEvent($TourId, 2, 1, str_replace('_','X',$vDiv),  $kDiv,  $vClass);
        }
    }
}

/*

FIELD DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-Field.php');

/*

3D DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-3D.php');

