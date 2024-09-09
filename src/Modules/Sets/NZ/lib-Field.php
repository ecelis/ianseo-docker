<?php

/*

FIELD DEFINITIONS (Target Tournaments)

*/

// creation of standard NZ field tournament competition classes
function CreateStandardFieldClasses($TourId, $SubRule) {
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

// creation of standard NZ field matchplay competition events
function CreateStandardFieldEvents($TourId, $SubRule) {
	$SettingsInd=array(
		'EvFinalFirstPhase' => '2',
		'EvFinalTargetType'=>TGT_FIELD,
		'EvElimEnds'=>12,
		'EvElimArrows'=>3,
		'EvElimSO'=>1,
		'EvFinEnds'=>4,
		'EvFinArrows'=>3,
		'EvFinSO'=>1,
		'EvElimType'=>2,
		'EvElim1'=>16,
		'EvE1Ends'=>12,
		'EvE1Arrows'=>3,
		'EvE1SO'=>1,
		'EvElim2'=>8,
		'EvE2Ends'=>8,
		'EvE2Arrows'=>3,
		'EvE2SO'=>1,
		'EvFinalAthTarget'=>0,
		'EvMatchArrowsNo'=>0,
	);
	switch($SubRule) {
		case '1':
		case '2':
			$SettingsTeam=array(
				'EvTeamEvent' => '1',
				'EvFinalFirstPhase' => '4',
				'EvFinalTargetType'=>TGT_FIELD,
				'EvElimEnds'=>8,
				'EvElimArrows'=>3,
				'EvElimSO'=>3,
				'EvFinEnds'=>4,
				'EvFinArrows'=>3,
				'EvFinSO'=>3,
				'EvFinalAthTarget'=>15,
				'EvMatchArrowsNo'=>FINAL_FROM_2,
			);
			$SettingsMixedTeam=array(
				'EvTeamEvent' => '1',
				'EvMixedTeam' => '1',
				'EvFinalFirstPhase' => '4',
				'EvFinalTargetType'=>TGT_FIELD,
				'EvElimEnds'=>8,
				'EvElimArrows'=>4,
				'EvElimSO'=>2,
				'EvFinEnds'=>4,
				'EvFinArrows'=>4,
				'EvFinSO'=>2,
				'EvFinalAthTarget'=>15,
				'EvMatchArrowsNo'=>FINAL_FROM_2,
			);

			$i=1;
			CreateEventNew($TourId,'RM',  'Recurve Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'RW',  'Recurve Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'RU21M', 'Recurve Under 21 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'RU21W', 'Recurve Under 21 Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'RU18M', 'Recurve Under 18 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'RU18W', 'Recurve Under 18 Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'R50M', 'Recurve 50+ Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'R50W', 'Recurve 50+ Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'CM',  'Compound Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'CW',  'Compound Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU21M', 'Compound Under 21 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU21W', 'Compound Under 21 Women',$i++, $SettingsInd);
			CreateEventNew($TourId,'CU18M', 'Compound Under 18 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU18W', 'Compound Under 18 Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'C50M', 'Compound 50+ Men',  $i++, $SettingsInd);
			CreateEventNew($TourId,'C50W', 'Compound 50+ Women',$i++, $SettingsInd);
			CreateEventNew($TourId,'BM',  'Barebow Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'BW',  'Barebow Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'B50M', 'Barebow 50+ Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'B50W', 'Barebow 50+ Women', $i++, $SettingsInd);
			break;
		case '3':
			$i=1;
			CreateEventNew($TourId,'RU21M', 'Recurve Under 21 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'RU21W', 'Recurve Under 21 Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'RU18M', 'Recurve Under 18 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'RU18W', 'Recurve Under 18 Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU21M', 'Compound Under 21 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU21W', 'Compound Under 21 Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU18M', 'Compound Under 18 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'CU18W', 'Compound Under 18 Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'BU21M', 'Barebow Under 21 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'BU21W', 'Barebow Under 21 Women', $i++, $SettingsInd);
			CreateEventNew($TourId,'BU18M', 'Barebow Under 18 Men', $i++, $SettingsInd);
			CreateEventNew($TourId,'BU18W', 'Barebow Under 18 Women', $i++, $SettingsInd);
			$i=1;
			CreateEventNew($TourId,'RYB',  'Recurve Youth Boys', $i++, $SettingsInd);
			CreateEventNew($TourId,'RYG',  'Recurve Youth Girls', $i++, $SettingsInd);
			CreateEventNew($TourId,'CYB', 'Compound Youth Boys', $i++, $SettingsInd);
			CreateEventNew($TourId,'CYG', 'Compound Youth Girls', $i++, $SettingsInd);
			break;
	}
}

function InsertStandardFieldEvents($TourId, $SubRule) {
	switch ($SubRule) {
		case '1':
		case '2':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RU21M', 'R', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'RU18M', 'R', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'R50M', 'R', '50M');
			InsertClassEvent($TourId, 0, 1, 'R50M', 'R', '65M');
			InsertClassEvent($TourId, 0, 1, 'RW',  'R',  'W');
			InsertClassEvent($TourId, 0, 1, 'RU21W', 'R', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'RU18W', 'R', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'R50W', 'R', '50W');
			InsertClassEvent($TourId, 0, 1, 'R50W', 'R', '65W');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CU21M', 'C', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'CU18M', 'C', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'C50M', 'C', '50M');
			InsertClassEvent($TourId, 0, 1, 'C50M', 'C', '65M');
			InsertClassEvent($TourId, 0, 1, 'CW',  'C',  'W');
			InsertClassEvent($TourId, 0, 1, 'CU21W', 'C', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'CU18W', 'C', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'C50W', 'C', '50W');
			InsertClassEvent($TourId, 0, 1, 'C50W', 'C', '65W');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'B50M', 'B', '50M');
			InsertClassEvent($TourId, 0, 1, 'B50M', 'B', '65M');
			InsertClassEvent($TourId, 0, 1, 'BW',  'B',  'W');
			InsertClassEvent($TourId, 0, 1, 'B50W', 'B', '50W');
			InsertClassEvent($TourId, 0, 1, 'B50W', 'B', '65W');
			InsertClassEvent($TourId, 0, 1, 'BM',  'L',  'M');
			InsertClassEvent($TourId, 0, 1, 'B50M', 'L', '50M');
			InsertClassEvent($TourId, 0, 1, 'B50M', 'L', '65M');
			InsertClassEvent($TourId, 0, 1, 'BW',  'L',  'W');
			InsertClassEvent($TourId, 0, 1, 'B50W', 'L', '50W');
			InsertClassEvent($TourId, 0, 1, 'B50W', 'L', '65W');
			break;
		case '3':
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'R', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'R', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'R', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'R', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'R', 'U11B');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'R', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'R', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'R', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'R', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'R', 'U11G');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'B', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'B', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'B', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'B', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'B', 'U11B');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'B', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'B', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'B', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'B', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'B', 'U11G');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'L', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'L', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'L', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'L', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'RU14B', 'L', 'U11B');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'L', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'L', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'L', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'L', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'RU14G', 'L', 'U11G');
			InsertClassEvent($TourId, 0, 1, 'CU14B', 'C', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'CU14B', 'C', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'CU14B', 'C', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'CU14B', 'C', 'U14B');
			InsertClassEvent($TourId, 0, 1, 'CU14B', 'C', 'U11B');
			InsertClassEvent($TourId, 0, 1, 'CU14G', 'C', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'CU14G', 'C', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'CU14G', 'C', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'CU14G', 'C', 'U14G');
			InsertClassEvent($TourId, 0, 1, 'CU14G', 'C', 'U11G');
			break;
	}
}

function InsertStandardFieldEliminations($TourId, $SubRule){
	$cls=array();
	switch($SubRule) {
		case '1':
		case '2':
			$cls=array('M', 'W', 'U21M', 'U21W', 'U18M', 'U18W', '50M', '50W');
			break;
		case '3':
			$cls=array('U21M', 'U21W', 'U18M', 'U18W', 'U16M', 'U16W', 'U14M', 'U14W');
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

?>
