<?php

//$pdf->HideCols=$PdfData->HideCols;
$pdf->NumberThousandsSeparator=$PdfData->NumberThousandsSeparator;
$pdf->NumberDecimalSeparator=$PdfData->NumberDecimalSeparator;
$pdf->Continue=$PdfData->Continue;
$pdf->TotalShort=$PdfData->TotalShort;
$pdf->ShotOffShort=$PdfData->ShotOffShort;
$pdf->CoinTossShort=$PdfData->CoinTossShort;

if(count($rankData['sections']))
{
	$DistSize = 11;
	$AddSize=($pdf->getPageWidth()-210)/2;
	$pdf->setDocUpdate($rankData['meta']['lastUpdate']);
	$pdf->SetCellPadding(0.5);
	foreach($rankData['sections'] as $IdEvent => $section) {

		//Verifico se l'header e qualche riga ci stanno nella stessa pagina altrimenti salto alla prosisma
		// if(!$pdf->SamePage(15+(strlen($section['meta']['printHeader']) ? 8:0)))
		if(!$pdf->SamePage(15)) {
			$pdf->AddPage();
		}
		$oldScore = array(0,0,0,0);
		$newGroup = true;
		$ShootOffScores=array();
		$OldTitle='';
		$OldLevel=0;
		$OldGroup=-1;
		$Header=false;
		$colW1=10;
		$colW2=15;
		$colW3=($pdf->getPageWidth()-20-($colW1*3)-$colW2)/2;
		foreach($section['levels'] as $idLevel => $level) {
			foreach($level['ranks'] as $idGroup => $group) {
				$ShootOff=[];
				foreach($group['items'] as $item) {
					if(!$item['so'] and !$item['ct']) {
						continue;
					}
					if($OldTitle!=$IdEvent) {
						$pdf->SetFont('','b',15);
						$pdf->cell(0,0,$section['meta']['descr'],'1',1, 'C', '1');
						$pdf->SetFont('','',8);
						$OldLevel=0;
						$OldGroup=-1;
						$Header=false;
						$OldTitle=$IdEvent;
					}
					if($OldLevel!=$idLevel) {
						$pdf->dy(5);
						$pdf->SetFont('','b',12);
						$pdf->cell(0,0,$level['name'],'1',1, 'C', '1');
						$pdf->SetFont('','',8);
						$OldLevel=$idLevel;
						$OldGroup=-1;
						$Header=false;
					}
					if($OldGroup!=$idGroup) {
						$pdf->dy(2);
						$pdf->SetFont('','b',10);
						$pdf->cell(0,0,$group['name'],'1',1, 'L', '1');
						$pdf->SetFont('','',8);
						$OldGroup=$idGroup;
						$Header=false;
						$ShootOff=[];
					}

					if(!$Header) {
						$pdf->SetFont('','b',8);
						$pdf->cell($colW1,0, $section['meta']['fields']['rank'],'1',0, 'C', '1');
						$pdf->cell($colW3,0, $section['meta']['fields']['athlete'],'1',0, 'L', '1');
						$pdf->cell($colW3,0, $section['meta']['fields']['countryName'],'1',0, 'L', '1');
						$pdf->cell($colW1,0, $section['meta']['fields']['score'],'1',0, 'C', '1');
						$pdf->cell($colW2,0, $level['tiebreaker'],'1',0, 'C', '1');
						$pdf->cell($colW2,0, $level['tiebreaker2'],'1',0, 'C', '1');
						$pdf->cell($colW1,0, '','1',1, 'C', '1');
						$pdf->SetFont('','',8);
						$Header=true;
					}

					$pdf->cell($colW1,0, $item['rankBefSO'],'1',0, 'C');
					$pdf->cell($colW3,0, $item['athlete'],'1',0, 'L');
					$pdf->cell($colW3,0, $item['countryName'],'1',0, 'L');
					$pdf->cell($colW1,0, $item['score'],'1',0, 'C');
					$pdf->cell($colW2,0, $item['tieBreaker'],'1',0, 'C');
					$pdf->cell($colW2,0, $item['tieBreaker2'],'1',0, 'C');
					$pdf->SetFont('','i',8);
					if($item['so']) {
						$pdf->cell($colW1,0, $section['meta']['fields']['so'],'1',1, 'C', '1');
						$ShootOff[$item['rankBefSO']][]=$item;
					} elseif($item['ct']) {
						$pdf->cell($colW1,0, $section['meta']['fields']['ct'],'1',1, 'C');
					} else {
						$pdf->cell($colW1,0, '','1',1, 'C');
					}
					$pdf->SetFont('','',8);
				}
				if($ShootOff) {
					$pdf->dy(1);
					$NumSo=$level['soNumArrows']*3;
					$colSO=10;
					$colW4=($pdf->getPageWidth()-20-$colW1-$colSO-$colSO*$level['soNumArrows']*3-3)/2;
					$pdf->SetFont('','i',9);
					$pdf->cell(0,0,$section['meta']['fields']['tiebreak'],'1',1, 'C', '1');
					$pdf->SetFont('','b',8);
					$pdf->cell($colW1,0, $section['meta']['fields']['rank'],'1',0, 'C', '1');
					$pdf->cell($colW4,0, $section['meta']['fields']['athlete'],'1',0, 'L', '1');
					$pdf->cell($colW4,0, $section['meta']['fields']['countryName'],'1',0, 'L', '1');
					$pdf->cell($colSO*$level['soNumArrows'],0, 'SO 1','1',0, 'C', '1');
					$pdf->cell(1,0, '','1',0, 'C', '1');
					$pdf->cell($colSO*$level['soNumArrows'],0, 'SO 1','1',0, 'C', '1');
					$pdf->cell(1,0, '','1',0, 'C', '1');
					$pdf->cell($colSO*$level['soNumArrows'],0, 'SO 1','1',0, 'C', '1');
					$pdf->cell(1,0, '','1',0, 'C', '1');
					$pdf->cell($colSO,0, $section['meta']['fields']['tiebreakClosest'],'1',0, 'C', '1');
					$pdf->ln();
					$pdf->SetFont('','',8);
					$first=true;
					foreach($ShootOff as $Ranks) {
						if (!$first) {
							$pdf->dy(0.5);
						}
						$first = false;
						foreach ($Ranks as $item) {
							$item['tieArrows']=str_pad($item['tieArrows'],$level['soNumArrows']*3, ' ');
							$pdf->cell($colW1, 0, $item['rankBefSO'], '1', 0, 'C');
							$pdf->cell($colW4, 0, $item['athlete'], '1', 0, 'L');
							$pdf->cell($colW4, 0, $item['countryName'], '1', 0, 'L');
							for ($i = 0; $i < 3; $i++) {
								for ($j = 0; $j < $level['soNumArrows']; $j++) {
									$pdf->cell($colSO, 0, DecodeFromLetter($item['tieArrows'][$i*$level['soNumArrows']+$j]), '1', 0, 'C');
								}
								$pdf->cell(1, 0, '', '1', 0, 'C', '1');
							}
							$pdf->cell($colSO, 0, $item['tieClosest'] ? 'X' : '', '1', 0, 'C');
							$pdf->ln();
						}
					}
				}
			}
		}

		$pdf->Output();
		die();

		foreach($section['items'] as $item)
		{
			if($item['ct']>1)
			{
				if($item['so']) {
					$ShootOffScores[]=$item;
				}
				if(($item['so']!=0 && $oldScore[0]!= $item['score']) || ($item['so']==0 && !($oldScore[0]== $item['score'] && $oldScore[1]== $item['gold'] && $oldScore[2]== $item['xnine'])))
				{
					$oldScore[3]=$item['ct'];
					if($newGroup)
					{
						$pdf->SetY($pdf->GetY()+2);
						$pdf->writeGroupHeaderPrnIndividualAbs($section['meta'], $DistSize, $AddSize, $section['meta']['running'], $section['meta']['numDist'], $rankData['meta']['double'], false);
						$newGroup = false;
					}
					else
					{
						$pdf->SetFont($pdf->FontStd,'',1);
						$pdf->Cell(0, 1,  '', 1, 1, 'C', 1);
					}
					if (!$pdf->SamePage(4* $oldScore[3]))
					{
						$pdf->AddPage();
						$pdf->writeGroupHeaderPrnIndividualAbs($section['meta'], $DistSize, $AddSize, $section['meta']['running'], $section['meta']['numDist'], $rankData['meta']['double'], true);
					}
				}

				$pdf->writeDataRowPrnIndividualAbs($item, $DistSize, $AddSize, $section['meta']['running'],$section['meta']['numDist'], $rankData['meta']['double'], ($PdfData->family=='Snapshot' ? $section['meta']['snapDistance']: 0), ($oldScore[3]==$item['ct'] ? 'T':($oldScore[3]==1 ? 'B':'')));
				if (!$pdf->SamePage(4*(--$oldScore[3])))
				{
					$pdf->AddPage();
					$pdf->writeGroupHeaderPrnIndividualAbs($section['meta'], $DistSize, $AddSize, $section['meta']['running'], $section['meta']['numDist'], $rankData['meta']['double'], true);
				}
				$oldScore = array($item['score'], $item['gold'], $item['xnine'], $oldScore[3]);
			}
		}

		if($ShootOffScores) {
			$CellHeight=6;
			$RestWidth=$pdf->getPageWidth()-159-$CellHeight;
			$pdf->SetFont($pdf->FontStd,'',1);
			$pdf->Cell(0, 1,  '', 0, 1, 'C', 0);
			$pdf->SetFont($pdf->FontStd,'B',9);
			$pdf->Cell(0, 6,  $PdfData->ShootOffArrows, 1, 1, 'C', 1);

			$pdf->SetFont($pdf->FontStd,'B',7);
			$pdf->Cell(8, 4, $section['meta']['fields']['rank'], 1, 0, 'C', 1);
			$pdf->Cell(8, 4, $PdfData->TargetShort, 1, 0, 'C', 1);
			$pdf->Cell(38, 4, $section['meta']['fields']['athlete'], 1, 0, 'C', 1);
			$pdf->Cell(10, 4, $section['meta']['fields']['class'], 1, 0, 'C', 1);
			$pdf->Cell(51, 4, $section['meta']['fields']['countryName'], 1, 0, 'C', 1);
			$pdf->Cell(24, 4, $PdfData->ShootOffArrows, 1, 0, 'C', 1);
			$pdf->Cell($CellHeight, 4, $PdfData->Winner, 1, 0, 'C', 1);
			$pdf->Cell($RestWidth, 4, $PdfData->Judge, 1, 0, 'C', 1);
			$pdf->ln();


			$OldPos='';
			foreach($ShootOffScores as $item) {
				if($OldPos and $OldPos!=$item['rankBeforeSO']) {
					$pdf->SetFont($pdf->FontStd,'',1);
					$pdf->Cell(0, 1,  '', 1, 1, 'C', 1);
				}
				$OldPos=$item['rankBeforeSO'];
				$pdf->SetFont($pdf->FontStd,'b',7);
				$pdf->Cell(8, $CellHeight,  $item['rankBeforeSO'], 1, 0, 'R', 0);
				$pdf->SetFont($pdf->FontStd,'',7);
				$pdf->Cell(8, $CellHeight,  $item['session'] . "- " . substr($item['target'],-1), 1, 0, 'R', 0);
				//Atleta
				$pdf->Cell(38, $CellHeight,  $item['athlete'], 1, 0, 'L', 0);
				//Classe
				$pdf->SetFont($pdf->FontStd,'',6);
				$pdf->Cell(5, $CellHeight, ($item['class']), 'TBL', 0, 'C', 0);
				$pdf->SetFont($pdf->FontStd,'',5);
				$pdf->Cell(5, $CellHeight, ($item['class']!=$item['ageclass'] ?  ' ' . ( $item['ageclass']) : ''), 'TBR', 0, 'C', 0);
				//Nazione
				$pdf->SetFont($pdf->FontStd,'',7);
				$pdf->Cell(8, $CellHeight,  $item['countryCode'], 'TBL', 0, 'L', 0);
				$pdf->Cell(43, $CellHeight,  $item['countryName'], 'TBR', 0, 'L', 0);

				// Arr1, 2 and 3
				$pdf->Cell(8, $CellHeight, '', 1, 0, 'C', 0);
				$pdf->Cell(8, $CellHeight, '', 1, 0, 'C', 0);
				$pdf->Cell(8, $CellHeight, '', 1, 0, 'C', 0);

				// Closest
				$pdf->Cell($CellHeight, $CellHeight, '', 1, 0, 'C', 0);
				// Signature
				$pdf->Cell($RestWidth, $CellHeight, '', 1, 0, 'C', 0);
				$pdf->ln();
			}
		}
	}
}

?>