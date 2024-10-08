<?php
$version='2022-09-06 00:00:01';

$AllowedTypes=array(1,2,3,6,7,8,9,11,44);

$SetType['CH']['descr']=get_text('Setup-CH', 'Install');
$SetType['CH']['noc'] = 'SUI';
$SetType['CH']['types']=array();
$SetType['CH']['rules']=array();

foreach($AllowedTypes as $val) {
	switch($val) {
		case 9:
			$SetType['CH']['types']["$val"]=get_text('TrgField');
			$SetType['CH']['rules']["$val"]=array(
				'Set12',
				'Set16',
				'Set20',
				'Set24',
				'Set12+12',
				'Set16+16',
				'Set20+20',
				'Set24+24');
		break;

		case 11:
			$SetType['CH']['types']["$val"]=get_text('Type_3D', 'Tournament');
			$SetType['CH']['rules']["$val"]=array(
				'Set24',
				'Set28',
				'Set32',
				'Set24+24');
			break;
		default:
			$SetType['CH']['types']["$val"]=$TourTypes[$val];

	}
}


