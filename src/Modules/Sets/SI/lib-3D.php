<?php

/*

3D DEFINITIONS (Target Tournaments)

*/

function CreateStandard3DEvents($TourId, $SubRule) {
    $SettingsInd=array(
        'EvFinalFirstPhase' => '2',
        'EvFinalTargetType'=>8,
        'EvElimEnds'=>6,
        'EvElimArrows'=>1,
        'EvElimSO'=>1,
        'EvFinEnds'=>4,
        'EvFinArrows'=>1,
        'EvFinSO'=>1,
        'EvElimType'=>4,
        'EvElim2'=>22,
        'EvFinalAthTarget'=>MATCH_NO_SEP,
        'EvMatchArrowsNo'=>FINAL_FROM_2,
    );
    $SettingsTeam=array(
        'EvTeamEvent' => '1',
        'EvFinalFirstPhase' => '4',
        'EvFinalTargetType'=>8,
        'EvElimEnds'=>8,
        'EvElimArrows'=>3,
        'EvElimSO'=>3,
        'EvFinEnds'=>4,
        'EvFinArrows'=>3,
        'EvFinSO'=>3,
        'EvFinalAthTarget'=>MATCH_NO_SEP,
        'EvMatchArrowsNo'=>FINAL_FROM_4,
        'EvMultiTeam'=>1,
        'EvMultiTeamNo'=>2,
        'EvPartialTeam'=>1
    );
    $SettingsMixedTeam = array(
        'EvTeamEvent' => '1',
        'EvMixedTeam' => '1',
        'EvFinalFirstPhase' => '2',
        'EvFinalTargetType' => TGT_3D,
        'EvElimEnds' => 8,
        'EvElimArrows' => 2,
        'EvElimSO' => 2,
        'EvFinEnds' => 4,
        'EvFinArrows' => 2,
        'EvFinSO' => 2,
        'EvFinalAthTarget' => MATCH_NO_SEP,
        'EvMatchArrowsNo' => FINAL_FROM_2,
        'EvMultiTeam'=>1,
        'EvMultiTeamNo'=>3,
        'EvPartialTeam'=>0
    );
    $i = 1;
    CreateEventNew($TourId,'DLW', 'Dolgi lok Članice', $i++, $SettingsInd);
    CreateEventNew($TourId,'DLM', 'Dolgi lok Člani', $i++, $SettingsInd);
    CreateEventNew($TourId,'TLW', 'Tradicionalni lok Članice', $i, $SettingsInd);
    CreateEventNew($TourId,'TLM', 'Tradicionalni lok Člani', $i++, $SettingsInd);
    CreateEventNew($TourId,'GLW', 'Goli lok Članice', $i++, $SettingsInd);
    CreateEventNew($TourId,'GLM', 'Goli lok Člani', $i++, $SettingsInd);
    CreateEventNew($TourId,'SLW', 'Sestavljeni lok Članice', $i++, $SettingsInd);
    CreateEventNew($TourId,'SLM', 'Sestavljeni lok Člani', $i++, $SettingsInd);
    CreateEventNew($TourId,'LLW', 'Lovski lok Članice', $i++, $SettingsInd);
    CreateEventNew($TourId,'LLM', 'Lovski lok Člani', $i++, $SettingsInd);
    CreateEventNew($TourId,'ULW', 'Ukrivljeni lok Članice', $i++, $SettingsInd);
    CreateEventNew($TourId,'ULM', 'Ukrivljeni lok Člani', $i++, $SettingsInd);
    $i = 1;
    CreateEventNew($TourId, 'DLX', 'Dolgi lok Mixed Team', $i++, $SettingsMixedTeam);
    CreateEventNew($TourId, 'TLX', 'Tradicionalni lok Mixed Team', $i, $SettingsMixedTeam);
    CreateEventNew($TourId, 'GLX', 'Goli lok Mixed Team', $i++, $SettingsMixedTeam);
    CreateEventNew($TourId, 'SLX', 'Sestavljeni lok Mixed Team', $i++, $SettingsMixedTeam);
    CreateEventNew($TourId, 'ULX', 'Ukrivljeni lok Mixed Team', $i++, $SettingsMixedTeam);
    CreateEventNew($TourId, 'LLX', 'Lovski lok Mixed Team', $i++, $SettingsMixedTeam);
    CreateEventNew($TourId, 'WT', 'Članice Ekipa', $i++, $SettingsTeam);
    CreateEventNew($TourId, 'MT', 'Člani Ekipa', $i++, $SettingsTeam);
}

function InsertStandard3DEvents($TourId, $SubRule) {
    foreach (array('GL','UL','SL','LL','DL','TL') as $vDiv) {
        $clsTmpArr = array('W','U21W','50W');
        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv.'W', $vDiv,  $vClass);
            if($vDiv!=='UL' AND $vDiv!=='LL') {
                InsertClassEvent($TourId, ($vDiv == 'SL' ? 1 : ($vDiv == 'DL' ? 2 : 3)), 1, 'WT', $vDiv, $vClass);
            }
            InsertClassEvent($TourId, 1, 1, $vDiv.'X', $vDiv, $vClass);
        }
        $clsTmpArr = array('M','U21M','50M');
        foreach($clsTmpArr as $kClass=>$vClass) {
            InsertClassEvent($TourId, 0, 1, $vDiv.'M', $vDiv,  $vClass);
            if($vDiv!=='UL' AND $vDiv!=='LL') {
                InsertClassEvent($TourId, ($vDiv == 'SL' ? 1 : ($vDiv == 'DL' ? 2 : 3)), 1, 'MT', $vDiv, $vClass);
            }
            InsertClassEvent($TourId, 2, 1, $vDiv . 'X', $vDiv, $vClass);
        }
    }
}