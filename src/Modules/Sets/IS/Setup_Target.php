<?php
/*
Common Setup for "Target" Archery
*/

require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(dirname(__FILE__)).'/lib.php');

// default Divisions
CreateStandardDivisions($TourId, $TourType, $SubRule);

// default Classes
CreateStandardClasses($TourId, $SubRule, $TourType);

// default Subclasses
CreateStandardSubClasses($TourId);

// default Distances
switch($TourType) {
case 3:	// Outdoor - Utandyra fjarlægðir - COMPLETE

	//Sveigbogi
	CreateDistanceNew($TourId, $TourType, 'RU14_', array(array('20m-1',20), array('20m-2',20)));
	CreateDistanceNew($TourId, $TourType, 'RU16_', array(array('40m-1',40), array('40m-2',40)));
	CreateDistanceNew($TourId, $TourType, 'RU18_', array(array('60m-1',60), array('60m-2',60)));
	CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('70m-1',70), array('70m-2',70)));
	CreateDistanceNew($TourId, $TourType, 'R3_', array(array('70m-1',70), array('70m-2',70)));
	CreateDistanceNew($TourId, $TourType, 'R4_', array(array('70m-1',70), array('70m-2',70)));
	CreateDistanceNew($TourId, $TourType, 'R5_', array(array('60m-1',60), array('60m-2',60)));
	CreateDistanceNew($TourId, $TourType, 'R6_', array(array('60m-1',60), array('60m-2',60)));
	CreateDistanceNew($TourId, $TourType, 'R7_', array(array('60m-1',60), array('60m-2',60)));
	CreateDistanceNew($TourId, $TourType, 'R_', array(array('70m-1',70), array('70m-2',70)));
	CreateDistanceNew($TourId, $TourType, 'RA_', array(array('50m-1',50), array('50m-2',50)));
	
	//Trissubogi
	CreateDistanceNew($TourId, $TourType, 'CU14_', array(array('20m-1',20), array('20m-2',20)));
	CreateDistanceNew($TourId, $TourType, 'CU16_', array(array('40m-1',40), array('40m-2',40)));
	CreateDistanceNew($TourId, $TourType, 'CU18_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'CU21_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'C3_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'C4_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'C5_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'C6_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'C7_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'C_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'CA_', array(array('30m-1',30), array('30m-2',30)));
	
	//Berbogi
	CreateDistanceNew($TourId, $TourType, 'BU14_', array(array('20m-1',20), array('20m-2',20)));
	CreateDistanceNew($TourId, $TourType, 'BU16_', array(array('30m-1',30), array('30m-2',30)));
	CreateDistanceNew($TourId, $TourType, 'BU18_', array(array('40m-1',40), array('40m-2',40)));
	CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'B3_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'B4_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'B5_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'B6_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'B7_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'B_', array(array('50m-1',50), array('50m-2',50)));
	CreateDistanceNew($TourId, $TourType, 'BA_', array(array('30m-1',30), array('30m-2',30)));
	
	//Langbogi
	CreateDistanceNew($TourId, $TourType, 'LU14_', array(array('20m-1',20), array('20m-2',20)));
	CreateDistanceNew($TourId, $TourType, 'LU16_', array(array('30m-1',30), array('30m-2',30)));
	CreateDistanceNew($TourId, $TourType, 'LU18_', array(array('30m-1',30), array('30m-2',30)));
	CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('30m-1',30), array('30m-2',30)));
	CreateDistanceNew($TourId, $TourType, 'L3_', array(array('30m-1',30), array('30m-2',30)));
	CreateDistanceNew($TourId, $TourType, 'L4_', array(array('30m-1',30), array('30m-2',30)));
	CreateDistanceNew($TourId, $TourType, 'L5_', array(array('30m-1',30), array('30m-2',30)));
	CreateDistanceNew($TourId, $TourType, 'L6_', array(array('30m-1',30), array('30m-2',30)));
	CreateDistanceNew($TourId, $TourType, 'L7_', array(array('30m-1',30), array('30m-2',30)));
	CreateDistanceNew($TourId, $TourType, 'L_', array(array('30m-1',30), array('30m-2',30)));
	CreateDistanceNew($TourId, $TourType, 'LA_', array(array('30m-1',30), array('30m-2',30)));
	
	
    break;
case 6:    // Indoor - Innandyra 18m fjarlægðir

	//Sveigbogi
	CreateDistanceNew($TourId, $TourType, 'RU14_', array(array('6m-1',6), array('6m-2',6)));
	CreateDistanceNew($TourId, $TourType, 'RU16_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'RU18_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'RU21_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'R3_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'R4_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'R5_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'R6_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'R7_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'R_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'RA_', array(array('12m-1',12), array('12m-2',12)));
	
	//Trissubogi
	CreateDistanceNew($TourId, $TourType, 'CU14_', array(array('6m-1',6), array('6m-2',6)));
	CreateDistanceNew($TourId, $TourType, 'CU16_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'CU18_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'CU21_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'C3_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'C4_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'C5_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'C6_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'C7_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'C_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'CA_', array(array('12m-1',12), array('12m-2',12)));
	
	//Berbogi
	CreateDistanceNew($TourId, $TourType, 'BU14_', array(array('6m-1',6), array('6m-2',6)));
	CreateDistanceNew($TourId, $TourType, 'BU16_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'BU18_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'BU21_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'B3_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'B4_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'B5_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'B6_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'B7_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'B_', array(array('18m-1',18), array('18m-2',18)));
	CreateDistanceNew($TourId, $TourType, 'BA_', array(array('12m-1',12), array('12m-2',12)));
	
	//Langbogi
	CreateDistanceNew($TourId, $TourType, 'LU14_', array(array('6m-1',6), array('6m-2',6)));
	CreateDistanceNew($TourId, $TourType, 'LU16_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'LU18_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'LU21_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'L3_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'L4_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'L5_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'L6_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'L7_', array(array('12m-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'L_', array(array('12-1',12), array('12m-2',12)));
	CreateDistanceNew($TourId, $TourType, 'LA_', array(array('12m-1',12), array('12m-2',12)));
	
    break;
}

// default Events
CreateStandardEvents($TourId, $TourType, $SubRule, $tourDetCategory=='1');

// Classes in Events
InsertStandardEvents($TourId, $TourType, $SubRule, $tourDetCategory=='1');

// Finals & TeamFinals
CreateFinals($TourId);

// Default Target
$i=1;
switch($TourType) {
	case 6: // Indoor - Innandyra skífur
        if ($SubRule==1) { //Allir flokkar
		// Optional Recurve - valmöguleika skífur fyrir sveigboga
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'R_', '', 2, 40, 2, 40);
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RU21_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'RU18_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'RU16_', '', 2, 60, 2, 60);
		// Recurve - Sveigboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R_', '1', 1, 40, 1, 40);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RU21_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RU18_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RU16_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RU14_', '1', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R3_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R4_', '1', 1, 40, 1, 40);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R5_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R6_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R7_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RA_', '1', 1, 60, 1, 60);		
		
		// optional Compound - valmöguleika skífur fyrir trissuboga
        CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'C_', '', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CU21_', '', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10 small-ten)', 'CU18_', '', 4, 60, 4, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10 small-ten)', 'CU16_', '', 4, 60, 4, 60);
		// Compound - Trissuboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CU21_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CU18_', '1', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CU16_', '1', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CU14_', '1', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C3_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C4_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C5_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C6_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C7_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CA_', '1', 3, 60, 3, 60);
		
        // optional Barebow - valmöguleika skífur fyrir berboga
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'B_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BU21_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'BU18_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'BU16_', '', 2, 60, 2, 60);
		// Barebow - Berboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BU21_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BU18_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BU16_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BU14_', '1', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B3_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B4_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B5_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B6_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B7_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BA_', '1', 1, 60, 1, 60);
		
		// optional Longbow - valmöguleika skífur fyrir langboga
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'L_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'LU21_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'LU18_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'LU16_', '', 2, 60, 2, 60);
		// Longbow - Langboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU21_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU18_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU16_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU14_', '1', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L3_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L4_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L5_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L6_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L7_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LA_', '1', 1, 60, 1, 60);
		
		}
		if ($SubRule==2) {
		// Optional Recurve - valmöguleika skífur fyrir sveigboga
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'R%', '', 2, 40, 2, 40);
		// Recurve - Sveigboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R%', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RA_', '1', 1, 60, 1, 60);
		
		// optional Compound - valmöguleika skífur fyrir trissuboga
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'C%', '', 4, 40, 4, 40);
		// Compound - Trissuboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C%', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CA_', '1', 3, 60, 3, 60);
		
		// optional Barebow - valmöguleika skífur fyrir berboga
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'B%', '', 2, 40, 2, 40);
        // Barebow - Berboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B%', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BA_', '1', 1, 60, 1, 60);
		
		// optional Longbow - valmöguleika skífur fyrir langboga
        CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'L%', '', 2, 60, 2, 60);
        // Longbow - Langboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L%', '1', 1, 60, 1, 60);
		}
		if ($SubRule==3) {
		// Optional Recurve - valmöguleika skífur fyrir sveigboga
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'R%', '', 2, 40, 2, 40);
		// Recurve - Sveigboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R%', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RA_', '1', 1, 60, 1, 60);
		
		// optional Compound - valmöguleika skífur fyrir trissuboga
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'C%', '', 4, 40, 4, 40);
		// Compound - Trissuboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C%', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CA_', '1', 3, 60, 3, 60);
		
		// optional Barebow - valmöguleika skífur fyrir berboga
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'B%', '', 2, 40, 2, 40);
        // Barebow - Berboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B%', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BA_', '1', 1, 60, 1, 60);
		
		// optional Longbow - valmöguleika skífur fyrir langboga
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'L%', '', 2, 60, 2, 60);
        // Longbow - Langboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L%', '1', 1, 60, 1, 60);
		}
		if ($SubRule==4) {
		// Youth Series
		// Recurve - Sveigboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RU21_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RU18_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RU16_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RU14_', '1', 1, 60, 1, 60);
		// Optional Recurve - valmöguleika skífur fyrir sveigboga
        CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'RU21_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'RU18_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'RU16_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'RU14_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'RA_', '', 2, 60, 2, 60);
		
		// Compound - Trissuboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CU21_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CU18_', '1', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CU16_', '1', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CU14_', '1', 3, 60, 3, 60);
		// optional Compound - valmöguleika skífur fyrir trissuboga
		CreateTargetFace($TourId, $i++, '~40cm (6-10 small-ten)', 'CU21_', '', 4, 40, 4, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10 small-ten)', 'CU18_', '', 4, 60, 4, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10 small-ten)', 'CU16_', '', 4, 60, 4, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10 small-ten)', 'CU14_', '', 4, 60, 4, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10 small-ten)', 'CA_', '', 4, 60, 4, 60);
		
		// Barebow - Berboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BU21_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BU18_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BU16_', '1', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BU14_', '1', 1, 60, 1, 60);
        // optional Barebow - valmöguleika skífur fyrir berboga
		CreateTargetFace($TourId, $i++, '~40cm (6-10)', 'BU21_', '', 2, 40, 2, 40);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'BU18_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'BU16_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'BU14_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'BA_', '', 2, 60, 2, 60);
		
		// Longbow - Langboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU21_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU18_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU16_', '1', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU14_', '1', 1, 60, 1, 60);
        // optional Longbow - valmöguleika skífur fyrir langboga
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'LU21_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'LU18_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'LU16_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'LU14_', '', 2, 60, 2, 60);
		CreateTargetFace($TourId, $i++, '~60cm (6-10)', 'LA_', '', 2, 60, 2, 60);
		}
		if ($SubRule==5) {
		// Recurve - Sveigboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R3_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R4_', '1', 1, 40, 1, 40);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R5_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R6_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R7_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RA_', '1', 1, 60, 1, 60);
		
		// Compound - Trissuboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C3_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C4_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C5_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C6_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C7_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CA_', '1', 3, 60, 3, 60);
		
		// Barebow - Berboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B3_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B4_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B5_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B6_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B7_', '1', 1, 40, 1, 40);	
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BA_', '1', 1, 60, 1, 60);	
		
		// Longbow - Langboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L3_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L4_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L5_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L6_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L7_', '1', 1, 60, 1, 60);	
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LA_', '1', 1, 60, 1, 60);	
		}
        if ($SubRule==6) { //Allir flokkar Unisex

		// Recurve - Sveigboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R_', '1', 1, 40, 1, 40);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'RU21_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RU18_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RU16_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RU14_', '1', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R3_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R4_', '1', 1, 40, 1, 40);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R5_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R6_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'R7_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'RA_', '1', 1, 60, 1, 60);		
		
		// Compound - Trissuboga skífur standard undankeppni
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'CU21_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CU18_', '1', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CU16_', '1', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CU14_', '1', 3, 60, 3, 60);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C3_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C4_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C5_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C6_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10 small-ten)', 'C7_', '1', 3, 40, 3, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10 small-ten)', 'CA_', '1', 3, 60, 3, 60);
		
		// Barebow - Berboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'BU21_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BU18_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BU16_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BU14_', '1', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B3_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B4_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B5_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B6_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~40cm (1-10)', 'B7_', '1', 1, 40, 1, 40);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'BA_', '1', 1, 60, 1, 60);
		
		// Longbow - Langboga skífur standard undankeppni
        CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU21_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU18_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU16_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LU14_', '1', 1, 60, 1, 60);
        CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L3_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L4_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L5_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L6_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'L7_', '1', 1, 60, 1, 60);
		CreateTargetFace($TourId, $i++, '~60cm (1-10)', 'LA_', '1', 1, 60, 1, 60);
		}

		
        break;
    case 3: // Outdoor - Utandyra skífur - COMPLETE
        CreateTargetFace($TourId, $i++, '122cm (1-10)', 'R%', '1', 5, 122, 5, 122);
        CreateTargetFace($TourId, $i++, '80cm (5-10)', 'C%', '1', 9, 80, 9, 80);
        CreateTargetFace($TourId, $i++, '122cm (1-10)', 'B%', '1', 5, 122, 5, 122);
		CreateTargetFace($TourId, $i++, '122cm (1-10)', 'L%', '1', 5, 122, 5, 122);
}

// create a first distance prototype
CreateDistanceInformation($TourId, $DistanceInfoArray, 10, 2);

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
   'ToIocCode'	=> $tourDetIocCode,
	);
UpdateTourDetails($TourId, $tourDetails);
?>