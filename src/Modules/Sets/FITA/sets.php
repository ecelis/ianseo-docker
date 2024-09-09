<?php
// global $TourTypes;
require_once('Common/Fun_Modules.php');
$version='2011-05-13 08:13:00';

$AllowedTypes=array(1,2,3,4,5,6,7,8,9,10,11,12,13,18,37,48);

$SetType['default']['descr']=get_text('Setup-Default', 'Install');
$SetType['default']['types']=array();
$SetType['default']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['default']['types']["$val"]=$TourTypes[$val];
}

// FITA, 2x FITA, 1/2 FITA, 70m Round, 18m, FITA+50, 2x70m
foreach(array(1, 2, 3, 4, 6, 18, 37) as $val) {
	$SetType['default']['rules']["$val"]=array(
		'SetAllClass',
		'SetOneClass',
		'SetJ-SClass',
		'SetYouthClass',
		);
	if(module_exists('QuotaTournament'))
		$SetType['default']['rules']["$val"][]='QuotaTournm';
}

// HF (all 3 types)
foreach(array(9, 10, 12) as $val) {
	$SetType['default']['rules']["$val"]=array(
		'SetAllClass',
		'SetJ-SClass',
		);
}

// 3D (both types)
foreach(array(11, 13) as $val) {
	$SetType['default']['rules']["$val"]=array(
		'SetAllClass',
		'SetOneClass',
		);
}
