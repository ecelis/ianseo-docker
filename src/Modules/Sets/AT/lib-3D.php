<?php

/*

3D DEFINITIONS (Target Tournaments)

*/

function CreateStandard3DEvents($TourId, $SubRule) {
	switch($SubRule) {
		case '1':
		case '2':
			$SettingsInd=array(
		                'EvFinalFirstPhase' => 8,
		                'EvFinalTargetType' => TGT_3D,
		                'EvElimEnds' => 6,
		                'EvElimArrows' => 2,
		                'EvElimSO' => 1,
		                'EvFinEnds' => 4,
		                'EvFinArrows' => 2,
		                'EvFinSO' => 1,
		                'EvFinalAthTarget' => MATCH_NO_SEP,
		                'EvMatchArrowsNo' => FINAL_FROM_2
			);
			$SettingsTeam=array(
				'EvTeamEvent' => '1',
				'EvFinalFirstPhase' => '4',
				'EvFinalTargetType'=>8,
				'EvElimEnds'=>4,
				'EvElimArrows'=>4,
				'EvElimSO'=>4,
				'EvFinEnds'=>4,
				'EvFinArrows'=>4,
				'EvFinSO'=>4,
				'EvFinalAthTarget'=>MATCH_NO_SEP,
				'EvMatchArrowsNo'=>FINAL_FROM_2,
                'EvMultiTeam'=>1,
                'EvMultiTeamNo'=>2,
                'EvPartialTeam'=>1
			);
            $SettingsMixedTeam = array(
                'EvTeamEvent' => '1',
                'EvMixedTeam' => '1',
                'EvFinalFirstPhase' => '4',
                'EvFinalTargetType' => TGT_3D,
                'EvElimEnds' => 4,
                'EvElimArrows' => 4,
                'EvElimSO' => 2,
                'EvFinEnds' => 4,
                'EvFinArrows' => 4,
                'EvFinSO' => 2,
                'EvFinalAthTarget' => MATCH_NO_SEP,
                'EvMatchArrowsNo' => FINAL_FROM_2,
                'EvMultiTeam'=>1,
                'EvMultiTeamNo'=>3,
                'EvPartialTeam'=>0
            );

            $i = 1;
            CreateEventNew($TourId,'LW', 'Langbogen Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'LM', 'Langbogen Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'TW', 'Traditional Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'TM', 'Traditional Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'BW', 'Blankbogen Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'BM', 'Blankbogen Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'CW', 'Compound Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'CM', 'Compound Herren', $i++, $SettingsInd);
            CreateEventNew($TourId,'RW', 'Recurve Damen', $i++, $SettingsInd);
            CreateEventNew($TourId,'RM', 'Recurve Herren', $i++, $SettingsInd);

            $i = 1;
            CreateEventNew($TourId, 'LX', 'Langbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'TX', 'Traditional Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'BX', 'Blankbogen Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $SettingsMixedTeam);
            CreateEventNew($TourId, 'WT', 'Damen Team', $i++, $SettingsTeam);
            CreateEventNew($TourId, 'MT', 'Herren Team', $i++, $SettingsTeam);
			break;
//		case '3':
//		case '4':
//			$SettingsInd=array(
//				'EvFinalFirstPhase' => '2',
//				'EvFinalTargetType'=>8,
//				'EvElimEnds'=>6,
//				'EvElimArrows'=>1,
//				'EvElimSO'=>1,
//				'EvFinEnds'=>4,
//				'EvFinArrows'=>1,
//				'EvFinSO'=>1,
//				'EvElimType'=>4,
//				'EvElim2'=>22,
//				'EvFinalAthTarget'=>MATCH_NO_SEP,
//				'EvMatchArrowsNo'=>FINAL_FROM_2,
//			);
//			$SettingsTeam=array(
//				'EvTeamEvent' => '1',
//				'EvFinalFirstPhase' => '4',
//				'EvFinalTargetType'=>8,
//				'EvElimEnds'=>4,
//				'EvElimArrows'=>4,
//				'EvElimSO'=>4,
//				'EvFinEnds'=>4,
//				'EvFinArrows'=>4,
//				'EvFinSO'=>4,
//				'EvFinalAthTarget'=>MATCH_NO_SEP,
//				'EvMatchArrowsNo'=>FINAL_FROM_2,
//                'EvMultiTeam'=>1,
//                'EvMultiTeamNo'=>2,
//                'EvPartialTeam'=>1
//			);
//            $SettingsMixedTeam = array(
//                'EvTeamEvent' => '1',
//                'EvMixedTeam' => '1',
//                'EvFinalFirstPhase' => '4',
//                'EvFinalTargetType' => TGT_3D,
//                'EvElimEnds' => 4,
//                'EvElimArrows' => 4,
//                'EvElimSO' => 2,
//                'EvFinEnds' => 4,
//                'EvFinArrows' => 4,
//                'EvFinSO' => 2,
//                'EvFinalAthTarget' => MATCH_NO_SEP,
//                'EvMatchArrowsNo' => FINAL_FROM_2,
//                'EvMultiTeam'=>1,
//                'EvMultiTeamNo'=>3,
//                'EvPartialTeam'=>0
//            );
//            $i = 1;
//            CreateEventNew($TourId,'LW', 'Langbogen Damen', $i++, $SettingsInd);
//            CreateEventNew($TourId,'LM', 'Langbogen Herren', $i++, $SettingsInd);
//            CreateEventNew($TourId,'TW', 'Traditional Damen', $i++, $SettingsInd);
//            CreateEventNew($TourId,'TM', 'Traditional Herren', $i++, $SettingsInd);
//            CreateEventNew($TourId,'BW', 'Blankbogen Damen', $i++, $SettingsInd);
//            CreateEventNew($TourId,'BM', 'Blankbogen Herren', $i++, $SettingsInd);
//            CreateEventNew($TourId,'CW', 'Compound Damen', $i++, $SettingsInd);
//            CreateEventNew($TourId,'CM', 'Compound Herren', $i++, $SettingsInd);
//            CreateEventNew($TourId,'RW', 'Recurve Damen', $i++, $SettingsInd);
//            CreateEventNew($TourId,'RM', 'Recurve Herren', $i++, $SettingsInd);
//            $i = 1;
//            CreateEventNew($TourId, 'LX', 'Langbogen Mixed Team', $i++, $SettingsMixedTeam);
//            CreateEventNew($TourId, 'TX', 'Traditional Mixed Team', $i++, $SettingsMixedTeam);
//            CreateEventNew($TourId, 'BX', 'Blankbogen Mixed Team', $i++, $SettingsMixedTeam);
//            CreateEventNew($TourId, 'CX', 'Compound Mixed Team', $i++, $SettingsMixedTeam);
//            CreateEventNew($TourId, 'RX', 'Recurve Mixed Team', $i++, $SettingsMixedTeam);
//            CreateEventNew($TourId, 'WT', 'Damen Team', $i++, $SettingsTeam);
//            CreateEventNew($TourId, 'MT', 'Herren Team', $i++, $SettingsTeam);
//
//			break;
	}
}

function InsertStandard3DEvents($TourId, $SubRule) {
    foreach (array('C'=>'C','B'=>'B','L'=>'L','T'=>'T','R'=>'R') as $kDiv=>$vDiv) {
        $clsTmpArr = array('W','U18W','U21W','60W');
        if($SubRule==2 OR $SubRule==4) {
            $clsTmpArr = array('W');
        }
        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv.'W', $kDiv,  $vClass);
            if($kDiv!=='R') {
                InsertClassEvent($TourId, ($kDiv == 'C' ? 1 : ($kDiv == 'L' ? 2 : ($kDiv == 'B' ? 3 : 4))), 1, 'WT', $kDiv, $vClass);
            }
            InsertClassEvent($TourId, 1, 1, $vDiv.'X', $kDiv, $vClass);
        }
        $clsTmpArr = array('M','U18M','U21M','60','60M');
        if($SubRule==2 OR $SubRule==4) {
            $clsTmpArr = array('M');
        }
        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv.'M', $kDiv,  $vClass);
            if($kDiv!=='R') {
                InsertClassEvent($TourId, ($kDiv == 'C' ? 1 : ($kDiv == 'L' ? 2 : ($kDiv == 'B' ? 3 : 4))), 1, 'MT', $kDiv, $vClass);
            }
            if(substr($vClass,-1,1) != 'U') {
                InsertClassEvent($TourId, 2, 1, $vDiv . 'X', $kDiv, $vClass);
            }
        }
    }
}

function InsertStandard3DEliminations($TourId, $SubRule){
    if($SubRule==1 OR $SubRule==2) {
        foreach (array('R', 'C', 'B', 'L', 'T') as $kDiv) {
            foreach (array('M','W') as $kCl) {
                for($n=1; $n<=16; $n++) {
                    safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=0, ElEventCode='{$kDiv}{$kCl}', ElTournament={$TourId}, ElQualRank={$n}");
                }
                for($n=1; $n<=8; $n++) {
                    safe_w_SQL("INSERT INTO Eliminations set ElId=0, ElElimPhase=1, ElEventCode='{$kDiv}{$kCl}', ElTournament={$TourId}, ElQualRank={$n}");
                }
            }
        }
    }
}
