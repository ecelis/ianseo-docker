<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'NZ';
if(empty($SubRule)) {
    $SubRule='1';
}

// creation of standard NZ tournament bow types
function CreateStandardDivisions($TourId, $TourType) {
	$i=1;
	CreateDivision($TourId, $i++, 'R', 'Recurve');
	CreateDivision($TourId, $i++, 'C', 'Compound');
	CreateDivision($TourId, $i++, 'B', 'Barebow');
	CreateDivision($TourId, $i++, 'L', 'Longbow');
	CreateDivision($TourId, $i++, 'T', 'Traditional');
	if(in_array($TourType,array(5,6,7,8,9,10,11,12,13,35))) {
		CreateDivision($TourId, $i++, 'X', 'Crossbow');
	}
}

// creation of standard NZ tournament competition classes
function CreateStandardClasses($TourId, $SubRule) {
	$i=1;
	switch($SubRule) {
		case '1': // All NZ Classes
			CreateClass($TourId, $i++, 21, 49, 0, 'M', 'M', 'Men');
			CreateClass($TourId, $i++, 21, 49, 1, 'W', 'W', 'Women');
			CreateClass($TourId, $i++, 50, 64, 0, '50M', '50M,M', '50+ Men');
			CreateClass($TourId, $i++, 50, 64, 1, '50W', '50W,W', '50+ Women');
			CreateClass($TourId, $i++, 65,100, 0, '65M', '65M,50M,M', '65+ Men');
			CreateClass($TourId, $i++, 65,100, 1, '65W', '65W,50W,W', '65+ Women');
			CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
			CreateClass($TourId, $i++, 16, 17, 0, 'U18M', 'U18M,U21M,M', 'Under 18 Men');
			CreateClass($TourId, $i++, 16, 17, 1, 'U18W', 'U18W,U21W,W', 'Under 18 Women');
			CreateClass($TourId, $i++, 14, 15, 0, 'U16B', 'U16B,U18M,U21M', 'Under 16 Boys');
			CreateClass($TourId, $i++, 14, 15, 1, 'U16G', 'U16G,U18W,U21W', 'Under 16 Girls');
			CreateClass($TourId, $i++, 11, 13, 0, 'U14B', 'U14B,U16B', 'Under 14 Boys');
			CreateClass($TourId, $i++, 11, 13, 1, 'U14G', 'U14G,U16G', 'Under 14 Girls');
			CreateClass($TourId, $i++,  1, 10, 0, 'U11B', 'U11B,U14B,U16B', 'Under 11 Boys');
			CreateClass($TourId, $i++,  1, 10, 1, 'U11G', 'U11G,U14G,U16G', 'Under 11 Girls');
			CreateClass($TourId, $i++,  1,100,-1, 'D', 'D', 'Development');
			CreateClass($TourId, $i++,  1,100,-1, 'N', 'N', 'Novice');
			break;
		case '2': // Senior NZ & WA Classes Only
			CreateClass($TourId, $i++, 21, 49, 0, 'M', 'M', 'Men');
			CreateClass($TourId, $i++, 21, 49, 1, 'W', 'W', 'Women');
			CreateClass($TourId, $i++, 50, 64, 0, '50M', '50M,M', '50+ Men');
			CreateClass($TourId, $i++, 50, 64, 1, '50W', '50W,W', '50+ Women');
			CreateClass($TourId, $i++, 65,100, 0, '65M', '65M,50M,M', '65+ Men');
			CreateClass($TourId, $i++, 65,100, 1, '65W', '65W,50W,W', '65+ Women');
			CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
			CreateClass($TourId, $i++,  1, 17, 0, 'U18M', 'U18M,U21M,M', 'Under 18 Men');
			CreateClass($TourId, $i++,  1, 17, 1, 'U18W', 'U18W,U21W,W', 'Under 18 Women');
			CreateClass($TourId, $i++,  1,100,-1, 'D', 'D', 'Development');
			CreateClass($TourId, $i++,  1,100,-1, 'N', 'N', 'Novice');
			break;
		case '3': // Junior Classes Only
			CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M', 'Under 21 Men');
			CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W', 'Under 21 Women');
			CreateClass($TourId, $i++, 16, 17, 0, 'U18M', 'U18M,U21M', 'Under 18 Men');
			CreateClass($TourId, $i++, 16, 17, 1, 'U18W', 'U18W,U21W', 'Under 18 Women');
			CreateClass($TourId, $i++, 14, 15, 0, 'U16B', 'U16B,U18M,U21M', 'Under 16 Boys');
			CreateClass($TourId, $i++, 14, 15, 1, 'U16G', 'U16G,U18W,U21W', 'Under 16 Girls');
			CreateClass($TourId, $i++, 11, 13, 0, 'U14B', 'U14B,U16B', 'Under 14 Boys');
			CreateClass($TourId, $i++, 11, 13, 1, 'U14G', 'U14G,U16G', 'Under 14 Girls');
			CreateClass($TourId, $i++,  1, 10, 0, 'U11B', 'U11B,U14B,U16B', 'Under 11 Boys');
			CreateClass($TourId, $i++,  1, 10, 1, 'U11G', 'U11G,U14G,U16G', 'Under 11 Girls');
			break;
	}
}

// creation of standard NZ matchplay competition events
function CreateStandardEvents($TourId, $SubRule, $Outdoor=true, $allowBB=true) {
	/*
		IANSEO Target Faces:
		1 - Indoor (1-big 10)
		2 - Indoor (6-big 10)
		3 - Indoor (1-small 10)
		4 - Indoor (6-small 10)
		5 - Outdoor (1-X)
		6 - Field Archery
		7 - Hit-Miss
		8 - 3D Standard
		9 - Outdoor (5-X)
		17 - Imperial Target (1-9)
	*/
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
    $TargetB=($Outdoor?5:1);
	$TargetIr=($Outdoor?5:1);
	$TargetIc=($Outdoor?9:4);
	$TargetYr=($Outdoor?5:1);
	$TargetYc=($Outdoor?5:3);

	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$TargetSizeB=($Outdoor ? 122 : 40);

	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceRcm=($Outdoor ? 60 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
	$DistanceB=($Outdoor ? 50 : 18);
	$DistanceI=($Outdoor ? 45 : 18);
	$DistanceY=($Outdoor ? 35 : 18);
	//$DistanceK=($Outdoor ? 20 : 18);

	$FirstPhase = ($Outdoor ? 8 : 8);
	$TeamFirstPhase = ($Outdoor ? 12 : 8);

	// CreateEvent function requires variables:
	// ($TourId, $Order, $Team, $MixTeam, $FirstPhase, $TargetType, $ElimEnds, $ElimArrows, $ElimSO, $FinEnds, $FinArrows, $FinSO,
	// $Code, $Description, $SetMode=0, $MatchArrows=0, $AthTarget=0, $ElimRound1=array(), $ElimRound2=array(), $RecCategory='', $WaCategory='',
	// $tgtSize=0, $shootingDist=0, $parentEvent='', $MultipleTeam=0, $Selected=0, $EvWinnerFinalRank=1, $CreationMode=0, $MultipleTeamNo=0, $PartialTeam=0)
	switch($SubRule) {
		case '1':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'R50M', 'Recurve 50+ Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'R50W', 'Recurve 50+ Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21M', 'Recurve Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21W', 'Recurve Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18M', 'Recurve Under 18 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18W', 'Recurve Under 18 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIr, 5, 3, 1, 5, 3, 1, 'RU16B', 'Recurve Under 16 Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIr, 5, 3, 1, 5, 3, 1, 'RU16G', 'Recurve Under 16 Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYr, 5, 3, 1, 5, 3, 1, 'RU14B', 'Recurve Under 14 Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYr, 5, 3, 1, 5, 3, 1, 'RU14G', 'Recurve Under 14 Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'C50M', 'Compound 50+ Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'C50W', 'Compound 50+ Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21M', 'Compound Under 21 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21W', 'Compound Under 21 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18M', 'Compound Under 18 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18W', 'Compound Under 18 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIc, 5, 3, 1, 5, 3, 1, 'CU16B', 'Compound Under 16 Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIc, 5, 3, 1, 5, 3, 1, 'CU16G', 'Compound Under 16 Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYc, 5, 3, 1, 5, 3, 1, 'CU14B', 'Compound Under 14 Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYc, 5, 3, 1, 5, 3, 1, 'CU14G', 'Compound Under 14 Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceY);
			if($allowBB) {
				CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Barebow Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'B50M', 'Barebow 50+ Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'B50W', 'Barebow 50+ Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
			}
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
			if($allowBB) {
				CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM', 'Barebow Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW', 'Barebow Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
				if ($Outdoor) {
					CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
				}
			}
			break;
		case '2':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'R50M', 'Recurve 50+ Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'R50W', 'Recurve 50+ Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21M', 'Recurve Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21W', 'Recurve Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18M', 'Recurve Under 18 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18W', 'Recurve Under 18 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'C50M', 'Compound 50+ Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'C50W', 'Compound 50+ Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21M', 'Compound Under 21 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21W', 'Compound Under 21 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18M', 'Compound Under 18 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18W', 'Compound Under 18 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($allowBB) {
				CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Barebow Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'B50M', 'Barebow 50+ Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'B50W', 'Barebow 50+ Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
			}
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
			if($allowBB) {
				CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM', 'Barebow Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW', 'Barebow Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
				if ($Outdoor) {
					CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
				}
			}
			break;
		case '3':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21M', 'Recurve Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21W', 'Recurve Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18M', 'Recurve Under 18 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU18W', 'Recurve Under 18 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIr, 5, 3, 1, 5, 3, 1, 'RU16B', 'Recurve Under 16 Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIr, 5, 3, 1, 5, 3, 1, 'RU16G', 'Recurve Under 16 Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYr, 5, 3, 1, 5, 3, 1, 'RU14B', 'Recurve Under 14 Boys', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYr, 5, 3, 1, 5, 3, 1, 'RU14G', 'Recurve Under 14 Girls', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21M', 'Compound Under 21 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21W', 'Compound Under 21 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18M', 'Compound Under 18 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU18W', 'Compound Under 18 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIc, 5, 3, 1, 5, 3, 1, 'CU16B', 'Compound Under 16 Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetIc, 5, 3, 1, 5, 3, 1, 'CU16G', 'Compound Under 16 Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceI);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYc, 5, 3, 1, 5, 3, 1, 'CU14B', 'Compound Under 14 Boys', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceY);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetYc, 5, 3, 1, 5, 3, 1, 'CU14G', 'Compound Under 14 Girls', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceY);
			if($allowBB) {
				CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU21M', 'Barebow Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
				CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU21W', 'Barebow Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
			}
			break;
	}
}
// ($TourId, $Team, $Number, $Code, $Division, $Class, $SubClass='')
function InsertStandardEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			InsertClassEvent($TourId, 0, 1, 'RM', 'R', 'M');
			InsertClassEvent($TourId, 0, 1, 'R50M', 'R', '50M');
			InsertClassEvent($TourId, 0, 1, 'RU21M', 'R', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'RU18M', 'R', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'RU16B', 'R', 'U16B');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'R', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'RW', 'R', 'W');
			InsertClassEvent($TourId, 0, 1, 'R50W', 'R', '50W');
			InsertClassEvent($TourId, 0, 1, 'RU21W', 'R', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'RU18W', 'R', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'RU16G', 'R', 'U16G');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'R', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'CM', 'C', 'M');
			InsertClassEvent($TourId, 0, 1, 'C50M', 'C', '50M');
			InsertClassEvent($TourId, 0, 1, 'CU21M', 'C', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'CU18M', 'C', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'CU16B', 'C', 'U16B');
			InsertClassEvent($TourId, 0, 1, 'CU14B', 'C', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'CW', 'C', 'W');
			InsertClassEvent($TourId, 0, 1, 'C50W', 'C', '50W');
			InsertClassEvent($TourId, 0, 1, 'CU21W', 'C', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'CU18W', 'C', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'CU16G', 'C', 'U16G');
			InsertClassEvent($TourId, 0, 1, 'CU14G', 'C', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'BM', 'B', 'M');
			InsertClassEvent($TourId, 0, 1, 'B50M', 'B', '50M');
			InsertClassEvent($TourId, 0, 1, 'BW', 'B', 'W');
			InsertClassEvent($TourId, 0, 1, 'B50W', 'B', '50W');

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RM', 'R', '50M');
			InsertClassEvent($TourId, 1, 3, 'RM', 'R', '65M');
			InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 1, 3, 'RW', 'R', '50W');
			InsertClassEvent($TourId, 1, 3, 'RW', 'R', '65W');
			InsertClassEvent($TourId, 1, 3, 'RW', 'R', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'RW', 'R', 'U18W');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CM', 'C', '50M');
			InsertClassEvent($TourId, 1, 3, 'CM', 'C', '65M');
			InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 1, 3, 'CW', 'C', '50W');
			InsertClassEvent($TourId, 1, 3, 'CW', 'C', '65W');
			InsertClassEvent($TourId, 1, 3, 'CW', 'C', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'CW', 'C', 'U18W');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 1, 3, 'BM',  'B',  '50M');
			InsertClassEvent($TourId, 1, 3, 'BM',  'B',  '65M');
			InsertClassEvent($TourId, 1, 3, 'BM', 'B', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'BM', 'B', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 1, 3, 'BW',  'B',  '50W');
			InsertClassEvent($TourId, 1, 3, 'BW',  'B',  '65W');
			InsertClassEvent($TourId, 1, 3, 'BW', 'B', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'BW', 'B', 'U18W');
			InsertClassEvent($TourId, 1, 1, 'BX',  'B',  'W');
			InsertClassEvent($TourId, 2, 1, 'BX',  'B',  'M');
			break;
		case '2':
			InsertClassEvent($TourId, 0, 1, 'RM', 'R', 'M');
			InsertClassEvent($TourId, 0, 1, 'R50M', 'R', '50M');
			InsertClassEvent($TourId, 0, 1, 'RU21M', 'R', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'RU18M', 'R', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'RW', 'R', 'W');
			InsertClassEvent($TourId, 0, 1, 'R50W', 'R', '50W');
			InsertClassEvent($TourId, 0, 1, 'RU21W', 'R', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'RU18W', 'R', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'CM', 'C', 'M');
			InsertClassEvent($TourId, 0, 1, 'C50M', 'C', '50M');
			InsertClassEvent($TourId, 0, 1, 'CU21M', 'C', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'CU18M', 'C', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'CW', 'C', 'W');
			InsertClassEvent($TourId, 0, 1, 'C50W', 'C', '50W');
			InsertClassEvent($TourId, 0, 1, 'CU21W', 'C', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'CU18W', 'C', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'BM', 'B', 'M');
			InsertClassEvent($TourId, 0, 1, 'B50M', 'B', '50M');
			InsertClassEvent($TourId, 0, 1, 'BW', 'B', 'W');
			InsertClassEvent($TourId, 0, 1, 'B50W', 'B', '50W');

			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RM', 'R', '50M');
			InsertClassEvent($TourId, 1, 3, 'RM', 'R', '65M');
			InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 1, 3, 'RW', 'R', '50W');
			InsertClassEvent($TourId, 1, 3, 'RW', 'R', '65W');
			InsertClassEvent($TourId, 1, 3, 'RW', 'R', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'RW', 'R', 'U18W');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'W');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CM', 'C', '50M');
			InsertClassEvent($TourId, 1, 3, 'CM', 'C', '65M');
			InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 1, 3, 'CW', 'C', '50W');
			InsertClassEvent($TourId, 1, 3, 'CW', 'C', '65W');
			InsertClassEvent($TourId, 1, 3, 'CW', 'C', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'CW', 'C', 'U18W');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'W');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 1, 3, 'BM',  'B',  '50M');
			InsertClassEvent($TourId, 1, 3, 'BM',  'B',  '65M');
			InsertClassEvent($TourId, 1, 3, 'BM', 'B', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'BM', 'B', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 1, 3, 'BW',  'B',  '50W');
			InsertClassEvent($TourId, 1, 3, 'BW',  'B',  '65W');
			InsertClassEvent($TourId, 1, 3, 'BW', 'B', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'BW', 'B', 'U18W');
			InsertClassEvent($TourId, 1, 1, 'BX',  'B',  'W');
			InsertClassEvent($TourId, 2, 1, 'BX',  'B',  'M');
			break;
		case '3':
			InsertClassEvent($TourId, 0, 1, 'RU21M', 'R', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'RU18M', 'R', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'RU16B', 'R', 'U16B');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'R', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'RU21W', 'R', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'RU18W', 'R', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'RU16G', 'R', 'U16G');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'R', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'CU21M', 'C', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'CU18M', 'C', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'CU16B', 'C', 'U16B');
			InsertClassEvent($TourId, 0, 1, 'CU14B', 'C', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'CU21W', 'C', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'CU18W', 'C', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'CU16G', 'C', 'U16G');
			InsertClassEvent($TourId, 0, 1, 'CU14G', 'C', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'BU21M', 'B', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'BU21W', 'B', 'U21W');
			break;
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

?>
