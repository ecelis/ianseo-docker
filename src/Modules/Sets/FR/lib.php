<?php

/*

STANDARD DEFINITIONS (Target Tournaments)

*/
require_once(dirname(dirname(__FILE__)).'/lib.php');

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'FRA';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type='FR') {
	$i=1;
	if($Type!='3D') CreateDivision($TourId, $i++, 'CL', 'Arc Classique', 1, 'R', 'R');
	CreateDivision($TourId, $i++, 'CO', 'Arc à Poulies', 1, 'C', 'C');
	if($Type=='FIELD') {
		CreateDivision($TourId, $i++, 'BB', 'Arc Nu', 1, 'B', 'B');
	} elseif($Type=='3D') {
		CreateDivision($TourId, $i++, 'BB', 'Arc Nu');
		CreateDivision($TourId, $i++, 'AD', 'Arc Droit');
		CreateDivision($TourId, $i++, 'AC', 'Arc Chasse');
		CreateDivision($TourId, $i++, 'TL', 'Traditionnel');
	}
}

function CreateStandardClasses($TourId, $TourType, $SubRule) {
	$SubYouth=array(1,2,3);
	$SubJuniors=array(1,2,3,4,5,6,7,9,10,11);
	$SubAdults=array(1,4,5,6,7,9,10,11);
	$SubFederal=array(8,11);
	$i=1;
	switch($TourType) {
		case '6': // INDOORS 18
		case '7': // INDOORS 25
		case '8': // INDOORS 25+18
			switch($SubRule) {
				case '1':
					// All classes...
					CreateClass($TourId, $i++,  1, 10, 1, 'U11F', 'U11F,U13F', 'U11 Femme', '1', 'CL', '', '');
					CreateClass($TourId, $i++,  1, 10, 0, 'U11H', 'U11H,U13H', 'U11 Homme', '1', 'CL', '', '');
					CreateClass($TourId, $i++, 11, 12, 1, 'U13F', 'U13F,U15F', 'U13 Femme', '1', 'CL', '', '');
					CreateClass($TourId, $i++, 11, 12, 0, 'U13H', 'U13H,U15H', 'U13 Homme', '1', 'CL', '', '');
					CreateClass($TourId, $i++, 13, 14, 1, 'U15F', 'U15F,U18F', 'U15 Femme', '1', 'BB,CL', '', '');
					CreateClass($TourId, $i++, 13, 14, 0, 'U15H', 'U15H,U18H', 'U15 Homme', '1', 'BB,CL', '', '');
					CreateClass($TourId, $i++, 15, 17, 1, 'U18F', 'U18F,U21F,S1F,SF', 'U18 Femme', '1', '', 'U18W', 'U18W');
					CreateClass($TourId, $i++, 15, 17, 0, 'U18H', 'U18H,U21H,S1H,SH', 'U18 Homme', '1', '', 'U18M', 'U18M');
					CreateClass($TourId, $i++, 18, 20, 1, 'U21F', 'U21F,S1F,SF', 'U21 Femme', '1', '', 'U21W', 'U21W');
					CreateClass($TourId, $i++, 18, 20, 0, 'U21H', 'U21H,S1H,SH', 'U21 Homme', '1', '', 'U21M', 'U21M');
					CreateClass($TourId, $i++, 21, 100, 1, 'SF', 'SF', 'Senior Femme', '1', 'BB', 'W', 'W');
					CreateClass($TourId, $i++, 21, 100, 0, 'SH', 'SH', 'Senior Homme', '1', 'BB', 'M', 'M');
					CreateClass($TourId, $i++, 21, 39, 1, 'S1F', 'S1F', 'Senior 1 Femme', '1', 'CO,CL', 'W', 'W');
					CreateClass($TourId, $i++, 21, 39, 0, 'S1H', 'S1H', 'Senior 1 Homme', '1', 'CO,CL', 'M', 'M');
					CreateClass($TourId, $i++, 40, 59, 1, 'S2F', 'S2F,S1F', 'Senior 2 Femme', '1', 'CO,CL', '', '');
					CreateClass($TourId, $i++, 40, 59, 0, 'S2H', 'S2H,S1H', 'Senior 2 Homme', '1', 'CO,CL', '', '');
					CreateClass($TourId, $i++, 60,100, 1, 'S3F', 'S3F,S2F,S1F', 'Senior 3 Femme', '1', 'CO,CL', '50W', '50W');
					CreateClass($TourId, $i++, 60,100, 0, 'S3H', 'S3H,S2H,S1H', 'Senior 3 Homme', '1', 'CO,CL', '50M', '50M');
					break;
				case '2':
					// Championships Adults...
					CreateClass($TourId, $i++, 18, 20, 1, 'U21F', 'U21F,S1F', 'U21 Femme', '1', '', 'U21W', 'U21W');
					CreateClass($TourId, $i++, 18, 20, 0, 'U21H', 'U21H,S1H', 'U21 Homme', '1', '', 'U21M', 'U21M');
					CreateClass($TourId, $i++, 21, 39, 1, 'S1F', 'S1F', 'Senior 1 Femme', '1', '', 'M', 'W');
					CreateClass($TourId, $i++, 21, 39, 0, 'S1H', 'S1H', 'Senior 1 Homme', '1', '', 'W', 'M');
					CreateClass($TourId, $i++, 40, 59, 1, 'S2F', 'S2F,S1F', 'Senior 2 Femme', '1', '', '', '');
					CreateClass($TourId, $i++, 40, 59, 0, 'S2H', 'S2H,S1H', 'Senior 2 Homme', '1', '', '', '');
					CreateClass($TourId, $i++, 60,100, 1, 'S3F', 'S3F,S2F,S1F', 'Senior 3 Femme', '1', '', '50W', '50W');
					CreateClass($TourId, $i++, 60,100, 0, 'S3H', 'S3H,S2H,S1H', 'Senior 3 Homme', '1', '', '50M', '50M');
					break;
				case '3':
					// Championships Youth...
					CreateClass($TourId, $i++, 11, 12, 1, 'U13F', 'U13F,U15F', 'U13 Femme', '1', 'CL', '', '');
					CreateClass($TourId, $i++, 11, 12, 0, 'U13H', 'U13H,U15H', 'U13 Homme', '1', 'CL', '', '');
					CreateClass($TourId, $i++, 13, 14, 1, 'U15F', 'U15F,U18F', 'U15 Femme', '1', 'BB,CL', '', '');
					CreateClass($TourId, $i++, 13, 14, 0, 'U15H', 'U15H,U18H', 'U15 Homme', '1', 'BB,CL', '', '');
					CreateClass($TourId, $i++, 15, 17, 1, 'U18F', 'U18F,U21F,1F', 'U18 Femme', '1', '', 'U18W', 'U18W');
					CreateClass($TourId, $i++, 15, 17, 0, 'U18H', 'U18H,U21H,1H', 'U18 Homme', '1', '', 'U18M', 'U18M');
					CreateClass($TourId, $i++, 18, 20, 1, 'U21F', 'U21F,1F', 'U21 Femme', '1', '', 'U21W', 'U21W');
					CreateClass($TourId, $i++, 18, 20, 0, 'U21H', 'U21H,1H', 'U21 Homme', '1', '', 'U21M', 'U21M');
					break;
			}
			break;
		case '3': // 72 arrows round
			// we create all classes anyway
			if(in_array($SubRule, $SubYouth)) {
				CreateClass($TourId, $i++,  1, 10, 1, 'U11F', 'U11F,U13F', 'U11 Femme', '1', 'CL', '', '');
				CreateClass($TourId, $i++,  1, 10, 0, 'U11H', 'U11H,U13H', 'U11 Homme', '1', 'CL', '', '');
				CreateClass($TourId, $i++, 11, 12, 1, 'U13F', 'U13F,U15F', 'U13 Femme', '1', 'CL', '', '');
				CreateClass($TourId, $i++, 11, 12, 0, 'U13H', 'U13H,U15H', 'U13 Homme', '1', 'CL', '', '');
				CreateClass($TourId, $i++, 13, 14, 1, 'U15F', 'U15F,U18F', 'U15 Femme', '1', 'CL', '', '');
				CreateClass($TourId, $i++, 13, 14, 0, 'U15H', 'U15H,U18H', 'U15 Homme', '1', 'CL', '', '');
			}
			if(in_array($SubRule, $SubJuniors)) {
				CreateClass($TourId, $i++, 15, 17, 1, 'U18F', 'U18F,U21F,S1F', 'U18 Femme', '1', 'CL,CO', 'U18W', 'U18W');
				CreateClass($TourId, $i++, 15, 17, 0, 'U18H', 'U18H,U21H,S1H', 'U18 Homme', '1', 'CL,CO', 'U18M', 'U18M');
				CreateClass($TourId, $i++, 18, 20, 1, 'U21F', 'U21F,S1F', 'U21 Femme', '1', 'CL,CO', 'U21W', 'U21W');
				CreateClass($TourId, $i++, 18, 20, 0, 'U21H', 'U21H,S1H', 'U21 Homme', '1', 'CL,CO', 'U21M', 'U21M');
			}
			if(in_array($SubRule, $SubAdults)) {
				CreateClass($TourId, $i++, 21, 39, 1, 'S1F', 'S1F', 'Senior 1 Femme', '1', 'CL,CO', 'W', 'W');
				CreateClass($TourId, $i++, 21, 39, 0, 'S1H', 'S1H', 'Senior 1 Homme', '1', 'CL,CO', 'M', 'M');
				CreateClass($TourId, $i++, 40, 59, 1, 'S2F', 'S2F,S1F', 'Senior 2 Femme', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 40, 59, 0, 'S2H', 'S2H,S1H', 'Senior 2 Homme', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 60,100, 1, 'S3F', 'S3F,S2F,S1F', 'Senior 3 Femme', '1', 'CL,CO', '50W', '50W');
				CreateClass($TourId, $i++, 60,100, 0, 'S3H', 'S3H,S2H,S1H', 'Senior 3 Homme', '1', 'CL,CO', '50M', '50M');
			}

			if(in_array($SubRule, $SubFederal)) {
				// Ex Federal joined together with international 72 arrows
				CreateClass($TourId, $i++, 18, 20, 1, 'JW', 'JW,1W', 'U21 Femme', '1', 'CL,CO', 'U21W', 'U21W');
				CreateClass($TourId, $i++, 18, 20, 0, 'JM', 'JM,1M', 'U21 Homme', '1', 'CL,CO', 'U21M', 'U21M');
				CreateClass($TourId, $i++, 21, 39, 1, '1W', '1W', 'Senior 1 Femme', '1', 'CL,CO', 'W', 'W');
				CreateClass($TourId, $i++, 21, 39, 0, '1M', '1M', 'Senior 1 Homme', '1', 'CL,CO', 'M', 'M');
				CreateClass($TourId, $i++, 40, 59, 1, '2W', '2W,1W', 'Senior 2 Femme', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 40, 59, 0, '2M', '2M,1M', 'Senior 2 Homme', '1', 'CL,CO', '', '');
				CreateClass($TourId, $i++, 60,100, 1, '3W', '3W,2W,1W', 'Senior 3 Femme', '1', 'CL,CO', '50W', '50W');
				CreateClass($TourId, $i++, 60,100, 0, '3M', '3M,2M,1M', 'Senior 3 Homme', '1', 'CL,CO', '50M', '50M');
			}
			break;
	}
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=false) {
	//$TargetR=($Outdoor?5:2);
	//$TargetC=($Outdoor?9:4);
	//$TargetSizeR=($Outdoor ? 122 : 40);
	//$TargetSizeC=($Outdoor ? 80 : 40);
	//$DistanceR=($Outdoor ? 70 : 18);
	//$DistanceRcm=($Outdoor ? 60 : 18);
	//$DistanceC=($Outdoor ? 50 : 18);

	$i=1;
	switch($TourType) {
		case 6: // INDOOR 18m
			$TargetR=2;
			$TargetC=4;
			$Distance=18;
			$TargetSize4=40;
			$TargetSize6=60;

			// NEVER as Team
			switch($SubRule) {
				case '2': // Championships Adults
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, '1FCL', 'Classique Senior 1 Femme', 1, 240, 240, 0, 0, 'RW', 'RW', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, '1HCL', 'Classique Senior 1 Homme', 1, 240, 240, 0, 0, 'RM', 'RM', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetR, 5, 3, 1, 5, 3, 1, '2FCL', 'Classique Senior 2 Femme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, '2HCL', 'Classique Senior 2 Homme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetR, 5, 3, 1, 5, 3, 1, '3FCL', 'Classique Senior 3 Femme', 1, 240, 240, 0, 0, 'R50W', 'R50W', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, '3HCL', 'Classique Senior 3 Homme', 1, 240, 240, 0, 0, 'R50M', 'R50M', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetC, 5, 3, 1, 5, 3, 1, '1FCO', 'Poulies Senior 1 Femme', 0, 240, 240, 0, 0, 'CW', 'CW', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetC, 5, 3, 1, 5, 3, 1, '1HCO', 'Poulies Senior 1 Homme', 0, 240, 240, 0, 0, 'CM', 'CM', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetC, 5, 3, 1, 5, 3, 1, '2FCO', 'Poulies Senior 2 Femme', 0, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetC, 5, 3, 1, 5, 3, 1, '2HCO', 'Poulies Senior 2 Homme', 0, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetC, 5, 3, 1, 5, 3, 1, '3FCO', 'Poulies Senior 3 Femme', 0, 240, 240, 0, 0, 'C50W', 'C50W', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetC, 5, 3, 1, 5, 3, 1, '3HCO', 'Poulies Senior 3 Homme', 0, 240, 240, 0, 0, 'C50M', 'C50M', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetR, 5, 3, 1, 5, 3, 1, 'AFBB', 'Arc Nu Scratch Femme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'AHBB', 'Arc Nu Scratch Homme', 1, 240, 240, 0, 0, '', '', $TargetSize4, $Distance);
					break;
				case '3': // Championships YOUTH
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetR, 5, 3, 1, 5, 3, 1, 'U13FCL', 'Classique U13 Femme', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'U13HCL', 'Classique U13 Homme', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'U15FCL', 'Classique U15 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'U15HCL', 'Classique U15 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'U18FCL', 'Classique U18 Femme',    1, 240, MATCH_SEP_MEDALS, 0, 0, 'RU18W', 'RU18W', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0, 16, $TargetR, 5, 3, 1, 5, 3, 1, 'U18HCL', 'Classique U18 Homme',    1, 240, MATCH_SEP_MEDALS, 0, 0, 'RU18M', 'RU18M', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'U21FCL', 'Classique U21 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, 'RU21W', 'RU21W', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  8, $TargetR, 5, 3, 1, 5, 3, 1, 'U21HCL', 'Classique U21 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, 'RU21M', 'RU21M', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetC, 5, 3, 1, 5, 3, 1, 'U18FCO', 'Poulies U18 Femme',      0, 240, MATCH_SEP_MEDALS, 0, 0, 'CU18W', 'CU18W', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetC, 5, 3, 1, 5, 3, 1, 'U18HCO', 'Poulies U18 Homme',      0, 240, MATCH_SEP_MEDALS, 0, 0, 'CU18M', 'CU18M', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetC, 5, 3, 1, 5, 3, 1, 'U21FCO', 'Poulies U21 Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, 'CU21W', 'CU21W', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetC, 5, 3, 1, 5, 3, 1, 'U21HCO', 'Poulies U21 Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, 'CU21M', 'CU21M', $TargetSize4, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  2, $TargetR, 5, 3, 1, 5, 3, 1, 'YFBB', 'Arc Nu Jeune Femme',       1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					CreateEvent($TourId, $i++, 0, 0,  4, $TargetR, 5, 3, 1, 5, 3, 1, 'YHBB', 'Arc Nu Jeune Homme',       1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', $TargetSize6, $Distance);
					break;
			}
			break;
		case 3: // Outdoor championships
			switch($SubRule) {
				case 2: // TNJ
					// Individuals
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U13FCL', 'Classique U13 Femme', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U13HCL', 'Classique U13 Homme', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U15FCL', 'Classique U15 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U15HCL', 'Classique U15 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U18FCL', 'Classique U18 Femme',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U18HCL', 'Classique U18 Homme',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U21FCL', 'Classique U21 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 14, 5, 5, 3, 1, 5, 3, 1, 'U21HCL', 'Classique U21 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 14, 9, 5, 3, 1, 5, 3, 1, 'YFCO', 'Poulies Jeune Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 14, 9, 5, 3, 1, 5, 3, 1, 'YHCO', 'Poulies Jeune Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U13FCL2', 'Classique U13 Femme (5-8)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'U13FCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U13HCL2', 'Classique U13 Homme (5-8)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'U13HCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U15FCL2', 'Classique U15 Femme (5-8)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'U15FCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U15HCL2', 'Classique U15 Homme (5-8)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'U15HCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U18FCL2', 'Classique U18 Femme (5-8)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'U18FCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U18HCL2', 'Classique U18 Homme (5-8)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'U18HCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U21FCL2', 'Classique U21 Femme (5-8)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'U21FCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U21HCL2', 'Classique U21 Homme (5-8)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'U21HCL', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'YFCO2', 'Poulies Jeune Femme (5-8)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YFCO', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'YHCO2', 'Poulies Jeune Homme (5-8)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YHCO', '0', '0', 5);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U13FCL3', 'Classique U13 Femme (9-12)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'U13FCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U13HCL3', 'Classique U13 Homme (9-12)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'U13HCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U15FCL3', 'Classique U15 Femme (9-12)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'U15FCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U15HCL3', 'Classique U15 Homme (9-12)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'U15HCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U18FCL3', 'Classique U18 Femme (9-12)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'U18FCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U18HCL3', 'Classique U18 Homme (9-12)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'U18HCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U21FCL3', 'Classique U21 Femme (9-12)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'U21FCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U21HCL3', 'Classique U21 Homme (9-12)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'U21HCL', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, 'YFCO3', 'Poulies Jeune Femme (9-12)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YFCO', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, 'YHCO3', 'Poulies Jeune Homme (9-12)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YHCO', '0', '0', 9);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U13FCL4', 'Classique U13 Femme (13-16)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'U13FCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U13HCL4', 'Classique U13 Homme (13-16)', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30, 'U13HCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U15FCL4', 'Classique U15 Femme (13-16)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'U15FCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U15HCL4', 'Classique U15 Homme (13-16)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40, 'U15HCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U18FCL4', 'Classique U18 Femme (13-16)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'U18FCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U18HCL4', 'Classique U18 Homme (13-16)',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60, 'U18HCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U21FCL4', 'Classique U21 Femme (13-16)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'U21FCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 5, 5, 3, 1, 5, 3, 1, 'U21HCL4', 'Classique U21 Homme (13-16)',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70, 'U21HCL3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'YFCO4', 'Poulies Jeune Femme (13-16)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YFCO3', '0', '0', 13);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'YHCO4', 'Poulies Jeune Homme (13-16)',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50, 'YHCO3', '0', '0', 13);

					// Team
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  2, 5, 4, 4, 2, 4, 4, 2, 'DMCU21', 'Double Mixte Classique Juniors',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 1,  2, 5, 4, 4, 2, 4, 4, 2, 'DMCU18', 'Double Mixte Classique Cadets',   1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60);
					CreateEvent($TourId, $i++, 1, 1,  2, 9, 4, 4, 2, 4, 4, 2, 'DMPY', 'Double Mixte Poulies Jeunes',    0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					// always second team!
					safe_w_sql("update Events set EvTeamCreationMode=2 where EvTeamEvent=1 and EvTournament=$TourId");
					break;
				case 3: // Championships Youth
					CreateEvent($TourId, $i++, 0, 0,  4, 5, 5, 3, 1, 5, 3, 1, 'U13FCL', 'Classique U13 Femme', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'U13HCL', 'Classique U13 Homme', 1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 30);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'U15FCL', 'Classique U15 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'U15HCL', 'Classique U15 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 40);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'U18FCL', 'Classique U18 Femme',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'U18HCL', 'Classique U18 Homme',    1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'U21FCL', 'Classique U21 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'U21HCL', 'Classique U21 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'U18FCO', 'Poulies U18 Femme',      0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, 'U18HCO', 'Poulies U18 Homme',      0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  2, 9, 5, 3, 1, 5, 3, 1, 'U21FCO', 'Poulies U21 Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, 'U21HCO', 'Poulies U21 Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);

					// MIXED TEAMS and Teams
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  4, 5, 4, 4, 2, 4, 4, 2, 'DMJ', 'Double Mixte Jeunes',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 2, 4, 6, 2, 'CJH', 'U18/U21 Hommes',   1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 2, 4, 6, 2, 'CJF', 'U18/U21 Filles',    1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 2, 4, 6, 2, 'BM',  'U13/U15',     1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 30);

					// drop out after 1/8
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 2, 4, 6, 2, 'CJH3', 'U18/U21 Hommes (9-12)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJH', '0', '0', '9');
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 2, 4, 6, 2, 'CJF3', 'U18/U21 Filles (9-12)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJF', '0', '0', '9');
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 2, 4, 6, 2, 'BM3',  'U13/U15 (9-12)',     1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 30,'BM', '0', '0', '9');

					// drop out after 1/4
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'CJH2', 'U18/U21 Hommes (5-8)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJH', '0', '0', '5');
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'CJF2', 'U18/U21 Filles (5-8)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJF', '0', '0', '5');
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'BM2',  'U13/U15 (5-8)',     1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 30,'BM', '0', '0', '5');

					// drop out after 1/4 of losers 1/8
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'CJH4', 'U18/U21 Hommes (13-16)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJH3', '0', '0', '13');
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'CJF4', 'U18/U21 Filles (13-16)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 60,'CJF3', '0', '0', '13');
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 2, 4, 6, 2, 'BM4',  'U13/U15 (13-16)',     1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 30,'BM3', '0', '0', '13');
					break;
				case 4: // Championships Scratch Recurve
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'FCL', 'Classique Scratch Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 24, 5, 5, 3, 1, 5, 3, 1, 'HCL', 'Classique Scratch Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);

					// MIXED TEAMS
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  4, 5, 4, 4, 2, 4, 4, 2, 'DMCL', 'Double Mixte Classique',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					break;
				case 5: // Championships Scratch Compound
					CreateEvent($TourId, $i++, 0, 0,  16, 9, 5, 3, 1, 5, 3, 1, 'FCO', 'Poulies Scratch Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  24, 9, 5, 3, 1, 5, 3, 1, 'HCO', 'Poulies Scratch Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);

					// MIXED TEAMS
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  4, 9, 4, 4, 2, 4, 4, 2, 'DMCO', 'Double Mixte Poulie',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					break;
				case 6: // Championships Veterans
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'VFCL', 'Classique Vétéran Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'VHCL', 'Classique Vétéran Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'WFCL', 'Classique Super Vétéran Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'WHCL', 'Classique Super Vétéran Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, 'VFCO', 'Poulies Vétéran Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 16, 9, 5, 3, 1, 5, 3, 1, 'VHCO', 'Poulies Vétéran Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, 'WFCO', 'Poulies Super Vétéran Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, 'WHCO', 'Poulies Super Vétéran Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					break;
				case 7: // D1/DNAP
					// 2019: we have 4 individual events and 1 match event for each of the 4 categories, sort of round robin
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'FCL1', 'Classique Femme 1',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'FCL2', 'Classique Femme 2',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'FCL3', 'Classique Femme 3',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'FCL4', 'Classique Femme 4',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'HCL1', 'Classique Homme 1',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'HCL2', 'Classique Homme 2',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'HCL3', 'Classique Homme 3',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 5, 5, 3, 1, 5, 3, 1, 'HCL4', 'Classique Homme 4',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'FCO1', 'Poulies Femme 1',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'FCO2', 'Poulies Femme 2',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'FCO3', 'Poulies Femme 3',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'FCO4', 'Poulies Femme 4',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'HCO1', 'Poulies Homme 1',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'HCO2', 'Poulies Homme 2',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'HCO3', 'Poulies Homme 3',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 64, 9, 5, 3, 1, 5, 3, 1, 'HCO4', 'Poulies Homme 4',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 0, 5, 5, 3, 1, 5, 3, 1, 'FCL', 'Classique Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 0, 5, 5, 3, 1, 5, 3, 1, 'HCL', 'Classique Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 0, 9, 5, 3, 1, 5, 3, 1, 'FCO', 'Poulies Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 0, 9, 5, 3, 1, 5, 3, 1, 'HCO', 'Poulies Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					// teams... Team matches
					$i=1;
					CreateEvent($TourId, $i++, 1, 0, 64, 5, 4, 6, 3, 4, 6, 3, 'FCL', 'Equipe Classique Femme',   1, 240, MATCH_ALL_SEP, 0, 0, '', '', 122, 70, '', '', '16');
					CreateEvent($TourId, $i++, 1, 0, 64, 5, 4, 6, 3, 4, 6, 3, 'HCL', 'Equipe Classique Homme',   1, 240, MATCH_ALL_SEP, 0, 0, '', '', 122, 70, '', '', '16');
					CreateEvent($TourId, $i++, 1, 0, 64, 9, 4, 6, 3, 4, 6, 3, 'FCO', 'Equipe Poulies Femme',     0, 240, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, '', '', '8');
					CreateEvent($TourId, $i++, 1, 0, 64, 9, 4, 6, 3, 4, 6, 3, 'HCO', 'Equipe Poulies Homme',     0, 240, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, '', '', '16');
					break;
				case 8: // Fédéral
					// NO EVENTS!!!
					break;
				case 9: // DR/D2
					CreateEvent($TourId, $i++, 1, 0,  12, 5, 4, 6, 3, 4, 6, 3, 'DRRF', 'Equipes DR Classique Femme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 0,  12, 5, 4, 6, 3, 4, 6, 3, 'DRRH', 'Equipes DR Classique Homme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 0,  12, 9, 4, 6, 3, 4, 6, 3, 'DRCF', 'Equipes DR Poulies Femme',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 1, 0,  12, 9, 4, 6, 3, 4, 6, 3, 'DRCH', 'Equipes DR Poulies Homme',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'D2F', 'Equipes D2 Femme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'D2H', 'Equipes D2 Homme',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);

					// losers of 1/12 brackets 1st round (all byes in 1/8 so go directly to 1/4 but need to be stated as 1/8 to work)
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'RF17', 'Equipes DR Classique Femme (17-20)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRF', '0', '8', 17);
					CreateEvent($TourId, $i++, 1, 0,  8, 5, 4, 6, 3, 4, 6, 3, 'RH17', 'Equipes DR Classique Homme (17-20)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRH', '0', '8', 17);
					CreateEvent($TourId, $i++, 1, 0,  8, 9, 4, 6, 3, 4, 6, 3, 'CF17', 'Equipes DR Poulies Femme (17-20)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCF', '0', '8', 17);
					CreateEvent($TourId, $i++, 1, 0,  8, 9, 4, 6, 3, 4, 6, 3, 'CH17', 'Equipes DR Poulies Homme (17-20)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCH', '0', '8', 17);

					// losers of 1/12 brackets 2nd round
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RF21', 'Equipes DR Classique Femme (21-24)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RF17', '0', '0', 21);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RH21', 'Equipes DR Classique Homme (21-24)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RH17', '0', '0', 21);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CF21', 'Equipes DR Poulies Femme (21-24)',   0, 0, MATCH_ALL_SEP, 0, 0, '', '',   80, 50, 'CF17', '0', '0', 21);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CH21', 'Equipes DR Poulies Homme (21-24)',   0, 0, MATCH_ALL_SEP, 0, 0, '', '',   80, 50, 'CH17', '0', '0', 21);

					// losers of 1/8 brackets of main stream
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'RF09', 'Equipes DR Classique Femme (9-12)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRF', '0', '0', 9);
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'RH09', 'Equipes DR Classique Homme (9-12)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRH', '0', '0', 9);
					CreateEvent($TourId, $i++, 1, 0,  4, 9, 4, 6, 3, 4, 6, 3, 'CF09', 'Equipes DR Poulies Femme (9-12)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCF', '0', '0', 9);
					CreateEvent($TourId, $i++, 1, 0,  4, 9, 4, 6, 3, 4, 6, 3, 'CH09', 'Equipes DR Poulies Homme (9-12)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCH', '0', '0', 9);
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'DF09', 'Equipes D2 Femme (9-12)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2F', '0', '0', 9);
					CreateEvent($TourId, $i++, 1, 0,  4, 5, 4, 6, 3, 4, 6, 3, 'DH09', 'Equipes D2 Homme (9-12)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2H', '0', '0', 9);

					// losers of 1/4 brackets of main stream
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RF05', 'Equipes DR Classique Femme (5-8)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRF', '0', '0', 5);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RH05', 'Equipes DR Classique Homme (5-8)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DRRH', '0', '0', 5);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CF05', 'Equipes DR Poulies Femme (5-8)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCF', '0', '0', 5);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CH05', 'Equipes DR Poulies Homme (5-8)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'DRCH', '0', '0', 5);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DF05', 'Equipes D2 Femme (5-8)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2F', '0', '0', 5);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DH05', 'Equipes D2 Homme (5-8)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'D2H', '0', '0', 5);

					// losers of 1/4 brackets of 1/8 losers (go for 13-16 position)
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RF13', 'Equipes DR Classique Femme (13-16)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RF09', '0', '0', 13);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'RH13', 'Equipes DR Classique Homme (13-16)', 1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'RH09', '0', '0', 13);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CF13', 'Equipes DR Poulies Femme (13-16)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'CF09', '0', '0', 13);
					CreateEvent($TourId, $i++, 1, 0,  2, 9, 4, 6, 3, 4, 6, 3, 'CH13', 'Equipes DR Poulies Homme (13-16)',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50, 'CH09', '0', '0', 13);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DF13', 'Equipes D2 Femme (13-16)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DF09', '0', '0', 13);
					CreateEvent($TourId, $i++, 1, 0,  2, 5, 4, 6, 3, 4, 6, 3, 'DH13', 'Equipes D2 Homme (13-16)',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70, 'DH09', '0', '0', 13);
					break;
				case 10: // Champs French
					// Championships Scratch Recurve
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'FCL', 'Classique Scratch Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 24, 5, 5, 3, 1, 5, 3, 1, 'HCL', 'Classique Scratch Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					// Championships Scratch Compound
					CreateEvent($TourId, $i++, 0, 0,  16, 9, 5, 3, 1, 5, 3, 1, 'FCO', 'Poulies Scratch Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  24, 9, 5, 3, 1, 5, 3, 1, 'HCO', 'Poulies Scratch Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);

					// MIXED TEAMS
					$i=1;
					CreateEvent($TourId, $i++, 1, 1,  4, 5, 4, 4, 2, 4, 4, 2, 'DMCL', 'Double Mixte Classique',  1, 0, MATCH_ALL_SEP, 0, 0, '', '',  122, 70);
					CreateEvent($TourId, $i++, 1, 1,  4, 9, 4, 4, 2, 4, 4, 2, 'DMCO', 'Double Mixte Poulie',  0, 0, MATCH_ALL_SEP, 0, 0, '', '',  80, 50);
					break;
				case 11:
					// Coupe de France
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, 'U21HCL', 'Classique U21 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, 'U21FCL', 'Classique U21 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, '1FCL', 'Classique Senior 1 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, '1HCL', 'Classique Senior 1 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, '2FCL', 'Classique Senior 2 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0, 16, 5, 5, 3, 1, 5, 3, 1, '2HCL', 'Classique Senior 2 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 70);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, '3FCL', 'Classique Senior 3 Femme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0,  8, 5, 5, 3, 1, 5, 3, 1, '3HCL', 'Classique Senior 3 Homme',   1, 240, MATCH_SEP_MEDALS, 0, 0, '', '', 122, 60);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, 'U21FCO', 'Poulies U21 Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 16, 9, 5, 3, 1, 5, 3, 1, 'U21HCO', 'Poulies U21 Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, '1FCO', 'Poulies Senior 1 Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 16, 9, 5, 3, 1, 5, 3, 1, '1HCO', 'Poulies Senior 1 Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, '2FCO', 'Poulies Senior 2 Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0, 16, 9, 5, 3, 1, 5, 3, 1, '2HCO', 'Poulies Senior 2 Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  4, 9, 5, 3, 1, 5, 3, 1, '3FCO', 'Poulies Senior 3 Femme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					CreateEvent($TourId, $i++, 0, 0,  8, 9, 5, 3, 1, 5, 3, 1, '3HCO', 'Poulies Senior 3 Homme',     0, 240, MATCH_SEP_MEDALS, 0, 0, '', '',  80, 50);
					break;
			}
			break;
	}
}

function InsertStandardEvents($TourId, $TourType, $SubRule) {
	switch($TourType) {
		case 6:
			switch($SubRule) {
				case '2':
					InsertClassEvent($TourId, 0, 1,'1HCL', 'CL', 'S1H');
					InsertClassEvent($TourId, 0, 1,'1FCL', 'CL', 'S1F');
					InsertClassEvent($TourId, 0, 1,'2HCL', 'CL', 'S2H');
					InsertClassEvent($TourId, 0, 1,'2FCL', 'CL', 'S2F');
					InsertClassEvent($TourId, 0, 1,'3HCL', 'CL', 'S3H');
					InsertClassEvent($TourId, 0, 1,'3FCL', 'CL', 'S3F');
					InsertClassEvent($TourId, 0, 1,'1HCO', 'CO', 'S1H');
					InsertClassEvent($TourId, 0, 1,'1FCO', 'CO', 'S1F');
					InsertClassEvent($TourId, 0, 1,'2HCO', 'CO', 'S2H');
					InsertClassEvent($TourId, 0, 1,'2FCO', 'CO', 'S2F');
					InsertClassEvent($TourId, 0, 1,'3HCO', 'CO', 'S3H');
					InsertClassEvent($TourId, 0, 1,'3FCO', 'CO', 'S3F');
					InsertClassEvent($TourId, 0, 1,'AHBB', 'BB', 'S1H');
					InsertClassEvent($TourId, 0, 1,'AFBB', 'BB', 'S1F');
					InsertClassEvent($TourId, 0, 1,'AHBB', 'BB', 'S2H');
					InsertClassEvent($TourId, 0, 1,'AFBB', 'BB', 'S2F');
					InsertClassEvent($TourId, 0, 1,'AHBB', 'BB', 'S3H');
					InsertClassEvent($TourId, 0, 1,'AFBB', 'BB', 'S3F');
					break;
				case '3': // Championships YOUTH
					$TargetSizeB=60;
					InsertClassEvent($TourId, 0, 1, 'U21HCL', 'CL','U21H');
					InsertClassEvent($TourId, 0, 1, 'U21FCL', 'CL','U21F');
					InsertClassEvent($TourId, 0, 1, 'U18HCL', 'CL','U18H');
					InsertClassEvent($TourId, 0, 1, 'U18FCL', 'CL','U18F');
					InsertClassEvent($TourId, 0, 1, 'U15HCL', 'CL','U15H');
					InsertClassEvent($TourId, 0, 1, 'U15FCL', 'CL','U15F');
					InsertClassEvent($TourId, 0, 1, 'U13HCL', 'CL','U13H');
					InsertClassEvent($TourId, 0, 1, 'U13FCL', 'CL','U13F');
					InsertClassEvent($TourId, 0, 1, 'U21HCO', 'CO','U21H');
					InsertClassEvent($TourId, 0, 1, 'U21FCO', 'CO','U21F');
					InsertClassEvent($TourId, 0, 1, 'U18HCO', 'CO','U18H');
					InsertClassEvent($TourId, 0, 1, 'U18FCO', 'CO','U18F');
					InsertClassEvent($TourId, 0, 1, 'YHBB', 'BB','U21H');
					InsertClassEvent($TourId, 0, 1, 'YFBB', 'BB','U21F');
					InsertClassEvent($TourId, 0, 1, 'YHBB', 'BB','U18H');
					InsertClassEvent($TourId, 0, 1, 'YFBB', 'BB','U18F');
					InsertClassEvent($TourId, 0, 1, 'YHBB', 'BB','U15H');
					InsertClassEvent($TourId, 0, 1, 'YFBB', 'BB','U15F');
					break;
			}
			break;
		case 3:
			switch($SubRule) {
				case 2: // TNJ
					InsertClassEvent($TourId, 0, 1, 'U21HCL', 'CL','U21H');
					InsertClassEvent($TourId, 0, 1, 'U21FCL', 'CL','U21F');
					InsertClassEvent($TourId, 0, 1, 'U18HCL', 'CL','U18H');
					InsertClassEvent($TourId, 0, 1, 'U18FCL', 'CL','U18F');
					InsertClassEvent($TourId, 0, 1, 'U15HCL', 'CL','U15H');
					InsertClassEvent($TourId, 0, 1, 'U15FCL', 'CL','U15F');
					InsertClassEvent($TourId, 0, 1, 'U13HCL', 'CL','U13H');
					InsertClassEvent($TourId, 0, 1, 'U13FCL', 'CL','U13F');
					InsertClassEvent($TourId, 0, 1, 'YHCO', 'CO','U21H');
					InsertClassEvent($TourId, 0, 1, 'YFCO', 'CO','U21F');
					InsertClassEvent($TourId, 0, 1, 'YHCO', 'CO','U18H');
					InsertClassEvent($TourId, 0, 1, 'YFCO', 'CO','U18F');
					// Mixed Team
					InsertClassEvent($TourId, 1, 1, 'DMCU21', 'CL','U21F');
					InsertClassEvent($TourId, 1, 1, 'DMCU18', 'CL','U18F');
					InsertClassEvent($TourId, 1, 1, 'DMPY', 'CO','U21F');
					InsertClassEvent($TourId, 1, 1, 'DMPY', 'CO','U18F');
					InsertClassEvent($TourId, 2, 1, 'DMCU21', 'CL','U21H');
					InsertClassEvent($TourId, 2, 1, 'DMCU18', 'CL','U18H');
					InsertClassEvent($TourId, 2, 1, 'DMPY', 'CO','U21H');
					InsertClassEvent($TourId, 2, 1, 'DMPY', 'CO','U18H');
					break;
				case 3: // Championship Youth
					InsertClassEvent($TourId, 0, 1, 'U21HCL', 'CL','U21H');
					InsertClassEvent($TourId, 0, 1, 'U21FCL', 'CL','U21F');
					InsertClassEvent($TourId, 0, 1, 'U18HCL', 'CL','U18H');
					InsertClassEvent($TourId, 0, 1, 'U18FCL', 'CL','U18F');
					InsertClassEvent($TourId, 0, 1, 'U15HCL', 'CL','U15H');
					InsertClassEvent($TourId, 0, 1, 'U15FCL', 'CL','U15F');
					InsertClassEvent($TourId, 0, 1, 'U13HCL', 'CL','U13H');
					InsertClassEvent($TourId, 0, 1, 'U13FCL', 'CL','U13F');
					InsertClassEvent($TourId, 0, 1, 'U21HCO', 'CO','U21H');
					InsertClassEvent($TourId, 0, 1, 'U21FCO', 'CO','U21F');
					InsertClassEvent($TourId, 0, 1, 'U18HCO', 'CO','U18H');
					InsertClassEvent($TourId, 0, 1, 'U18FCO', 'CO','U18F');
					// Mixed Team
					InsertClassEvent($TourId, 1, 1, 'DMJ', 'CL','U21F');
					InsertClassEvent($TourId, 1, 1, 'DMJ', 'CL','U18F');
					InsertClassEvent($TourId, 2, 1, 'DMJ', 'CL','U21H');
					InsertClassEvent($TourId, 2, 1, 'DMJ', 'CL','U18H');
					// Teams
					InsertClassEvent($TourId, 1, 3, 'CJH', 'CL','U21H');
					InsertClassEvent($TourId, 1, 3, 'CJH', 'CL','U18H');
					InsertClassEvent($TourId, 1, 3, 'CJF', 'CL','U21F');
					InsertClassEvent($TourId, 1, 3, 'CJF', 'CL','U18F');
					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','U13F');
					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','U13H');
					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','U15F');
					InsertClassEvent($TourId, 1, 3, 'BM', 'CL','U15H');
					break;
				case 4: // deprecated
					// Championships Scratch Recurve
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','U18F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','U21F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','SF');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','VF');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','WF');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','U18H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','U21H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','SH');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','VH');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','WH');
					// Mixed Team
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','U18F');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','U21F');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','SF');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','VF');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','WF');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','U18H');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','U21H');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','SH');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','VH');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','WH');
					break;
				case 5: // deprecated
					// Championships Scratch Compound
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','U18F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','U21F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','SF');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','VF');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','WF');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','U18H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','U21H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','SH');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','VH');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','WH');
					// Mixed Team
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','U18F');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','U21F');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','SF');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','VF');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','WF');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','U18H');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','U21H');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','SH');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','VH');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','WH');
					break;
				case 6: // deprecated
					// Championship Veteran
					InsertClassEvent($TourId, 0, 1, 'VFCL', 'CL','VF');
					InsertClassEvent($TourId, 0, 1, 'VHCL', 'CL','VH');
					InsertClassEvent($TourId, 0, 1, 'WFCL', 'CL','WF');
					InsertClassEvent($TourId, 0, 1, 'WHCL', 'CL','WH');
					InsertClassEvent($TourId, 0, 1, 'VFCO', 'CO','VF');
					InsertClassEvent($TourId, 0, 1, 'VHCO', 'CO','VH');
					InsertClassEvent($TourId, 0, 1, 'WFCO', 'CO','WF');
					InsertClassEvent($TourId, 0, 1, 'WHCO', 'CO','WH');
					break;
				case 7: // D1/DNAP... team events selection are as usual, indivudal no
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','U18F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','U21F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','S1F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','S2F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','S3F');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','U18H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','U21H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','S1H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','S2H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','S3H');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','U18F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','U21F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','S1F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','S2F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','S3F');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','U18H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','U21H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','S1H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','S2H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','S3H');
					// Teams
					InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','U18F');
					InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','U21F');
					InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','S1F');
					InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','S2F');
					InsertClassEvent($TourId, 1, 3, 'FCL', 'CL','S3F');
					InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','U18H');
					InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','U21H');
					InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','S1H');
					InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','S2H');
					InsertClassEvent($TourId, 1, 3, 'HCL', 'CL','S3H');
					InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','U18F');
					InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','U21F');
					InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','S1F');
					InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','S2F');
					InsertClassEvent($TourId, 1, 3, 'FCO', 'CO','S3F');
					InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','U18H');
					InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','U21H');
					InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','S1H');
					InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','S2H');
					InsertClassEvent($TourId, 1, 3, 'HCO', 'CO','S3H');
					break;
				case 8: // deprecated
					// Fédéral
					// no events
					break;
				case 9: // DR/D2
					InsertClassEvent($TourId, 1, 3, 'DRRF', 'CL','U18F');
					InsertClassEvent($TourId, 1, 3, 'DRRF', 'CL','U21F');
					InsertClassEvent($TourId, 1, 3, 'DRRF', 'CL','SF');
					InsertClassEvent($TourId, 1, 3, 'DRRF', 'CL','VF');
					InsertClassEvent($TourId, 1, 3, 'DRRF', 'CL','WF');
					InsertClassEvent($TourId, 1, 3, 'DRCF', 'CO','U18F');
					InsertClassEvent($TourId, 1, 3, 'DRCF', 'CO','U21F');
					InsertClassEvent($TourId, 1, 3, 'DRCF', 'CO','SF');
					InsertClassEvent($TourId, 1, 3, 'DRCF', 'CO','VF');
					InsertClassEvent($TourId, 1, 3, 'DRCF', 'CO','WF');
					InsertClassEvent($TourId, 1, 3, 'D2F', 'CL','U18F');
					InsertClassEvent($TourId, 1, 3, 'D2F', 'CL','U21F');
					InsertClassEvent($TourId, 1, 3, 'D2F', 'CL','SF');
					InsertClassEvent($TourId, 1, 3, 'D2F', 'CL','VF');
					InsertClassEvent($TourId, 1, 3, 'D2F', 'CL','WF');
					InsertClassEvent($TourId, 1, 3, 'DRRH', 'CL','U18H');
					InsertClassEvent($TourId, 1, 3, 'DRRH', 'CL','U21H');
					InsertClassEvent($TourId, 1, 3, 'DRRH', 'CL','SH');
					InsertClassEvent($TourId, 1, 3, 'DRRH', 'CL','VH');
					InsertClassEvent($TourId, 1, 3, 'DRRH', 'CL','WH');
					InsertClassEvent($TourId, 1, 3, 'DRCH', 'CO','U18H');
					InsertClassEvent($TourId, 1, 3, 'DRCH', 'CO','U21H');
					InsertClassEvent($TourId, 1, 3, 'DRCH', 'CO','SH');
					InsertClassEvent($TourId, 1, 3, 'DRCH', 'CO','VH');
					InsertClassEvent($TourId, 1, 3, 'DRCH', 'CO','WH');
					InsertClassEvent($TourId, 1, 3, 'D2H', 'CL','U18H');
					InsertClassEvent($TourId, 1, 3, 'D2H', 'CL','U21H');
					InsertClassEvent($TourId, 1, 3, 'D2H', 'CL','SH');
					InsertClassEvent($TourId, 1, 3, 'D2H', 'CL','VH');
					InsertClassEvent($TourId, 1, 3, 'D2H', 'CL','WH');
					break;
				case 10:
					// Championships Scratch Recurve
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','U18F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','U21F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','S1F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','S2F');
					InsertClassEvent($TourId, 0, 1, 'FCL', 'CL','S3F');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','U18H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','U21H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','S1H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','S2H');
					InsertClassEvent($TourId, 0, 1, 'HCL', 'CL','S3H');
					// Championships Scratch Compound
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','U18F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','U21F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','S1F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','S2F');
					InsertClassEvent($TourId, 0, 1, 'FCO', 'CO','S3F');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','U18H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','U21H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','S1H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','S2H');
					InsertClassEvent($TourId, 0, 1, 'HCO', 'CO','S3H');
					// Mixed Team Recurve
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','U18F');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','U21F');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','S1F');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','S2F');
					InsertClassEvent($TourId, 1, 1, 'DMCL', 'CL','S3F');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','U18H');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','U21H');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','S1H');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','S2H');
					InsertClassEvent($TourId, 2, 1, 'DMCL', 'CL','S3H');
					// Mixed Team Compound
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','U18F');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','U21F');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','S1F');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','S2F');
					InsertClassEvent($TourId, 1, 1, 'DMCO', 'CO','S3F');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','U18H');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','U21H');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','S1H');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','S2H');
					InsertClassEvent($TourId, 2, 1, 'DMCO', 'CO','S3H');
					break;
				case 11: // Coupe France
					// Recurve
					InsertClassEvent($TourId, 0, 1, 'U21FCL', 'CL','U21F');
					InsertClassEvent($TourId, 0, 1, '1FCL', 'CL','S1F');
					InsertClassEvent($TourId, 0, 1, '2FCL', 'CL','S2F');
					InsertClassEvent($TourId, 0, 1, '3FCL', 'CL','S3F');
					InsertClassEvent($TourId, 0, 1, 'U21HCL', 'CL','U21H');
					InsertClassEvent($TourId, 0, 1, '1HCL', 'CL','S1H');
					InsertClassEvent($TourId, 0, 1, '2HCL', 'CL','S2H');
					InsertClassEvent($TourId, 0, 1, '3HCL', 'CL','S3H');
					// Compound
					InsertClassEvent($TourId, 0, 1, 'U21FCO', 'CO','U21F');
					InsertClassEvent($TourId, 0, 1, '1FCO', 'CO','S1F');
					InsertClassEvent($TourId, 0, 1, '2FCO', 'CO','S2F');
					InsertClassEvent($TourId, 0, 1, '3FCO', 'CO','S3F');
					InsertClassEvent($TourId, 0, 1, 'U21HCO', 'CO','U21H');
					InsertClassEvent($TourId, 0, 1, '1HCO', 'CO','S1H');
					InsertClassEvent($TourId, 0, 1, '2HCO', 'CO','S2H');
					InsertClassEvent($TourId, 0, 1, '3HCO', 'CO','S3H');
					break;
			}
			break;
	}
}

function CreateFinals_FR_3_SetFRChampsD1DNAP($TourId) {
	CreateFinalsInd_FR_3_SetFRChampsD1DNAP($TourId);
	CreateFinalsTeam_FR_3_SetFRChampsD1DNAP($TourId);
}

/**
 * @param $TourId
 * @param string $StrEv2Delete [optional] SQL-escaped string that goes in the IN () statement
 */
function CreateFinalsInd_FR_3_SetFRChampsD1DNAP($TourId, $StrEv2Delete='') {
	safe_w_sql("INSERT INTO Finals (FinEvent, FinMatchNo, FinTournament, FinDateTime) 
		SELECT EvCode, GrMatchNo, EvTournament, " . StrSafe_DB(date('Y-m-d H:i:s')) . "
		FROM Events 
		INNER JOIN Grids ON GrMatchNo between 128 and 207
		WHERE EvTournament=$TourId AND EvTeamEvent='0' and right(EvCode, 1) in (1,2,3,4)".($StrEv2Delete ? " AND EvCode IN ($StrEv2Delete)" : ""));
}

/**
 * @param $TourId
 * @param string $StrEv2Delete [optional] SQL-escaped string that goes in the IN () statement
 */
function CreateFinalsTeam_FR_3_SetFRChampsD1DNAP($TourId, $StrEv2Delete='') {
	safe_w_sql("INSERT INTO TeamFinals (TfEvent, TfMatchNo, TfTournament, TfDateTime) 
		SELECT EvCode, GrMatchNo, EvTournament, " . StrSafe_DB(date('Y-m-d H:i:s')) . " 
		FROM Events 
		INNER JOIN Grids ON GrMatchNo between 128 and 207
		WHERE EvTournament=$TourId AND EvTeamEvent='1'".($StrEv2Delete ? " AND EvCode IN ($StrEv2Delete)" : ""));
}

/*

FIELD DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-Field.php');

/*

3D DEFINITIONS (Target Tournaments)

*/

require_once(dirname(__FILE__).'/lib-3D.php');

