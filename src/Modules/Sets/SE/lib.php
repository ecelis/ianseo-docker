<?php
/*

STANDARD THINGS

*/

// these go here as it is a "global" definition, used or not
$tourCollation = 'swedish';
$tourDetIocCode = 'SWE';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) {
	$i=1;
	CreateDivision($TourId,$i++,'R','Recurve');
	CreateDivision($TourId,$i++,'B','Barebow');
	CreateDivision($TourId,$i++,'C','Compound');
	CreateDivision($TourId,$i++,'L','Longbow');
	CreateDivision($TourId,$i++,'T','Traditional');
}

function CreateStandardClasses($TourId, $SubRule, $Field='', $Type=0) {
	$i=1;
	CreateClass($TourId, $i++,  0,  12, 0, 'U13M', 'U13M,U16M', 'Under 13 Men',1);
	CreateClass($TourId, $i++,  0,  12, 1, 'U13W', 'U13W,U16W,U13M,U16M', 'Under 13 Women',1);
	CreateClass($TourId, $i++, 13,  15, 0, 'U16M', 'U16M,U21M,21M,M', 'Under 16 Men',1);
	CreateClass($TourId, $i++, 13,  15, 1, 'U16W', 'U16W,U21W,21W,W,U16M,U21M,21M,M', 'Under 16 Women',1);
    CreateClass($TourId, $i++, 16,  20, 0, 'U21M', 'U21M,21M,M', 'Under 21 Men',1);
    CreateClass($TourId, $i++, 16,  20, 1, 'U21W', 'U21W,21W,W,U21M,21M,M', 'Under 21 Women',1);
    CreateClass($TourId, $i++, 21,  49, 0, '21M', '21M,M', '21 Men',1);
    CreateClass($TourId, $i++, 21,  49, 1, '21W', '21W,W,21M,M', '21 Women',1);
	CreateClass($TourId, $i++, 21,  49, 0, 'M', 'M,21M', 'Men',1);
	CreateClass($TourId, $i++, 21,  49, 1, 'W', 'W,21W,M,21M', 'Women',1);
	CreateClass($TourId, $i++, 50,  59, 0, '50M', '50M,21M,M', '50+ Men',1);
	CreateClass($TourId, $i++, 50,  59, 1, '50W', '50W,21W,W,50M,21M,M', '50+ Women',1);
	CreateClass($TourId, $i++, 60, 100, 0, '60M', '60M,50M,21M,M', '60+ Men',1);
	CreateClass($TourId, $i++, 60, 100, 1, '60W', '60W,50W,21W,W,60M,50M,21M,M', '60+ Women',1);
}

function CreateStandardSubClasses($TourId) {
	$i=1;
	CreateSubClass($TourId, $i++, 'M', 'Motion');
	CreateSubClass($TourId, $i++, 'DM', 'Distriktsmästerskap');
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$TargetR=($Outdoor?5:2);
	$TargetC=($Outdoor?9:4);
	$TargetOther=($Outdoor?5:1);

    // Create Finals for all classes. Note that FirstPhase is set to 0 so the final is not activated until the user sets it manually
    $dv = array('R'=>'Recurve','B'=>'Barebow','C'=>'Compound','L'=>'Longbow','T'=>'Traditional');
    $cl = array('U16'=>'Under 16','U21'=>'Under 21',''=>'','21'=>'21','50'=>'50','60'=>'60');
    $ge = array('M'=>'Men','W'=>'Women');
    $i=1;
    foreach($dv as $k_dv => $v_dv) {
        foreach($cl as $k_cl => $v_cl) {
            foreach($ge as $k_ge => $v_ge) {
                $CurrTarget = ($k_dv=='C' ? $TargetC : ($k_dv=='R' ? $TargetR : $TargetOther));
                CreateEvent($TourId, $i++, 0, 0, 0, $CurrTarget, 5, 3, 1, 5, 3, 1, $k_dv . $k_cl . $k_ge,  $v_dv . ' ' . $v_cl . ' ' . $v_ge, ($k_dv=='C' ? 0 : 1), 240, 240);
            }
        }
    }

    // Create Team Finals
    $i=1;
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'LU16C',  'Lag Under 16 Compound');
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'LU21C',  'Lag Under 21 Compound');
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetC, 4, 6, 3, 4, 6, 3, 'LSC',  'Lag Senior Compound');
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetOther, 4, 6, 3, 4, 6, 3, 'LU16B',  'Lag Under 16 Barebow', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetOther, 4, 6, 3, 4, 6, 3, 'LU21B',  'Lag Under 21 Barebow', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetOther, 4, 6, 3, 4, 6, 3, 'LSB',  'Lag Senior Barebow', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'LU16R',  'Lag Under 16 Recurve', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'LU21R',  'Lag Under 21 Recurve', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetR, 4, 6, 3, 4, 6, 3, 'LSR',  'Lag Senior Recurve', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetOther, 4, 6, 3, 4, 6, 3, 'LU16L',  'Lag Under 16 Longbow', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetOther, 4, 6, 3, 4, 6, 3, 'LU21L',  'Lag Under 21 Longbow', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetOther, 4, 6, 3, 4, 6, 3, 'LSL',  'Lag Senior Longbow', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetOther, 4, 6, 3, 4, 6, 3, 'LU16T',  'Lag Under 16 Traditional', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetOther, 4, 6, 3, 4, 6, 3, 'LU21T',  'Lag Under 21 Traditional', 1);
    CreateEvent($TourId, $i++, 1, 0, 0, $TargetOther, 4, 6, 3, 4, 6, 3, 'LST',  'Lag Senior Traditional', 1);
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) {
	$dv = array('R','B','C','L','T');
	$cl = array('U13'=>array('U13M','U13W'), 'U16'=>array('U16M','U16W'), 'U21'=>array('U21M','U21W'), 'S'=>array('21M','50M','60M','M','21W','50W','60W','W'));

	if($TourType==6 || $TourType==3 || $TourType==37 || $TourType==1) {
		foreach($dv as $v_dv) {
			foreach($cl as $k_cl => $v_cl) {
				foreach($v_cl as $dett_cl) {
					// Individual event
					if($k_cl != '13') {
						InsertClassEvent($TourId, 0, 1, $v_dv.$dett_cl, $v_dv, $dett_cl);
						// Team composition
						InsertClassEvent($TourId, 1, 3, 'L' . $k_cl . $v_dv, $v_dv, $dett_cl);
					}
				}
			}
		}
	}
}
