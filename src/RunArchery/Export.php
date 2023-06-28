<?php

global $CFG;
$IncludeJquery=true;
$IncludeFA=true;

require_once(dirname(__DIR__) . '/config.php');

CheckTourSession(true);
checkACL(AclQualification, AclReadWrite);

require_once('Common/ods/ods.php');
require_once('Common/Lib/Obj_RankFactory.php');

$rank=Obj_RankFactory::create('Run', [$_SESSION['TourId']]);
$rank->read();
$rankData=$rank->getData();

$ODS = new ods();
$ODS->setStyle('Title', array('style:text-properties' => array('fo:font-weight' => 'bold')));
$ODS->setStyle('TitleRow',
	array('style:table-row-properties' => array('style:row-height' => '21pt', 'style:use-optimal-row-height' => 'true')),
	array('style:family'=>'table-row')
);

foreach($rankData['sections'] as $Team => $Events) {
	foreach($Events as $EvCode => $EvItems) {
		if($Team) {
			// Team
			$TITLES=['Rnk','Bib','CoCode','CoName','Final','Running', 'Penalty Time','Adjustments',
				'LAP','Code','Fam','Giv','Time','ArrShot','Hits','LoopsToDo','Loopsdone','Arrow Penalty','Loop Penalty'
			];
			foreach($EvItems['phases'] as $Phase => $Items) {
				$Tab=$EvCode." T";
				switch($Phase) {
					case '1':
						$Tab.=" Finals";
						break;
					case '2':
						$Tab.=" Semi";
						break;
				}
				$ODS->setActiveSheet($Tab);
				$ODS->setRowStyle('TitleRow');
				foreach(range(0, count($TITLES)) as $cell) {
					$ODS->setCellStyle('Title', null, $cell);
				}
				$ODS->addRow($TITLES);
				foreach($Items['items'] as $Id=>$Item) {
					$ODS->addRow([]);
					$row=[
						$Item['rank'],
						$Item['bib'],
						$Item['countryCode'],
						$Item['countryName'],
						$Item['FinalTime'],
						$Item['RunningTime'],
						$Item['PenaltyTime'],
						$Item['AdjustedTime'],
						$Item['laps'][1]['lap'],
						$Item['laps'][1]['enCode'],
						$Item['laps'][1]['famName'],
						$Item['laps'][1]['givName'],
						$Item['laps'][1]['time'],
						$Item['laps'][1]['shots'],
						$Item['laps'][1]['hits'],
						$Item['laps'][1]['loopsToDo'],
						$Item['laps'][1]['loopsDone'],
						$Item['laps'][1]['ArrPenalty'],
						$Item['laps'][1]['LoopPenalty'],
					];
					$ODS->addRow($row);
					foreach($Item['laps'] as $Lap) {
						if($Lap['lap']==1) {
							continue;
						}
						$row=array_fill(0, 8,'');
						$row=array_merge($row, [
							$Lap['lap'],
							$Lap['enCode'],
							$Lap['famName'],
							$Lap['givName'],
							$Lap['time'],
							$Lap['shots'],
							$Lap['hits'],
							$Lap['loopsToDo'],
							$Lap['loopsDone'],
							$Lap['ArrPenalty'],
							$Lap['LoopPenalty'],
						]);
						$ODS->addRow($row);
					}
				}
			}
		} else {
			// Individual
			$TITLES=['Rnk','Bib','Code','Fam','Giv','Age','CoCode','CoName','Final','Running', 'Penalty Time','Adjustments',
				'LAP','Rime','ArrShot','Hits','LoopsToDo','Loopsdone','Arrow Penalty','Loop Penalty'
				];
			foreach($EvItems['phases'] as $Phase => $Items) {
				$Tab=$EvCode." ".($Team ? 'T' : 'I');
				switch($Phase) {
					case '1':
						$Tab.=" Finals";
						break;
					case '2':
						$Tab.=" Semi";
						break;
					default:
						$Tab.=" Qual";
				}
				$ODS->setActiveSheet($Tab);
				$ODS->setRowStyle('TitleRow');
				foreach(range(0, count($TITLES)) as $cell) {
					$ODS->setCellStyle('Title', null, $cell);
				}
				$ODS->addRow($TITLES);
				foreach($Items['items'] as $Id=>$Item) {
					$ODS->addRow([]);
					$row=[
						$Item['rank'],
						$Item['bib'],
						$Item['encode'],
						$Item['familyname'],
						$Item['givenname'],
						$Item['ageclass'],
						$Item['countryCode'],
						$Item['countryName'],
						$Item['FinalTime'],
						$Item['RunningTime'],
						$Item['PenaltyTime'],
						$Item['AdjustedTime'],
						$Item['laps'][1]['lap'],
						$Item['laps'][1]['time'],
						$Item['laps'][1]['shots'],
						$Item['laps'][1]['hits'],
						$Item['laps'][1]['loopsToDo'],
						$Item['laps'][1]['loopsDone'],
						$Item['laps'][1]['ArrPenalty'],
						$Item['laps'][1]['LoopPenalty'],
						];
					$ODS->addRow($row);
					foreach($Item['laps'] as $Lap) {
						if($Lap['lap']==1) {
							continue;
						}
						$row=array_fill(0, 12,'');
						if($Lap['lap']==count($Item['laps'])) {
							$row=array_merge($row, [
								$Lap['lap'],
								$Lap['time'],
							]);
						} else {
							$row=array_merge($row, [
								$Lap['lap'],
								$Lap['time'],
								$Lap['shots'],
								$Lap['hits'],
								$Lap['loopsToDo'],
								$Lap['loopsDone'],
								$Lap['ArrPenalty'],
								$Lap['LoopPenalty'],
							]);
						}
						$ODS->addRow($row);
					}
				}
			}
		}
	}
}



//
//
// foreach($ROWS as $ROW) {
// 	$ODS->addRow($ROW);
// }

$ODS->save($_SESSION['TourCode'].'_RunArchery.ods', 'a');