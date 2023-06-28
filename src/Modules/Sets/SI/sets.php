<?php
require_once('Common/Fun_Modules.php');
$version = '2022-01-01 00:00:00';

$AllowedTypes=array(1,2,3,37,6,7,5,9,10,12,11,13,19);

$SetType['SI']['descr']=get_text('Setup-SI', 'Install');
$SetType['SI']['noc'] = 'SLO';
$SetType['SI']['types']=array();
$SetType['SI']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['SI']['types'][$val]=$TourTypes[$val];
}

$SetType['SI']['rules']["19"]=array(
    'SetIndoor',
    'SetOutdoor',
);
