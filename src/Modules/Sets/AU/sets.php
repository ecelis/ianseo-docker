<?php
/*
Created by peter.whitfield@viz.com.au
used AUT module as a starting point
*/

require_once('Common/Fun_Modules.php');
$version = '2023-09-28 00:00:00';

$AllowedTypes=array(1,3,5);

$SetType['AU']['descr']=get_text('Setup-AU', 'Install');
$SetType['AU']['noc'] = 'AUS';
$SetType['AU']['types']=array();
$SetType['AU']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['AU']['types']["$val"]=$TourTypes[$val];
}

// determine which tournament types have which subrules
foreach($AllowedTypes as $val) {
        $SetType['AU']['rules']["$val"] = array(
            'SetAllClass',
            'SetOneClass'
        );
        $tmp = $SetType['AU']['rules']["$val"];
    if($val == 3) {
        array_push($SetType['AU']['rules']["$val"], 'SetAUAustOpen');
    }
    // < 9 means WA types, 9 or over are org/country specific types
    // if($val >= 9 and $val != 37) {
    //     $SetType['AT']['rules']["$val"][] = 'SetWAPools-All';
    //     $SetType['AT']['rules']["$val"][] = 'SetWAPools-One';
    // }
}

/* No subrules requests as of today */