<?php
/*
3 	Type_70m Round

$TourId is the ID of the tournament!
$SubRule is the eventual subrule (see sets.php for the order)
$TourType is the Tour Type (3)

*/

$TourType=50;

$tourDetTypeName		= 'Type_FR_Beursault';
$tourDetNumDist			= '1';
$tourDetNumEnds			= '40';
$tourDetMaxDistScore	= $SubRule==2?'160':'120';
$tourDetMaxFinIndScore	= '0';
$tourDetMaxFinTeamScore	= '0';
$tourDetCategory		= '1'; // 0: Other, 1: Outdoor, 2: Indoor, 4:Field, 8:3D
$tourDetElabTeam		= '0'; // 0:Standard, 1:Field, 2:3DI
$tourDetElimination		= '0'; // 0: No Eliminations, 1: Elimination Allowed
$tourDetGolds			= $SubRule==2?'4':'3';
$tourDetXNine			= $SubRule==2?'3':'2';
$tourDetGoldsChars		= $SubRule==2?'E':'D';
$tourDetXNineChars		= $SubRule==2?'D':'C';
$tourDetDouble			= '0';
$DistanceInfoArray=array(array(40, 1));

require_once('Setup_Target.php');

