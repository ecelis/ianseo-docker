<?php

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = '';
if(empty($SubRule)) {
    $SubRule='1';
}

function CreateStandardDivisions($TourId, $SubRule, $Type='FITA') {
	$i=1;
	if($SubRule=='1') {
        CreateDivision($TourId, $i++, 'R', 'Recurve Open', '1', 'R', 'R', 1);
        CreateDivision($TourId, $i++, 'C', 'Compound Open', '1', 'C', 'C', 1);
    } elseif($SubRule=='2') {
        CreateDivision($TourId, $i++, 'RO', 'Recurve Open', '1', 'R', 'R', 1);
        CreateDivision($TourId, $i++, 'CO', 'Compound Open', '1', 'C', 'C', 1);
        CreateDivision($TourId, $i++, 'R', 'Recurve', '1', 'R', 'R', 0);
        CreateDivision($TourId, $i++, 'C', 'Compound', '1', 'C', 'C', 0);
    }
	CreateDivision($TourId, $i++, 'W1', 'W1 Open','1','W1','W1',1);
	CreateDivision($TourId, $i++, 'VI', 'Visually Impaired','1','VI','VI',1);
}

function CreateStandardClasses($TourId, $SubRule) {
	CreateClass($TourId, 1, 1,100, 0, 'M', 'M', 'Men', 1, ($SubRule==2 ? 'RO,CO,' : '') . 'C,R,W1','M','M',1);
	CreateClass($TourId, 2, 1,100, 1, 'W', 'W', 'Women', 1, ($SubRule==2 ? 'RO,CO,' : '') . 'C,R,W1','W','W',1);
	CreateClass($TourId, 3, 1,20, 0, 'U21M', 'U21M,M', 'Under 21 Men', 1, ($SubRule==2 ? 'RO,CO,' : '') . 'C,R,W1','U21M','U21M',1);
	CreateClass($TourId, 4, 1,20, 1, 'U21W', 'U21W,W', 'Under 21 Women', 1, ($SubRule==2 ? 'RO,CO,' : '') . 'C,R,W1','U21W','U21W',1);
	CreateClass($TourId, 5, 1,100, -1, '1', '1', '1', 1, 'VI','1','1',1);
	CreateClass($TourId, 6, 1,100, -1, '23', '23', '2/3', 1, 'VI','23','23',1);
	CreateClass($TourId, 7, 1,100, -1, '1U21', '1,1U21', '1 Under 21', 1, 'VI','1U21','1U21',1);
	CreateClass($TourId, 8, 1,100, -1, '23U21', '23,23U21', '2/3 Under 21', 1, 'VI','23U21','23U21',1);
}

function CreateStandardEvents($TourId, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
    $TargetW1=($Outdoor?5:4);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
	$TargetSizeV=($Outdoor ? 80 : 60);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
	$DistanceV=($Outdoor ? 30 : 18);

	$Settings=array(
		'EvElimEnds'=>5,
		'EvElimArrows'=>3,
		'EvElimSO'=>1,
		'EvFinEnds'=>5,
		'EvFinArrows'=>3,
		'EvFinSO'=>1,
		'EvFinalAthTarget'=>240,
		'EvMatchArrowsNo'=>240,
		'EvIsPara'=>1,
		'EvMatchMode'=>1,
		'EvFinalFirstPhase' => 16,
		'EvFinalTargetType'=>$TargetR,
		'EvTargetSize'=>$TargetSizeR,
		'EvDistance'=>$DistanceR,
	);


	$i=1;
	CreateEventNew($TourId, 'RMO', 'Recurve Men Open', $i++, $Settings);
	CreateEventNew($TourId, 'RWO', 'Recurve Women Open', $i++, $Settings);
	CreateEventNew($TourId, 'RU21MO', 'Recurve U21 Men Open', $i++, $Settings);
	CreateEventNew($TourId, 'RU21WO', 'Recurve U21 Women Open', $i++, $Settings);
	if($SubRule=='2') {
        $Settings['EvIsPara']=0;
        CreateEventNew($TourId, 'RM', 'Recurve Men', $i++, $Settings);
        CreateEventNew($TourId, 'RW', 'Recurve Women', $i++, $Settings);
        CreateEventNew($TourId, 'RU21M', 'Recurve U21 Men', $i++, $Settings);
        CreateEventNew($TourId, 'RU21W', 'Recurve U21 Women', $i++, $Settings);
        $Settings['EvIsPara']=1;
    }
	$Settings['EvMatchMode']=0;
	$Settings['EvFinalFirstPhase']=32;
	$Settings['EvFinalTargetType']=$TargetC;
	$Settings['EvTargetSize']=$TargetSizeC;
	$Settings['EvDistance']=$DistanceC;
	CreateEventNew($TourId, 'CMO', 'Compound Men Open', $i++, $Settings);
    $Settings['EvFinalFirstPhase']=16;
    CreateEventNew($TourId, 'CWO', 'Compound Women Open', $i++, $Settings);
    $Settings['EvFinalFirstPhase']=32;
	CreateEventNew($TourId, 'CU21MO', 'Compound U21 Men Open', $i++, $Settings);
	$Settings['EvFinalFirstPhase']=16;
	CreateEventNew($TourId, 'CU21WO', 'Compound U21 Women Open', $i++, $Settings);
    if($SubRule=='2') {
        $Settings['EvIsPara']=0;
        $Settings['EvFinalFirstPhase']=32;
        CreateEventNew($TourId, 'CM', 'Compound Men', $i++, $Settings);
        CreateEventNew($TourId, 'CU21M', 'Compound U21 Men', $i++, $Settings);
        $Settings['EvFinalFirstPhase']=16;
        CreateEventNew($TourId, 'CW', 'Compound Women', $i++, $Settings);
        CreateEventNew($TourId, 'CU21W', 'Compound U21 Women', $i++, $Settings);
        $Settings['EvIsPara']=1;
    }
	$Settings['EvFinalFirstPhase']=8;
	$Settings['EvFinalTargetType']=$TargetW1;
	CreateEventNew($TourId, 'MW1', 'Men W1 Open', $i++, $Settings);
	CreateEventNew($TourId, 'WW1', 'Women W1 Open', $i++, $Settings);
	CreateEventNew($TourId, 'U21MW1', 'U21 Men W1 Open', $i++, $Settings);
	CreateEventNew($TourId, 'U21WW1', 'U21 Women W1 Open', $i++, $Settings);
	$Settings['EvMatchMode']=1;
	$Settings['EvFinalFirstPhase']=2;
	$Settings['EvFinalTargetType']=$TargetR;
	$Settings['EvTargetSize']=$TargetSizeV;
	$Settings['EvDistance']=$DistanceV;
	CreateEventNew($TourId, 'VI1', 'Visually Impaired 1', $i++, $Settings);
	CreateEventNew($TourId, 'VI23', 'Visually Impaired 2/3', $i++, $Settings);
	CreateEventNew($TourId, 'VI1U21', 'Visually Impaired 1 U21', $i++, $Settings);
	CreateEventNew($TourId, 'VI23U21', 'Visually Impaired 2/3 U21', $i++, $Settings);
	//Team
    $i=1;
	$Settings['EvTeamEvent']=1;
	$Settings['EvFinalAthTarget']=0;
	$Settings['EvElimEnds']=4;
	$Settings['EvElimArrows']=4;
	$Settings['EvElimSO']=2;
	$Settings['EvFinEnds']=4;
	$Settings['EvFinArrows']=4;
	$Settings['EvFinSO']=2;
	$Settings['EvMatchArrowsNo']=0;
	$Settings['EvMatchMode']=1;
	$Settings['EvFinalFirstPhase']=8;
	$Settings['EvFinalTargetType']=$TargetR;
	$Settings['EvTargetSize']=$TargetSizeR;
	$Settings['EvDistance']=$DistanceR;
	CreateEventNew($TourId, 'RMO', 'Recurve Men Open Doubles', $i++, $Settings);
	CreateEventNew($TourId, 'RWO', 'Recurve Women Open Doubles', $i++, $Settings);
	CreateEventNew($TourId, 'RU21MO', 'Recurve U21 Men Open Doubles', $i++, $Settings);
	CreateEventNew($TourId, 'RU21WO', 'Recurve U21 Women Open Doubles', $i++, $Settings);
    if($SubRule=='2') {
        $Settings['EvIsPara']=0;
        $Settings['EvElimArrows']=6;
        $Settings['EvElimSO']=3;
        $Settings['EvFinArrows']=6;
        $Settings['EvFinSO']=3;
        CreateEventNew($TourId, 'RM', 'Recurve Men Team', $i++, $Settings);
        CreateEventNew($TourId, 'RW', 'Recurve Women Team', $i++, $Settings);
        CreateEventNew($TourId, 'RU21M', 'Recurve U21 Men Team', $i++, $Settings);
        CreateEventNew($TourId, 'RU21W', 'Recurve U21 Women Team', $i++, $Settings);
        $Settings['EvIsPara']=1;
        $Settings['EvElimArrows']=4;
        $Settings['EvElimSO']=2;
        $Settings['EvFinArrows']=4;
        $Settings['EvFinSO']=2;
    }
	if($Outdoor) {
		$Settings['EvMixedTeam']=1;
		CreateEventNew($TourId, 'RXO', 'Recurve Open Mixed Team', $i++, $Settings);
		CreateEventNew($TourId, 'RU21XO', 'Recurve Open U21 Mixed Team', $i++, $Settings);
        if($SubRule=='2') {
            $Settings['EvIsPara']=0;
            CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $Settings);
            CreateEventNew($TourId, 'RU21X', 'Recurve U21 Mixed Team', $i++, $Settings);
            $Settings['EvIsPara']=1;
        }
        $Settings['EvMixedTeam']=0;
	}
    $Settings['EvMatchMode']=0;
    $Settings['EvTargetSize']=$TargetSizeC;
    $Settings['EvDistance']=$DistanceC;
    $Settings['EvFinalTargetType']=$TargetC;
    CreateEventNew($TourId, 'CMO', 'Compound Men Open Doubles', $i++, $Settings);
    CreateEventNew($TourId, 'CWO', 'Compound Women Open Doubles', $i++, $Settings);
    CreateEventNew($TourId, 'CU21MO', 'Compound U21 Men Open Doubles', $i++, $Settings);
    CreateEventNew($TourId, 'CU21WO', 'Compound U21 Women Open Doubles', $i++, $Settings);
    if($SubRule=='2') {
        $Settings['EvIsPara']=0;
        $Settings['EvElimArrows']=6;
        $Settings['EvElimSO']=3;
        $Settings['EvFinArrows']=6;
        $Settings['EvFinSO']=3;
        CreateEventNew($TourId, 'CM', 'Compound Men Team', $i++, $Settings);
        CreateEventNew($TourId, 'CW', 'Compound Women Team', $i++, $Settings);
        CreateEventNew($TourId, 'CU21M', 'Compound U21 Men Team', $i++, $Settings);
        CreateEventNew($TourId, 'CU21W', 'Compound U21 Women Team', $i++, $Settings);
        $Settings['EvIsPara']=1;
        $Settings['EvElimArrows']=4;
        $Settings['EvElimSO']=2;
        $Settings['EvFinArrows']=4;
        $Settings['EvFinSO']=2;
    }
    if($Outdoor) {
        $Settings['EvMixedTeam']=1;
        CreateEventNew($TourId, 'CXO', 'Compound Open Mixed Team', $i++, $Settings);
        CreateEventNew($TourId, 'CU21XO', 'Compound U21 Open Mixed Team', $i++, $Settings);
        if($SubRule=='2') {
            $Settings['EvIsPara']=0;
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $Settings);
            CreateEventNew($TourId, 'CU21X', 'Compound U21 Mixed Team', $i++, $Settings);
        }
    }

	$Settings['EvFinalTargetType']=$TargetW1;
	CreateEventNew($TourId, 'MW1', 'Men W1 Doubles', $i++, $Settings);
	CreateEventNew($TourId, 'WW1', 'Women W1 Doubles', $i++, $Settings);
	CreateEventNew($TourId, 'U21MW1', 'U21 Men W1 Doubles', $i++, $Settings);
	CreateEventNew($TourId, 'U21WW1', 'U21 Women W1 Doubles', $i++, $Settings);
	if($Outdoor) {
		$Settings['EvMixedTeam']=1;
		CreateEventNew($TourId, 'W1X', 'W1 Mixed Team', $i++, $Settings);
		CreateEventNew($TourId, 'U21W1X', 'U21 W1 Mixed Team', $i++, $Settings);
		$Settings['EvMixedTeam']=0;
	}

}

function InsertStandardEvents($TourId, $SubRule, $Outdoor=true) {
    if($SubRule=='1') {
	    InsertClassEvent($TourId, 0, 1, 'RMO', 'R', 'M');
	    InsertClassEvent($TourId, 0, 1, 'RWO', 'R', 'W');
        InsertClassEvent($TourId, 0, 1, 'CMO', 'C', 'M');
        InsertClassEvent($TourId, 0, 1, 'CWO', 'C', 'W');
	    InsertClassEvent($TourId, 0, 1, 'RU21MO', 'R', 'U21M');
	    InsertClassEvent($TourId, 0, 1, 'RU21WO', 'R', 'U21W');
        InsertClassEvent($TourId, 0, 1, 'CU21MO', 'C', 'U21M');
        InsertClassEvent($TourId, 0, 1, 'CU21WO', 'C', 'U21W');
	} else if($SubRule=='2') {
        InsertClassEvent($TourId, 0, 1, 'RMO', 'RO', 'M');
        InsertClassEvent($TourId, 0, 1, 'RWO', 'RO', 'W');
        InsertClassEvent($TourId, 0, 1, 'CMO', 'CO', 'M');
        InsertClassEvent($TourId, 0, 1, 'CWO', 'CO', 'W');
        InsertClassEvent($TourId, 0, 1, 'RM', 'R', 'M');
        InsertClassEvent($TourId, 0, 1, 'RW', 'R', 'W');
        InsertClassEvent($TourId, 0, 1, 'CM', 'C', 'M');
        InsertClassEvent($TourId, 0, 1, 'CW', 'C', 'W');
        InsertClassEvent($TourId, 0, 1, 'RU21MO', 'RO', 'U21M');
        InsertClassEvent($TourId, 0, 1, 'RU21WO', 'RO', 'U21W');
        InsertClassEvent($TourId, 0, 1, 'CU21MO', 'CO', 'U21M');
        InsertClassEvent($TourId, 0, 1, 'CU21WO', 'CO', 'U21W');
        InsertClassEvent($TourId, 0, 1, 'RU21M', 'R', 'U21M');
        InsertClassEvent($TourId, 0, 1, 'RU21W', 'R', 'U21W');
        InsertClassEvent($TourId, 0, 1, 'CU21M', 'C', 'U21M');
        InsertClassEvent($TourId, 0, 1, 'CU21W', 'C', 'U21W');
    }
	InsertClassEvent($TourId, 0, 1, 'MW1', 'W1', 'M');
	InsertClassEvent($TourId, 0, 1, 'WW1', 'W1', 'W');
	InsertClassEvent($TourId, 0, 1, 'VI1', 'VI', '1');
	InsertClassEvent($TourId, 0, 1, 'VI23', 'VI', '23');
	InsertClassEvent($TourId, 0, 1, 'U21MW1', 'W1', 'U21M');
	InsertClassEvent($TourId, 0, 1, 'U21WW1', 'W1', 'U21W');
	InsertClassEvent($TourId, 0, 1, 'VI1U21', 'VI', '1U21');
	InsertClassEvent($TourId, 0, 1, 'VI23U21', 'VI', '23U21');
    if($SubRule=='1') {
        InsertClassEvent($TourId, 1, 2, 'RMO', 'R', 'M');
        InsertClassEvent($TourId, 1, 2, 'RWO', 'R', 'W');
        InsertClassEvent($TourId, 1, 2, 'CMO', 'C', 'M');
        InsertClassEvent($TourId, 1, 2, 'CWO', 'C', 'W');
        InsertClassEvent($TourId, 1, 2, 'RU21MO', 'R', 'U21M');
        InsertClassEvent($TourId, 1, 2, 'RU21WO', 'R', 'U21W');
        InsertClassEvent($TourId, 1, 2, 'CU21MO', 'C', 'U21M');
        InsertClassEvent($TourId, 1, 2, 'CU21WO', 'C', 'U21W');
    } elseif($SubRule=='2') {
        InsertClassEvent($TourId, 1, 2, 'RMO', 'RO', 'M');
        InsertClassEvent($TourId, 1, 2, 'RWO', 'RO', 'W');
        InsertClassEvent($TourId, 1, 2, 'CMO', 'CO', 'M');
        InsertClassEvent($TourId, 1, 2, 'CWO', 'CO', 'W');
        InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'M');
        InsertClassEvent($TourId, 1, 3, 'RW', 'R', 'W');
        InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'M');
        InsertClassEvent($TourId, 1, 3, 'CW', 'C', 'W');
        InsertClassEvent($TourId, 1, 2, 'RU21MO', 'RO', 'U21M');
        InsertClassEvent($TourId, 1, 2, 'RU21WO', 'RO', 'U21W');
        InsertClassEvent($TourId, 1, 2, 'CU21MO', 'CO', 'U21M');
        InsertClassEvent($TourId, 1, 2, 'CU21WO', 'CO', 'U21W');
        InsertClassEvent($TourId, 1, 3, 'RU21M', 'R', 'U21M');
        InsertClassEvent($TourId, 1, 3, 'RU21W', 'R', 'U21W');
        InsertClassEvent($TourId, 1, 3, 'CU21M', 'C', 'U21M');
        InsertClassEvent($TourId, 1, 3, 'CU21W', 'C', 'U21W');
    }
	InsertClassEvent($TourId, 1, 2, 'MW1', 'W1', 'M');
	InsertClassEvent($TourId, 1, 2, 'WW1', 'W1', 'W');
	InsertClassEvent($TourId, 1, 2, 'U21MW1', 'W1', 'U21M');
	InsertClassEvent($TourId, 1, 2, 'U21WW1', 'W1', 'U21W');
	if($Outdoor) {
        if($SubRule=='1') {
            InsertClassEvent($TourId, 1, 1, 'RXO', 'R', 'W');
            InsertClassEvent($TourId, 2, 1, 'RXO', 'R', 'M');
            InsertClassEvent($TourId, 1, 1, 'CXO', 'C', 'W');
            InsertClassEvent($TourId, 2, 1, 'CXO', 'C', 'M');
            InsertClassEvent($TourId, 1, 1, 'RU21XO', 'R', 'U21W');
            InsertClassEvent($TourId, 2, 1, 'RU21XO', 'R', 'U21M');
            InsertClassEvent($TourId, 1, 1, 'CU21XO', 'C', 'U21W');
            InsertClassEvent($TourId, 2, 1, 'CU21XO', 'C', 'U21M');
        } elseif($SubRule=='2') {
            InsertClassEvent($TourId, 1, 1, 'RX', 'R', 'W');
            InsertClassEvent($TourId, 2, 1, 'RX', 'R', 'M');
            InsertClassEvent($TourId, 1, 1, 'CX', 'C', 'W');
            InsertClassEvent($TourId, 2, 1, 'CX', 'C', 'M');
            InsertClassEvent($TourId, 1, 1, 'RXO', 'RO', 'M');
            InsertClassEvent($TourId, 2, 1, 'RXO', 'RO', 'W');
            InsertClassEvent($TourId, 1, 1, 'CXO', 'CO', 'W');
            InsertClassEvent($TourId, 2, 1, 'CXO', 'CO', 'M');
            InsertClassEvent($TourId, 1, 1, 'RU21X', 'R', 'U21W');
            InsertClassEvent($TourId, 2, 1, 'RU21X', 'R', 'U21M');
            InsertClassEvent($TourId, 1, 1, 'CU21X', 'C', 'U21W');
            InsertClassEvent($TourId, 2, 1, 'CU21X', 'C', 'U21M');
            InsertClassEvent($TourId, 1, 1, 'RU21XO', 'RO', 'U21M');
            InsertClassEvent($TourId, 2, 1, 'RU21XO', 'RO', 'U21W');
            InsertClassEvent($TourId, 1, 1, 'CU21XO', 'CO', 'U21W');
            InsertClassEvent($TourId, 2, 1, 'CU21XO', 'CO', 'U21M');
        }
		InsertClassEvent($TourId, 1, 1, 'W1X', 'W1', 'W');
		InsertClassEvent($TourId, 2, 1, 'W1X', 'W1', 'M');
		InsertClassEvent($TourId, 1, 1, 'U21W1X', 'W1', 'U21W');
		InsertClassEvent($TourId, 2, 1, 'U21W1X', 'W1', 'U21M');
	}
}

