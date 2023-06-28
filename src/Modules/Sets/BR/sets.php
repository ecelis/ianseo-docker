<?php
$version='2012-01-24 15:16:00';

$AllowedTypes=array(3,6,1);

$SetType['BR']['descr']=get_text('Setup-BR', 'Install');
$SetType['BR']['noc'] = 'BRA';
$SetType['BR']['types']=array();
$SetType['BR']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['BR']['types'][$val]=$TourTypes[$val];
}


$SetType['BR']['rules'][3] = array(
    'SetAllClass',
    'SetOneClass',
    'SetBRJunior', // Base (Junior + Cadet + Infant (14-)) + Master
	'SetPara' // Para Archery
);
