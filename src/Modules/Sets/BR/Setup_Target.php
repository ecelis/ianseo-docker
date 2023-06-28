<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default SubClasses
$i=1;
CreateSubClass($TourId, $i++, 'CN', 'Convidado');
CreateSubClass($TourId, $i++, 'AC', 'Acre');
CreateSubClass($TourId, $i++, 'AL', 'Alagoas');
CreateSubClass($TourId, $i++, 'AP', 'Amapá');
CreateSubClass($TourId, $i++, 'AM', 'Amazonas');
CreateSubClass($TourId, $i++, 'BA', 'Bahia');
CreateSubClass($TourId, $i++, 'CE', 'Ceará');
CreateSubClass($TourId, $i++, 'DF', 'Distrito Federal');
CreateSubClass($TourId, $i++, 'ES', 'Espírito Santo');
CreateSubClass($TourId, $i++, 'GO', 'Goiás');
CreateSubClass($TourId, $i++, 'MA', 'Maranhão');
CreateSubClass($TourId, $i++, 'MT', 'Mato Grosso');
CreateSubClass($TourId, $i++, 'MS', 'Mato Grosso do Sul');
CreateSubClass($TourId, $i++, 'MG', 'Minas Gerais');
CreateSubClass($TourId, $i++, 'PA', 'Pará');
CreateSubClass($TourId, $i++, 'PB', 'Paraíba');
CreateSubClass($TourId, $i++, 'PR', 'Paraná');
CreateSubClass($TourId, $i++, 'PE', 'Pernambuco');
CreateSubClass($TourId, $i++, 'PI', 'Piauí');
CreateSubClass($TourId, $i++, 'RJ', 'Rio de Janeiro');
CreateSubClass($TourId, $i++, 'RN', 'Rio Grande do Norte');
CreateSubClass($TourId, $i++, 'RS', 'Rio Grande do Sul');
CreateSubClass($TourId, $i++, 'RO', 'Rondônia');
CreateSubClass($TourId, $i++, 'RR', 'Roraima');
CreateSubClass($TourId, $i++, 'SC', 'Santa Catarina');
CreateSubClass($TourId, $i++, 'SP', 'São Paulo');
CreateSubClass($TourId, $i++, 'SE', 'Sergipe');
CreateSubClass($TourId, $i++, 'TO', 'Tocantins');


// default Classes
CreateStandardClasses($TourId, $TourType, $SubRule);

// default Distances
switch($TourType) {
	case 1: // ROUND 1440
		CreateDistanceNew($TourId, $TourType, '_M',  array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_F',  array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_MJ', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_FJ', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_MC', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_FC', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_MI', array(array('40 m',40), array('30 m',30), array('20 m',20), array('15 m',15)));
		CreateDistanceNew($TourId, $TourType, '_FI', array(array('40 m',40), array('30 m',30), array('20 m',20), array('15 m',15)));
		CreateDistanceNew($TourId, $TourType, '_MM', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_FM', array(array('60 m',60), array('50 m',50), array('40 m',40), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_MO', array(array('90 m',90), array('70 m',70), array('50 m',50), array('30 m',30)));
		CreateDistanceNew($TourId, $TourType, '_FO', array(array('70 m',70), array('60 m',60), array('50 m',50), array('30 m',30)));
		break;
	case 3: // 70m + 50m
		switch($SubRule) {
			case '1':
				CreateDistanceNew($TourId, $TourType, 'RM',  array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'RF',  array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'R_J', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'R_C', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'R_I', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'R_M', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'R_O', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'CM', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'CF', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_J', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_C', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_I', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'C_O', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'BM',  array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'BF',  array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'B_J', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'B_C', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'B_I', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50), array('50m-2',50)));
				break;
			case '2':
				CreateDistanceNew($TourId, $TourType, 'R%', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'B%', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50)));
				break;
			case '3':
				CreateDistanceNew($TourId, $TourType, 'R_J', array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'R_C', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'R_I', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'R_M', array(array('60m-1',60), array('60m-2',60)));
				CreateDistanceNew($TourId, $TourType, 'C_J', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_C', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_M', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'C_I', array(array('30m-1',30), array('30m-2',30)));
				CreateDistanceNew($TourId, $TourType, 'B_J', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'B_C', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'B_I', array(array('20m-1',20), array('20m-2',20)));
				CreateDistanceNew($TourId, $TourType, 'B_M', array(array('50m-1',50), array('50m-2',50)));				
				break;
			case '4':
				CreateDistanceNew($TourId, $TourType, 'R%',  array(array('70m-1',70), array('70m-2',70)));
				CreateDistanceNew($TourId, $TourType, 'C%', array(array('50m-1',50), array('50m-2',50)));
				CreateDistanceNew($TourId, $TourType, 'W1%', array(array('50m-1',50), array('50m-2',50)));
		}
		break;
	case 6:
		CreateDistanceNew($TourId, $TourType, '%', array(array('18m-1',18), array('18m-2',18)));
		break;
}

if($TourType==3) { // Only have matches on 50/70m 
	// default Events
	CreateStandardEvents($TourId, $SubRule, $TourType!=6);

	// Classes in Events
	InsertStandardEvents($TourId, $SubRule, $TourType!=6);

	// Finals & TeamFinals
	CreateFinals($TourId);
}

// Default Target
switch($TourType) {
	case 1:
		CreateTargetFace($TourId, 1, '~Padrão', '%', '1', 5, 122, 5, 122, 9, 80, 9, 80);
		// optional target faces
		CreateTargetFace($TourId, 2, '~Opção1', '%', '',  5, 122, 5, 122, 5, 80,  9, 80);
		CreateTargetFace($TourId, 3, '~Opção2', '%', '',  5, 122, 5, 122, 9, 80, 10, 80);
		break;
	case 3:
		CreateTargetFace($TourId, 1, '~Padrão R/B', 'REG-^R|^B', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, 2, '~Padrão C', 'REG-^C', '1',  9, 80, 9, 80);
		if ($SubRule==1 or $SubRule==4){
		CreateTargetFace($TourId, 3, '~Padrão W1', 'REG-^W1', '1', 5, 80, 5, 80);
		}
		break;
	case 6:
		CreateTargetFace($TourId, 1, '~Face Simples', 'REG-^R|^B', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, 2, '~CO Triplo', 'REG-^C', '1', 4, 40, 4, 40);
		// optional target faces
		CreateTargetFace($TourId, 3, '~Triplo Rec/BB', 'REG-^R|^B', '',  2, 40, 2, 40);
		break;
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 16, 4);

// Update Tour details
$tourDetails=array(
	'ToCollation' => $tourCollation,
	'ToTypeName' => $tourDetTypeName,
	'ToNumDist' => $tourDetNumDist,
	'ToNumEnds' => $tourDetNumEnds,
	'ToMaxDistScore' => $tourDetMaxDistScore,
	'ToMaxFinIndScore' => $tourDetMaxFinIndScore,
	'ToMaxFinTeamScore' => $tourDetMaxFinTeamScore,
	'ToCategory' => $tourDetCategory,
	'ToElabTeam' => $tourDetElabTeam,
	'ToElimination' => $tourDetElimination,
	'ToGolds' => $tourDetGolds,
	'ToXNine' => $tourDetXNine,
	'ToGoldsChars' => $tourDetGoldsChars,
	'ToXNineChars' => $tourDetXNineChars,
	'ToDouble' => $tourDetDouble,
//	'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);

