<?php
/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'BRA';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=3, $SubRule=1) {
	$i=1;
	CreateDivision($TourId, $i++, 'R', 'Recurvo');
	CreateDivision($TourId, $i++, 'C', 'Composto');
	if ($SubRule!='4') CreateDivision($TourId, $i++, 'B', 'Barebow');
	if ($SubRule=='1' or $SubRule=='4') { // if Para existed, insert W1 Division
		// code...
		//CreateDivision($TourId, $Order, $Id, $Description, $Athlete='1', $RecDiv='', $WaDiv='', $IsPara=false) 
		CreateDivision($TourId, $i++, 'W1', 'W1',1,'','',true);
	}
}

function CreateStandardClasses($TourId, $TourType=3, $SubRule=1) {
	$i=1;
	switch ($TourType) {
        case 1:
        case 3:       
        case 6:
        	switch ($SubRule) {
                case 1:
                    CreateClass($TourId, $i++, 21, 49, 0, 'M', 'M', 'Masculino',1,'C,R,B');
					CreateClass($TourId, $i++, 21, 49, 1, 'F', 'F', 'Feminino',1,'C,R,B');
					CreateClass($TourId, $i++, 18, 20, 0, 'MJ', 'MJ,M', 'Masculino Juvenil',1,'C,R,B');
					CreateClass($TourId, $i++, 18, 20, 1, 'FJ', 'FJ,F', 'Feminino Juvenil',1,'C,R,B');
					CreateClass($TourId, $i++, 1, 17, 0, 'MC', 'MC,MJ,M', 'Masculino Cadete',1,'C,R,B');
					CreateClass($TourId, $i++, 1, 17, 1, 'FC', 'FC,FJ,F', 'Feminino Cadete',1,'C,R,B');
					CreateClass($TourId, $i++, 1, 14, 0, 'MI', 'MI,MC,MJ,M', 'Masculino Infantil',1,'C,R,B');
					CreateClass($TourId, $i++, 1, 14, 1, 'FI', 'FI,FC,FJ,F', 'Feminino Infantil',1,'C,R,B');
					CreateClass($TourId, $i++, 50,100, 0, 'MM', 'MM,M', 'Masculino Master',1,'C,R,B');
					CreateClass($TourId, $i++, 50,100, 1, 'FM', 'FM,F', 'Feminino Master',1,'C,R,B');
					//CreateClass($TourId, $Order, $From, $To, $Sex, $Id, $ValidClass, $Description, $Athlete='1', $AlDivision='', $RecCl='', $WaCl='', $IsPara=false)
					CreateClass($TourId, $i++, 1, 100, 0, 'MO', 'MO', 'Masculino Open - Paralímpico',1,'C,R,W1','','',true);
					CreateClass($TourId, $i++, 1, 100, 1, 'FO', 'FO', 'Feminino Open - Paralímpico',1,'C,R,W1','','',true);
					break;
                case 2:
                	CreateClass($TourId, $i++, 21, 49, 0, 'M', 'M', 'Masculino');
					CreateClass($TourId, $i++, 21, 49, 1, 'F', 'F', 'Feminino');
					break;
                case 3:
                	CreateClass($TourId, $i++, 18, 20, 0, 'MJ', 'MJ', 'Masculino Juvenil');
					CreateClass($TourId, $i++, 18, 20, 1, 'FJ', 'FJ', 'Feminino Juvenil');
					CreateClass($TourId, $i++, 1, 17, 0, 'MC', 'MC,MJ', 'Masculino Cadete');
					CreateClass($TourId, $i++, 1, 17, 1, 'FC', 'FC,FJ', 'Feminino Cadete');
					CreateClass($TourId, $i++, 1, 14, 0, 'MI', 'MI,MC,MJ', 'Masculino Infantil');
					CreateClass($TourId, $i++, 1, 14, 1, 'FI', 'FI,FC,FJ', 'Feminino Infantil');
					CreateClass($TourId, $i++, 50,100, 0, 'MM', 'MM', 'Masculino Master');
					CreateClass($TourId, $i++, 50,100, 1, 'FM', 'FM', 'Feminino Master');
					break;
				case 4:
					CreateClass($TourId, $i++, 1, 100, 0, 'MO', 'MO', 'Masculino Open - Paralímpico',1,'','','',true);
					CreateClass($TourId, $i++, 1, 100, 1, 'FO', 'FO', 'Feminino Open - Paralímpico',1,'','','',true);
					break;

            }
            break;
        
    }

}


function CreateStandardEvents($TourId, $SubRule, $Outdoor=true) {
    $TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
    $TargetB=($Outdoor?5:1);
    $TargetW1=($Outdoor?5:2);
	$TargetSizeR=($Outdoor ? 122 : 40);
	$TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
	$DistanceR=($Outdoor ? 70 : 18);
	$DistanceRcm=($Outdoor ? 60 : 18);
	$DistanceRi=($Outdoor ? 30 : 18);
	$DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
    $DistanceBi=($Outdoor ? 20 : 18);
	$FirstPhase = ($Outdoor ? 32 : 16);
	$TeamFirstPhase = ($Outdoor ? 8 : 8);

    $i=1;
	switch($SubRule) {
		case '1': // All Classes
			$FirstPhase = ($Outdoor ? 8 : 8);
			$TeamFirstPhase = ($Outdoor ? 4 : 8);
			// RECURVE
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM', 'Recurvo Masculino', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RF', 'Recurvo Feminino', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RMJ', 'Recurvo Masculino Juvenil', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RFJ', 'Recurvo Feminino Juvenil', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RMC', 'Recurvo Masculino Cadete', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RFC', 'Recurvo Feminino Cadete', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RMI', 'Recurvo Masculino Infantil', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRi);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RFI', 'Recurvo Feminino Infantil', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRi);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RMM', 'Recurvo Masculino Master', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RFM', 'Recurvo Feminino Master', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RMO', 'Recurvo Masculino Open', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetR, 5, 3, 1, 5, 3, 1, 'RFO', 'Recurvo Feminino Open', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			// COMPOUND
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM', 'Composto Masculino', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CF', 'Composto Feminino', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CMJ', 'Composto Masculino Juvenil', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CFJ', 'Composto Feminino Juvenil', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CMC', 'Composto Masculino Cadete', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CFC', 'Composto Feminino Cadete', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CMI', 'Composto Masculino Infantil', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CFI', 'Composto Feminino Infantil', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CMM', 'Composto Masculino Master', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CFM', 'Composto Feminino Master', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CMO', 'Composto Masculino Open', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetC, 5, 3, 1, 5, 3, 1, 'CFO', 'Composto Feminino Open', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			// BAREBOW
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Masculino', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BF', 'Barebow Feminino', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BMJ', 'Barebow Masculino Juvenil', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BFJ', 'Barebow Feminino Juvenil', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BMC', 'Barebow Masculino Cadete', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BFC', 'Barebow Feminino Cadete', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BMI', 'Barebow Masculino Infantil', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceBi);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BFI', 'Barebow Feminino Infantil', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceBi);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BMM', 'Barebow Masculino Master', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BFM', 'Barebow Feminino Master', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			// W1
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetW1, 5, 3, 1, 5, 3, 1, 'W1M', 'W1 Masculino Open', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetW1, 5, 3, 1, 5, 3, 1, 'W1F', 'W1 Feminino Open', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			//TEAMS
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM', 'Recurvo Masculino Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RF', 'Recurvo Feminino Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RMJ', 'Recurvo Masculino Juvenil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RFJ', 'Recurvo Feminino Juvenil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RMC', 'Recurvo Masculino Cadete Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RFC', 'Recurvo Feminino Cadete Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RMI', 'Recurvo Masculino Infantil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RFI', 'Recurvo Feminino Infantil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RMM', 'Recurvo Masculino Master Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RFM', 'Recurvo Feminino Master Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RMO', 'Recurvo Masculino Open Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RFO', 'Recurvo Feminino Open Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX', 'Recurvo Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetR, 4, 4, 2, 4, 4, 2, 'RJX', 'Recurvo Juvenil Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetR, 4, 4, 2, 4, 4, 2, 'RCX', 'Recurvo Cadete Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetR, 4, 4, 2, 4, 4, 2, 'RIX', 'Recurvo Infantil Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRi, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetR, 4, 4, 2, 4, 4, 2, 'RMX', 'Recurvo Master Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetR, 4, 4, 2, 4, 4, 2, 'ROX', 'Recurvo Open Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Composto Masculino Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CF',  'Composto Feminino Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CMJ', 'Composto Masculino Juvenil Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CFJ', 'Composto Feminino Juvenil Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CMC', 'Composto Masculino Cadete Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CFC', 'Composto Feminino Cadete Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CMI', 'Composto Masculino Infantil Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceRi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CFI', 'Composto Feminino Infantil Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceRi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CMM', 'Composto Masculino Master Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CFM', 'Composto Feminino Master Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CMO', 'Composto Masculino Open Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CFO', 'Composto Feminino Open Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Composto Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetC, 4, 4, 2, 4, 4, 2, 'CJX', 'Composto Juvenil Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetC, 4, 4, 2, 4, 4, 2, 'CCX', 'Composto Cadete Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetC, 4, 4, 2, 4, 4, 2, 'CIX', 'Composto Infantil Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceRi, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetC, 4, 4, 2, 4, 4, 2, 'CMX', 'Composto Master Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetC, 4, 4, 2, 4, 4, 2, 'COX', 'Composto Open Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			}
			// BAREBOW
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM',  'Barebow Masculino Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BF',  'Barebow Feminino Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BMJ', 'Barebow Masculino Juvenil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BFJ', 'Barebow Feminino Juvenil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BMC', 'Barebow Masculino Cadete Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BFC', 'Barebow Feminino Cadete Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BMI', 'Barebow Masculino Infantil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BFI', 'Barebow Feminino Infantil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BMM', 'Barebow Masculino Master Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BFM', 'Barebow Feminino Master Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetB, 4, 4, 2, 4, 4, 2, 'BX',  'Barebow Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetB, 4, 4, 2, 4, 4, 2, 'BJX', 'Barebow Juvenil Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetB, 4, 4, 2, 4, 4, 2, 'BCX', 'Barebow Cadete Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetB, 4, 4, 2, 4, 4, 2, 'BIX', 'Barebow Infantil Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBi, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetB, 4, 4, 2, 4, 4, 2, 'BMX', 'Barebow Master Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			}
			// W1
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetW1, 4, 6, 3, 4, 6, 3, 'W1M', 'W1 Masculino Open Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetW1, 4, 6, 3, 4, 6, 3, 'W1F', 'W1 Feminino Open Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetW1, 4, 4, 2, 4, 4, 2, 'W1X',  'W1 Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			}
			break;
		case '2': // One Class
			$i=1;
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Recurvo Masculino', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetR, 5, 3, 1, 5, 3, 1, 'RF',  'Recurvo Feminino', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Composto Masculino', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetC, 5, 3, 1, 5, 3, 1, 'CF',  'Composto Feminino', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM', 'Barebow Masculino', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, $FirstPhase, $TargetB, 5, 3, 1, 5, 3, 1, 'BF', 'Barebow Feminino', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			//Teams
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RM',  'Recurvo Masculino Equipe', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetR, 4, 6, 3, 4, 6, 3, 'RF',  'Recurvo Feminino Equipe', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetR, 4, 4, 2, 4, 4, 2, 'RX',  'Recurvo Equipe Mista', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CM',  'Composto Masculino Equipe', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetC, 4, 6, 3, 4, 6, 3, 'CF',  'Composto Feminino Equipe', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, $TeamFirstPhase, $TargetC, 4, 4, 2, 4, 4, 2, 'CX',  'Composto Equipe Mista', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			}
			CreateEvent($TourId, $i++, 1, 0, $TeamFirstPhase, $TargetB, 4, 6, 3, 4, 6, 3, 'BM',  'Barebow Masculino Equipe', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 4, $TargetB, 4, 6, 3, 4, 6, 3, 'BF',  'Barebow Feminino Equipe', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetB, 4, 4, 2, 4, 4, 2, 'BX', 'Barebow Equipe Mista', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			}
			break;
		case '3':
			// RECURVE
			CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RMJ', 'Recurvo Masculino Juvenil', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RFJ', 'Recurvo Feminino Juvenil', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RMC', 'Recurvo Masculino Cadete', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetR, 5, 3, 1, 5, 3, 1, 'RFC', 'Recurvo Feminino Cadete', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetR, 5, 3, 1, 5, 3, 1, 'RMI', 'Recurvo Masculino Infantil', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRi);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetR, 5, 3, 1, 5, 3, 1, 'RFI', 'Recurvo Feminino Infantil', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRi);
			CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RMM', 'Recurvo Masculino Master', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetR, 5, 3, 1, 5, 3, 1, 'RFM', 'Recurvo Feminino Master', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm);
			// COMPOUND
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetC, 5, 3, 1, 5, 3, 1, 'CMJ', 'Composto Masculino Juvenil', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetC, 5, 3, 1, 5, 3, 1, 'CFJ', 'Composto Feminino Juvenil', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetC, 5, 3, 1, 5, 3, 1, 'CMC', 'Composto Masculino Cadete', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetC, 5, 3, 1, 5, 3, 1, 'CFC', 'Composto Feminino Cadete', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetC, 5, 3, 1, 5, 3, 1, 'CMI', 'Composto Masculino Infantil', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetC, 5, 3, 1, 5, 3, 1, 'CFI', 'Composto Feminino Infantil', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CMM', 'Composto Masculino Master', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CFM', 'Composto Feminino Master', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC);
			// BAREBOW
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BMJ', 'Barebow Masculino Juvenil', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BFJ', 'Barebow Feminino Juvenil', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BMC', 'Barebow Masculino Cadete', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BFC', 'Barebow Feminino Cadete', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BMI', 'Barebow Masculino Infantil', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceBi);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BFI', 'Barebow Feminino Infantil', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceBi);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BMM', 'Barebow Masculino Master', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetB, 5, 3, 1, 5, 3, 1, 'BFM', 'Barebow Feminino Master', 1, 240, 0, 0, 0, '', '', $TargetSizeB, $DistanceB);
			//TEAMS
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RMJ', 'Recurvo Masculino Juvenil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RFJ', 'Recurvo Feminino Juvenil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RMC', 'Recurvo Masculino Cadete Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RFC', 'Recurvo Feminino Cadete Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RMI', 'Recurvo Masculino Infantil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RFI', 'Recurvo Feminino Infantil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RMM', 'Recurvo Masculino Master Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RFM', 'Recurvo Feminino Master Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'RJX', 'Recurvo Juvenil Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
				CreateEvent($TourId, $i++, 1, 1, 8, $TargetR, 4, 4, 2, 4, 4, 2, 'RCX', 'Recurvo Cadete Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
				CreateEvent($TourId, $i++, 1, 1, 4, $TargetR, 4, 4, 2, 4, 4, 2, 'RIX', 'Recurvo Infantil Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRi, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetR, 4, 4, 2, 4, 4, 2, 'RMX', 'Recurvo Master Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeR, $DistanceRcm, '',1);
			}
			//COMPOUND
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CMJ', 'Composto Masculino Juvenil Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CFJ', 'Composto Feminino Juvenil Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CMC', 'Composto Masculino Cadete Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CFC', 'Composto Feminino Cadete Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CMI', 'Composto Masculino Infantil Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceRi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CFI', 'Composto Feminino Infantil Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceRi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CMM', 'Composto Masculino Master Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CFM', 'Composto Feminino Master Equipe', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetC, 4, 4, 2, 4, 4, 2, 'CJX', 'Composto Juvenil Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetC, 4, 4, 2, 4, 4, 2, 'CCX', 'Composto Cadete Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetC, 4, 4, 2, 4, 4, 2, 'CIX', 'Composto Infantil Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceRi, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetC, 4, 4, 2, 4, 4, 2, 'CMX', 'Composto Master Equipe Mista', 0, 0, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			}
			// BAREBOW
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BMJ', 'Barebow Masculino Juvenil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BFJ', 'Barebow Feminino Juvenil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BMC', 'Barebow Masculino Cadete Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BFC', 'Barebow Feminino Cadete Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BMI', 'Barebow Masculino Infantil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BFI', 'Barebow Feminino Infantil Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBi, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BMM', 'Barebow Masculino Master Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetB, 4, 6, 3, 4, 6, 3, 'BFM', 'Barebow Feminino Master Equipe', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetB, 4, 4, 2, 4, 4, 2, 'BJX', 'Barebow Juvenil Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetB, 4, 4, 2, 4, 4, 2, 'BCX', 'Barebow Cadete Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetB, 4, 4, 2, 4, 4, 2, 'BIX', 'Barebow Infantil Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceBi, '',1);
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetB, 4, 4, 2, 4, 4, 2, 'BMX', 'Barebow Master Equipe Mista', 1, 0, 0, 0, 0, '', '', $TargetSizeB, $DistanceB, '',1);
			}		
			break;
		case '4': //Paralympic
			$i=1;
			// RECURVE
			CreateEvent($TourId, $i++, 0, 0, 8, $TargetR, 5, 3, 1, 5, 3, 1, 'RMO', 'Recurvo Masculino Open', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetR, 5, 3, 1, 5, 3, 1, 'RFO', 'Recurvo Feminino Open', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR);
			// COMPOUND
			CreateEvent($TourId, $i++, 0, 0, 8, $TargetC, 5, 3, 1, 5, 3, 1, 'CMO', 'Composto Masculino Open', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 4, $TargetC, 5, 3, 1, 5, 3, 1, 'CFO', 'Composto Feminino Open', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			// W1
			CreateEvent($TourId, $i++, 0, 0, 2, $TargetW1, 5, 3, 1, 5, 3, 1, 'W1M', 'W1 Masculino Open', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			CreateEvent($TourId, $i++, 0, 0, 0, $TargetW1, 5, 3, 1, 5, 3, 1, 'W1F', 'W1 Feminino Open', 0, 240, 240, 0, 0, '', '', $TargetSizeC, $DistanceC);
			//TEAMS
			$i=1;
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RMO', 'Recurvo Masculino Open Equipe', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'RFO', 'Recurvo Feminino Open Equipe', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetR, 4, 4, 2, 4, 4, 2, 'ROX', 'Recurvo Open Equipe Mista', 1, 240, 0, 0, 0, '', '', $TargetSizeR, $DistanceR, '',1);
			}
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CMO', 'Composto Masculino Open Equipe', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'CFO', 'Composto Feminino Open Equipe', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 2, $TargetC, 4, 4, 2, 4, 4, 2, 'COX', 'Composto Open Equipe Mista', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			}
			// W1
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetW1, 4, 6, 3, 4, 6, 3, 'W1M', 'W1 Masculino Open Equipe', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			CreateEvent($TourId, $i++, 1, 0, 0, $TargetW1, 4, 6, 3, 4, 6, 3, 'W1F', 'W1 Feminino Open Equipe', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
			if($Outdoor) {
				CreateEvent($TourId, $i++, 1, 1, 0, $TargetW1, 4, 4, 2, 4, 4, 2, 'W1X', 'W1 Equipe Mista', 0, 240, 0, 0, 0, '', '', $TargetSizeC, $DistanceC, '',1);
				//CreateEvent($TourId, $Order, $Team, $MixTeam, $FirstPhase, $TargetType, $ElimEnds, $ElimArrows, $ElimSO, $FinEnds, $FinArrows, $FinSO, $Code, $Description, $SetMode=0, $MatchArrows=0, $AthTarget=0, $ElimRound1=array(), $ElimRound2=array(), $RecCategory='', $WaCategory='', $tgtSize=0, $shootingDist=0, $parentEvent='', $MultipleTeam=0, $Selected=0, $EvWinnerFinalRank=1, $CreationMode=0, $MultipleTeamNo=0, $PartialTeam=0)
			}
			break;
	}
}

function InsertStandardEvents($TourId, $SubRule, $Outdoor=true) {
    switch($SubRule) {
    	case '1':
    		// individuais
			InsertClassEvent($TourId, 0, 1, 'RM', 'R', 'M');
			InsertClassEvent($TourId, 0, 1, 'RMJ', 'R', 'MJ');
			InsertClassEvent($TourId, 0, 1, 'RMC', 'R', 'MC');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 0, 1, 'RMI', 'R', 'MI');
			InsertClassEvent($TourId, 0, 1, 'RMO', 'R', 'MO');
			InsertClassEvent($TourId, 0, 1, 'RF', 'R', 'F');
			InsertClassEvent($TourId, 0, 1, 'RFJ', 'R', 'FJ');
			InsertClassEvent($TourId, 0, 1, 'RFC', 'R', 'FC');
			InsertClassEvent($TourId, 0, 1, 'RFM', 'R', 'FM');
			InsertClassEvent($TourId, 0, 1, 'RFI', 'R', 'FI');
			InsertClassEvent($TourId, 0, 1, 'RFO', 'R', 'FO');
			InsertClassEvent($TourId, 0, 1, 'CM', 'C', 'M');
			InsertClassEvent($TourId, 0, 1, 'CMJ', 'C', 'MJ');
			InsertClassEvent($TourId, 0, 1, 'CMC', 'C', 'MC');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 0, 1, 'CMI', 'C', 'MI');
			InsertClassEvent($TourId, 0, 1, 'CMO', 'C', 'MO');
			InsertClassEvent($TourId, 0, 1, 'CF', 'C', 'F');
			InsertClassEvent($TourId, 0, 1, 'CFJ', 'C', 'FJ');
			InsertClassEvent($TourId, 0, 1, 'CFC', 'C', 'FC');
			InsertClassEvent($TourId, 0, 1, 'CFM', 'C', 'FM');
			InsertClassEvent($TourId, 0, 1, 'CFI', 'C', 'FI');
			InsertClassEvent($TourId, 0, 1, 'CFO', 'C', 'FO');
			// BAREBOW
			InsertClassEvent($TourId, 0, 1, 'BM', 'B', 'M');
			InsertClassEvent($TourId, 0, 1, 'BMJ', 'B', 'MJ');
			InsertClassEvent($TourId, 0, 1, 'BMC', 'B', 'MC');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'MM');
			InsertClassEvent($TourId, 0, 1, 'BMI', 'B', 'MI');
			InsertClassEvent($TourId, 0, 1, 'BF', 'B', 'F');
			InsertClassEvent($TourId, 0, 1, 'BFJ', 'B', 'FJ');
			InsertClassEvent($TourId, 0, 1, 'BFC', 'B', 'FC');
			InsertClassEvent($TourId, 0, 1, 'BFM', 'B', 'FM');
			InsertClassEvent($TourId, 0, 1, 'BFI', 'B', 'FI');
			// W1
			InsertClassEvent($TourId, 0, 1, 'W1M', 'W1', 'MO');
			InsertClassEvent($TourId, 0, 1, 'W1F', 'W1', 'FO');
			// equipes
			InsertClassEvent($TourId, 1, 3, 'RM', 'R', 'M');
			InsertClassEvent($TourId, 1, 3, 'RMJ', 'R', 'MJ');
			InsertClassEvent($TourId, 1, 3, 'RMC', 'R', 'MC');
			InsertClassEvent($TourId, 1, 3, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'RMI', 'R', 'MI');
			InsertClassEvent($TourId, 1, 3, 'RMO', 'R', 'MO');
			InsertClassEvent($TourId, 1, 3, 'RF', 'R', 'F');
			InsertClassEvent($TourId, 1, 3, 'RFJ', 'R', 'FJ');
			InsertClassEvent($TourId, 1, 3, 'RFC', 'R', 'FC');
			InsertClassEvent($TourId, 1, 3, 'RFM', 'R', 'FM');
			InsertClassEvent($TourId, 1, 3, 'RFI', 'R', 'FI');
			InsertClassEvent($TourId, 1, 3, 'RFO', 'R', 'FO');
			InsertClassEvent($TourId, 1, 1, 'RX', 'R', 'F');
			InsertClassEvent($TourId, 2, 1, 'RX', 'R', 'M');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R', 'FJ');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R', 'MJ');
			InsertClassEvent($TourId, 1, 1, 'RCX', 'R', 'FC');
			InsertClassEvent($TourId, 2, 1, 'RCX', 'R', 'MC');
			InsertClassEvent($TourId, 1, 1, 'RMX', 'R', 'FM');
			InsertClassEvent($TourId, 2, 1, 'RMX', 'R', 'MM');
			InsertClassEvent($TourId, 1, 1, 'RIX', 'R', 'FI');
			InsertClassEvent($TourId, 2, 1, 'RIX', 'R', 'MI');
			InsertClassEvent($TourId, 1, 1, 'ROX', 'R', 'FO');
			InsertClassEvent($TourId, 2, 1, 'ROX', 'R', 'MO');
			InsertClassEvent($TourId, 1, 3, 'CM', 'C', 'M');
			InsertClassEvent($TourId, 1, 3, 'CMJ', 'C', 'MJ');
			InsertClassEvent($TourId, 1, 3, 'CMC', 'C', 'MC');
			InsertClassEvent($TourId, 1, 3, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CMI', 'C', 'MI');
			InsertClassEvent($TourId, 1, 3, 'CMO', 'C', 'MO');
			InsertClassEvent($TourId, 1, 3, 'CF', 'C', 'F');
			InsertClassEvent($TourId, 1, 3, 'CFJ', 'C', 'FJ');
			InsertClassEvent($TourId, 1, 3, 'CFC', 'C', 'FC');
			InsertClassEvent($TourId, 1, 3, 'CFM', 'C', 'FM');
			InsertClassEvent($TourId, 1, 3, 'CFI', 'C', 'FI');
			InsertClassEvent($TourId, 1, 3, 'CFO', 'C', 'FO');
			InsertClassEvent($TourId, 1, 1, 'CX', 'C', 'F');
			InsertClassEvent($TourId, 2, 1, 'CX', 'C', 'M');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C', 'FJ');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C', 'MJ');
			InsertClassEvent($TourId, 1, 1, 'CCX', 'C', 'FC');
			InsertClassEvent($TourId, 2, 1, 'CCX', 'C', 'MC');
			InsertClassEvent($TourId, 1, 1, 'CMX', 'C', 'FM');
			InsertClassEvent($TourId, 2, 1, 'CMX', 'C', 'MM');
			InsertClassEvent($TourId, 1, 1, 'CIX', 'C', 'FI');
			InsertClassEvent($TourId, 2, 1, 'CIX', 'C', 'MI');
			InsertClassEvent($TourId, 1, 1, 'COX', 'C', 'FO');
			InsertClassEvent($TourId, 2, 1, 'COX', 'C', 'MO');
			// BAREBOW
			InsertClassEvent($TourId, 1, 3, 'BM', 'B', 'M');
			InsertClassEvent($TourId, 1, 3, 'BMJ', 'B', 'MJ');
			InsertClassEvent($TourId, 1, 3, 'BMC', 'B', 'MC');
			InsertClassEvent($TourId, 1, 3, 'BMM', 'B', 'MM');
			InsertClassEvent($TourId, 1, 3, 'BMI', 'B', 'MI');
			InsertClassEvent($TourId, 1, 3, 'BF', 'B', 'F');
			InsertClassEvent($TourId, 1, 3, 'BFJ', 'B', 'FJ');
			InsertClassEvent($TourId, 1, 3, 'BFC', 'B', 'FC');
			InsertClassEvent($TourId, 1, 3, 'BFM', 'B', 'FM');
			InsertClassEvent($TourId, 1, 3, 'BFI', 'B', 'FI');
			InsertClassEvent($TourId, 1, 1, 'BX', 'B', 'F');
			InsertClassEvent($TourId, 2, 1, 'BX', 'B', 'M');
			InsertClassEvent($TourId, 1, 1, 'BJX', 'B', 'FJ');
			InsertClassEvent($TourId, 2, 1, 'BJX', 'B', 'MJ');
			InsertClassEvent($TourId, 1, 1, 'BCX', 'B', 'FC');
			InsertClassEvent($TourId, 2, 1, 'BCX', 'B', 'MC');
			InsertClassEvent($TourId, 1, 1, 'BMX', 'B', 'FM');
			InsertClassEvent($TourId, 2, 1, 'BMX', 'B', 'MM');
			InsertClassEvent($TourId, 1, 1, 'BIX', 'B', 'FI');
			InsertClassEvent($TourId, 2, 1, 'BIX', 'B', 'MI');
			// W1
			InsertClassEvent($TourId, 1, 3, 'W1M', 'W1', 'M');
			InsertClassEvent($TourId, 1, 3, 'W1F', 'W1', 'F');
			InsertClassEvent($TourId, 1, 1, 'W1X', 'W1', 'F');
			InsertClassEvent($TourId, 2, 1, 'W1X', 'W1', 'M');
    		break;
    	case '2':
			InsertClassEvent($TourId, 0, 1, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 0, 1, 'RF',  'R',  'F');
			InsertClassEvent($TourId, 0, 1, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 0, 1, 'CF',  'C',  'F');
			InsertClassEvent($TourId, 0, 1, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 0, 1, 'BF',  'B',  'F');
			InsertClassEvent($TourId, 1, 3, 'RM',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'RF',  'R',  'F');
			InsertClassEvent($TourId, 1, 1, 'RX',  'R',  'F');
			InsertClassEvent($TourId, 2, 1, 'RX',  'R',  'M');
			InsertClassEvent($TourId, 1, 3, 'CM',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'CF',  'C',  'F');
			InsertClassEvent($TourId, 1, 1, 'CX',  'C',  'F');
			InsertClassEvent($TourId, 2, 1, 'CX',  'C',  'M');
			InsertClassEvent($TourId, 1, 3, 'BM',  'B',  'M');
			InsertClassEvent($TourId, 1, 3, 'BF',  'B',  'F');
			InsertClassEvent($TourId, 1, 1, 'BX',  'B',  'F');
			InsertClassEvent($TourId, 2, 1, 'BX',  'B',  'M');
    		break;    	
    	case '3':
			$i=1;
			// individuais
			InsertClassEvent($TourId, 0, 1, 'RMJ', 'R', 'MJ');
			InsertClassEvent($TourId, 0, 1, 'RMC', 'R', 'MC');
			InsertClassEvent($TourId, 0, 1, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 0, 1, 'RMI', 'R', 'MI');
			InsertClassEvent($TourId, 0, 1, 'RFJ', 'R', 'FJ');
			InsertClassEvent($TourId, 0, 1, 'RFC', 'R', 'FC');
			InsertClassEvent($TourId, 0, 1, 'RFM', 'R', 'FM');
			InsertClassEvent($TourId, 0, 1, 'RFI', 'R', 'FI');
			// COMPOUND
			InsertClassEvent($TourId, 0, 1, 'CMJ', 'C', 'MJ');
			InsertClassEvent($TourId, 0, 1, 'CMC', 'C', 'MC');
			InsertClassEvent($TourId, 0, 1, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 0, 1, 'CMI', 'C', 'MI');
			InsertClassEvent($TourId, 0, 1, 'CFJ', 'C', 'FJ');
			InsertClassEvent($TourId, 0, 1, 'CFC', 'C', 'FC');
			InsertClassEvent($TourId, 0, 1, 'CFM', 'C', 'FM');
			InsertClassEvent($TourId, 0, 1, 'CFI', 'C', 'FI');
			// BAREBOW
			InsertClassEvent($TourId, 0, 1, 'BMJ', 'B', 'MJ');
			InsertClassEvent($TourId, 0, 1, 'BMC', 'B', 'MC');
			InsertClassEvent($TourId, 0, 1, 'BMM', 'B', 'MM');
			InsertClassEvent($TourId, 0, 1, 'BMI', 'B', 'MI');
			InsertClassEvent($TourId, 0, 1, 'BFJ', 'B', 'FJ');
			InsertClassEvent($TourId, 0, 1, 'BFC', 'B', 'FC');
			InsertClassEvent($TourId, 0, 1, 'BFM', 'B', 'FM');
			InsertClassEvent($TourId, 0, 1, 'BFI', 'B', 'FI');
			// equipes
			InsertClassEvent($TourId, 1, 3, 'RMJ', 'R', 'MJ');
			InsertClassEvent($TourId, 1, 3, 'RMC', 'R', 'MC');
			InsertClassEvent($TourId, 1, 3, 'RMM', 'R', 'MM');
			InsertClassEvent($TourId, 1, 3, 'RMI', 'R', 'MI');
			InsertClassEvent($TourId, 1, 3, 'RFJ', 'R', 'FJ');
			InsertClassEvent($TourId, 1, 3, 'RFC', 'R', 'FC');
			InsertClassEvent($TourId, 1, 3, 'RFM', 'R', 'FM');
			InsertClassEvent($TourId, 1, 3, 'RFI', 'R', 'FI');
			InsertClassEvent($TourId, 1, 1, 'RJX', 'R', 'FJ');
			InsertClassEvent($TourId, 2, 1, 'RJX', 'R', 'MJ');
			InsertClassEvent($TourId, 1, 1, 'RCX', 'R', 'FC');
			InsertClassEvent($TourId, 2, 1, 'RCX', 'R', 'MC');
			InsertClassEvent($TourId, 1, 1, 'RMX', 'R', 'FM');
			InsertClassEvent($TourId, 2, 1, 'RMX', 'R', 'MM');
			InsertClassEvent($TourId, 1, 1, 'RIX', 'R', 'FI');
			InsertClassEvent($TourId, 2, 1, 'RIX', 'R', 'MI');
			// COMPOUND
			InsertClassEvent($TourId, 1, 3, 'CMJ', 'C', 'MJ');
			InsertClassEvent($TourId, 1, 3, 'CMC', 'C', 'MC');
			InsertClassEvent($TourId, 1, 3, 'CMM', 'C', 'MM');
			InsertClassEvent($TourId, 1, 3, 'CMI', 'C', 'MI');
			InsertClassEvent($TourId, 1, 3, 'CFJ', 'C', 'FJ');
			InsertClassEvent($TourId, 1, 3, 'CFC', 'C', 'FC');
			InsertClassEvent($TourId, 1, 3, 'CFM', 'C', 'FM');
			InsertClassEvent($TourId, 1, 3, 'CFI', 'C', 'FI');
			InsertClassEvent($TourId, 1, 1, 'CJX', 'C', 'FJ');
			InsertClassEvent($TourId, 2, 1, 'CJX', 'C', 'MJ');
			InsertClassEvent($TourId, 1, 1, 'CCX', 'C', 'FC');
			InsertClassEvent($TourId, 2, 1, 'CCX', 'C', 'MC');
			InsertClassEvent($TourId, 1, 1, 'CMX', 'C', 'FM');
			InsertClassEvent($TourId, 2, 1, 'CMX', 'C', 'MM');
			InsertClassEvent($TourId, 1, 1, 'CIX', 'C', 'FI');
			InsertClassEvent($TourId, 2, 1, 'CIX', 'C', 'MI');
			// BAREBOW
			InsertClassEvent($TourId, 1, 3, 'BMJ', 'B', 'MJ');
			InsertClassEvent($TourId, 1, 3, 'BMC', 'B', 'MC');
			InsertClassEvent($TourId, 1, 3, 'BMM', 'B', 'MM');
			InsertClassEvent($TourId, 1, 3, 'BMI', 'B', 'MI');
			InsertClassEvent($TourId, 1, 3, 'BFJ', 'B', 'FJ');
			InsertClassEvent($TourId, 1, 3, 'BFC', 'B', 'FC');
			InsertClassEvent($TourId, 1, 3, 'BFM', 'B', 'FM');
			InsertClassEvent($TourId, 1, 3, 'BFI', 'B', 'FI');
			InsertClassEvent($TourId, 1, 1, 'BJX', 'B', 'FJ');
			InsertClassEvent($TourId, 2, 1, 'BJX', 'B', 'MJ');
			InsertClassEvent($TourId, 1, 1, 'BCX', 'B', 'FC');
			InsertClassEvent($TourId, 2, 1, 'BCX', 'B', 'MC');
			InsertClassEvent($TourId, 1, 1, 'BMX', 'B', 'FM');
			InsertClassEvent($TourId, 2, 1, 'BMX', 'B', 'MM');
			InsertClassEvent($TourId, 1, 1, 'BIX', 'B', 'FI');
			InsertClassEvent($TourId, 2, 1, 'BIX', 'B', 'MI');
    		break;    	
    	case '4':
			// individuais
			InsertClassEvent($TourId, 0, 1, 'RMO', 'R', 'MO');
			InsertClassEvent($TourId, 0, 1, 'RFO', 'R', 'FO');
			InsertClassEvent($TourId, 0, 1, 'CMO', 'C', 'MO');
			InsertClassEvent($TourId, 0, 1, 'CFO', 'C', 'FO');
			// W1
			InsertClassEvent($TourId, 0, 1, 'W1M', 'W1', 'MO');
			InsertClassEvent($TourId, 0, 1, 'W1F', 'W1', 'FO');
			// equipes
			InsertClassEvent($TourId, 1, 3, 'RMO', 'R', 'MO');
			InsertClassEvent($TourId, 1, 3, 'RFO', 'R', 'FO');
			InsertClassEvent($TourId, 1, 1, 'ROX', 'R', 'FO');
			InsertClassEvent($TourId, 2, 1, 'ROX', 'R', 'MO');
			InsertClassEvent($TourId, 1, 3, 'CMO', 'C', 'MO');
			InsertClassEvent($TourId, 1, 3, 'CFO', 'C', 'FO');
			InsertClassEvent($TourId, 1, 1, 'COX', 'C', 'FO');
			InsertClassEvent($TourId, 2, 1, 'COX', 'C', 'MO');
			// W1
			InsertClassEvent($TourId, 1, 3, 'W1M', 'W1', 'M');
			InsertClassEvent($TourId, 1, 3, 'W1F', 'W1', 'F');
			InsertClassEvent($TourId, 1, 1, 'W1X', 'W1', 'F');
			InsertClassEvent($TourId, 2, 1, 'W1X', 'W1', 'M');
    		break;    	
    }
}