<?php
require_once('Common/Fun_Modules.php');
$version='2017-11-23 18:13:00';

$AllowedTypes=array(3, 6, 7, 8, 50);

$SetType['FR']['descr']=get_text('Setup-FR', 'Install');
$SetType['FR']['noc'] = 'FRA';
$SetType['FR']['types']=array();
$SetType['FR']['rules']=array();

foreach($AllowedTypes as $val) {
	$SetType['FR']['types']["$val"]=$TourTypes[$val];
}

// BUILD ONE PER TIME... When finished we can group
// 70m round have several championship and "styles"
$SetType['FR']['rules']["3"]["12"]='SetFRTAE-Valides';
$SetType['FR']['rules']["3"]["13"]='SetFRTAE-Para';
//$SetType['FR']['rules']["3"]["0"]='SetAllClass';
$SetType['FR']['rules']["3"]["2"]='SetFRChampionshipJun';
$SetType['FR']['rules']["3"]["14"]='SetFRChampJunTeams';
$SetType['FR']['rules']["3"]["10"]='SetFRCoupeFrance';
$SetType['FR']['rules']["3"]["9"]='SetFRTAE';
$SetType['FR']['rules']["3"]["1"]='SetFRChampsTNJ';
$SetType['FR']['rules']["3"]["8"]='SetFRFinDRD2';
$SetType['FR']['rules']["3"]["15"]='SetFRFinalsD2';
$SetType['FR']['rules']["3"]["11"]='SetFRD12023';

// 18m have championships
$SetType['FR']['rules']["6"][0]='SetFrSelectif';
$SetType['FR']['rules']["6"][3]='SetFrSelectifPara';
$SetType['FR']['rules']["6"][2]='SetFRChampionshipJun';
$SetType['FR']['rules']["6"][1]='SetFRChampionshipSen';

// 25m and 25+18 have no championships
$SetType['FR']['rules']["7"][0]='SetFrSelectif';
$SetType['FR']['rules']["7"][3]='SetFrSelectifPara';

$SetType['FR']['rules']["8"][0]='SetFrSelectif';
$SetType['FR']['rules']["8"][3]='SetFrSelectifPara';

// Beursault
$SetType['FR']['rules']["50"][0]='SetFrBouquet';
$SetType['FR']['rules']["50"][1]='SetFrBeursault';
