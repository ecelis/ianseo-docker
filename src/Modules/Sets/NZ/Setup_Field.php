<?php
/*

Common setup for Field

*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, 'FIELD');

// default SubClasses
CreateSubClass($TourId, 1, 'NZ', 'New Zealand');
CreateSubClass($TourId, 2, 'IN', 'International');
CreateSubClass($TourId, 3, 'OP', 'Open');

// default Classes
CreateStandardFieldClasses($TourId, $SubRule);

// default Distances
switch($TourType) {
	case 9: // Type_HF 12+12 	(1 distance)
		CreateDistanceNew($TourId, $TourType, '%', array(array('Course',0)));
		break;
	case 10: // Type_HF 24+24 	(2 distances)
	case 12: // Type_HF 12+12 	(2 distances)
		CreateDistanceNew($TourId, $TourType, '%', array(array('Unmarked',0), array('Marked',0)));
		break;
}

// default Events
CreateStandardFieldEvents($TourId, $SubRule);

// insert class in events
InsertStandardFieldEvents($TourId, $SubRule);

// Elimination rounds
InsertStandardFieldEliminations($TourId, $SubRule);

// Finals & TeamFinals
CreateFinals($TourId);

// Default Target
/*
ArcheryNZ customisation
9 - default WA field round (Unmarked or Marked) 24 targets
10 & 12 - assume WA Unmarked round + WA Marked round
	default target peg plus optional target pegs for NZ 3D Unmarked + WA Marked
*/

switch($TourType) {
	// function expects ($TourId, $Id, $Name, $Classes, $Default, $T1, $W1, $T2=0, $W2=0, etc.)
	/*
		IANSEO Target Faces: ($Tn)
		1 - Indoor (1-big 10)
		2 - Indoor (6-big 10)
		3 - Indoor (1-small 10)
		4 - Indoor (6-small 10)
		5 - Outdoor (1-X)
		6 - Field Archery
		7 - Hit-Miss
		8 - 3D Standard
		9 - Outdoor (5-X)
		10 - Outdoor (6-X)
	*/
	case 9:
		switch ($SubRule) {
			case '1':
				$i=1;
				// All WA and Jnr Pegs
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R65M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R50M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RM', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU16B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RD', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU14B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RN', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU11B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R65W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R50W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RW', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU16G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU14G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU11G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C65M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C50M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CM', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU16B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CD', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU14B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CN', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU11B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C65W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C50W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CW', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU16G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU14G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU11G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B65M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B50M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BM', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU16B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BD', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU14B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BN', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU11B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B65W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B50W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BW', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU16G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU14G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU11G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T65M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T50M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TM', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU16B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TD', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU14B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TN', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU11B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T65W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T50W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TW', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU16G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU14G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU11G', '1', 6, 0);
				// Optional WA and Jnr 3D Pegs
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'X%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'L%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'T%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'B%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'R65M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'R50M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RM', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU21M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU18M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU16B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RD', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU14B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RN', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU11B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'R65W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'R50W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RW', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU21W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU18W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU16G', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU14G', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU11G', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'C65M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'C50M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CM', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU21M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU18M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU16B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CD', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU14B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CN', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU11B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'C65W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'C50W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CW', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU21W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU18W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU16G', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU14G', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU11G', '0', 8, 0);
				break;
			case '2':
				$i=1;
				// All WA Pegs
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R65M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R50M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RM', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R65W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R50W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RW', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C65M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C50M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CM', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C65W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C50W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CW', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B65M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B50M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BM', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B65W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B50W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BW', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T65M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T50M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TM', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T65W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T50W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TW', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18W', '1', 6, 0);
				// Optional WA 3D Pegs
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'X%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'L%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'T%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'B%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'R65M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'R50M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RM', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU21M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU18M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'R65W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'R50W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RW', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU21W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU18W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'C65M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'C50M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CM', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU21M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU18M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'C65W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'C50W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CW', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU21W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU18W', '0', 8, 0);
				break;
			case '3':
				$i=1;
				// All Jnr Pegs
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU16B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU14B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU11B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU16G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU14G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU11G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU16B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU14B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU11B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU16G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU14G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU11G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU16B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU14B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU11B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU16G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU14G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU11G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18M', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU16B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU14B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU11B', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18W', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU16G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU14G', '1', 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU11G', '1', 6, 0);
				// Optional Jnr 3D Pegs
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'X%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'L%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'T%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'B%', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU21M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU18M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU16B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU14B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU11B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU21W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'RU18W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU16G', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU14G', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'RU11G', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU21M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU18M', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU16B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU14B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU11B', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU21W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Red 3D Peg', 'CU18W', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU16G', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU14G', '0', 8, 0);
				CreateTargetFace($TourId, $i++, 'Blue 3D Peg', 'CU11G', '0', 8, 0);
				break;
		}
		break;
	case 10:
	case 12:
		switch ($SubRule) {
			case '1':
				$i=1;
				// Default All WA round target pegs
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R65M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R50M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RM', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU16B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RD', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU14B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RN', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU11B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R65W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R50W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RW', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU16G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU14G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU11G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C65M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C50M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CM', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU16B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CD', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU14B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CN', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU11B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C65W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C50W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CW', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU16G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU14G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU11G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B65M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B50M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BM', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU16B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BD', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU14B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BN', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU11B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B65W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B50W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BW', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU16G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU14G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU11G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T65M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T50M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TM', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU16B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TD', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU14B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TN', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU11B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T65W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T50W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TW', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU16G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU14G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU11G', '1', 6, 0, 6, 0);
				// Optional NZ-3D/WA-Marked round target pegs
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'X%', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'L%', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'R65M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'R50M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'RM', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'RU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'RU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'RU16B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'RD', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'RU14B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'RN', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'RU11B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'R65W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'R50W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'RW', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'RU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'RU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'RU16G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'RU14G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'RU11G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'C65M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'C50M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'CM', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'CU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'CU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'CU16B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'CD', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'CU14B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'CN', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'CU11B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'C65W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'C50W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'CW', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'CU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'CU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'CU16G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'CU14G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'CU11G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'B65M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'B50M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'BM', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'BU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU16B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BD', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU14B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BN', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU11B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'B65W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'B50W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'BW', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'BU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU16G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU14G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU11G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'T65M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'T50M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'TM', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'TU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU16B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TD', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU14B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TN', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU11B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'T65W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'T50W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'TW', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'TU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU16G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU14G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU11G', '0', 8, 0, 6, 0);
				break;
			case '2':
				$i=1;
				// Default All WA round target pegs - WA
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R65M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R50M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RM', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R65W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'R50W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RW', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C65M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C50M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CM', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C65W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'C50W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CW', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B65M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B50M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BM', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B65W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'B50W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BW', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T65M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T50M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TM', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T65W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'T50W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TW', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18W', '1', 6, 0, 6, 0);
				// Optional NZ-3D/WA-Marked round target pegs - WA
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'X%', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'L%', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'R65M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'R50M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'RM', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'RU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'RU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'R65W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'R50W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'RW', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'RU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'RU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'C65M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'C50M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'CM', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'CU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'CU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'C65W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'C50W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'CW', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'CU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'CU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'B65M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'B50M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'BM', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'BU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'B65W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'B50W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'BW', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'BU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'T65M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'T50M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'TM', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'TU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'T65W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'T50W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'TW', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'TU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU18W', '0', 8, 0, 6, 0);
				break;
			case '3':
				$i=1;
				// Default All WA round target pegs - JNR
				CreateTargetFace($TourId, $i++, 'Red Peg', 'X%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'L%', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU16B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU14B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU11B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'RU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'RU16G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU14G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'RU11G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU16B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU14B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU11B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Red Peg', 'CU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'CU16G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU14G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'CU11G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU16B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU14B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU11B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'BU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU16G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU14G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'BU11G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18M', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU16B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU14B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU11B', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Blue Peg', 'TU21W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU18W', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU16G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU14G', '1', 6, 0, 6, 0);
				CreateTargetFace($TourId, $i++, 'Yellow Peg', 'TU11G', '1', 6, 0, 6, 0);
				// Optional NZ-3D/WA-Marked round target pegs - JNR
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'X%', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'L%', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'RU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'RU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'RU16B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'RU14B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'RU11B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'RU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'RU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'RU16G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'RU14G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'RU11G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'CU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'CU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'CU16B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'CU14B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'CU11B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Red', 'CU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Red / WA Blu', 'CU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'CU16G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'CU14G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'CU11G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'BU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU16B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU14B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU11B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'BU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU16G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU14G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'BU11G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'TU21M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU18M', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU16B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU14B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU11B', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Blu', 'TU21W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU18W', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU16G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU14G', '0', 8, 0, 6, 0);
				CreateTargetFace($TourId, $i++, '3D Blu / WA Ylw', 'TU11G', '0', 8, 0, 6, 0);
				break;
		}
		break;
}


// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, $tourDetNumEnds, 4);

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

?>
