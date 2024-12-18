<?php

// these go here as it is a "global" definition, used or not
$tourCollation = '';
$tourDetIocCode = 'IS';
if(empty($SubRule)) $SubRule='1';

function CreateStandardDivisions($TourId, $Type=1, $SubRule=0) { //Bogaflokkar/Undankeppni. Þessum parti er lokið COMPLETE
    // Ignoring sub-rules for now. Þetta function section bætir við Bogaflokkum í "Division and classes" partinn af Ianseo
    $i=1;
	CreateDivision($TourId, $i++, 'R', 'Sveigbogi/ Recurve');
	CreateDivision($TourId, $i++, 'C', 'Trissubogi/ Compound');
    CreateDivision($TourId, $i++, 'B', 'Berbogi/ Barebow');
	CreateDivision($TourId, $i++, 'L', 'Langbogi/ Longbow');
}

function CreateStandardClasses($TourId, $SubRule, $Type) { //Aldursflokkar og kyn/Undankeppni. COMPLETE
    //Aldursflokkar. Þetta function section bætir við aldursflokkum og kynjum í "Division and classes" partinn af Ianseo
    $i=1;

	if ($SubRule==1) { // "Championship" Allir Aldursflokkar COMPLETE með kynsegin - Breytt úr JM í U21M
		CreateClass($TourId, $i++, 21, 29, 0, 'M', 'M,AM,U,AU', 'Meistaraflokkur Karla/ Men', 1, '');
		CreateClass($TourId, $i++, 21, 29, 1, 'W', 'W,AW,U,AU', 'Meistaraflokkur Kvenna/ Women', 1, '');
		CreateClass($TourId, $i++, 21, 29, -1, 'U', 'U,AU', 'Meistaraflokkur Allir/ Unisex');
        CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M,M,AM,U21U,U,AU', 'U21 Karla/ U21 Men', 1, '');
        CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W,W,AW,U21U,U,AU', 'U21 Kvenna/ U21 Women', 1, '');
		CreateClass($TourId, $i++, 18, 20, -1, 'U21U', 'U21U,U,AU', 'U21 Allir/ U21 Unisex');
        CreateClass($TourId, $i++, 16, 17, 0, 'U18M', 'U18M,U21M,M,AM,U18U,U21U,U,AU', 'U18 Karla/ U18 Men', 1, '');
        CreateClass($TourId, $i++, 16, 17, 1, 'U18W', 'U18W,U21W,W,AW,U18U,U21U,U,AU', 'U18 Kvenna/ U18 Women', 1, '');
		CreateClass($TourId, $i++, 16, 17, -1, 'U18U', 'U18U,U21U,U,AU', 'U18 Allir/ U18 Unisex');
        CreateClass($TourId, $i++, 14, 15, 0, 'U16M', 'U16M,U18M,U21M,M,AM,U16U,U18U,U21U,U,AU', 'U16 Karla/ U16 Men', 1, '');
        CreateClass($TourId, $i++, 14, 15, 1, 'U16W', 'U16W,U18W,U21W,W,AW,U16U,U18U,U21U,U,AU', 'U16 Kvenna/ U16 Women', 1, '');
		CreateClass($TourId, $i++, 14, 15, -1, 'U16U', 'U16U,U18U,U21U,U,AU', 'U16 Allir/ U16 Unisex');
		CreateClass($TourId, $i++, 1, 13, 0, 'U14M', 'U14M,U16M,U18M,U21M,M,AM,U14U,U16U,U18U,U21U,U,AU', 'U14 Karla/ U14 Men', 1, '');
        CreateClass($TourId, $i++, 1, 13, 1, 'U14W', 'U14W,U16W,U18W,U21W,W,AW,U14U,U16U,U18U,U21U,U,AU', 'U14 Kvenna/ U14 Women', 1, '');
		CreateClass($TourId, $i++, 1, 13, -1, 'U14U', 'U14U,U16U,U18U,U21U,U,AU', 'U14 Allir/ U14 Unisex');
		CreateClass($TourId, $i++, 30, 39, 0, '3M', '3M,M,AM,3U,U,AU', '30+ Karla/ 30+ Men', 1, '');
        CreateClass($TourId, $i++, 30, 39, 1, '3W', '3W,W,AW,3U,U,AU', '30+ Kvenna/ 30+ Women', 1, '');
		CreateClass($TourId, $i++, 30, 39, -1, '3U', '3U,U,AU', '30+ Allir/ 30+ Unisex');
        CreateClass($TourId, $i++, 40, 49, 0, '4M', '3M,4M,M,AM,3U,4U,U,AU', '40+ Karla/ 40+ Men', 1, '');
        CreateClass($TourId, $i++, 40, 49, 1, '4W', '3W,4W,W,AW,3U,4U,U,AU', '40+ Kvenna/ 40+ Women', 1, '');
		CreateClass($TourId, $i++, 40, 49, -1, '4U', '3U,4U,U,AU', '40+ Allir/ 40+ Unisex');
        CreateClass($TourId, $i++, 50, 59, 0, '5M', '3M,4M,5M,M,AM,3U,4U,5U,U,AU', '50+ Karla/ 50+ Men-Masters', 1, '');
        CreateClass($TourId, $i++, 50, 59, 1, '5W', '3W,4W,5W,W,AW,3U,4U,5U,U,AU', '50+ Kvenna/ 50+ Women-Masters', 1, '');
		CreateClass($TourId, $i++, 50, 59, -1, '5U', '3U,4U,5U,U,AU', '50+ Allir/ 50+ Unisex');
		CreateClass($TourId, $i++, 60, 69, 0, '6M', '3M,4M,5M,6M,M,AM,3U,4U,5U,6U,U,AU', '60+ Karla/ 60+ Men-Veteran', 1, '');
        CreateClass($TourId, $i++, 60, 69, 1, '6W', '3W,4W,5W,6W,W,AW,3U,4U,5U,6U,U,AU', '60+ Kvenna/ 60+ Women-Veteran', 1, '');
		CreateClass($TourId, $i++, 60, 69, -1, '6U', '3U,4U,5U,6U,U,AU', '60+ Allir/ 60+ Unisex');
		CreateClass($TourId, $i++, 70, 99, 0, '7M', '3M,4W,5M,6M,7M,M,AM,3U,4U,5U,6U,7U,U,AU', '70+ Karla/ 70+ Men', 1, '');
        CreateClass($TourId, $i++, 70, 99, 1, '7W', '3W,4W,5W,6W,7W,W,AW,3U,4U,5U,6U,7U,U,AU', '70+ Kvenna/ 70+ Women', 1, '');
		CreateClass($TourId, $i++, 70, 99, -1, '7U', '3U,4U,5U,6U,7U,U,AU', '70+  Allir/ 70+ Unisex');
		CreateClass($TourId, $i++, 99, 100, 0, 'AM', 'AM,AU', 'Áhugamenn/Byrjendur Karla', 1, '');
        CreateClass($TourId, $i++, 99, 100, 1, 'AW', 'AW,AU', 'Áhugamenn/Byrjendur Kvenna', 1, '');
		CreateClass($TourId, $i++, 99, 100, -1, 'AU', 'AU', 'Áhugamenn/Byrjendur Unisex');
		}

	if ($SubRule==2) { // "Only Adult Classes" Bara opinn flokkur en skilgreindir aldursflokkar fyrir úrlistakerfi, COMPLETE með kynsegin - Breytt úr JM í U21M
		CreateClass($TourId, $i++, 21, 29, 0, 'M', 'M,AM,U,AU', 'Meistaraflokkur Karla/ Men', 1, '');
		CreateClass($TourId, $i++, 21, 29, 1, 'W', 'W,AW,U,AU', 'Meistaraflokkur Kvenna/ Women', 1, '');
		CreateClass($TourId, $i++, 21, 29, -1, 'U', 'U,AU', 'Meistaraflokkur Allir/ Unisex');
        CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'M,AM,U,AU', 'U21 Karla/ U21 Men', 1, '');
        CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'W,AW,U,AU', 'U21 Kvenna/ U21 Women', 1, '');
		CreateClass($TourId, $i++, 18, 20, -1, 'U21U', 'U,AU', 'U21 Allir/ U21 Unisex');
        CreateClass($TourId, $i++, 16, 17, 0, 'U18M', 'M,AM,U,AU', 'U18 Karla/ U18 Men', 1, '');
        CreateClass($TourId, $i++, 16, 17, 1, 'U18W', 'W,AW,U,AU', 'U18 Kvenna/ U18 Women', 1, '');
		CreateClass($TourId, $i++, 16, 17, -1, 'U18U', 'U,AU', 'U18 Allir/ U18 Unisex');
        CreateClass($TourId, $i++, 14, 15, 0, 'U16M', 'M,AM,U,AU', 'U16 Karla/ U16 Men', 1, '');
        CreateClass($TourId, $i++, 14, 15, 1, 'U16W', 'W,AW,U,AU', 'U16 Kvenna/ U16 Women', 1, '');
		CreateClass($TourId, $i++, 14, 15, -1, 'U16U', 'U,AU', 'U16 Allir/ U16 Unisex');
		CreateClass($TourId, $i++, 1, 13, 0, 'U14M', 'M,AM,U,AU', 'U14 Karla/ U14 Men', 1, '');
        CreateClass($TourId, $i++, 1, 13, 1, 'U14W', 'W,AW,U,AU', 'U14 Kvenna/ U14 Women', 1, '');
		CreateClass($TourId, $i++, 1, 13, -1, 'U14U', 'U,AU', 'U14 Allir/ U14 Unisex');
		CreateClass($TourId, $i++, 30, 39, 0, '3M', 'M,AM,U,AU', '30+ Karla/ 30+ Men', 1, '');
        CreateClass($TourId, $i++, 30, 39, 1, '3W', 'W,AW,U,AU', '30+ Kvenna/ 30+ Women', 1, '');
		CreateClass($TourId, $i++, 30, 39, -1, '3U', 'U,AU', '30+ Allir/ 30+ Unisex');
        CreateClass($TourId, $i++, 40, 49, 0, '4M', 'M,AM,U,AU', '40+ Karla/ 40+ Men', 1, '');
        CreateClass($TourId, $i++, 40, 49, 1, '4W', 'W,AW,U,AU', '40+ Kvenna/ 40+ Women', 1, '');
		CreateClass($TourId, $i++, 40, 49, -1, '4U', 'U,AU', '40+ Allir/ 40+ Unisex');
        CreateClass($TourId, $i++, 50, 59, 0, '5M', 'M,AM,U,AU', '50+ Karla/ 50+ Men-Masters', 1, '');
        CreateClass($TourId, $i++, 50, 59, 1, '5W', 'W,AW,U,AU', '50+ Kvenna/ 50+ Women-Masters', 1, '');
		CreateClass($TourId, $i++, 50, 59, -1, '5U', 'U,AU', '50+ Allir/ 50+ Unisex');
		CreateClass($TourId, $i++, 60, 69, 0, '6M', 'M,AM,U,AU', '60+ Karla/ 60+ Men-Veteran', 1, '');
        CreateClass($TourId, $i++, 60, 69, 1, '6W', 'W,AW,U,AU', '60+ Kvenna/ 60+ Women-Veteran', 1, '');
		CreateClass($TourId, $i++, 60, 69, -1, '6U', 'U,AU', '60+ Allir/ 60+ Unisex');
		CreateClass($TourId, $i++, 70, 99, 0, '7M', 'M,AM,U,AU', '70+ Karla/ 70+ Men', 1, '');
        CreateClass($TourId, $i++, 70, 99, 1, '7W', 'W,AW,U,AU', '70+ Kvenna/ 70+ Women', 1, '');
		CreateClass($TourId, $i++, 70, 99, -1, '7U', 'U,AU', '70+ Allir/ 70+ Unisex');
		CreateClass($TourId, $i++, 99, 100, 0, 'AM', 'AM,AU', 'Áhugamenn/Byrjendur Karla', 1, '');
        CreateClass($TourId, $i++, 99, 100, 1, 'AW', 'AW,AU', 'Áhugamenn/Byrjendur Kvenna', 1, '');
		CreateClass($TourId, $i++, 99, 100, -1, 'AU', 'AU', 'Áhugamenn/Byrjendur Unisex');
		}

    if ($SubRule==3) { // "All-in-one class" UNISEX/Bikarmót bara opinn flokkur en skilgreindir aldursflokkar fyrir úrslitakerfi, COMPLETE - Breytt úr JM í U21M
        CreateClass($TourId, $i++, 21, 29, 0, 'M', 'U,AU', 'Meistaraflokkur Karla/ Men', 1, '');
		CreateClass($TourId, $i++, 21, 29, 1, 'W', 'U,AU', 'Meistaraflokkur Kvenna/ Women', 1, '');
		CreateClass($TourId, $i++, 21, 29, -1, 'U', 'U,AU', 'Meistaraflokkur Allir/ Unisex');
        CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U,AU', 'U21 Karla/ U21 Men', 1, '');
        CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U,AU', 'U21 Kvenna/ U21 Women', 1, '');
		CreateClass($TourId, $i++, 18, 20, -1, 'U21U', 'U,AU', 'U21 Allir/ U21 Unisex');
        CreateClass($TourId, $i++, 16, 17, 0, 'U18M', 'U,AU', 'U18 Karla/ U18 Men', 1, '');
        CreateClass($TourId, $i++, 16, 17, 1, 'U18W', 'U,AU', 'U18 Kvenna/ U18 Women', 1, '');
		CreateClass($TourId, $i++, 16, 17, -1, 'U18U', 'U,AU', 'U18 Allir/ U18 Unisex');
        CreateClass($TourId, $i++, 14, 15, 0, 'U16M', 'U,AU', 'U16 Karla/ U16 Men', 1, '');
        CreateClass($TourId, $i++, 14, 15, 1, 'U16W', 'U,AU', 'U16 Kvenna/ U16 Women', 1, '');
		CreateClass($TourId, $i++, 14, 15, -1, 'U16U', 'U,AU', 'U16 Allir/ U16 Unisex');
		CreateClass($TourId, $i++, 1, 13, 0, 'U14M', 'U,AU', 'U14 Karla/ U14 Men', 1, '');
        CreateClass($TourId, $i++, 1, 13, 1, 'U14W', 'U,AU', 'U14 Kvenna/ U14 Women', 1, '');
		CreateClass($TourId, $i++, 1, 13, -1, 'U14U', 'U,AU', 'U14 Allir/ U14 Unisex');
		CreateClass($TourId, $i++, 30, 39, 0, '3M', 'U,AU', '30+ Karla/ 30+ Men', 1, '');
        CreateClass($TourId, $i++, 30, 39, 1, '3W', 'U,AU', '30+ Kvenna/ 30+ Women', 1, '');
		CreateClass($TourId, $i++, 30, 39, -1, '3U', 'U,AU', '30+ Allir/ 30+ Unisex');
        CreateClass($TourId, $i++, 40, 49, 0, '4M', 'U,AU', '40+ Karla/ 40+ Men', 1, '');
        CreateClass($TourId, $i++, 40, 49, 1, '4W', 'U,AU', '40+ Kvenna/ 40+ Women', 1, '');
		CreateClass($TourId, $i++, 40, 49, -1, '4U', 'U,AU', '40+ Allir/ 40+ Unisex');
        CreateClass($TourId, $i++, 50, 59, 0, '5M', 'U,AU', '50+ Karla/ 50+ Men-Masters', 1, '');
        CreateClass($TourId, $i++, 50, 59, 1, '5W', 'U,AU', '50+ Kvenna/ 50+ Women-Masters', 1, '');
		CreateClass($TourId, $i++, 50, 59, -1, '5U', 'U,AU', '50+ Allir/ 50+ Unisex');
		CreateClass($TourId, $i++, 60, 69, 0, '6M', 'U,AU', '60+ Karla/ 60+ Men-Veteran', 1, '');
        CreateClass($TourId, $i++, 60, 69, 1, '6W', 'U,AU', '60+ Kvenna/ 60+ Women-Veteran', 1, '');
		CreateClass($TourId, $i++, 60, 69, -1, '6U', 'U,AU', '60+ Allir/ 60+ Unisex');
		CreateClass($TourId, $i++, 70, 99, 0, '7M', 'U,AU', '70+ Karla/ 70+ Men', 1, '');
        CreateClass($TourId, $i++, 70, 99, 1, '7W', 'U,AU', '70+ Kvenna/ 70+ Women', 1, '');
		CreateClass($TourId, $i++, 70, 99, -1, '7U', 'U,AU', '70+ Allir/ 70+ Unisex');
		CreateClass($TourId, $i++, 99, 100, 0, 'AM', 'AU', 'Áhugamenn/Byrjendur Karla', 1, '');
        CreateClass($TourId, $i++, 99, 100, 1, 'AW', 'AU', 'Áhugamenn/Byrjendur Kvenna', 1, '');
		CreateClass($TourId, $i++, 99, 100, -1, 'AU', 'AU', 'Áhugamenn/Byrjendur Unisex');
		}
	
	if ($SubRule==4) { // "EVERY CLASSES" Allir aldurflokkar ungmenna UNGMENNADEILDIN COMPLETE með kynsegin - Breytt úr JM í U21M
		CreateClass($TourId, $i++, 18, 20, 0, 'U21M', 'U21M,AM,U21U,AU', 'U21 Karla/ U21 Men', 1, '');
        CreateClass($TourId, $i++, 18, 20, 1, 'U21W', 'U21W,AW,U21U,AU', 'U21 Kvenna/ U21 Women', 1, '');
		CreateClass($TourId, $i++, 18, 20, -1, 'U21U', 'U21U,AU', 'U21 Allir/ U21 Unisex');
		CreateClass($TourId, $i++, 16, 17, 0, 'U18M', 'U18M,U21M,AM,U18U,U21U,AU', 'U18 Karla/ U18 Men', 1, '');
        CreateClass($TourId, $i++, 16, 17, 1, 'U18W', 'U18W,U21W,AW,U18U,U21U,AU', 'U18 Kvenna/ U18 Women', 1, '');
		CreateClass($TourId, $i++, 16, 17, -1, 'U18U', 'U18U,U21U,AU', 'U18 Allir/ U18 Unisex');
        CreateClass($TourId, $i++, 14, 15, 0, 'U16M', 'U16M,U18M,U21M,AM,U16U,U18U,U21U,AU', 'U16 Karla/ U16 Men', 1, '');
        CreateClass($TourId, $i++, 14, 15, 1, 'U16W', 'U16W,U18W,U21W,AW,U16U,U18U,U21U,AU', 'U16 Kvenna/ U16 Women', 1, '');
		CreateClass($TourId, $i++, 14, 15, -1, 'U16U', 'U16U,U18U,U21U,AU', 'U16 Allir/ U16 Unisex');
		CreateClass($TourId, $i++, 1, 13, 0, 'U14M', 'U14M,U16M,U18M,U21M,AM,U14U,U16U,U18U,U21U,AU', 'U14 Karla/ U14 Men', 1, '');
        CreateClass($TourId, $i++, 1, 13, 1, 'U14W', 'U14W,U16W,U18W,U21W,AW,U14U,U16U,U18U,U21U,AU', 'U14 Kvenna/ U14 Women', 1, '');
		CreateClass($TourId, $i++, 1, 13, -1, 'U14U', 'U14U,U16U,U18U,U21U,AU', 'U14 Allir/ U14 Unisex');
		CreateClass($TourId, $i++, 99, 100, 0, 'AM', 'AM,AU', 'Áhugamenn/Byrjendur Karla', 1, '');
        CreateClass($TourId, $i++, 99, 100, 1, 'AW', 'AW,AU', 'Áhugamenn/Byrjendur Kvenna', 1, '');
		CreateClass($TourId, $i++, 99, 100, -1, 'AU', 'AU', 'Áhugamenn/Byrjendur Unisex');
		}
		
	if ($SubRule==5) { // "Set Kids classes" Allir aldursflokkar öldunga ÍSLANDSMÓT ÖLDUNGA COMPLETE með kynsegin
		CreateClass($TourId, $i++, 30, 39, 0, '3M', '3M,AM,3U,AU', '30+ Karla/ 30+ Men', 1, '');
        CreateClass($TourId, $i++, 30, 39, 1, '3W', '3W,AW,3U,AU', '30+ Kvenna/ 30+ Women', 1, '');
		CreateClass($TourId, $i++, 30, 39, -1, '3U', '3U,AU', '30+ Allir/ 30+ Unisex');
        CreateClass($TourId, $i++, 40, 49, 0, '4M', '3M,4M,AM,3U,4U,AU', '40+ Karla/ 40+ Men', 1, '');
        CreateClass($TourId, $i++, 40, 49, 1, '4W', '3W,4W,AW,3U,4U,AM', '40+ Kvenna/ 40+ Women', 1, '');
		CreateClass($TourId, $i++, 40, 49, -1, '4U', '3U,4U,AU', '40+ Allir/ 40+ Unisex');
        CreateClass($TourId, $i++, 50, 59, 0, '5M', '3M,4M,5M,AM,3U,4U,5U,AU', '50+ Karla/ 50+ Men-Masters', 1, '');
        CreateClass($TourId, $i++, 50, 59, 1, '5W', '3W,4W,5W,AW,3U,4U,5U,AU', '50+ Kvenna/ 50+ Women-Masters', 1, '');
		CreateClass($TourId, $i++, 50, 59, -1, '5U', '3U,4U,5U,AU', '50+ Allir/ 50+ Unisex');
		CreateClass($TourId, $i++, 60, 69, 0, '6M', '3M,4M,5M,6M,AM,3U,4U,5U,6U,AU', '60+ Karla/ 60+ Men-Veteran', 1, '');
        CreateClass($TourId, $i++, 60, 69, 1, '6W', '3W,4W,5W,6W,AW,3U,4U,5U,6U,AU', '60+ Kvenna/ 60+ Women-Veteran', 1, '');
		CreateClass($TourId, $i++, 60, 69, -1, '6U', '3U,4U,5U,6U,AU', '60+ Allir/ 60+ Unisex');
		CreateClass($TourId, $i++, 70, 99, 0, '7M', '3M,4M,5M,6M,7M,AM,3U,4U,5U,6U,7U,AU', '70+ Karla/ 70+ Men', 1, '');
        CreateClass($TourId, $i++, 70, 99, 1, '7W', '3W,4W,5W,6W,7W,AW,3U,4U,5U,6U,7U,AU', '70+ Kvenna/ 70+ Women', 1, '');
		CreateClass($TourId, $i++, 70, 99, -1, '7U', '3U,4U,5U,6U,7U,AU', '70+ Allir/ 70+ Unisex');
		CreateClass($TourId, $i++, 99, 100, 0, 'AM', 'AM,AU', 'Áhugamenn/Byrjendur Karla', 1, '');
        CreateClass($TourId, $i++, 99, 100, 1, 'AW', 'AW,AU', 'Áhugamenn/Byrjendur Kvenna', 1, '');
		CreateClass($TourId, $i++, 99, 100, -1, 'AU', 'AU', 'Áhugamenn/Byrjendur Unisex');
		}

    if ($SubRule==6) { // "All Classes WA 4 Pools" UNISEX allir aldursflokkar og skilgreindir aldursflokkar fyrir úrslitakerfi, COMPLETE - Breytt úr JM í U21M
        CreateClass($TourId, $i++, 70, 99, -1, '7U', '3U,4U,5U,6U,7U,U,AU', '70+ Allir/ 70+ Unisex');
		CreateClass($TourId, $i++, 60, 69, -1, '6U', '3U,4U,5U,6U,U,AU', '60+ Allir/ 60+ Unisex');
		CreateClass($TourId, $i++, 50, 59, -1, '5U', '3U,4U,5U,U,AU', '50+ Allir/ 50+ Unisex');
		CreateClass($TourId, $i++, 40, 49, -1, '4U', '3U,4U,U,AU', '40+ Allir/ 40+ Unisex');
		CreateClass($TourId, $i++, 30, 39, -1, '3U', '3U,U,AU', '30+ Allir/ 30+ Unisex');
        CreateClass($TourId, $i++, 21, 29, -1, 'U', 'U,AU', 'Meistaraflokkur Allir/ Unisex');
		CreateClass($TourId, $i++, 18, 20, -1, 'U21U', 'U21U,U,AU', 'U21 Allir/ U21 Unisex');
		CreateClass($TourId, $i++, 16, 17, -1, 'U18U', 'U18U,U21U,U,AU', 'U18 Allir/ U18 Unisex');
		CreateClass($TourId, $i++, 14, 15, -1, 'U16U', 'U16U,U18U,U21U,U,AU', 'U16 Allir/ U16 Unisex');
		CreateClass($TourId, $i++, 1, 13, -1, 'U14U', 'U14U,U16U,U18U,U21U,U,AU', 'U14 Allir/ U14 Unisex');
		CreateClass($TourId, $i++, 99, 100, -1, 'AU', 'AU', 'Áhugamenn/Byrjendur Unisex');
		}		
		
}

function CreateStandardSubClasses($TourId) { //Undirflokkar. Þessi partur er ekki notaður COMPLETE
	// Hérna seturðu inn subclasses/undirflokka
}

function CreateStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) { //Útsláttarkeppni. COMPLETE
    //StandardEvents = Eliminations/Matches Útsláttarkeppni uppsetning, útskýring er fyrir ofan hvern hluta um hvað sá hluti gerir

{	// Hér fyrir neðan er skilgreining á því hvaða skífustærðir og fjarlægðir eru notaðar í ÚTSLÁTTARKEPPNI fyrir mismunandi flokka. COMPLETE
	// Senior - Opinn flokkur Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR=($Outdoor ? 122 : 40);
    $TargetSizeC=($Outdoor ? 80 : 40);
    $TargetSizeB=($Outdoor ? 122 : 40);
	$TargetSizeL=($Outdoor ? 122 : 60);
    $DistanceR=($Outdoor ? 70 : 18);
    $DistanceC=($Outdoor ? 50 : 18);
    $DistanceB=($Outdoor ? 50 : 18);
	$DistanceL=($Outdoor ? 30 : 12);

    // Junior - U21 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRU21=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCU21=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBU21=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLU21=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRU21=($Outdoor ? 122 : 40);
    $TargetSizeCU21=($Outdoor ? 80 : 40);
	$TargetSizeBU21=($Outdoor ? 122 : 40);
	$TargetSizeLU21=($Outdoor ? 122 : 60);
    $DistanceRU21=($Outdoor ? 70 : 18);
    $DistanceCU21=($Outdoor ? 50 : 18);
	$DistanceBU21=($Outdoor ? 50 : 18);
	$DistanceLU21=($Outdoor ? 30 : 12);

    // Cadet - U18 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRU18=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCU18=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBU18=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLU18=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRU18=($Outdoor ? 122 : 60);
    $TargetSizeCU18=($Outdoor ? 80 : 60);
	$TargetSizeBU18=($Outdoor ? 122 : 60);
	$TargetSizeLU18=($Outdoor ? 122 : 60);
    $DistanceRU18=($Outdoor ? 60 : 18);
    $DistanceCU18=($Outdoor ? 50 : 18);
	$DistanceBU18=($Outdoor ? 40 : 18);
	$DistanceLU18=($Outdoor ? 30 : 12);

    // Nordic - U16 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRU16=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCU16=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBU16=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLU16=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRU16=($Outdoor ? 122 : 60);
    $TargetSizeCU16=($Outdoor ? 80 : 60);
	$TargetSizeBU16=($Outdoor ? 122 : 60);
	$TargetSizeLU16=($Outdoor ? 122 : 60);
    $DistanceRU16=($Outdoor ? 40 : 12);
    $DistanceCU16=($Outdoor ? 40 : 12);
	$DistanceBU16=($Outdoor ? 30 : 12);
	$DistanceLU16=($Outdoor ? 30 : 12);
	
	// U14 Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRU14=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCU14=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
	$TargetBU14=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLU14=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRU14=($Outdoor ? 122 : 60);
    $TargetSizeCU14=($Outdoor ? 80 : 60);
	$TargetSizeBU14=($Outdoor ? 122 : 60);
	$TargetSizeLU14=($Outdoor ? 122 : 60);
    $DistanceRU14=($Outdoor ? 20 : 6);
    $DistanceCU14=($Outdoor ? 20 : 6);
	$DistanceBU14=($Outdoor ? 20 : 6);
	$DistanceLU14=($Outdoor ? 20 : 6);
	
	// Master 30+ - 30+ Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR3=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC3=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB3=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL3=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR3=($Outdoor ? 122 : 40);
    $TargetSizeC3=($Outdoor ? 80 : 40);
    $TargetSizeB3=($Outdoor ? 122 : 40);
	$TargetSizeL3=($Outdoor ? 122 : 60);
    $DistanceR3=($Outdoor ? 70 : 18);
    $DistanceC3=($Outdoor ? 50 : 18);
    $DistanceB3=($Outdoor ? 50 : 18);
	$DistanceL3=($Outdoor ? 30 : 12);
	
	// Master 40+ - 40+ Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR4=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC4=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB4=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL4=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR4=($Outdoor ? 122 : 40);
    $TargetSizeC4=($Outdoor ? 80 : 40);
    $TargetSizeB4=($Outdoor ? 122 : 40);
	$TargetSizeL4=($Outdoor ? 122 : 60);
    $DistanceR4=($Outdoor ? 70 : 18);
    $DistanceC4=($Outdoor ? 50 : 18);
    $DistanceB4=($Outdoor ? 50 : 18);
	$DistanceL4=($Outdoor ? 30 : 12);
	
	// Master 50+ Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR5=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC5=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB5=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL5=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR5=($Outdoor ? 122 : 40);
    $TargetSizeC5=($Outdoor ? 80 : 40);
    $TargetSizeB5=($Outdoor ? 122 : 40);
	$TargetSizeL5=($Outdoor ? 122 : 60);
    $DistanceR5=($Outdoor ? 60 : 18);
    $DistanceC5=($Outdoor ? 50 : 18);
    $DistanceB5=($Outdoor ? 50 : 18);
	$DistanceL5=($Outdoor ? 30 : 12);

	// Master 60+ - 60+ Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR6=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC6=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB6=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL6=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR6=($Outdoor ? 122 : 40);
    $TargetSizeC6=($Outdoor ? 80 : 40);
    $TargetSizeB6=($Outdoor ? 122 : 40);
	$TargetSizeL6=($Outdoor ? 122 : 60);
    $DistanceR6=($Outdoor ? 60 : 18);
    $DistanceC6=($Outdoor ? 50 : 18);
    $DistanceB6=($Outdoor ? 50 : 18);
	$DistanceL6=($Outdoor ? 30 : 12);
	
	// Master 70+ - 70+ Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetR7=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetC7=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetB7=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetL7=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeR7=($Outdoor ? 122 : 40);
    $TargetSizeC7=($Outdoor ? 80 : 40);
    $TargetSizeB7=($Outdoor ? 122 : 40);
	$TargetSizeL7=($Outdoor ? 122 : 60);
    $DistanceR7=($Outdoor ? 60 : 18);
    $DistanceC7=($Outdoor ? 50 : 18);
    $DistanceB7=($Outdoor ? 50 : 18);
	$DistanceL7=($Outdoor ? 30 : 12);
	
	// Áhugamannaflokkur - Áhugamannaflokkur Útsláttarkeppni Skífustærðir og Fjarlægðir
    $TargetRA=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetCA=($Outdoor ? TGT_OUT_5_big10 : TGT_IND_1_small10);
    $TargetBA=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
	$TargetLA=($Outdoor ? TGT_OUT_FULL : TGT_IND_1_big10);
    $TargetSizeRA=($Outdoor ? 122 : 60);
    $TargetSizeCA=($Outdoor ? 80 : 60);
    $TargetSizeBA=($Outdoor ? 122 : 60);
	$TargetSizeLA=($Outdoor ? 122 : 60);
    $DistanceRA=($Outdoor ? 50 : 12);
    $DistanceCA=($Outdoor ? 30 : 12);
    $DistanceBA=($Outdoor ? 30 : 12);
	$DistanceLA=($Outdoor ? 30 : 12);
}
	
	// $Phase stillir globally í hvaða útslætti útsláttarkeppni byrjar 0=engin útsláttur "---" 1=semi finals, 2=quarter finals og svo framvegis.
	// Ef þú vilt stilla suma útslætti til að byrja á ákveðnum stað þarftu að finna þann útslátt og bæta við t.d =0 fyrir aftan $Phase í útsláttarlínuni fyrir þann flokk
    $Phase=0; 
    $i=0;

	// Einstaklinga útslættir
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni EINSTAKLINGA COMPLETE með kynsegin - Breytt úr RJM í RU21M
		// Sveigbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR,  5, 3, 1, 5, 3, 1, 'RM',  'Sveigbogi Karla M.fl/ Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR,  5, 3, 1, 5, 3, 1, 'RW',  'Sveigbogi Kvenna M.fl/ Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR,  5, 3, 1, 5, 3, 1, 'RU',  'Sveigbogi Allir M.fl/ Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU21, 5, 3, 1, 5, 3, 1, 'RU21M', 'Sveigbogi Karla U21 Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRU21, $DistanceRU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU21, 5, 3, 1, 5, 3, 1, 'RU21W', 'Sveigbogi Kvenna U21 Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRU21, $DistanceRU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU21, 5, 3, 1, 5, 3, 1, 'RU21U', 'Sveigbogi Allir U21 Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeRU21, $DistanceRU21);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'RU18M', 'Sveigbogi Karla U18 Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRU18, $DistanceRU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'RU18W', 'Sveigbogi Kvenna U18 Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRU18, $DistanceRU18);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'RU18U', 'Sveigbogi Allir U18 Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeRU18, $DistanceRU18);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU16, 5, 3, 1, 5, 3, 1, 'RU16M', 'Sveigbogi Karla U16 Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRU16, $DistanceRU16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU16, 5, 3, 1, 5, 3, 1, 'RU16W', 'Sveigbogi Kvenna U16 Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRU16, $DistanceRU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU16, 5, 3, 1, 5, 3, 1, 'RU16U', 'Sveigbogi Allir U16 Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeRU16, $DistanceRU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU14, 5, 3, 1, 5, 3, 1, 'RU14M', 'Sveigbogi Karla U16 Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRU14, $DistanceRU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU14, 5, 3, 1, 5, 3, 1, 'RU14W', 'Sveigbogi Kvenna U14 Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRU14, $DistanceRU14);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU14, 5, 3, 1, 5, 3, 1, 'RU14U', 'Sveigbogi Allir U14 Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeRU14, $DistanceRU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5M', 'Sveigbogi Karla 50+ Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5W', 'Sveigbogi Kvenna 50+ Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5U', 'Sveigbogi Allir 50+ Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAM', 'Sveigbogi Áhugamenn/Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAW', 'Sveigbogi Áhugamenn/Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAU', 'Sveigbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);
	    // Trissubogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC,  5, 3, 1, 5, 3, 1, 'CM',  'Trissubogi Karla M.fl/ Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC,  5, 3, 1, 5, 3, 1, 'CW',  'Trissubogi Kvenna M.fl/ Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC,  5, 3, 1, 5, 3, 1, 'CU',  'Trissubogi Allir M.fl/ Compound Unisex', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU21, 5, 3, 1, 5, 3, 1, 'CU21M', 'Trissubogi Karla U21 Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCU21, $DistanceCU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU21, 5, 3, 1, 5, 3, 1, 'CU21W', 'Trissubogi Kvenna U21 Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCU21, $DistanceCU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU21, 5, 3, 1, 5, 3, 1, 'CU21U', 'Trissubogi Allir U21 Compound Unisex', 0, 240, 255, 0, 0, '', '', $TargetSizeCU21, $DistanceCU21);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU18, 5, 3, 1, 5, 3, 1, 'CU18M', 'Trissubogi Karla U18 Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCU18, $DistanceCU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU18, 5, 3, 1, 5, 3, 1, 'CU18W', 'Trissubogi Kvenna U18 Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCU18, $DistanceCU18);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU18, 5, 3, 1, 5, 3, 1, 'CU18U', 'Trissubogi Allir U18 Compound Unisex', 0, 240, 255, 0, 0, '', '', $TargetSizeCU18, $DistanceCU18);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU16, 5, 3, 1, 5, 3, 1, 'CU16M', 'Trissubogi Karla U16 Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCU16, $DistanceCU16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU16, 5, 3, 1, 5, 3, 1, 'CU16W', 'Trissubogi Kvenna U16 Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCU16, $DistanceCU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU16, 5, 3, 1, 5, 3, 1, 'CU16U', 'Trissubogi Allir U16 Compound Unisex', 0, 240, 255, 0, 0, '', '', $TargetSizeCU16, $DistanceCU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU14, 5, 3, 1, 5, 3, 1, 'CU14M', 'Trissubogi Karla U14 Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCU14, $DistanceCU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU14, 5, 3, 1, 5, 3, 1, 'CU14W', 'Trissubogi Kvenna U14 Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCU14, $DistanceCU14);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU14, 5, 3, 1, 5, 3, 1, 'CU14U', 'Trissubogi Allir U14 Compound Unisex', 0, 240, 255, 0, 0, '', '', $TargetSizeCU14, $DistanceCU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5M', 'Trissubogi Karla 50+ Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5W', 'Trissubogi Kvenna 50+ Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5U', 'Trissubogi Allir 50+ Compound Unisex', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);	
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAM', 'Trissubogi Áhugamenn/Byrjendur Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAW', 'Trissubogi Áhugamenn/Byrjendur Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAU', 'Trissubogi Áhugamenn/Byrjendur Allir', 0, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);	
	    // Berbogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB,  5, 3, 1, 5, 3, 1, 'BM',  'Berbogi Karla M.fl/ Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB,  5, 3, 1, 5, 3, 1, 'BW',  'Berbogi Kvenna M.fl/ Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB,  5, 3, 1, 5, 3, 1, 'BU',  'Berbogi Allir M.fl/ Barebow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU21, 5, 3, 1, 5, 3, 1, 'BU21M', 'Berbogi Karla U21 Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBU21, $DistanceBU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU21, 5, 3, 1, 5, 3, 1, 'BU21W', 'Berbogi Kvenna U21 Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBU21, $DistanceBU21);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU21, 5, 3, 1, 5, 3, 1, 'BU21U', 'Berbogi Allir U21 Barebow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeBU21, $DistanceBU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU18, 5, 3, 1, 5, 3, 1, 'BU18M', 'Berbogi Karla U18 Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBU18, $DistanceBU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU18, 5, 3, 1, 5, 3, 1, 'BU18W', 'Berbogi Kvenna U18 Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBU18, $DistanceBU18);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU18, 5, 3, 1, 5, 3, 1, 'BU18U', 'Berbogi Allir U18 Barebow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeBU18, $DistanceBU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU16, 5, 3, 1, 5, 3, 1, 'BU16M', 'Berbogi Karla U16 Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBU16, $DistanceBU16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU16, 5, 3, 1, 5, 3, 1, 'BU16W', 'Berbogi Kvenna U16 Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBU16, $DistanceBU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU16, 5, 3, 1, 5, 3, 1, 'BU16U', 'Berbogi Allir U16 Barebow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeBU16, $DistanceBU16);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU14, 5, 3, 1, 5, 3, 1, 'BU14M', 'Berbogi Karla U14 Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBU14, $DistanceBU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU14, 5, 3, 1, 5, 3, 1, 'BU14W', 'Berbogi Kvenna U14 Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBU14, $DistanceBU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU14, 5, 3, 1, 5, 3, 1, 'BU14U', 'Berbogi U14 Allir/ Barebow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeBU14, $DistanceBU14);		
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5M', 'Berbogi Karla 50+ Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5W', 'Berbogi Kvenna 50+ Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5U', 'Berbogi Allir 50+ Barebow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAM', 'Berbogi Áhugamenn/Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBA, $DistanceBA);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAW', 'Berbogi Áhugamenn/Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBA, $DistanceBA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAU', 'Berbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeBA, $DistanceBA);
		// Langbogi 
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL,  5, 3, 1, 5, 3, 1, 'LM',  'Longbow & Traditional Men', 1, 240, 255, 0, 0, '', '', $TargetSizeL, $DistanceL);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL,  5, 3, 1, 5, 3, 1, 'LW',  'Longbow & Traditional Women', 1, 240, 255, 0, 0, '', '', $TargetSizeL, $DistanceL);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL,  5, 3, 1, 5, 3, 1, 'LU',  'Longbow & Traditional Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeL, $DistanceL);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU21, 5, 3, 1, 5, 3, 1, 'LU21M', 'Longbow & Traditional U21 Men', 1, 240, 255, 0, 0, '', '', $TargetSizeLU21, $DistanceLU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU21, 5, 3, 1, 5, 3, 1, 'LU21W', 'Longbow & Traditional U21 Women', 1, 240, 255, 0, 0, '', '', $TargetSizeLU21, $DistanceLU21);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU21, 5, 3, 1, 5, 3, 1, 'LU21U', 'Longbow & Tratitional U21 Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeLU21, $DistanceLU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU18, 5, 3, 1, 5, 3, 1, 'LU18M', 'Longbow & Traditional U18 Men', 1, 240, 255, 0, 0, '', '', $TargetSizeLU18, $DistanceLU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU18, 5, 3, 1, 5, 3, 1, 'LU18W', 'Longbow & Traditional U18 Women', 1, 240, 255, 0, 0, '', '', $TargetSizeLU18, $DistanceLU18);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU18, 5, 3, 1, 5, 3, 1, 'LU18U', 'Longbow & Traditional U18 Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeLU18, $DistanceLU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU16, 5, 3, 1, 5, 3, 1, 'LU16M', 'Longbow & Traditional U16 Men', 1, 240, 255, 0, 0, '', '', $TargetSizeLU16, $DistanceLU16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU16, 5, 3, 1, 5, 3, 1, 'LU16W', 'Longbow & Traditional U16 Women', 1, 240, 255, 0, 0, '', '', $TargetSizeLU16, $DistanceLU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU16, 5, 3, 1, 5, 3, 1, 'LU16U', 'Longbow & Traditional U16 Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeLU16, $DistanceLU16);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU14, 5, 3, 1, 5, 3, 1, 'LU14M', 'Longbow & Traditional U14 Men', 1, 240, 255, 0, 0, '', '', $TargetSizeLU14, $DistanceLU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU14, 5, 3, 1, 5, 3, 1, 'LU14W', 'Longbow & Traditional U14 Women', 1, 240, 255, 0, 0, '', '', $TargetSizeLU14, $DistanceLU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU14, 5, 3, 1, 5, 3, 1, 'LU14U', 'Longbow & Traditional U14 Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeLU14, $DistanceLU14);		
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL5, 5, 3, 1, 5, 3, 1, 'L5M', 'Longbow & Traditional 50+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeL5, $DistanceL5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL5, 5, 3, 1, 5, 3, 1, 'L5W', 'Longbow & Traditional 50+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeL5, $DistanceL5);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL5, 5, 3, 1, 5, 3, 1, 'L5U', 'Longbow & Traditional 50+ Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeL5, $DistanceL5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAM', 'Langbogi Áhugamenn/Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeLA, $DistanceLA);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAW', 'Langbogi Áhugamenn/Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeLA, $DistanceLA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAU', 'Langbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeLA, $DistanceLA);
        }

	if ($SubRule==2) { //"Only Adult Classes" Bara opinn flokkur útsláttarkeppni EINSTAKLINGA COMPLETE með kynsegin
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RM',  'Sveigbogi Karla M.fl/ Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RW',  'Sveigbogi Kvenna M.fl/ Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'RU',  'Sveigbogi Allir M.fl/ Recurve Unisex', 1, 240, 255, 0, 0, '', '',$TargetSizeR, $DistanceR);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CM',  'Trissubogi Karla M.fl/ Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CW',  'Trissubogi Kvenna M.fl/ Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'CU',  'Trissubogi Allir M.fl/ Compound Unisex', 0, 240, 255, 0, 0, '', '',$TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BM',  'Berbogi Karla M.fl/ Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BW',  'Berbogi Kvenna M.fl/ Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'BU',  'Berbogi Allir M.fl/ Barebow Unisex', 1, 240, 255, 0, 0, '', '',$TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL, 5, 3, 1, 5, 3, 1, 'LM',  'Longbow & Traditional Men', 1, 240, 255, 0, 0, '', '', $TargetSizeL, $DistanceL);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL, 5, 3, 1, 5, 3, 1, 'LW',  'Longbow & Traditional Women', 1, 240, 255, 0, 0, '', '', $TargetSizeL, $DistanceL);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL, 5, 3, 1, 5, 3, 1, 'LU',  'Longbow & Traditional Unisex', 1, 240, 255, 0, 0, '', '',$TargetSizeL, $DistanceL);
		//Áhugamenn/Byrjendur
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAM',  'Sveigbogi Áhugamenn/Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAW',  'Sveigbogi Áhugamenn/Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAU',  'Sveigbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '',$TargetSizeRA, $DistanceRA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAM',  'Trissubogi Áhugamenn/Byrjendur Karla', 1, 240, 255, 0, 0, '', '',$TargetSizeCA, $DistanceCA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAW',  'Trissubogi Áhugamenn/Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAU',  'Trissubogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAM',  'Berbogi Áhugamenn/Byrjendur Karla', 1, 240, 255, 0, 0, '', '',$TargetSizeBA, $DistanceBA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAW',  'Berbogi Áhugamenn/Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '',$TargetSizeBA, $DistanceBA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAU',  'Berbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeBA, $DistanceBA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAM',  'Langbogi Áhugamenn/Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeLA, $DistanceLA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAW',  'Langbogi Áhugamenn/Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '',$TargetSizeLA, $DistanceLA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAU',  'Langbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '',$TargetSizeLA, $DistanceLA);
		}

    if ($SubRule==3) { // "All-in-one class" Bara opinn flokkur UNISEX ÚTSLÁTTARKEPPNI EINSTAKLINGA COMPLETE
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR, 5, 3, 1, 5, 3, 1, 'R',  'Sveigbogi M.fl/ Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);    
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC, 5, 3, 1, 5, 3, 1, 'C',  'Trissubogi M.fl/ Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB, 5, 3, 1, 5, 3, 1, 'B',  'Berbogi M.fl/ Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL, 5, 3, 1, 5, 3, 1, 'L',  'Longbow & Traditional', 1, 240, 255, 0, 0, '', '', $TargetSizeL, $DistanceL);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RA',  'Sveigbogi Áhugamenn/Byrjendur', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);    
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CA',  'Trissubogi Áhugamenn/Byrjendur', 0, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BA',  'Berbogi Áhugamenn/Byrjendur', 1, 240, 255, 0, 0, '', '', $TargetSizeBA, $DistanceBA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LA',  'Langbogi Áhugamenn/Byrjendur', 1, 240, 255, 0, 0, '', '', $TargetSizeLA, $DistanceLA);
		}
		
	if ($SubRule==4) { // "Every Classes" Allir aldurflokkar ÚTSLÁTTARKEPPNI UNGMENNAMÓT COMPLETE með kynsegin - Breytt úr RJM í RU21M
		// Sveigbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU21, 5, 3, 1, 5, 3, 1, 'RU21M', 'Sveigbogi Karla U21 Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRU21, $DistanceRU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU21, 5, 3, 1, 5, 3, 1, 'RU21W', 'Sveigbogi Kvenna U21 Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRU21, $DistanceRU21);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU21, 5, 3, 1, 5, 3, 1, 'RU21U', 'Sveigbogi Allir U21 Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeRU21, $DistanceRU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'RU18M', 'Sveigbogi Karla U18 Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRU18, $DistanceRU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'RU18W', 'Sveigbogi Kvenna U18 Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRU18, $DistanceRU18);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'RU18U', 'Sveigbogi Allir U18 Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeRU18, $DistanceRU18);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU16, 5, 3, 1, 5, 3, 1, 'RU16M', 'Sveigbogi Karla U16 Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRU16, $DistanceRU16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU16, 5, 3, 1, 5, 3, 1, 'RU16W', 'Sveigbogi Kvenna U16 Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRU16, $DistanceRU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU16, 5, 3, 1, 5, 3, 1, 'RU16U', 'Sveigbogi Allir U16 Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeRU16, $DistanceRU16);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU14, 5, 3, 1, 5, 3, 1, 'RU14M', 'Sveigbogi Karla U14 Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeRU14, $DistanceRU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU14, 5, 3, 1, 5, 3, 1, 'RU14W', 'Sveigbogi Kvenna U14 Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeRU14, $DistanceRU14);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU14, 5, 3, 1, 5, 3, 1, 'RU14U', 'Sveigbogi Allir U14 Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeRU14, $DistanceRU14);	
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAM', 'Sveigbogi Áhugamenn/Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAW', 'Sveigbogi Áhugamenn/Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAU', 'Sveigbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);	
		// Trissubogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU21, 5, 3, 1, 5, 3, 1, 'CU21M', 'Trissubogi Karla U21 Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCU21, $DistanceCU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU21, 5, 3, 1, 5, 3, 1, 'CU21W', 'Trissubogi Kvenna U21 Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCU21, $DistanceCU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU21, 5, 3, 1, 5, 3, 1, 'CU21U', 'Trissubogi Allir U21 Compound Unisex', 0, 240, 255, 0, 0, '', '', $TargetSizeCU21, $DistanceCU21);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU18, 5, 3, 1, 5, 3, 1, 'CU18M', 'Trissubogi Karla U18 Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCU18, $DistanceCU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU18, 5, 3, 1, 5, 3, 1, 'CU18W', 'Trissubogi Kvenna U18 Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCU18, $DistanceCU18);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU18, 5, 3, 1, 5, 3, 1, 'CU18U', 'Trissubogi Allir U18 Compound Unisex', 0, 240, 255, 0, 0, '', '', $TargetSizeCU18, $DistanceCU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU16, 5, 3, 1, 5, 3, 1, 'CU16M', 'Trissubogi Karla U16 Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCU16, $DistanceCU16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU16, 5, 3, 1, 5, 3, 1, 'CU16W', 'Trissubogi Kvenna U16 Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCU16, $DistanceCU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU16, 5, 3, 1, 5, 3, 1, 'CU16U', 'Trissubogi Allir U16 Compound Unisex', 0, 240, 255, 0, 0, '', '', $TargetSizeCU16, $DistanceCU16);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU14, 5, 3, 1, 5, 3, 1, 'CU14M', 'Trissubogi Karla U14 Compound Men', 0, 240, 255, 0, 0, '', '', $TargetSizeCU14, $DistanceCU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU14, 5, 3, 1, 5, 3, 1, 'CU14W', 'Trissubogi Kvenna U14 Compound Women', 0, 240, 255, 0, 0, '', '', $TargetSizeCU14, $DistanceCU14);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU14, 5, 3, 1, 5, 3, 1, 'CU14U', 'Trissubogi Allir U14 Compound Unisex', 0, 240, 255, 0, 0, '', '', $TargetSizeCU14, $DistanceCU14);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAM', 'Trissubogi Áhugamenn/Byrjendur Karla', 0, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAW', 'Trissubogi Áhugamenn/Byrjendur Kvenna', 0, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAU', 'Trissubogi Áhugamenn/Byrjendur Allir', 0, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);
		// Berbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU21, 5, 3, 1, 5, 3, 1, 'BU21M', 'Berbogi Karla U21 Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBU21, $DistanceBU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU21, 5, 3, 1, 5, 3, 1, 'BU21W', 'Berbogi Kvenna U21 Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBU21, $DistanceBU21);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU21, 5, 3, 1, 5, 3, 1, 'BU21U', 'Berbogi Allir U21 Barebow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeBU21, $DistanceBU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU18, 5, 3, 1, 5, 3, 1, 'BU18M', 'Berbogi Karla U18 Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBU18, $DistanceBU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU18, 5, 3, 1, 5, 3, 1, 'BU18W', 'Berbogi Kvenna U18 Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBU18, $DistanceBU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU18, 5, 3, 1, 5, 3, 1, 'BU18U', 'Berbogi Allir U18 Barebow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeBU18, $DistanceBU18);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU16, 5, 3, 1, 5, 3, 1, 'BU16M', 'Berbogi Karla U16 Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBU16, $DistanceBU16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU16, 5, 3, 1, 5, 3, 1, 'BU16W', 'Berbogi Kvenna U16 Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBU16, $DistanceBU16);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU16, 5, 3, 1, 5, 3, 1, 'BU16U', 'Berbogi Allir U16 Barebow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeBU16, $DistanceBU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU14, 5, 3, 1, 5, 3, 1, 'BU14M', 'Berbogi Karla U14 Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeBU14, $DistanceBU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU14, 5, 3, 1, 5, 3, 1, 'BU14W', 'Berbogi Kvenna U14 Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeBU14, $DistanceBU14);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU14, 5, 3, 1, 5, 3, 1, 'BU14U', 'Berbogi Allir U14 Barebow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeBU14, $DistanceBU14);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAM', 'Berbogi Áhugamenn/Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeBA, $DistanceBA);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAW', 'Berbogi Áhugamenn/Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeBA, $DistanceBA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAU', 'Berbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeBA, $DistanceBA);
		// Langbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU21, 5, 3, 1, 5, 3, 1, 'LU21M', 'Longbow & Traditional U21 Men', 1, 240, 255, 0, 0, '', '', $TargetSizeLU21, $DistanceLU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU21, 5, 3, 1, 5, 3, 1, 'LU21W', 'Longbow & Traditional U21 Women', 1, 240, 255, 0, 0, '', '', $TargetSizeLU21, $DistanceLU21);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU21, 5, 3, 1, 5, 3, 1, 'LU21U', 'Longbow & Traditional U21 Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeLU21, $DistanceLU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU18, 5, 3, 1, 5, 3, 1, 'LU18M', 'Longbow & Traditional U18 Men', 1, 240, 255, 0, 0, '', '', $TargetSizeLU18, $DistanceLU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU18, 5, 3, 1, 5, 3, 1, 'LU18W', 'Longbow & Traditional U18 Women', 1, 240, 255, 0, 0, '', '', $TargetSizeLU18, $DistanceLU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU18, 5, 3, 1, 5, 3, 1, 'LU18U', 'Longbow & Traditional U18 Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeLU18, $DistanceLU18);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU16, 5, 3, 1, 5, 3, 1, 'LU16M', 'Longbow & Traditional U16 Men', 1, 240, 255, 0, 0, '', '', $TargetSizeLU16, $DistanceLU16);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU16, 5, 3, 1, 5, 3, 1, 'LU16W', 'Longbow & Traditional U16 Women', 1, 240, 255, 0, 0, '', '', $TargetSizeLU16, $DistanceLU16);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU16, 5, 3, 1, 5, 3, 1, 'LU16U', 'Longbow & Traditional U16 Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeLU16, $DistanceLU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU14, 5, 3, 1, 5, 3, 1, 'LU14M', 'Longbow & Traditional U14 Men', 1, 240, 255, 0, 0, '', '', $TargetSizeLU14, $DistanceLU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU14, 5, 3, 1, 5, 3, 1, 'LU14W', 'Longbow & Traditional U14 Women', 1, 240, 255, 0, 0, '', '', $TargetSizeLU14, $DistanceLU14);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU14, 5, 3, 1, 5, 3, 1, 'LU14U', 'Longbow & Traditional U14 Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeLU14, $DistanceLU14);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAM', 'Langbogi Áhugamenn/Byrjendur Karla', 1, 240, 255, 0, 0, '', '', $TargetSizeLA, $DistanceLA);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAW', 'Langbogi Áhugamenn/Byrjendur Kvenna', 1, 240, 255, 0, 0, '', '', $TargetSizeLA, $DistanceLA);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAU', 'Langbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeLA, $DistanceLA);
		}

	if ($SubRule==5) { // "Set Kids classes" ÖLDUNGAMÓT ÚTSLÆTTIR COMPLETE með kynsegin
		// Sveigbogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR3, 5, 3, 1, 5, 3, 1, 'R3U', 'Sveigbogi Allir 30+ Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3);
 		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR4, 5, 3, 1, 5, 3, 1, 'R4U', 'Sveigbogi Allir 40+ Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4);
 		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5M', 'Sveigbogi Karla 50+ Recurve Men', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5W', 'Sveigbogi Kvenna 50+ Recurve Women', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5U', 'Sveigbogi Allir 50+ Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
 		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR6, 5, 3, 1, 5, 3, 1, 'R6U', 'Sveigbogi Allir 60+ Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6);
 		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR7, 5, 3, 1, 5, 3, 1, 'R7U', 'Sveigbogi Allir 70+ Recurve Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7);
 		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAU', 'Sveigbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);
 		// Trissubogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC3, 5, 3, 1, 5, 3, 1, 'C3U', 'Trissubogi Allir 30+ Compound Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3);
 		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC4, 5, 3, 1, 5, 3, 1, 'C4U', 'Trissubogi Allir 40+ Compound Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4);
 		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5M', 'Trissubogi Karla 50+ Compound Men', 1, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5W', 'Trissubogi Kvenna 50+ Compound Women', 1, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5U', 'Trissubogi Allir 50+ Compound Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
 		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC6, 5, 3, 1, 5, 3, 1, 'C6U', 'Trissubogi Allir 60+ Compound Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6);
 		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC7, 5, 3, 1, 5, 3, 1, 'C7U', 'Trissubogi Allir 70+ Compound Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7);
 		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAU', 'Trissubogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);
 		// Berbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB3, 5, 3, 1, 5, 3, 1, 'B3U', 'Berbogi Allir 30+ Barbow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB4, 5, 3, 1, 5, 3, 1, 'B4U', 'Berbogi Allir 40+ Barbow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5M', 'Berbogi Karla 50+ Barebow Men', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5W', 'Berbogi Kvenna 50+ Barebow Women', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5U', 'Berbogi Allir 50+ Barbow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB6, 5, 3, 1, 5, 3, 1, 'B6U', 'Berbogi Allir 60+ Barbow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB7, 5, 3, 1, 5, 3, 1, 'B7U', 'Berbogi Allir 70+ Barbow Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAU', 'Berbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeBA, $DistanceBA);
		// Langbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL3, 5, 3, 1, 5, 3, 1, 'L3U', 'Longbow & Traditional 30+ Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeL3, $DistanceL3);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL4, 5, 3, 1, 5, 3, 1, 'L4U', 'Longbow & Traditional 40+ Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeL4, $DistanceL4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL5, 5, 3, 1, 5, 3, 1, 'L5M', 'Longbow & Traditional 50+ Men', 1, 240, 255, 0, 0, '', '', $TargetSizeL5, $DistanceL5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL5, 5, 3, 1, 5, 3, 1, 'L5W', 'Longbow & Traditional 50+ Women', 1, 240, 255, 0, 0, '', '', $TargetSizeL5, $DistanceL5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL5, 5, 3, 1, 5, 3, 1, 'L5U', 'Longbow & Traditional 50+ Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeL5, $DistanceL5);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL6, 5, 3, 1, 5, 3, 1, 'L6U', 'Longbow & Traditional 60+ Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeL6, $DistanceL6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL7, 5, 3, 1, 5, 3, 1, 'L7U', 'Longbow & Traditional 60+ Unisex', 1, 240, 255, 0, 0, '', '', $TargetSizeL7, $DistanceL7);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAU', 'Langbogi Áhugamenn/Byrjendur Allir', 1, 240, 255, 0, 0, '', '', $TargetSizeLA, $DistanceLA);
		}
		
	if ($SubRule==6) { // "All Classes WA 4 Pools" UNISEX allir aldursflokkar útsláttarkeppni EINSTAKLINGA COMPLETE - Breytt úr RJU í RU21U
		// Sveigbogi
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR,  5, 3, 1, 5, 3, 1, 'RU',  'Sveigbogi M.fl/ Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU21, 5, 3, 1, 5, 3, 1, 'RU21U', 'Sveigbogi U21 Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeRU21, $DistanceRU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU18, 5, 3, 1, 5, 3, 1, 'RU18U', 'Sveigbogi U18 Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeRU18, $DistanceRU18);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU16, 5, 3, 1, 5, 3, 1, 'RU16U', 'Sveigbogi U16 Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeRU16, $DistanceRU16);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRU14, 5, 3, 1, 5, 3, 1, 'RU14U', 'Sveigbogi U14 Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeRU14, $DistanceRU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR3, 5, 3, 1, 5, 3, 1, 'R3U', 'Sveigbogi 30+ Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeR3, $DistanceR3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR4, 5, 3, 1, 5, 3, 1, 'R4U', 'Sveigbogi 40+ Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeR4, $DistanceR4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR5, 5, 3, 1, 5, 3, 1, 'R5U', 'Sveigbogi 50+ Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR6, 5, 3, 1, 5, 3, 1, 'R6U', 'Sveigbogi 60+ Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeR6, $DistanceR6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetR7, 5, 3, 1, 5, 3, 1, 'R7U', 'Sveigbogi 70+ Recurve', 1, 240, 255, 0, 0, '', '', $TargetSizeR7, $DistanceR7);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetRA, 5, 3, 1, 5, 3, 1, 'RAU', 'Sveigbogi Áhugamenn/Byrjendur', 1, 240, 255, 0, 0, '', '', $TargetSizeRA, $DistanceRA);
		// Trissubogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC,  5, 3, 1, 5, 3, 1, 'CU',  'Trissubogi M.fl/ Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU21, 5, 3, 1, 5, 3, 1, 'CU21U', 'Trissubogi U21 Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeCU21, $DistanceCU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU18, 5, 3, 1, 5, 3, 1, 'CU18U', 'Trissubogi U18 Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeCU18, $DistanceCU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU16, 5, 3, 1, 5, 3, 1, 'CU16U', 'Trissubogi U16 Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeCU16, $DistanceCU16);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCU14, 5, 3, 1, 5, 3, 1, 'CU14U', 'Trissubogi U14 Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeCU14, $DistanceCU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC3, 5, 3, 1, 5, 3, 1, 'C3U', 'Trissubogi 30+ Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeC3, $DistanceC3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC4, 5, 3, 1, 5, 3, 1, 'C4U', 'Trissubogi 40+ Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeC4, $DistanceC4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC5, 5, 3, 1, 5, 3, 1, 'C5U', 'Trissubogi 50+ Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC6, 5, 3, 1, 5, 3, 1, 'C6U', 'Trissubogi 60+ Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeC6, $DistanceC6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetC7, 5, 3, 1, 5, 3, 1, 'C7U', 'Trissubogi 70+ Compound', 0, 240, 255, 0, 0, '', '', $TargetSizeC7, $DistanceC7);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetCA, 5, 3, 1, 5, 3, 1, 'CAU', 'Trissubogi Áhugamenn/Byrjendur', 0, 240, 255, 0, 0, '', '', $TargetSizeCA, $DistanceCA);
		// Berbogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB,  5, 3, 1, 5, 3, 1, 'BU',  'Berbogi M.fl/ Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU21, 5, 3, 1, 5, 3, 1, 'BU21U', 'Berbogi U21 Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeBU21, $DistanceBU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU18, 5, 3, 1, 5, 3, 1, 'BU18U', 'Berbogi U18 Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeBU18, $DistanceBU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU16, 5, 3, 1, 5, 3, 1, 'BU16U', 'Berbogi U16 Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeBU16, $DistanceBU16);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBU14, 5, 3, 1, 5, 3, 1, 'BU14U', 'Berbogi U14 Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeBU14, $DistanceBU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB3, 5, 3, 1, 5, 3, 1, 'B3U', 'Berbogi 30+ Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeB3, $DistanceB3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB4, 5, 3, 1, 5, 3, 1, 'B4U', 'Berbogi 40+ Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeB4, $DistanceB4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB5, 5, 3, 1, 5, 3, 1, 'B5U', 'Berbogi 50+ Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB6, 5, 3, 1, 5, 3, 1, 'B6U', 'Berbogi 60+ Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeB6, $DistanceB6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetB7, 5, 3, 1, 5, 3, 1, 'B7U', 'Berbogi 70+ Barebow', 1, 240, 255, 0, 0, '', '', $TargetSizeB7, $DistanceB7);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetBA, 5, 3, 1, 5, 3, 1, 'BAU', 'Berbogi Áhugamenn/Byrjendur', 1, 240, 255, 0, 0, '', '', $TargetSizeBA, $DistanceBA);
		// Langbogi
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL,  5, 3, 1, 5, 3, 1, 'LU',  'Longbow & Traditional', 1, 240, 255, 0, 0, '', '', $TargetSizeL, $DistanceL);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU21, 5, 3, 1, 5, 3, 1, 'LU21U', 'Longbow & Traditional U21', 1, 240, 255, 0, 0, '', '', $TargetSizeLU21, $DistanceLU21);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU18, 5, 3, 1, 5, 3, 1, 'LU18U', 'Longbow & Traditional U18', 1, 240, 255, 0, 0, '', '', $TargetSizeLU18, $DistanceLU18);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU16, 5, 3, 1, 5, 3, 1, 'LU16U', 'Longbow & Traditional U16', 1, 240, 255, 0, 0, '', '', $TargetSizeLU16, $DistanceLU16);
	    CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLU14, 5, 3, 1, 5, 3, 1, 'LU14U', 'Longbow & Traditional U14', 1, 240, 255, 0, 0, '', '', $TargetSizeLU14, $DistanceLU14);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL3, 5, 3, 1, 5, 3, 1, 'L3U', 'Longbow & Tradtional 30+', 1, 240, 255, 0, 0, '', '', $TargetSizeL3, $DistanceL3);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL4, 5, 3, 1, 5, 3, 1, 'L4U', 'Longbow & Traditonal 40+', 1, 240, 255, 0, 0, '', '', $TargetSizeL4, $DistanceL4);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL5, 5, 3, 1, 5, 3, 1, 'L5U', 'Longbow & Tradtional 50+', 1, 240, 255, 0, 0, '', '', $TargetSizeL5, $DistanceL5);
        CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL6, 5, 3, 1, 5, 3, 1, 'L6U', 'Longbow & Tradtional 60+', 1, 240, 255, 0, 0, '', '', $TargetSizeL6, $DistanceL6);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetL7, 5, 3, 1, 5, 3, 1, 'L7U', 'Longbow & Tradtional 70+', 1, 240, 255, 0, 0, '', '', $TargetSizeL7, $DistanceL7);
		CreateEvent($TourId, $i++, 0, 0, $Phase, $TargetLA, 5, 3, 1, 5, 3, 1, 'LAU', 'Langbogi Áhugamenn/Byrjendur', 1, 240, 255, 0, 0, '', '', $TargetSizeL7, $DistanceL7);
		}

	// LIÐA útslættir
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni LIÐA COMPLETE - Breytt úr RJM í RU21M
		// Til upplýsinga tölurnar á eftir $Targetx standa fyrir "fjöldi umferða","fjöldi örva","fjöldi örva bráðabani" og svo sömu tölur enduteknar. Tölurnar á eftir $i++ eru ? og mixed team 1 er Yes 2 er No
		// Tölurnar á efir distance tengjast því að búa til mörg lið eða 1 lið
		// Sveigbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR,  4, 6, 3, 4, 6, 3, 'RU',  'Sveigbogi Lið M.fl/ Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRU21, 4, 4, 2, 4, 4, 2, 'RU21U', 'Sveigbogi Lið U21 Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeRU21, $DistanceRU21, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRU18, 4, 4, 2, 4, 4, 2, 'RU18U', 'Sveigbogi Lið U18 Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeRU18, $DistanceRU18, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRU16, 4, 4, 2, 4, 4, 2, 'RU16U', 'Sveigbogi Lið U16 Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeRU16, $DistanceRU16, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRU14, 4, 4, 2, 4, 4, 2, 'RU14U', 'Sveigbogi Lið U14 Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeRU14, $DistanceRU14, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR5, 4, 4, 2, 4, 4, 2, 'R5U', 'Sveigbogi Lið 50+ Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5, '', 1);		
		// Trissubogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC,  4, 6, 3, 4, 6, 3, 'CU',  'Trissubogi Lið M.fl/ Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCU21, 4, 4, 2, 4, 4, 2, 'CU21U', 'Trissubogi Lið U21 Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeCU21, $DistanceCU21, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCU18, 4, 4, 2, 4, 4, 2, 'CU18U', 'Trissubogi Lið U18 Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeCU18, $DistanceCU18, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCU16, 4, 4, 2, 4, 4, 2, 'CU16U', 'Trissubogi Lið U16 Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeCU16, $DistanceCU16, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCU14, 4, 4, 2, 4, 4, 2, 'CU14U', 'Trissubogi Lið U14 Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeCU14, $DistanceCU14, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC5, 4, 4, 2, 4, 4, 2, 'C5U', 'Trissubogi Lið 50+ Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5, '', 1);
        // Berbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB,  4, 6, 3, 4, 6, 3, 'BU',  'Berbogi Lið M.fl/ Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBU21, 4, 4, 2, 4, 4, 2, 'BU21U', 'Berbogi Lið U21 Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeBU21, $DistanceBU21, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBU18, 4, 4, 2, 4, 4, 2, 'BU18U', 'Berbogi Lið U18 Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeBU18, $DistanceBU18, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBU16, 4, 4, 2, 4, 4, 2, 'BU16U', 'Berbogi Lið U16 Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeBU16, $DistanceBU16, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBU14, 4, 4, 2, 4, 4, 2, 'BU14U', 'Berbogi Lið U14 Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeBU14, $DistanceBU14, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB5, 4, 4, 2, 4, 4, 2, 'B5U', 'Berbogi Lið 50+ Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5, '', 1);		
		// Langbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetL,  4, 6, 3, 4, 6, 3, 'LU',  'Longbow & Traditional Team', 1, 240, 255, 0, 0, '', '', $TargetSizeL, $DistanceL, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetLU21, 4, 4, 2, 4, 4, 2, 'LU21U', 'Longbow & Traditional U21 Team', 1, 240, 255, 0, 0, '', '', $TargetSizeLU21, $DistanceLU21, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetLU18, 4, 4, 2, 4, 4, 2, 'LU18U', 'Longbow & Traditional U18 Team', 1, 240, 255, 0, 0, '', '', $TargetSizeLU18, $DistanceLU18, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetLU16, 4, 4, 2, 4, 4, 2, 'LU16U', 'Longbow & Traditional U16 Team', 1, 240, 255, 0, 0, '', '', $TargetSizeLU16, $DistanceLU16, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetLU14, 4, 4, 2, 4, 4, 2, 'LU14U', 'Longbow & Traditional U14 Team', 1, 240, 255, 0, 0, '', '', $TargetSizeLU14, $DistanceLU14, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetL5, 4, 4, 2, 4, 4, 2, 'L5U', 'Longbow & Traditional 50+ Team', 1, 240, 255, 0, 0, '', '', $TargetSizeL5, $DistanceL5, '', 1);		
		}
		
	if ($SubRule==2) { //"Only Adult Classes" Bara opinn flokkur útsláttarkeppni LIÐA COMPLETE
		// Sveigbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU', 'Sveigbogi Lið M.fl/ Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);
		// Trissubogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU', 'Trissubogi Lið M.fl/ Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
		// Berbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU', 'Berbogi Lið M.fl/ Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1);
		// Langbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetL, 4, 6, 3, 4, 6, 3, 'LU', 'Longbow & Traditional Team', 1, 240, 255, 0, 0, '', '', $TargetSizeL, $DistanceL, '', 1);
		}

    if ($SubRule==3) { //"All-in-one class" Bara opinn flokkur útsláttarkeppni LIÐA COMPLETE
		// Sveigbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR, 4, 6, 3, 4, 6, 3, 'RU', 'Sveigbogi Lið M.fl/ Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeR, $DistanceR, '', 1);
		// Trissubogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC, 4, 6, 3, 4, 6, 3, 'CU', 'Trissubogi Lið M.fl/ Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeC, $DistanceC, '', 1);
		// Berbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB, 4, 6, 3, 4, 6, 3, 'BU', 'Berbogi Lið M.fl/ Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeB, $DistanceB, '', 1);
		// Langbogi
		CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetL, 4, 6, 3, 4, 6, 3, 'LU', 'Longbow & Traditional Team', 1, 240, 255, 0, 0, '', '', $TargetSizeL, $DistanceL, '', 1);
		}


	if ($SubRule==4) { // "EVERY CLASSES" YOUTH SERIES útsláttarkeppni LIÐA COMPLETE - Breytt úr RJM í RU21M
		// Til upplýsinga tölurnar á eftir $Targetx standa fyrir "fjöldi umferða","fjöldi örva","fjöldi örva bráðabani" og svo sömu tölur enduteknar.
		// Sveigbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRU21, 4, 4, 2, 4, 4, 2, 'RU21U', 'Sveigbogi Lið U21 Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeRU21, $DistanceRU21, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRU18, 4, 4, 2, 4, 4, 2, 'RU18U', 'Sveigbogi Lið U18 Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeRU18, $DistanceRU18, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRU16, 4, 4, 2, 4, 4, 2, 'RU16U', 'Sveigbogi Lið U16 Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeRU16, $DistanceRU16, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetRU14, 4, 4, 2, 4, 4, 2, 'RU14U', 'Sveigbogi Lið U14 Recurve Team', 1, 240, 255, 0, 0, '', '', $TargetSizeRU14, $DistanceRU14, '', 1);
		// Trissubogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCU21, 4, 4, 2, 4, 4, 2, 'CU21U', 'Trissubogi Lið U21 Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeCU21, $DistanceCU21, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCU18, 4, 4, 2, 4, 4, 2, 'CU18U', 'Trissubogi Lið U18 Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeCU18, $DistanceCU18, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCU16, 4, 4, 2, 4, 4, 2, 'CU16U', 'Trissubogi Lið U16 Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeCU16, $DistanceCU16, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetCU14, 4, 4, 2, 4, 4, 2, 'CU14U', 'Trissubogi Lið U14 Compound Team', 0, 240, 255, 0, 0, '', '', $TargetSizeCU14, $DistanceCU14, '', 1);
        // Berbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBU21, 4, 4, 2, 4, 4, 2, 'BU21U', 'Berbogi Lið U21 Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeBU21, $DistanceBU21, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBU18, 4, 4, 2, 4, 4, 2, 'BU18U', 'Berbogi Lið U18 Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeBU18, $DistanceBU18, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBU16, 4, 4, 2, 4, 4, 2, 'BU16U', 'Berbogi Lið U16 Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeBU16, $DistanceBU16, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetBU14, 4, 4, 2, 4, 4, 2, 'BU14U', 'Berbogi Lið U14 Barebow Team', 1, 240, 255, 0, 0, '', '', $TargetSizeBU14, $DistanceBU14, '', 1);
		// Langbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetLU21, 4, 4, 2, 4, 4, 2, 'LU21U', 'Longbow & Traditional U21 Team', 1, 240, 255, 0, 0, '', '', $TargetSizeLU21, $DistanceLU21, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetLU18, 4, 4, 2, 4, 4, 2, 'LU18U', 'Longbow & Traditional U18 Team', 1, 240, 255, 0, 0, '', '', $TargetSizeLU18, $DistanceLU18, '', 1);
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetLU16, 4, 4, 2, 4, 4, 2, 'LU16U', 'Longbow & Traditional U16 Team', 1, 240, 255, 0, 0, '', '', $TargetSizeLU16, $DistanceLU16, '', 1);
	    CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetLU14, 4, 4, 2, 4, 4, 2, 'LU14U', 'Longbow & Traditional U14 Team', 1, 240, 255, 0, 0, '', '', $TargetSizeLU14, $DistanceLU14, '', 1);
        }
		
	if ($SubRule==5) { // "ONLY YOUTH CLASSES" ÖLDUNGAFLOKKA útsláttarkeppni LIÐA ÓKLÁRAÐ COMPLETE FOR NOW ÞARF AÐ ÁKVEÐA HVAÐ Á AÐ GERA MEÐ MASTERS LIÐAKEPPNI
		// Til upplýsinga tölurnar á eftir $Targetx standa fyrir "fjöldi umferða","fjöldi örva","fjöldi örva bráðabani" og svo sömu tölur enduteknar.
		// Sveigbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetR5, 4, 4, 2, 4, 4, 2, 'R5U', 'Sveigbogi Lið 50+ Recurve Team', 		1, 240, 255, 0, 0, '', '', $TargetSizeR5, $DistanceR5, '', 1);		
		// Trissubogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetC5, 4, 4, 2, 4, 4, 2, 'C5U', 'Trissubogi Lið 50+ Compound Team', 		0, 240, 255, 0, 0, '', '', $TargetSizeC5, $DistanceC5, '', 1);		
		// Berbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetB5, 4, 4, 2, 4, 4, 2, 'B5U', 'Berbogi Lið 50+ Barebow Team', 			1, 240, 255, 0, 0, '', '', $TargetSizeB5, $DistanceB5, '', 1);
        // Langbogi
        CreateEvent($TourId, $i++, 1, 1, $Phase, $TargetL5, 4, 4, 2, 4, 4, 2, 'L5U', 'Longbow & Traditional 50+ Team', 			1, 240, 255, 0, 0, '', '', $TargetSizeL5, $DistanceL5, '', 1);
        }		
		
	//}
}

function InsertStandardEvents($TourId, $TourType, $SubRule, $Outdoor=true) { //Tenging undankeppni við útsláttarkeppni. COMPLETE
	//Þetta function tengir Útsláttarkeppni í ákveðnum flokki við Undankeppnina. Takkinn sem maður ýtir á vinstra megin við útsláttarkeppnina í "Individual final setup/manage events" í Ianseo.
	
	//TENGINGAR Í EINSTAKLINGA ÚTSlÆTTI
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni EINSTAKLINGA tenging COMPLETE með kynsegin - Breytt úr RJM í RU21M
	// Til upplýsinga fyrsta talan er fjöldi lína í tenginguni sem á að búa til 0=disabled/einstaklingskeppni og önnur talan er fjöldi keppenda. 
	// Þess vegna stendur 1,1 og 2,1 í mixed team "lína 1 = 1 RM, lína 2 = 1 RW" og 1,3 í liða "lína 1 = 3 RM"
		// Recurve Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'RM',  'R', 'M');
		InsertClassEvent($TourId, 0, 1, 'RW',  'R', 'W');
		InsertClassEvent($TourId, 0, 1, 'RU',  'R', 'M');
		InsertClassEvent($TourId, 0, 1, 'RU',  'R', 'W');
		InsertClassEvent($TourId, 0, 1, 'RU',  'R', 'U');
		InsertClassEvent($TourId, 0, 1, 'RU21M',  'R', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'RU21W',  'R', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'RU21U',  'R', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'RU21U',  'R', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'RU21U',  'R', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'RU18M',  'R', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'RU18W',  'R', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'RU18U',  'R', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'RU18U',  'R', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'RU18U',  'R', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'RU16M',  'R', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'RU16W',  'R', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'RU16U',  'R', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'RU16U',  'R', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'RU16U',  'R', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'RU14M',  'R', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'RU14W',  'R', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'RU14U',  'R', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'RU14U',  'R', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'RU14U',  'R', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'R5M',  'R', '5M');
		InsertClassEvent($TourId, 0, 1, 'R5W',  'R', '5W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '5M');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '5W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '5U');
		InsertClassEvent($TourId, 0, 1, 'R5M',  'R', '6M');
		InsertClassEvent($TourId, 0, 1, 'R5W',  'R', '6W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '6M');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '6W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '6U');
		InsertClassEvent($TourId, 0, 1, 'R5M',  'R', '7M');
		InsertClassEvent($TourId, 0, 1, 'R5W',  'R', '7W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '7M');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '7W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '7U');
		InsertClassEvent($TourId, 0, 1, 'RAM',  'R', 'AM');
		InsertClassEvent($TourId, 0, 1, 'RAW',  'R', 'AW');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AM');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AW');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AU');
		// Compound Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'CM',  'C', 'M');
		InsertClassEvent($TourId, 0, 1, 'CW',  'C', 'W');
		InsertClassEvent($TourId, 0, 1, 'CU',  'C', 'M');
		InsertClassEvent($TourId, 0, 1, 'CU',  'C', 'W');
		InsertClassEvent($TourId, 0, 1, 'CU',  'C', 'U');
		InsertClassEvent($TourId, 0, 1, 'CU21M',  'C', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'CU21W',  'C', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'CU21U',  'C', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'CU21U',  'C', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'CU21U',  'C', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'CU18M',  'C', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'CU18W',  'C', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'CU18U',  'C', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'CU18U',  'C', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'CU18U',  'C', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'CU16M',  'C', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'CU16W',  'C', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'CU16U',  'C', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'CU16U',  'C', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'CU16U',  'C', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'CU14M',  'C', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'CU14W',  'C', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'CU14U',  'C', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'CU14U',  'C', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'CU14U',  'C', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'C5M',  'C', '5M');
		InsertClassEvent($TourId, 0, 1, 'C5W',  'C', '5W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '5M');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '5W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '5U');
		InsertClassEvent($TourId, 0, 1, 'C5M',  'C', '6M');
		InsertClassEvent($TourId, 0, 1, 'C5W',  'C', '6W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '6M');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '6W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '6U');
		InsertClassEvent($TourId, 0, 1, 'C5M',  'C', '7M');
		InsertClassEvent($TourId, 0, 1, 'C5W',  'C', '7W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '7M');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '7W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '7U');
		InsertClassEvent($TourId, 0, 1, 'CAM',  'C', 'AM');
		InsertClassEvent($TourId, 0, 1, 'CAW',  'C', 'AW');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AM');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AW');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AU');
		// Barebow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'BM',  'B', 'M');
		InsertClassEvent($TourId, 0, 1, 'BW',  'B', 'W');
		InsertClassEvent($TourId, 0, 1, 'BU',  'B', 'M');
		InsertClassEvent($TourId, 0, 1, 'BU',  'B', 'W');
		InsertClassEvent($TourId, 0, 1, 'BU',  'B', 'U');
		InsertClassEvent($TourId, 0, 1, 'BU21M',  'B', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'BU21W',  'B', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'BU21U',  'B', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'BU21U',  'B', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'BU21U',  'B', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'BU18M',  'B', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'BU18W',  'B', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'BU18U',  'B', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'BU18U',  'B', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'BU18U',  'B', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'BU16M',  'B', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'BU16W',  'B', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'BU16U',  'B', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'BU16U',  'B', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'BU16U',  'B', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'BU14M',  'B', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'BU14W',  'B', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'BU14U',  'B', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'BU14U',  'B', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'BU14U',  'B', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'B5M',  'B', '5M');
		InsertClassEvent($TourId, 0, 1, 'B5W',  'B', '5W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '5M');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '5W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '5U');
		InsertClassEvent($TourId, 0, 1, 'B5M',  'B', '6M');
		InsertClassEvent($TourId, 0, 1, 'B5W',  'B', '6W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '6M');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '6W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '6U');
		InsertClassEvent($TourId, 0, 1, 'B5M',  'B', '7M');
		InsertClassEvent($TourId, 0, 1, 'B5W',  'B', '7W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '7M');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '7W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '7U');
		InsertClassEvent($TourId, 0, 1, 'BAM',  'B', 'AM');
		InsertClassEvent($TourId, 0, 1, 'BAW',  'B', 'AW');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AM');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AW');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AU');
		// Longbow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'LM',  'L', 'M');
		InsertClassEvent($TourId, 0, 1, 'LW',  'L', 'W');
		InsertClassEvent($TourId, 0, 1, 'LU',  'L', 'M');
		InsertClassEvent($TourId, 0, 1, 'LU',  'L', 'W');
		InsertClassEvent($TourId, 0, 1, 'LU',  'L', 'U');
		InsertClassEvent($TourId, 0, 1, 'LU21M',  'L', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'LU21W',  'L', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'LU21U',  'L', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'LU21U',  'L', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'LU21U',  'L', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'LU18M',  'L', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'LU18W',  'L', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'LU18U',  'L', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'LU18U',  'L', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'LU18U',  'L', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'LU16M',  'L', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'LU16W',  'L', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'LU16U',  'L', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'LU16U',  'L', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'LU16U',  'L', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'LU14M',  'L', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'LU14W',  'L', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'LU14U',  'L', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'LU14U',  'L', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'LU14U',  'L', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'L5M',  'L', '5M');
		InsertClassEvent($TourId, 0, 1, 'L5W',  'L', '5W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '5M');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '5W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '5U');
		InsertClassEvent($TourId, 0, 1, 'L5M',  'L', '6M');
		InsertClassEvent($TourId, 0, 1, 'L5W',  'L', '6W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '6M');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '6W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '6U');
		InsertClassEvent($TourId, 0, 1, 'L5M',  'L', '7M');
		InsertClassEvent($TourId, 0, 1, 'L5W',  'L', '7W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '7M');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '7W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '7U');
		InsertClassEvent($TourId, 0, 1, 'LAM',  'L', 'AM');
		InsertClassEvent($TourId, 0, 1, 'LAW',  'L', 'AW');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AM');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AW');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AU');
		}

	if ($SubRule==2) { // "Only Adult Classes" Bara opinn flokkur útsláttarkeppni EINSTAKLINGA tenging COMPLETE með kynsegin
		// Recurve Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'RM',  'R', 'M');
		InsertClassEvent($TourId, 0, 1, 'RW',  'R', 'W');
		InsertClassEvent($TourId, 0, 1, 'RU',  'R', 'M');
		InsertClassEvent($TourId, 0, 1, 'RU',  'R', 'W');
		InsertClassEvent($TourId, 0, 1, 'RU',  'R', 'U');
		InsertClassEvent($TourId, 0, 1, 'RAM',  'R', 'AM');
		InsertClassEvent($TourId, 0, 1, 'RAW',  'R', 'AW');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AM');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AW');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AU');
		// Compound Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'CM',  'C', 'M');
		InsertClassEvent($TourId, 0, 1, 'CW',  'C', 'W');
		InsertClassEvent($TourId, 0, 1, 'CU',  'C', 'M');
		InsertClassEvent($TourId, 0, 1, 'CU',  'C', 'W');
		InsertClassEvent($TourId, 0, 1, 'CU',  'C', 'U');
		InsertClassEvent($TourId, 0, 1, 'CAM',  'C', 'AM');
		InsertClassEvent($TourId, 0, 1, 'CAW',  'C', 'AW');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AM');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AW');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AU');
		// Barebow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'BM',  'B', 'M');
		InsertClassEvent($TourId, 0, 1, 'BW',  'B', 'W');
        InsertClassEvent($TourId, 0, 1, 'BU',  'B', 'M');
		InsertClassEvent($TourId, 0, 1, 'BU',  'B', 'W');
		InsertClassEvent($TourId, 0, 1, 'BU',  'B', 'U');
		InsertClassEvent($TourId, 0, 1, 'BAM',  'B', 'AM');
		InsertClassEvent($TourId, 0, 1, 'BAW',  'B', 'AW');
        InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AM');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AW');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AU');
		// Longbow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'LM',  'L', 'M');
		InsertClassEvent($TourId, 0, 1, 'LW',  'L', 'W');
        InsertClassEvent($TourId, 0, 1, 'LU',  'L', 'M');
		InsertClassEvent($TourId, 0, 1, 'LU',  'L', 'W');
		InsertClassEvent($TourId, 0, 1, 'LU',  'L', 'U');
		InsertClassEvent($TourId, 0, 1, 'LAM',  'L', 'AM');
		InsertClassEvent($TourId, 0, 1, 'LAW',  'L', 'AW');
        InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AM');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AW');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AU');
		}
	
    if ($SubRule==3) {	// "All-in-one class" Bara opinn flokkur UNISEX ÚTSLÁTTARKEPPNI EINSTAKLINGA TENGING COMPLETE
        InsertClassEvent($TourId, 0, 1, 'R',  'R', 'M');
		InsertClassEvent($TourId, 0, 1, 'R',  'R', 'W');
		InsertClassEvent($TourId, 0, 1, 'R',  'R', 'U');
		InsertClassEvent($TourId, 0, 1, 'C',  'C', 'M');
		InsertClassEvent($TourId, 0, 1, 'C',  'C', 'W');
        InsertClassEvent($TourId, 0, 1, 'C',  'C', 'U');
		InsertClassEvent($TourId, 0, 1, 'B',  'B', 'M');
		InsertClassEvent($TourId, 0, 1, 'B',  'B', 'W');
		InsertClassEvent($TourId, 0, 1, 'B',  'B', 'U');
		InsertClassEvent($TourId, 0, 1, 'L',  'L', 'M');
		InsertClassEvent($TourId, 0, 1, 'L',  'L', 'W');
		InsertClassEvent($TourId, 0, 1, 'L',  'L', 'U');
		//Áhugamenn/Byrjendur Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'RA',  'R', 'AM');
		InsertClassEvent($TourId, 0, 1, 'RA',  'R', 'AW');
		InsertClassEvent($TourId, 0, 1, 'RA',  'R', 'AU');
		InsertClassEvent($TourId, 0, 1, 'CA',  'C', 'AM');
		InsertClassEvent($TourId, 0, 1, 'CA',  'C', 'AW');
        InsertClassEvent($TourId, 0, 1, 'CA',  'C', 'AU');
		InsertClassEvent($TourId, 0, 1, 'BA',  'B', 'AM');
		InsertClassEvent($TourId, 0, 1, 'BA',  'B', 'AW');
		InsertClassEvent($TourId, 0, 1, 'BA',  'B', 'AU');
		InsertClassEvent($TourId, 0, 1, 'LA',  'L', 'AM');
		InsertClassEvent($TourId, 0, 1, 'LA',  'L', 'AW');
		InsertClassEvent($TourId, 0, 1, 'LA',  'L', 'AU');
		}
		
	if ($SubRule==4) {	// "EVERY CLASSES" Ungmenna ÚTSLÁTTARKEPPNI EINSTAKLINGA TENGING COMPLETE með kynsegin - Breytt úr RJM í RU21M
		// Recurve Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'RU21M',  'R', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'RU21W',  'R', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'RU21U',  'R', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'RU21U',  'R', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'RU21U',  'R', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'RU18M',  'R', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'RU18W',  'R', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'RU18U',  'R', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'RU18U',  'R', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'RU18U',  'R', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'RU16M',  'R', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'RU16W',  'R', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'RU16U',  'R', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'RU16U',  'R', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'RU16U',  'R', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'RU14M',  'R', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'RU14W',  'R', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'RU14U',  'R', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'RU14U',  'R', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'RU14U',  'R', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'RAM',  'R', 'AM');
		InsertClassEvent($TourId, 0, 1, 'RAW',  'R', 'AW');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AM');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AW');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AU');
		// Compound Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'CU21M',  'C', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'CU21W',  'C', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'CU21U',  'C', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'CU21U',  'C', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'CU21U',  'C', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'CU18M',  'C', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'CU18W',  'C', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'CU18U',  'C', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'CU18U',  'C', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'CU18U',  'C', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'CU16M',  'C', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'CU16W',  'C', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'CU16U',  'C', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'CU16U',  'C', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'CU16U',  'C', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'CU14M',  'C', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'CU14W',  'C', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'CU14U',  'C', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'CU14U',  'C', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'CU14U',  'C', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'CAM',  'C', 'AM');
		InsertClassEvent($TourId, 0, 1, 'CAW',  'C', 'AW');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AM');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AW');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AU');
		// Barebow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'BU21M',  'B', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'BU21W',  'B', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'BU21U',  'B', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'BU21U',  'B', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'BU21U',  'B', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'BU18M',  'B', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'BU18W',  'B', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'BU18U',  'B', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'BU18U',  'B', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'BU18U',  'B', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'BU16M',  'B', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'BU16W',  'B', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'BU16U',  'B', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'BU16U',  'B', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'BU16U',  'B', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'BU14M',  'B', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'BU14W',  'B', 'U14W');	
		InsertClassEvent($TourId, 0, 1, 'BU14U',  'B', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'BU14U',  'B', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'BU14U',  'B', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'BAM',  'B', 'AM');
		InsertClassEvent($TourId, 0, 1, 'BAW',  'B', 'AW');	
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AM');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AW');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AU');
		// Longbow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'LU21M',  'L', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'LU21W',  'L', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'LU21U',  'L', 'U21M');
		InsertClassEvent($TourId, 0, 1, 'LU21U',  'L', 'U21W');
		InsertClassEvent($TourId, 0, 1, 'LU21U',  'L', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'LU18M',  'L', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'LU18W',  'L', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'LU18U',  'L', 'U18M');
		InsertClassEvent($TourId, 0, 1, 'LU18U',  'L', 'U18W');
		InsertClassEvent($TourId, 0, 1, 'LU18U',  'L', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'LU16M',  'L', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'LU16W',  'L', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'LU16U',  'L', 'U16M');
		InsertClassEvent($TourId, 0, 1, 'LU16U',  'L', 'U16W');
		InsertClassEvent($TourId, 0, 1, 'LU16U',  'L', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'LU14M',  'L', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'LU14W',  'L', 'U14W');	
		InsertClassEvent($TourId, 0, 1, 'LU14U',  'L', 'U14M');
		InsertClassEvent($TourId, 0, 1, 'LU14U',  'L', 'U14W');
		InsertClassEvent($TourId, 0, 1, 'LU14U',  'L', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'LAM',  'L', 'AM');
		InsertClassEvent($TourId, 0, 1, 'LAW',  'L', 'AW');	
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AM');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AW');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AU');
		}
		
	if ($SubRule==5) { // "SET KIDS CLASSES" ÖLDUNGAMÓT ALDURFLOKKAR 30+,40+,50+,ETC EINSTAKLINGA tenging COMPLETE með kynsegin
	// Til upplýsinga fyrsta talan er fjöldi lína í tenginguni sem á að búa til 0=disabled/einstaklingskeppni og önnur talan er fjöldi keppenda. 
	// Þess vegna stendur 1,1 og 2,1 í mixed team "lína 1 = 1 RM, lína 2 = 1 RW" og 1,3 í liða "lína 1 = 3 RM"
		// Recurve Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'R3U',  'R', '3M');
		InsertClassEvent($TourId, 0, 1, 'R3U',  'R', '3W');
		InsertClassEvent($TourId, 0, 1, 'R3U',  'R', '3U');
		InsertClassEvent($TourId, 0, 1, 'R4U',  'R', '4M');
		InsertClassEvent($TourId, 0, 1, 'R4U',  'R', '4W');
		InsertClassEvent($TourId, 0, 1, 'R4U',  'R', '4U');
		InsertClassEvent($TourId, 0, 1, 'R5M',  'R', '5M');
		InsertClassEvent($TourId, 0, 1, 'R5W',  'R', '5W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '5M');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '5W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '5U');
		InsertClassEvent($TourId, 0, 1, 'R5M',  'R', '6M');
		InsertClassEvent($TourId, 0, 1, 'R5W',  'R', '6W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '6M');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '6W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '6U');
		InsertClassEvent($TourId, 0, 1, 'R5M',  'R', '7M');
		InsertClassEvent($TourId, 0, 1, 'R5W',  'R', '7W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '7M');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '7W');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '7U');
		InsertClassEvent($TourId, 0, 1, 'R6U',  'R', '6M');
		InsertClassEvent($TourId, 0, 1, 'R6U',  'R', '6W');
		InsertClassEvent($TourId, 0, 1, 'R6U',  'R', '6U');
		InsertClassEvent($TourId, 0, 1, 'R7U',  'R', '7M');
		InsertClassEvent($TourId, 0, 1, 'R7U',  'R', '7W');
		InsertClassEvent($TourId, 0, 1, 'R7U',  'R', '7U');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AM');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AW');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AU');
		// Compound Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'C3U',  'C', '3M');
		InsertClassEvent($TourId, 0, 1, 'C3U',  'C', '3W');
		InsertClassEvent($TourId, 0, 1, 'C3U',  'C', '3U');
		InsertClassEvent($TourId, 0, 1, 'C4U',  'C', '4M');
		InsertClassEvent($TourId, 0, 1, 'C4U',  'C', '4W');
		InsertClassEvent($TourId, 0, 1, 'C4U',  'C', '4U');
		InsertClassEvent($TourId, 0, 1, 'C5M',  'C', '5M');
		InsertClassEvent($TourId, 0, 1, 'C5W',  'C', '5W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '5M');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '5W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '5U');
		InsertClassEvent($TourId, 0, 1, 'C5M',  'C', '6M');
		InsertClassEvent($TourId, 0, 1, 'C5W',  'C', '6W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '6M');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '6W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '6U');
		InsertClassEvent($TourId, 0, 1, 'C5M',  'C', '7M');
		InsertClassEvent($TourId, 0, 1, 'C5W',  'C', '7W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '7M');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '7W');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '7U');
		InsertClassEvent($TourId, 0, 1, 'C6U',  'C', '6M');
		InsertClassEvent($TourId, 0, 1, 'C6U',  'C', '6W');
		InsertClassEvent($TourId, 0, 1, 'C6U',  'C', '6U');
		InsertClassEvent($TourId, 0, 1, 'C7U',  'C', '7M');
		InsertClassEvent($TourId, 0, 1, 'C7U',  'C', '7W');
		InsertClassEvent($TourId, 0, 1, 'C7U',  'C', '7U');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AM');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AW');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AU');
		// Barebow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'B3U',  'B', '3M');
		InsertClassEvent($TourId, 0, 1, 'B3U',  'B', '3W');
		InsertClassEvent($TourId, 0, 1, 'B3U',  'B', '3U');
		InsertClassEvent($TourId, 0, 1, 'B4U',  'B', '4M');
		InsertClassEvent($TourId, 0, 1, 'B4U',  'B', '4W');
		InsertClassEvent($TourId, 0, 1, 'B4U',  'B', '4U');
		InsertClassEvent($TourId, 0, 1, 'B5M',  'B', '5M');
		InsertClassEvent($TourId, 0, 1, 'B5W',  'B', '5W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '5M');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '5W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '5U');
		InsertClassEvent($TourId, 0, 1, 'B5M',  'B', '6M');
		InsertClassEvent($TourId, 0, 1, 'B5W',  'B', '6W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '6M');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '6W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '6U');
		InsertClassEvent($TourId, 0, 1, 'B5M',  'B', '7M');
		InsertClassEvent($TourId, 0, 1, 'B5W',  'B', '7W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '7M');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '7W');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '7U');
		InsertClassEvent($TourId, 0, 1, 'B6U',  'B', '6M');
		InsertClassEvent($TourId, 0, 1, 'B6U',  'B', '6W');
		InsertClassEvent($TourId, 0, 1, 'B6U',  'B', '6U');
		InsertClassEvent($TourId, 0, 1, 'B7U',  'B', '7M');
		InsertClassEvent($TourId, 0, 1, 'B7U',  'B', '7W');
		InsertClassEvent($TourId, 0, 1, 'B7U',  'B', '7U');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AM');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AW');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AU');
		// Longbow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'L3U',  'L', '3M');
		InsertClassEvent($TourId, 0, 1, 'L3U',  'L', '3W');
		InsertClassEvent($TourId, 0, 1, 'L3U',  'L', '3U');
		InsertClassEvent($TourId, 0, 1, 'L4U',  'L', '4M');
		InsertClassEvent($TourId, 0, 1, 'L4U',  'L', '4W');
		InsertClassEvent($TourId, 0, 1, 'L4U',  'L', '4U');
		InsertClassEvent($TourId, 0, 1, 'L5M',  'L', '5M');
		InsertClassEvent($TourId, 0, 1, 'L5W',  'L', '5W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '5M');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '5W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '5U');
		InsertClassEvent($TourId, 0, 1, 'L5M',  'L', '6M');
		InsertClassEvent($TourId, 0, 1, 'L5W',  'L', '6W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '6M');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '6W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '6U');
		InsertClassEvent($TourId, 0, 1, 'L5M',  'L', '7M');
		InsertClassEvent($TourId, 0, 1, 'L5W',  'L', '7W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '7M');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '7W');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '7U');
		InsertClassEvent($TourId, 0, 1, 'L6U',  'L', '6M');
		InsertClassEvent($TourId, 0, 1, 'L6U',  'L', '6W');
		InsertClassEvent($TourId, 0, 1, 'L6U',  'L', '6U');
		InsertClassEvent($TourId, 0, 1, 'L7U',  'L', '7M');
		InsertClassEvent($TourId, 0, 1, 'L7U',  'L', '7W');
		InsertClassEvent($TourId, 0, 1, 'L7U',  'L', '7U');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AM');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AW');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AU');
		}
		
		if ($SubRule==6) { // "All Classes WA 4 Pools" UNISEX allir aldursflokkar, útsláttarkeppni EINSTAKLINGA tenging COMPLETE - Breytt úr RJU í RU21U
		InsertClassEvent($TourId, 0, 1, 'RU',  'R', 'U');
		InsertClassEvent($TourId, 0, 1, 'RU21U',  'R', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'RU18U',  'R', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'RU16U',  'R', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'RU14U',  'R', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'R3U',  'R', '3U');
		InsertClassEvent($TourId, 0, 1, 'R4U',  'R', '4U');
		InsertClassEvent($TourId, 0, 1, 'R5U',  'R', '5U');
		InsertClassEvent($TourId, 0, 1, 'R6U',  'R', '6U');
		InsertClassEvent($TourId, 0, 1, 'R7U',  'R', '7U');
		InsertClassEvent($TourId, 0, 1, 'RAU',  'R', 'AU');
		// Compound Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'CU',  'C', 'U');
		InsertClassEvent($TourId, 0, 1, 'CU21U',  'C', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'CU18U',  'C', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'CU16U',  'C', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'CU14U',  'C', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'C3U',  'C', '3U');
		InsertClassEvent($TourId, 0, 1, 'C4U',  'C', '4U');
		InsertClassEvent($TourId, 0, 1, 'C5U',  'C', '5U');
		InsertClassEvent($TourId, 0, 1, 'C6U',  'C', '6U');
		InsertClassEvent($TourId, 0, 1, 'C7U',  'C', '7U');
		InsertClassEvent($TourId, 0, 1, 'CAU',  'C', 'AU');
		// Barebow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'BU',  'B', 'U');
		InsertClassEvent($TourId, 0, 1, 'BU21U',  'B', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'BU18U',  'B', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'BU16U',  'B', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'BU14U',  'B', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'B3U',  'B', '3U');
		InsertClassEvent($TourId, 0, 1, 'B4U',  'B', '4U');
		InsertClassEvent($TourId, 0, 1, 'B5U',  'B', '5U');
		InsertClassEvent($TourId, 0, 1, 'B6U',  'B', '6U');
		InsertClassEvent($TourId, 0, 1, 'B7U',  'B', '7U');
		InsertClassEvent($TourId, 0, 1, 'BAU',  'B', 'AU');
		// Longbow Einstaklinga tenging, þeas hver má keppa í hvaða útslætti
		InsertClassEvent($TourId, 0, 1, 'LU',  'L', 'U');
		InsertClassEvent($TourId, 0, 1, 'LU21U',  'L', 'U21U');
		InsertClassEvent($TourId, 0, 1, 'LU18U',  'L', 'U18U');
		InsertClassEvent($TourId, 0, 1, 'LU16U',  'L', 'U16U');
		InsertClassEvent($TourId, 0, 1, 'LU14U',  'L', 'U14U');
		InsertClassEvent($TourId, 0, 1, 'L3U',  'L', '3U');
		InsertClassEvent($TourId, 0, 1, 'L4U',  'L', '4U');
		InsertClassEvent($TourId, 0, 1, 'L5U',  'L', '5U');
		InsertClassEvent($TourId, 0, 1, 'L6U',  'L', '6U');
		InsertClassEvent($TourId, 0, 1, 'L7U',  'L', '7U');
		InsertClassEvent($TourId, 0, 1, 'LAU',  'L', 'AU');
		}	
  
	//TENGINGAR Í LIÐAÚTSLÆTTI
	if ($SubRule==1) { // "Championship" Allir aldursflokkar útsláttarkeppni LIÐA tenging COMPLETE - Breytt úr RJM í RU21M
		//Recurve Team Liðatenging 3 manna standard worldarchery í opnum flokki, 2 manna liðakeppni annarsstaðar
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', 'M');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', 'W');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', 'U');
		InsertClassEvent($TourId, 1, 2, 'RU21U', 'R', 'U21M');
		InsertClassEvent($TourId, 1, 2, 'RU21U', 'R', 'U21W');
		InsertClassEvent($TourId, 1, 2, 'RU21U', 'R', 'U21U');
		InsertClassEvent($TourId, 1, 2, 'RU18U', 'R', 'U18M');
		InsertClassEvent($TourId, 1, 2, 'RU18U', 'R', 'U18W');
		InsertClassEvent($TourId, 1, 2, 'RU18U', 'R', 'U18U');
		InsertClassEvent($TourId, 1, 2, 'RU16U', 'R', 'U16M');
		InsertClassEvent($TourId, 1, 2, 'RU16U', 'R', 'U16W');
		InsertClassEvent($TourId, 1, 2, 'RU16U', 'R', 'U16U');
		InsertClassEvent($TourId, 1, 2, 'RU14U', 'R', 'U14M');
		InsertClassEvent($TourId, 1, 2, 'RU14U', 'R', 'U14W');
		InsertClassEvent($TourId, 1, 2, 'RU14U', 'R', 'U14U');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', '3M');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', '3W');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', '3U');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', '4M');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', '4W');		
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', '4U');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '5M');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '5W');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '5U');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '6M');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '6W');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '6U');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '7M');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '7W');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '7U');

		//Compound Team Liðatenging 3 manna standard worldarchery í opnum flokki, 2 manna liðakeppni annarsstaðar
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', 'M');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', 'W');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', 'U');
		InsertClassEvent($TourId, 1, 2, 'CU21U', 'C', 'U21M');
		InsertClassEvent($TourId, 1, 2, 'CU21U', 'C', 'U21W');
		InsertClassEvent($TourId, 1, 2, 'CU21U', 'C', 'U21U');
		InsertClassEvent($TourId, 1, 2, 'CU18U', 'C', 'U18M');
		InsertClassEvent($TourId, 1, 2, 'CU18U', 'C', 'U18W');
		InsertClassEvent($TourId, 1, 2, 'CU18U', 'C', 'U18U');
		InsertClassEvent($TourId, 1, 2, 'CU16U', 'C', 'U16M');
		InsertClassEvent($TourId, 1, 2, 'CU16U', 'C', 'U16W');
		InsertClassEvent($TourId, 1, 2, 'CU16U', 'C', 'U16U');
		InsertClassEvent($TourId, 1, 2, 'CU14U', 'C', 'U14M');
		InsertClassEvent($TourId, 1, 2, 'CU14U', 'C', 'U14W');
		InsertClassEvent($TourId, 1, 2, 'CU14U', 'C', 'U14U');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', '3M');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', '3W');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', '3U');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', '4M');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', '4W');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', '4U');		
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '5M');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '5W');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '5U');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '6M');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '6W');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '6U');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '7M');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '7W');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '7U');
	
		//Barebow Team Liðatenging 3 manna standard worldarchery í opnum flokki, 2 manna liðakeppni annarsstaðar
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', 'M');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', 'W');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', 'U');
		InsertClassEvent($TourId, 1, 2, 'BU21U', 'B', 'U21M');
		InsertClassEvent($TourId, 1, 2, 'BU21U', 'B', 'U21W');
		InsertClassEvent($TourId, 1, 2, 'BU21U', 'B', 'U21U');
		InsertClassEvent($TourId, 1, 2, 'BU18U', 'B', 'U18M');
		InsertClassEvent($TourId, 1, 2, 'BU18U', 'B', 'U18W');
		InsertClassEvent($TourId, 1, 2, 'BU18U', 'B', 'U18U');
		InsertClassEvent($TourId, 1, 2, 'BU16U', 'B', 'U16M');
		InsertClassEvent($TourId, 1, 2, 'BU16U', 'B', 'U16W');
		InsertClassEvent($TourId, 1, 2, 'BU16U', 'B', 'U16U');
		InsertClassEvent($TourId, 1, 2, 'BU14U', 'B', 'U14M');
		InsertClassEvent($TourId, 1, 2, 'BU14U', 'B', 'U14W');
		InsertClassEvent($TourId, 1, 2, 'BU14U', 'B', 'U14U');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', '3M');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', '3W');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', '3U');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', '4M');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', '4W');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', '4U');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '5M');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '5W');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '5U');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '6M');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '6W');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '6U');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '7M');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '7W');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '7U');		
		
		//Longbow Team Liðatenging 3 manna standard worldarchery í opnum flokki, 2 manna liðakeppni annarsstaðar
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', 'M');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', 'W');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', 'U');
		InsertClassEvent($TourId, 1, 2, 'LU21U', 'L', 'U21M');
		InsertClassEvent($TourId, 1, 2, 'LU21U', 'L', 'U21W');
		InsertClassEvent($TourId, 1, 2, 'LU21U', 'L', 'U21U');
		InsertClassEvent($TourId, 1, 2, 'LU18U', 'L', 'U18M');
		InsertClassEvent($TourId, 1, 2, 'LU18U', 'L', 'U18W');
		InsertClassEvent($TourId, 1, 2, 'LU18U', 'L', 'U18U');
		InsertClassEvent($TourId, 1, 2, 'LU16U', 'L', 'U16M');
		InsertClassEvent($TourId, 1, 2, 'LU16U', 'L', 'U16W');
		InsertClassEvent($TourId, 1, 2, 'LU16U', 'L', 'U16U');
		InsertClassEvent($TourId, 1, 2, 'LU14U', 'L', 'U14M');
		InsertClassEvent($TourId, 1, 2, 'LU14U', 'L', 'U14W');
		InsertClassEvent($TourId, 1, 2, 'LU14U', 'L', 'U14U');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', '3M');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', '3W');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', '3U');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', '4M');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', '4W');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', '4U');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '5M');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '5W');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '5U');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '6M');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '6W');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '6U');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '7M');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '7W');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '7U');		
		}
	
	if ($SubRule==2) { // "Only Adult Classes" Bara opinn flokkur útsláttarkeppni LIÐA tenging COMPLETE
		//Recurve Team Liðatenging 3 manna 
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', 'M');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', 'W');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', 'U');
		//Compound Team Liðatenging 3 manna 
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', 'M');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', 'W');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', 'U');
		//Barebow Team Liðatenging 3 manna
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', 'M');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', 'W');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', 'U');
		//Longbow Team Liðatenging 3 manna 
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', 'M');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', 'W');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', 'U');
		}
		
	if ($SubRule==3) { // "All-in-one class" Bara opinn flokkur útsláttarkeppni (Bikarmót) LIÐA tenging COMPLETE
		//Recurve Team Liðatenging 3 manna 
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', 'M');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', 'W');
		InsertClassEvent($TourId, 1, 3, 'RU', 'R', 'U');
		//Compound Team Liðatenging 3 manna 
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', 'M');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', 'W');
		InsertClassEvent($TourId, 1, 3, 'CU', 'C', 'U');
		//Barebow Team Liðatenging 3 manna
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', 'M');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', 'W');
		InsertClassEvent($TourId, 1, 3, 'BU', 'B', 'U');
		//Longbow Team Liðatenging 3 manna 
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', 'M');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', 'W');
		InsertClassEvent($TourId, 1, 3, 'LU', 'L', 'U');
		}
				
	if ($SubRule==4) { // "EVERY CLASSES" Ungmenna LIÐA tenging COMPLETE - Breytt úr RJM í RU21M
		//Recurve Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'RU21U',  'R', 'U21M');
		InsertClassEvent($TourId, 1, 2, 'RU21U',  'R', 'U21W');
		InsertClassEvent($TourId, 1, 2, 'RU21U',  'R', 'U21U');
		InsertClassEvent($TourId, 1, 2, 'RU18U',  'R', 'U18M');
		InsertClassEvent($TourId, 1, 2, 'RU18U',  'R', 'U18W');
		InsertClassEvent($TourId, 1, 2, 'RU18U',  'R', 'U18U');
		InsertClassEvent($TourId, 1, 2, 'RU16U',  'R', 'U16M');
		InsertClassEvent($TourId, 1, 2, 'RU16U',  'R', 'U16W');
		InsertClassEvent($TourId, 1, 2, 'RU16U',  'R', 'U16U');
		InsertClassEvent($TourId, 1, 2, 'RU14U',  'R', 'U14M');
		InsertClassEvent($TourId, 1, 2, 'RU14U',  'R', 'U14W');
		InsertClassEvent($TourId, 1, 2, 'RU14U',  'R', 'U14U');
		//Compound Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'CU21U',  'C', 'U21M');
		InsertClassEvent($TourId, 1, 2, 'CU21U',  'C', 'U21W');
		InsertClassEvent($TourId, 1, 2, 'CU21U',  'C', 'U21U');
		InsertClassEvent($TourId, 1, 2, 'CU18U',  'C', 'U18M');
		InsertClassEvent($TourId, 1, 2, 'CU18U',  'C', 'U18W');
		InsertClassEvent($TourId, 1, 2, 'CU18U',  'C', 'U18U');
		InsertClassEvent($TourId, 1, 2, 'CU16U',  'C', 'U16M');
		InsertClassEvent($TourId, 1, 2, 'CU16U',  'C', 'U16W');
		InsertClassEvent($TourId, 1, 2, 'CU16U',  'C', 'U16U');
		InsertClassEvent($TourId, 1, 2, 'CU14U',  'C', 'U14M');
		InsertClassEvent($TourId, 1, 2, 'CU14U',  'C', 'U14W');
		InsertClassEvent($TourId, 1, 2, 'CU14U',  'C', 'U14U');
		//Barebow Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'BU21U',  'B', 'U21M');
		InsertClassEvent($TourId, 1, 2, 'BU21U',  'B', 'U21W');
		InsertClassEvent($TourId, 1, 2, 'BU21U',  'B', 'U21U');
		InsertClassEvent($TourId, 1, 2, 'BU18U',  'B', 'U18M');
		InsertClassEvent($TourId, 1, 2, 'BU18U',  'B', 'U18W');
		InsertClassEvent($TourId, 1, 2, 'BU18U',  'B', 'U18U');
		InsertClassEvent($TourId, 1, 2, 'BU16U',  'B', 'U16M');
		InsertClassEvent($TourId, 1, 2, 'BU16U',  'B', 'U16W');
		InsertClassEvent($TourId, 1, 2, 'BU16U',  'B', 'U16U');
		InsertClassEvent($TourId, 1, 2, 'BU14U',  'B', 'U14M');
		InsertClassEvent($TourId, 1, 2, 'BU14U',  'B', 'U14W');
		InsertClassEvent($TourId, 1, 2, 'BU14U',  'B', 'U14U');
		//Longbow Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'LU21U',  'L', 'U21M');
		InsertClassEvent($TourId, 1, 2, 'LU21U',  'L', 'U21W');
		InsertClassEvent($TourId, 1, 2, 'LU21U',  'L', 'U21U');
		InsertClassEvent($TourId, 1, 2, 'LU18U',  'L', 'U18M');
		InsertClassEvent($TourId, 1, 2, 'LU18U',  'L', 'U18W');
		InsertClassEvent($TourId, 1, 2, 'LU18U',  'L', 'U18U');
		InsertClassEvent($TourId, 1, 2, 'LU16U',  'L', 'U16M');
		InsertClassEvent($TourId, 1, 2, 'LU16U',  'L', 'U16W');
		InsertClassEvent($TourId, 1, 2, 'LU16U',  'L', 'U16U');
		InsertClassEvent($TourId, 1, 2, 'LU14U',  'L', 'U14M');
		InsertClassEvent($TourId, 1, 2, 'LU14U',  'L', 'U14W');
		InsertClassEvent($TourId, 1, 2, 'LU14U',  'L', 'U14U');
		}
		
	if ($SubRule==5) { // "Only youth classes" Öldunga LIÐA tenging COMPLETE
		//Recurve Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '5M');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '5W');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '5U');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '6M');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '6W');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '6U');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '7M');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '7W');
		InsertClassEvent($TourId, 1, 2, 'R5U', 'R', '7U');
		//Compound Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '5M');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '5W');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '5U');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '6M');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '6W');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '6U');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '7M');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '7W');
		InsertClassEvent($TourId, 1, 2, 'C5U', 'C', '7U');
		//Barebow Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '5M');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '5W');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '5U');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '6M');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '6W');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '6U');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '7M');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '7W');
		InsertClassEvent($TourId, 1, 2, 'B5U', 'B', '7U');
		//Longbow Team Liðatenging 2 manna worldarchery af youth Olympics og Universiade
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '5M');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '5W');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '5U');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '6M');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '6W');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '6U');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '7M');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '7W');
		InsertClassEvent($TourId, 1, 2, 'L5U', 'L', '7U');
		}		
	

		
		
//}
		
	
}

