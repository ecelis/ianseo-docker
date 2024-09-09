<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/

function CreateRunArcheryDivisions($TourId, $Type='RUN') {
	$i=1;
	CreateDivision($TourId, $i++, 'RA', 'Run Archery');
}

function CreateRunArcheryClasses($TourId, $SubRule, $Type='FITA') {
	switch($SubRule) {
		case '1':
			CreateClass($TourId, 1, 21, 49, 0, 'M', 'M', 'Men');
			CreateClass($TourId, 2, 21, 49, 1, 'W', 'W', 'Women');
			CreateClass($TourId, 3, 18, 20, 0, 'U21M', 'U21M,M', 'Under 21 Men');
			CreateClass($TourId, 4, 18, 20, 1, 'U21W', 'U21W,W', 'Under 21 Women');
			CreateClass($TourId, 5,  1, 17, 0, 'U18M', 'U18M,U21M,M', 'Under 18 Men');
			CreateClass($TourId, 6,  1, 17, 1, 'U18W', 'U18W,U21W,W', 'Under 18 Women');
			CreateClass($TourId, 7, 50,100, 0, '50M', '50M,M', '50+ Men');
			CreateClass($TourId, 8, 50,100, 1, '50W', '50W,W', '50+ Women');
			break;
	}
}

function CreateRunArcheryEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			$i=1;
			$Options=[
				'EvTeamEvent' => 0,
				'EvMixedTeam'=>0,
				'EvFinalFirstPhase' => 0,
				'EvFinalTargetType'=>7,
				'EvE1Arrows'=>4,
				'EvFinEnds'=>3,
				'EvFinArrows'=>6,
				'EvTargetSize'=>400,
				'EvDistance'=>60,
				'EvElimType'=>1,
				'EvMultiTeam'=>1,
				'EvArrowPenalty'=>45,
				'EvLoopPenalty'=>45,
			];
			CreateEventNew($TourId, 'SPM',  'Sprint Men', $i++, $Options);
			CreateEventNew($TourId, 'SPW',  'Sprint Women', $i++, $Options);
			// CreateEventNew($TourId, 'SPU21M',  'Sprint Under 21 Men', $i++, $Options);
			// CreateEventNew($TourId, 'SPU21W',  'Sprint Under 21 Women', $i++, $Options);
			// CreateEventNew($TourId, 'SPU18M',  'Sprint Under 18 Men', $i++, $Options);
			// CreateEventNew($TourId, 'SPU18W',  'Sprint Under 18 Women', $i++, $Options);
			// CreateEventNew($TourId, 'SP50M',  'Sprint 50+ Men', $i++, $Options);
			// CreateEventNew($TourId, 'SP50W',  'Sprint 50+ Women', $i++, $Options);
			$Options['EvFinEnds']=4;
			$Options['EvFinArrows']=4;
			$Options['EvTargetSize']=1000;
			$Options['EvDistance']=150;
			$Options['EvElimType']=0;
			$Options['EvArrowPenalty']=120;
			$Options['EvLoopPenalty']=120;
			CreateEventNew($TourId, '4KM',  '4K Men', $i++, $Options);
			CreateEventNew($TourId, '4KW',  '4K Women', $i++, $Options);
			// CreateEventNew($TourId, '4KU21M',  '4K Under 21 Men', $i++, $Options);
			// CreateEventNew($TourId, '4KU21W',  '4K Under 21 Women', $i++, $Options);
			// CreateEventNew($TourId, '4KU18M',  '4K Under 18 Men', $i++, $Options);
			// CreateEventNew($TourId, '4KU18W',  '4K Under 18 Women', $i++, $Options);
			// CreateEventNew($TourId, '4K50M',  '4K 50+ Men', $i++, $Options);
			// CreateEventNew($TourId, '4K50W',  '4K 50+ Women', $i++, $Options);
			$i=1;
			$Options=[
				'EvTeamEvent' => 1,
				'EvMixedTeam'=>0,
				'EvFinalFirstPhase' => 0,
				'EvFinalTargetType'=>7,
				'EvE1Arrows'=>4,
				'EvFinEnds'=>3,
				'EvFinArrows'=>6,
				'EvTargetSize'=>400,
				'EvDistance'=>60,
				'EvElimType'=>0,
				'EvArrowPenalty'=>45,
				'EvLoopPenalty'=>45,
			];
			CreateEventNew($TourId, 'SPTM',  'Team Sprint Men', $i++, $Options);
			CreateEventNew($TourId, 'SPTW',  'Team Sprint Women', $i++, $Options);
			$Options['EvMixedTeam']=1;
			$Options['EvFinEnds']=2;
			CreateEventNew($TourId, 'SPTX',  'Mixed Team Sprint', $i++, $Options);
			$Options['EvMixedTeam']=0;
			$Options['EvFinEnds']=6;
			CreateEventNew($TourId, 'DBLSPTM',  'Team Double Sprint Men', $i++, $Options);
			CreateEventNew($TourId, 'DBLSPTW',  'Team Double Sprint Women', $i++, $Options);
			$Options['EvMixedTeam']=1;
			$Options['EvFinEnds']=4;
			CreateEventNew($TourId, 'DBLSPTX',  'Mixed Team Double Sprint', $i++, $Options);
			break;
	}
}

function InsertRunArcheryEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
			InsertClassEvent($TourId, 0, 1, 'SPM', 'RA', 'M');
			InsertClassEvent($TourId, 0, 1, 'SPM', 'RA', 'U21M');
			InsertClassEvent($TourId, 0, 1, 'SPM', 'RA', 'U18M');
			InsertClassEvent($TourId, 0, 1, 'SPM', 'RA', '50M');
			// InsertClassEvent($TourId, 0, 1, 'SPU21M', 'RA', 'U21M');
			// InsertClassEvent($TourId, 0, 1, 'SPU18M', 'RA', 'U18M');
			// InsertClassEvent($TourId, 0, 1, 'SP50M', 'RA', '50M');
			InsertClassEvent($TourId, 0, 1, 'SPW', 'RA', 'W');
			InsertClassEvent($TourId, 0, 1, 'SPW', 'RA', 'U21W');
			InsertClassEvent($TourId, 0, 1, 'SPW', 'RA', 'U18W');
			InsertClassEvent($TourId, 0, 1, 'SPW', 'RA', '50W');
			// InsertClassEvent($TourId, 0, 1, 'SPU21W', 'RA', 'U21W');
			// InsertClassEvent($TourId, 0, 1, 'SPU18W', 'RA', 'U18W');
			// InsertClassEvent($TourId, 0, 1, 'SP50W', 'RA', '50W');
			InsertClassEvent($TourId, 0, 1, '4KM', 'RA', 'M');
			InsertClassEvent($TourId, 0, 1, '4KM', 'RA', 'U21M');
			InsertClassEvent($TourId, 0, 1, '4KM', 'RA', 'U18M');
			InsertClassEvent($TourId, 0, 1, '4KM', 'RA', '50M');
			// InsertClassEvent($TourId, 0, 1, '4KU21M', 'RA', 'U21M');
			// InsertClassEvent($TourId, 0, 1, '4KU18M', 'RA', 'U18M');
			// InsertClassEvent($TourId, 0, 1, '4K50M', 'RA', '50M');
			InsertClassEvent($TourId, 0, 1, '4KW', 'RA', 'W');
			InsertClassEvent($TourId, 0, 1, '4KW', 'RA', 'U21W');
			InsertClassEvent($TourId, 0, 1, '4KW', 'RA', 'U18W');
			InsertClassEvent($TourId, 0, 1, '4KW', 'RA', '50W');
			// InsertClassEvent($TourId, 0, 1, '4KU21W', 'RA', 'U21W');
			// InsertClassEvent($TourId, 0, 1, '4KU18W', 'RA', 'U18W');
			// InsertClassEvent($TourId, 0, 1, '4K50W', 'RA', '50W');

			InsertClassEvent($TourId, 1, 3, 'SPTM', 'RA', 'M');
			InsertClassEvent($TourId, 1, 3, 'SPTM', 'RA', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'SPTM', 'RA', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'SPTM', 'RA', '50M');
			InsertClassEvent($TourId, 1, 3, 'SPTW', 'RA', 'W');
			InsertClassEvent($TourId, 1, 3, 'SPTW', 'RA', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'SPTW', 'RA', 'U18W');
			InsertClassEvent($TourId, 1, 3, 'SPTW', 'RA', '50W');
			InsertClassEvent($TourId, 1, 1, 'SPTX', 'RA', 'W');
			InsertClassEvent($TourId, 1, 1, 'SPTX', 'RA', 'U21W');
			InsertClassEvent($TourId, 1, 1, 'SPTX', 'RA', 'U18W');
			InsertClassEvent($TourId, 1, 1, 'SPTX', 'RA', '50W');
			InsertClassEvent($TourId, 2, 1, 'SPTX', 'RA', 'M');
			InsertClassEvent($TourId, 2, 1, 'SPTX', 'RA', 'U21M');
			InsertClassEvent($TourId, 2, 1, 'SPTX', 'RA', 'U18M');
			InsertClassEvent($TourId, 2, 1, 'SPTX', 'RA', '50M');
			InsertClassEvent($TourId, 1, 3, 'DBLSPTM', 'RA', 'M');
			InsertClassEvent($TourId, 1, 3, 'DBLSPTM', 'RA', 'U21M');
			InsertClassEvent($TourId, 1, 3, 'DBLSPTM', 'RA', 'U18M');
			InsertClassEvent($TourId, 1, 3, 'DBLSPTM', 'RA', '50M');
			InsertClassEvent($TourId, 1, 3, 'DBLSPTW', 'RA', 'W');
			InsertClassEvent($TourId, 1, 3, 'DBLSPTW', 'RA', 'U21W');
			InsertClassEvent($TourId, 1, 3, 'DBLSPTW', 'RA', 'U18W');
			InsertClassEvent($TourId, 1, 3, 'DBLSPTW', 'RA', '50W');
			InsertClassEvent($TourId, 1, 1, 'DBLSPTX', 'RA', 'W');
			InsertClassEvent($TourId, 1, 1, 'DBLSPTX', 'RA', 'U21W');
			InsertClassEvent($TourId, 1, 1, 'DBLSPTX', 'RA', 'U18W');
			InsertClassEvent($TourId, 1, 1, 'DBLSPTX', 'RA', '50W');
			InsertClassEvent($TourId, 2, 1, 'DBLSPTX', 'RA', 'M');
			InsertClassEvent($TourId, 2, 1, 'DBLSPTX', 'RA', 'U21M');
			InsertClassEvent($TourId, 2, 1, 'DBLSPTX', 'RA', 'U18M');
			InsertClassEvent($TourId, 2, 1, 'DBLSPTX', 'RA', '50M');
			break;
	}
}
