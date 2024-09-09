<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'FITA';
if(empty($SubRule)) {
    $SubRule='1';
}

function CreateStandardDivisions($TourId, $Type='FITA') {
	$i=1;
	if($Type!='3D') {
	    CreateDivision($TourId, $i++, 'R', 'Recurve');
    }
	CreateDivision($TourId, $i++, 'C', 'Compound');
    if($Type!='FITA') {
        CreateDivision($TourId, $i++, 'B', 'Barebow');
    }
    if($Type=='3D') {
		CreateDivision($TourId, $i++, 'L', 'Longbow');
		CreateDivision($TourId, $i++, 'T', 'Traditional');
	}
}

function CreateStandardClasses($TourId, $SubRule, $Type='FITA') {
    $i=1;
	switch($SubRule) {
		case '1':
			CreateClass($TourId, $i++, 21, 49, 0, 'M', 'M', 'Men');
			CreateClass($TourId, $i++, 21, 49, 1, 'W', 'W', 'Women');
			CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
            if(in_array($Type, [3,37])) {
                // 70m and 2x70m have U15 too
                CreateClass($TourId, $i++,  15, 17, 0, 'U18M', 'U18M,U21M,M', 'Under 18 Men');
                CreateClass($TourId, $i++,  15, 17, 1, 'U18W', 'U18W,U21W,W', 'Under 18 Women');
                CreateClass($TourId, $i++,  1, 14, 0, 'U15M', 'U15M,U18M,U21M,M', 'Under 15 Men');
                CreateClass($TourId, $i++,  1, 14, 1, 'U15W', 'U15W,U18W,U21W,W', 'Under 15 Women');
            } else {
                // only U18
                CreateClass($TourId, $i++,  1, 17, 0, 'U18M', 'U18M,U21M,M', 'Under 18 Men');
                CreateClass($TourId, $i++,  1, 17, 1, 'U18W', 'U18W,U21W,W', 'Under 18 Women');
            }
			CreateClass($TourId, $i++, 50,100, 0, '50M', '50M,M', '50+ Men');
			CreateClass($TourId, $i++, 50,100, 1, '50W', '50W,W', '50+ Women');
			break;
		case '2':
		case '5':
			CreateClass($TourId, 1, 1,100, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 1,100, 1, 'W', 'W', 'Women');
			break;
		case '3':
			CreateClass($TourId, 1, 21,100, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21,100, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 1, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, 4, 1, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
			break;
		case '4':
			CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M', 'Under 21 Men');
			CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W', 'Under 21 Women');
            if(in_array($Type, [3,37])) {
                // 70m and 2x70m have U15 too
                CreateClass($TourId, $i++,  15, 17, 0, 'U18M', 'U18M,U21M,M', 'Under 18 Men');
                CreateClass($TourId, $i++,  15, 17, 1, 'U18W', 'U18W,U21W,W', 'Under 18 Women');
                CreateClass($TourId, $i++,  1, 14, 0, 'U15M', 'U15M,U18M,U21M,M', 'Under 15 Men');
                CreateClass($TourId, $i++,  1, 14, 1, 'U15W', 'U15W,U18W,U21W,W', 'Under 15 Women');
            } else {
                // only U18
                CreateClass($TourId, $i++,  1, 17, 0, 'U18M', 'U18M,U21M,M', 'Under 18 Men');
                CreateClass($TourId, $i++,  1, 17, 1, 'U18W', 'U18W,U21W,W', 'Under 18 Women');
            }
			break;
	}
}

function CreateStandardEvents($TourId, $SubRule, $TourType) {
    $Outdoor=($TourType!=6);
    $allowBB=(in_array($TourType,array(3,6,7,8,37)));
    $allowU15=(in_array($TourType, [3,37]));
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
    $TargetB=($Outdoor?5:1);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceRcm=($Outdoor ? 60 : 18);
	$DistanceU15=($Outdoor ? 40 : 18);
	$DistanceU15B=($Outdoor ? 30 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
	$FirstPhase = ($Outdoor ? 48 : 16);
	$TeamFirstPhase = ($Outdoor ? 12 : 8);
    $Options=[
        'EvFinalFirstPhase' => $FirstPhase,
        'EvFinalTargetType' => $TargetR,
        'EvElimEnds'=>5,
        'EvElimArrows'=>3,
        'EvElimSO'=>1,
        'EvFinEnds'=>5,
        'EvFinArrows'=>3,
        'EvFinSO'=>1,
        'EvMatchMode'=>1,
        'EvMatchArrowsNo'=>240,
        'EvFinalAthTarget' => 240,
        'EvTargetSize' => $TargetSizeR,
        'EvDistance' => $DistanceR,
        'EvGolds' => $Outdoor ? '10+X' : '10',
        'EvXNine' => $Outdoor ? 'X' : '9',
        'EvGoldsChars' => $Outdoor ? 'KL' : 'L',
        'EvXNineChars' => $Outdoor ? 'K' : 'J',
        'EvCheckGolds' => 0,
        'EvCheckXNines' => 0,
    ];
	switch($SubRule) {
		case '1':
		case '4':
			$i=1;
            // RECURVE
            if($SubRule==1) {
                CreateEventNew($TourId, 'RM',  'Recurve Men', $i++, $Options);
                CreateEventNew($TourId, 'RW',  'Recurve Women', $i++, $Options);
            }
			CreateEventNew($TourId, 'RU21M', 'Recurve Under 21 Men', $i++, $Options);
			CreateEventNew($TourId, 'RU21W', 'Recurve Under 21 Women', $i++, $Options);
            $Options['EvDistance']=$DistanceRcm;
            CreateEventNew($TourId, 'RU18M', 'Recurve Under 18 Men', $i++, $Options);
            CreateEventNew($TourId, 'RU18W', 'Recurve Under 18 Women', $i++, $Options);
            if($allowU15) {
                $Options['EvDistance']=$DistanceU15;
                CreateEventNew($TourId, 'RU15M', 'Recurve Under 15 Men', $i++, $Options);
                CreateEventNew($TourId, 'RU15W', 'Recurve Under 15 Women', $i++, $Options);
            }
            if($SubRule==1) {
                $Options['EvDistance']=$DistanceRcm;
                CreateEventNew($TourId, 'R50M', 'Recurve 50+ Men', $i++, $Options);
                CreateEventNew($TourId, 'R50W', 'Recurve 50+ Women', $i++, $Options);
            }

            // COMPOUND
            $Options['EvMatchMode']=0;
            $Options['EvFinalTargetType']=$TargetC;
            $Options['EvTargetSize']=$TargetSizeC;
            $Options['EvDistance']=$DistanceC;
            if($SubRule==1) {
                CreateEventNew($TourId, 'CM',  'Compound Men', $i++, $Options);
                CreateEventNew($TourId, 'CW',  'Compound Women', $i++, $Options);
            }
            CreateEventNew($TourId, 'CU21M', 'Compound Under 21 Men', $i++, $Options);
			CreateEventNew($TourId, 'CU21W', 'Compound Under 21 Women', $i++, $Options);
			CreateEventNew($TourId, 'CU18M', 'Compound Under 18 Men', $i++, $Options);
			CreateEventNew($TourId, 'CU18W', 'Compound Under 18 Women', $i++, $Options);
            if($allowU15) {
                $Options['EvDistance']=$DistanceU15;
                CreateEventNew($TourId, 'CU15M', 'Compound Under 15 Men', $i++, $Options);
                CreateEventNew($TourId, 'CU15W', 'Compound Under 15 Women', $i++, $Options);
            }
            if($SubRule==1) {
                $Options['EvDistance']=$DistanceC;
                CreateEventNew($TourId, 'C50M', 'Compound 50+ Men', $i++, $Options);
                CreateEventNew($TourId, 'C50W', 'Compound 50+ Women', $i++, $Options);
            }

            // BAREBOW
			if($allowBB) {
                $Options['EvMatchMode']=1;
                $Options['EvFinalTargetType']=$TargetB;
                $Options['EvTargetSize']=$TargetSizeB;
                $Options['EvDistance']=$DistanceB;
                if($SubRule==1) {
                    CreateEventNew($TourId, 'BM', 'Barebow Men', $i++, $Options);
                    CreateEventNew($TourId, 'BW', 'Barebow Women', $i++, $Options);
                }
                CreateEventNew($TourId,'BU21M', 'Barebow Under 21 Men', $i++, $Options);
                CreateEventNew($TourId,'BU21W', 'Barebow Under 21 Women', $i++, $Options);
                CreateEventNew($TourId,'BU18M', 'Barebow Under 18 Men', $i++, $Options);
                CreateEventNew($TourId,'BU18W', 'Barebow Under 18 Women', $i++, $Options);
                if($allowU15) {
                    $Options['EvDistance']=$DistanceU15B;
                    CreateEventNew($TourId, 'BU15M', 'Barebow Under 15 Men', $i++, $Options);
                    CreateEventNew($TourId, 'BU15W', 'Barebow Under 15 Women', $i++, $Options);
                }
                if($SubRule==1) {
                    $Options['EvDistance'] = $DistanceB;
                    CreateEventNew($TourId, 'B50M', 'Barebow 50+ Men', $i++, $Options);
                    CreateEventNew($TourId, 'B50W', 'Barebow 50+ Women', $i++, $Options);
                }
            }

            // TEAMS
            // RECURVE
			$i=1;
            $Options['EvTeamEvent']=1;
            $Options['EvMatchArrowsNo']=0;
            $Options['EvFinalAthTarget']=0;
            $Options['EvFinalFirstPhase']=$TeamFirstPhase;
            $Options['EvElimEnds']=4;
            $Options['EvFinEnds']=4;
            $Options['EvMatchMode']=1;
            $Options['EvMaxTeamPerson']=3;
            $Options['EvMixedTeam']=0;
            $Options['EvFinalTargetType']=$TargetR;
            $Options['EvElimArrows']=6;
            $Options['EvElimSO']=3;
            $Options['EvFinArrows']=6;
            $Options['EvFinSO']=3;
            $Options['EvTargetSize']=$TargetSizeR;
            $Options['EvDistance']=$DistanceR;
            if($SubRule==1) {
                CreateEventNew($TourId, 'RM', 'Recurve Men Team', $i++, $Options);
                CreateEventNew($TourId, 'RW', 'Recurve Women Team', $i++, $Options);
            }
			CreateEventNew($TourId,'RU21M', 'Recurve Under 21 Men Team', $i++, $Options);
			CreateEventNew($TourId,'RU21W', 'Recurve Under 21 Women Team', $i++, $Options);
            $Options['EvDistance']=$DistanceRcm;
			CreateEventNew($TourId,'RU18M', 'Recurve Under 18 Men Team', $i++, $Options);
			CreateEventNew($TourId,'RU18W', 'Recurve Under 18 Women Team', $i++, $Options);
            if($allowU15) {
                $Options['EvDistance']=$DistanceU15;
                CreateEventNew($TourId,'RU15M', 'Recurve Under 15 Men Team', $i++, $Options);
                CreateEventNew($TourId,'RU15W', 'Recurve Under 15 Women Team', $i++, $Options);
            }
            if($SubRule==1) {
                $Options['EvDistance'] = $DistanceRcm;
                CreateEventNew($TourId, 'R50M', 'Recurve 50+ Men Team', $i++, $Options);
                CreateEventNew($TourId, 'R50W', 'Recurve 50+ Women Team', $i++, $Options);
            }

			if($Outdoor) {
                $Options['EvMixedTeam']=1;
                $Options['EvDistance']=$DistanceR;
                $Options['EvElimArrows']=4;
                $Options['EvElimSO']=2;
                $Options['EvFinArrows']=4;
                $Options['EvFinSO']=2;
                $Options['EvMaxTeamPerson']=2;
                if($SubRule==1) {
                    CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $Options);
                }
				CreateEventNew($TourId,'RU21X', 'Recurve Under 21 Mixed Team', $i++, $Options);
                $Options['EvDistance']=$DistanceRcm;
				CreateEventNew($TourId,'RU18X', 'Recurve Under 18 Mixed Team', $i++, $Options);
                if($allowU15) {
                    $Options['EvDistance']=$DistanceU15;
                    CreateEventNew($TourId,'RU15X', 'Recurve Under 15 Mixed Team', $i++, $Options);
                }
                if($SubRule==1) {
                    $Options['EvDistance'] = $DistanceRcm;
                    CreateEventNew($TourId, 'R50X', 'Recurve 50+ Mixed Team', $i++, $Options);
                }
			}

            // COMPOUND
            $Options['EvMatchMode']=0;
            $Options['EvMaxTeamPerson']=3;
            $Options['EvMixedTeam']=0;
            $Options['EvFinalTargetType']=$TargetC;
            $Options['EvElimArrows']=6;
            $Options['EvElimSO']=3;
            $Options['EvFinArrows']=6;
            $Options['EvFinSO']=3;
            $Options['EvTargetSize']=$TargetSizeC;
            $Options['EvDistance']=$DistanceC;
            if($SubRule==1) {
                CreateEventNew($TourId, 'CM', 'Compound Men Team', $i++, $Options);
                CreateEventNew($TourId, 'CW', 'Compound Women Team', $i++, $Options);
            }
			CreateEventNew($TourId,'CU21M', 'Compound Under 21 Men Team', $i++, $Options);
			CreateEventNew($TourId,'CU21W', 'Compound Under 21 Women Team', $i++, $Options);
			CreateEventNew($TourId,'CU18M', 'Compound Under 18 Men Team', $i++, $Options);
			CreateEventNew($TourId,'CU18W', 'Compound Under 18 Women Team', $i++, $Options);
            if($allowU15) {
                $Options['EvDistance']=$DistanceU15;
                CreateEventNew($TourId,'CU15M', 'Compound Under 15 Men Team', $i++, $Options);
                CreateEventNew($TourId,'CU15W', 'Compound Under 15 Women Team', $i++, $Options);
            }
            if($SubRule==1) {
                $Options['EvDistance'] = $DistanceC;
                CreateEventNew($TourId, 'C50M', 'Compound 50+ Men Team', $i++, $Options);
                CreateEventNew($TourId, 'C50W', 'Compound 50+ Women Team', $i++, $Options);
            }
			if($Outdoor) {
                $Options['EvMixedTeam']=1;
                $Options['EvDistance']=$DistanceC;
                $Options['EvElimArrows']=4;
                $Options['EvElimSO']=2;
                $Options['EvFinArrows']=4;
                $Options['EvFinSO']=2;
                $Options['EvMaxTeamPerson']=2;
                if($SubRule==1) {
                    CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $Options);
                }
				CreateEventNew($TourId, 'CU21X', 'Compound Under 21 Mixed Team',$i++, $Options);
				CreateEventNew($TourId, 'CU18X', 'Compound Under 18 Mixed Team',$i++, $Options);
                if($allowU15) {
                    $Options['EvDistance']=$DistanceU15;
                    CreateEventNew($TourId,'CU15X', 'Compound Under 15 Mixed Team', $i++, $Options);
                }
                if($SubRule==1) {
                    $Options['EvDistance'] = $DistanceC;
                    CreateEventNew($TourId, 'C50X', 'Compound 50+ Mixed Team', $i++, $Options);
                }
			}

            if($allowBB) {
                $Options['EvMatchMode']=1;
                $Options['EvMaxTeamPerson']=3;
                $Options['EvMixedTeam']=0;
                $Options['EvFinalTargetType']=$TargetB;
                $Options['EvElimArrows']=6;
                $Options['EvElimSO']=3;
                $Options['EvFinArrows']=6;
                $Options['EvFinSO']=3;
                $Options['EvTargetSize']=$TargetSizeB;
                $Options['EvDistance']=$DistanceB;
                if($SubRule==1) {
                    CreateEventNew($TourId, 'BM', 'Barebow Men Team', $i++, $Options);
                    CreateEventNew($TourId, 'BW', 'Barebow Women Team', $i++, $Options);
                }
                CreateEventNew($TourId,'BU21M', 'Barebow Under 21 Men Team', $i++, $Options);
                CreateEventNew($TourId,'BU21W', 'Barebow Under 21 Women Team', $i++, $Options);
                CreateEventNew($TourId,'BU18M', 'Barebow Under 18 Men Team', $i++, $Options);
                CreateEventNew($TourId,'BU18W', 'Barebow Under 18 Women Team', $i++, $Options);
                if($allowU15) {
                    $Options['EvDistance']=$DistanceU15B;
                    CreateEventNew($TourId,'BU15M', 'Barebow Under 15 Men Team', $i++, $Options);
                    CreateEventNew($TourId,'BU15W', 'Barebow Under 15 Women Team', $i++, $Options);
                }
                if($SubRule==1) {
                    $Options['EvDistance'] = $DistanceB;
                    CreateEventNew($TourId, 'B50M', 'Barebow 50+ Men Team', $i++, $Options);
                    CreateEventNew($TourId, 'B50W', 'Barebow 50+ Women Team', $i++, $Options);
                }
                if ($Outdoor) {
                    $Options['EvMixedTeam']=1;
                    $Options['EvDistance']=$DistanceB;
                    $Options['EvElimArrows']=4;
                    $Options['EvElimSO']=2;
                    $Options['EvFinArrows']=4;
                    $Options['EvFinSO']=2;
                    $Options['EvMaxTeamPerson']=2;
                    if($SubRule==1) {
                        CreateEventNew($TourId, 'BX', 'Barebow Mixed Team', $i++, $Options);
                    }
                    CreateEventNew($TourId,'BU21X', 'Barebow Under 21 Mixed Team', $i++, $Options);
                    CreateEventNew($TourId,'BU18X', 'Barebow Under 18 Mixed Team', $i++, $Options);
                    if($allowU15) {
                        $Options['EvDistance']=$DistanceU15B;
                        CreateEventNew($TourId,'BU15X', 'Barebow Under 15 Mixed Team', $i++, $Options);
                    }
                    if($SubRule==1) {
                        $Options['EvDistance'] = $DistanceB;
                        CreateEventNew($TourId, 'B50X', 'Barebow 50+ Mixed Team', $i++, $Options);
                    }
                }
            }
            break;
		case '2':
		case '5':
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            if($allowBB) {
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Barebow Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
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
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurve Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Recurve Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21M', 'Recurve Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU21W', 'Recurve Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Compound Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Compound Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21M', 'Compound Under 21 Men', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU21W', 'Compound Under 21 Women', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
            if($allowBB) {
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW', 'Barebow Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU21M', 'Barebow Under 21 Men', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU21W', 'Barebow Under 21 Women', 1, 240, 240, 0, 0, '', '', $TargetSizeB, $DistanceB);
            }
            $i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurve Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RW',  'Recurve Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU21M', 'Recurve Under 21 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU21W', 'Recurve Under 21 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurve Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RU21X', 'Recurve Under 21 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Compound Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CW',  'Compound Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU21M', 'Compound Under 21 Men Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU21W', 'Compound Under 21 Women Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Compound Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CU21X', 'Compound Under 21 Mixed Team', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			}
            if($allowBB) {
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM', 'Barebow Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BW', 'Barebow Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU21M', 'Barebow Under 21 Men Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU21W', 'Barebow Under 21 Women Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                if ($Outdoor) {
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                    CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BU21X', 'Barebow Under 21 Mixed Team', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
                }
            }
			break;
	}
}

function InsertStandardEvents($TourId, $SubRule) {
    $divs=['R','C','B'];
	switch($SubRule) {
		case '1':
            $cls=['','U21','U18','U15','50'];
			break;
		case '2':
		case '5':
            $cls=[''];
			break;
		case '3':
            $cls=['','U21'];
			break;
		case '4':
            $cls=['U21','U18','U15'];
			break;
	}
    foreach($divs as $div) {
        foreach($cls as $cl) {
            InsertClassEvent($TourId, 0, 1, "{$div}{$cl}M", $div, "{$cl}M");
            InsertClassEvent($TourId, 0, 1, "{$div}{$cl}W", $div, "{$cl}W");
            InsertClassEvent($TourId, 1, 3, "{$div}{$cl}M", $div, "{$cl}M");
            InsertClassEvent($TourId, 1, 3, "{$div}{$cl}W", $div, "{$cl}W");
            InsertClassEvent($TourId, 1, 1, "{$div}{$cl}X",  $div,  "{$cl}W");
            InsertClassEvent($TourId, 2, 1, "{$div}{$cl}X",  $div,  "{$cl}M");
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

/*

Run Archery DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-Run.php');

