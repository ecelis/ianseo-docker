<?php
require_once('Common/Fun_Modules.php');
$version = '2020-01-01 00:00:00';

$AllowedTypes=array(1,2,3,37,5,6,7,8,9,12,10,11,13);

$SetType['AT']['descr']=get_text('Setup-AT', 'Install');
$SetType['AT']['noc'] = 'AUT';
$SetType['AT']['types']=array();
$SetType['AT']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['AT']['types']["$val"]=$TourTypes[$val];
}

foreach($AllowedTypes as $val) {
    if($val!=1 AND $val!=5) {
        $SetType['AT']['rules']["$val"] = array(
            'SetAllClass',
            'SetOneClass'
        );
    }
}

/* No subrules requests as of today */