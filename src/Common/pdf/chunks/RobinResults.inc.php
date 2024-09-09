<?php

//$pdf->HideCols=$PdfData->HideCols;
$pdf->NumberThousandsSeparator=$PdfData->NumberThousandsSeparator;
$pdf->NumberDecimalSeparator=$PdfData->NumberDecimalSeparator;
$pdf->Continue=$PdfData->Continue;
$pdf->TotalShort=$PdfData->TotalShort;
$pdf->ShotOffShort=$PdfData->ShotOffShort;
$pdf->CoinTossShort=$PdfData->CoinTossShort;

$pdf->ShowMatches=($pdf->ShowMatches??1);
$pdf->ShowRank=($pdf->ShowRank??1);

// if(!$pdf->ShowRank) {
// 	$pdf->setPageOrientation('L');
// }

$CellHeight=3.5;
if(count($rankData['sections'])) {
	$pdf->setDocUpdate($rankData['meta']['lastUpdate']);
	$OldTitle='';
	foreach($rankData['sections'] as $IdEvent => $section) {
        $colW1=10;
        $colW2=15;
        $colW3=($pdf->getPageWidth()-20-($colW1*4)-$colW2*2)/2;
		if(empty($Desc)) {
			// this is for upload purposes!
			$Desc=$section['meta']['descr'];
		}
		if($pdf->ShowRank) {
			//Verifico se l'header e qualche riga ci stanno nella stessa pagina altrimenti salto alla prosisma
			// if(!$pdf->SamePage(15+(strlen($section['meta']['printHeader']) ? 8:0)))
			if(!$pdf->SamePage(15) or $OldTitle) {
				$pdf->AddPage();
			}
			$pdf->SetFont('','b',15);
			$pdf->cell(0,0,$section['meta']['descr'],'1',1, 'C', '1');
			$pdf->SetFont('','',8);

			$OldTitle=$IdEvent;
			$oldScore = array(0,0,0,0);
			$newGroup = true;
			$ShootOffScores=array();
			$OldLevel=0;
			$OldGroup=-1;
			$Header=false;
			foreach($section['levels'] as $idLevel => $level) {
				if(!$level['ranks']) {
					continue;
				}
				if(!$pdf->SamePage(15) or $OldLevel) {
					$pdf->AddPage();
					$pdf->SetFont('','b',15);
					$pdf->cell(0,0,$section['meta']['descr'],'1',0, 'C', '1');
					$pdf->SetFont('','i',6);
					$pdf->setx($pdf->getPageWidth()-30);
					$pdf->cell(20,6,$pdf->Continue,'',1, 'R','','','','','','B');
					$pdf->SetFont('','',8);
				}
				$pdf->dy(5);
				$pdf->SetFont('','b',12);
				$pdf->cell(0,0,$level['name'],'1',1, 'C', '1');
				$pdf->SetFont('','',8);
				$OldLevel=$idLevel;
				$OldGroup=-1;
				$Header=false;

				$Header=true;
				foreach($level['ranks'] as $idGroup => $group) {
					$pdf->dy(2);
					$pdf->SetFont('','b',10);
					$pdf->cell(0,0,$group['name'],'1',1, 'L', '1');
					$pdf->SetFont('','',8);

					$pdf->SetFont('','b',8);
					$pdf->cell($colW1, $CellHeight, $section['meta']['fields']['rank'],'1',0, 'C', '1');
					$pdf->cell($colW3, $CellHeight, $section['meta']['fields']['athlete'],'1',0, 'L', '1');
					$pdf->cell($colW3, $CellHeight, $section['meta']['fields']['countryName'],'1',0, 'L', '1');
					$pdf->cell($colW1, $CellHeight, $section['meta']['fields']['points'],'1',0, 'C', '1');
					$pdf->cell($colW2, $CellHeight, $level['tiebreaker'],'1',0, 'C', '1');
					$pdf->cell($colW2, $CellHeight, $level['tiebreaker2'],'1',0, 'C', '1');
					$pdf->cell($colW1*2, $CellHeight, '','1',1, 'C', '1');
					$pdf->SetFont('','',8);

					foreach($group['items'] as $item) {
						$pdf->cell($colW1, $CellHeight, $item['rank']?:'','1',0, 'C');
						$pdf->cell($colW3, $CellHeight, $item['athlete'],'1',0, 'L');
						$pdf->cell($colW3, $CellHeight, $item['countryName'],'1',0, 'L');
						if($item['irm']) {
							$pdf->SetFont('','i',8);
							$pdf->cell($colW1+$colW2, $CellHeight, '','1',0, 'L');
							$pdf->cell($colW1*2, $CellHeight, $item['irmText'],'1',1, 'L');
						} else {
							$pdf->cell($colW1, $CellHeight, $item['rank']?$item['score']:'','1',0, 'C');
							$pdf->cell($colW2, $CellHeight, $item['rank']?$item['tieBreaker']:'','1',0, 'C');
							$pdf->cell($colW2, $CellHeight, $item['rank']?$item['tieBreaker2']:'','1',0, 'C');
							$pdf->SetFont('','i',8);
							if($item['rank']) {
								if($item['so']) {
									$pdf->cell($colW1, $CellHeight, $section['meta']['fields']['so'],'1',0, 'C', '1');
									$ShootOff[$item['rankBefSO']][]=$item;
								} elseif($item['ct']) {
									$pdf->cell($colW1, $CellHeight, $section['meta']['fields']['ct'],'1',0, 'C');
								} else {
									$pdf->cell($colW1, $CellHeight, '','1',0, 'C');
								}
							} else {
								$pdf->cell($colW1, $CellHeight, '','1',0, 'C');
							}
							$pdf->cell($colW1, $CellHeight, $item['qualified'],'1',1, 'C');
						}
						$pdf->SetFont('','',8);
					}
				}
			}
		}

		if($pdf->ShowMatches) {
			if(!$pdf->SamePage(15) or $OldTitle) {
				$pdf->AddPage();
			}
			$pdf->SetFont('','b',15);
			$pdf->cell(0,0,$section['meta']['descr'],'1',1, 'C', '1');
			$pdf->SetFont('','',8);

			$OldTitle=$IdEvent;
			$oldScore = array(0,0,0,0);
			$newGroup = true;
			$ShootOffScores=array();
			$OldLevel=0;
			$OldGroup=-1;
			$Header=false;

			$AthScoreField=($section['meta']['matchMode'] ? 'setScore' : 'score');
			$OppScoreField=($section['meta']['matchMode'] ? 'oppSetScore' : 'oppScore');

			foreach($section['levels'] as $idLevel => $level) {
				if (!$pdf->SamePage(15) or $OldLevel) {
					$pdf->AddPage();
					$pdf->SetFont('', 'b', 15);
					$pdf->cell(0, 0, $section['meta']['descr'], '1', 0, 'C', '1');
					$pdf->SetFont('', 'i', 6);
					$pdf->setx($pdf->getPageWidth() - 30);
					$pdf->cell(20, 6, $pdf->Continue, '', 1, 'R', '', '', '', '', '', 'B');
					$pdf->SetFont('', '', 8);
				}
				$pdf->dy(2);
				$pdf->SetFont('', 'b', 12);
				$pdf->cell(0, 0, $level['name'], '1', 1, 'C', '1');
				$pdf->SetFont('', '', 8);
				$OldLevel = $idLevel;
				$OldGroup = -1;
				$Header = false;

				$Header = true;
				$MatchWidth=( $pdf->getPageWidth()-20-2 ) / 2;
				$colW1=7;
				$colW2=12;
				$NumW2=1+($level['tb-1']?1:0)+($level['tb-2']?1:0);
				$colW3=($MatchWidth-$colW1*($level['tiesAllowed'] ? 3 : 4)-$colW2*$NumW2);
				foreach ($level['matches'] as $idGroup => $group) {
					$pdf->dy(2);
					$pdf->SetFont('', 'b', 10);
					$pdf->cell(0, 0, $group['name'], '1', 1, 'L', '1');
					$pdf->SetFont('', '', 8);

					$Offset=0;
					$pdf->SetLeftMargin(10+$Offset);
					$Y=$pdf->GetY();
					foreach ($group['rounds'] as $round) {
						$pdf->SetLeftMargin(10+$Offset);
						$pdf->SetY($Y);

						if(!$pdf->samePage($level['numMatches']*3*$CellHeight)) {
							$pdf->AddPage();
							$pdf->SetLeftMargin(10);
							// redraws all the continue things
							$pdf->SetFont('', 'b', 15);
							$pdf->cell(0, 7, $section['meta']['descr'], '1', 0, 'C', '1');
							$pdf->SetFont('', 'i', 6);
							$pdf->setx($pdf->getPageWidth() - 30);
							$pdf->cell(20, 6, $pdf->Continue, '', 0, 'R', '', '', '', '', '', 'B');
							$pdf->ln(7);

							$pdf->dy(2);
							$pdf->SetFont('', 'b', 12);
							$pdf->cell(0, 0, $level['name'], '1', 0, 'C', '1');
							$pdf->SetFont('', 'i', 6);
							$pdf->setx($pdf->getPageWidth() - 30);
							$pdf->cell(20, 4, $pdf->Continue, '', 0, 'R', '', '', '', '', '', 'B');
							$pdf->ln(5);

							$pdf->dy(2);
							$pdf->SetFont('', 'b', 10);
							$pdf->cell(0, 4.5, $group['name'], '1', 0, 'L', '1');
							$pdf->SetFont('', 'i', 6);
							$pdf->setx($pdf->getPageWidth() - 30);
							$pdf->cell(20, 4, $pdf->Continue, '', 0, 'R', '', '', '', '', '', 'B');
							$pdf->SetFont('', '', 8);
							$pdf->ln(4.5);

							$Offset=0;
							$Y=$pdf->GetY();
						}
						$pdf->SetFont('', 'b', 8);
						$pdf->cell($MatchWidth,0,$round['name'],1,1,'C',1);
						$pdf->cell($colW1, $CellHeight, $section['meta']['fields']['rank'], '1', 0, 'C', '1');
						$pdf->cell($colW3, $CellHeight, $section['meta']['fields']['athlete'], '1', 0, 'L', '1');
						$pdf->cell($colW2, $CellHeight, $section['meta']['fields']['countryName'], '1', 0, 'L', '1');
						$pdf->cell($colW1, $CellHeight, $section['meta']['fields']['score'], '1', 0, 'C', '1');
						if(!$level['tiesAllowed']) {
							$pdf->cell($colW1, $CellHeight, $section['meta']['fields']['so'], '1', 0, 'C', '1');
						}
						$pdf->cell($colW1, $CellHeight, $section['meta']['fields']['points'], '1', 0, 'C', '1');
						if($level['tb-1']) {
							$pdf->cell($colW2, $CellHeight, $level['tb-1'], '1', 0, 'C', '1');
						}
						if($level['tb-2']) {
							$pdf->cell($colW2, $CellHeight, $level['tb-2'], '1', 0, 'C', '1');
						}
						$pdf->ln();
						$pdf->SetFont('', '', 8);
						foreach ($round['items'] as $item) {
							if($item['athlete'] or $item['oppAthlete']) {
								// at least one of the opponents is there
								$print=($item[$AthScoreField] or $item[$OppScoreField] or $item['irm'] or $item['oppIrm'] or (($item['tie'] or $item['oppTie']) and $level['bestRankMode']==0));
								if($item['irm']) {
									$pdf->SetFont('', 'i', 7);
								}
								$pdf->cell($colW1, $CellHeight, $item['target'] ?: '', '1', 0, 'C');
								$pdf->cell($colW3, $CellHeight, $item['athlete'] ?: $rankData['meta']['bye'] , '1', 0, 'L');
								$pdf->cell($colW2, $CellHeight, $item['countryCode'] ?:'', '1', 0, 'L');
								if($item['irm']) {
									$pdf->cell($colW2+$colW1*($level['tiesAllowed'] ? 2 : 3), $CellHeight, $item['irmText'], '1', 0, 'L');
									$pdf->SetFont('', '', 8);
								} elseif($print) {
									if($item['athlete']) {
										$pdf->cell($colW1, $CellHeight, $item[$AthScoreField], '1', 0, 'C');
										if(!$level['tiesAllowed']) {
											$pdf->cell($colW1, $CellHeight, $item['tiebreakDecoded'], '1', 0, 'C');
										}
										$pdf->cell($colW1, $CellHeight, $item['points'], '1', 0, 'C');
										if($level['tb-1']) {
											$pdf->cell($colW2, $CellHeight, $item['tieBreaker'], '1', 0, 'C');
										}
										if($level['tb-2']) {
											$pdf->cell($colW2, $CellHeight, $item['tieBreaker2'], '1', 0, 'C');
										}
									} else {
										$pdf->cell($colW2+$colW1*($level['tiesAllowed'] ? 2 : 3), $CellHeight, '', '1', 0, 'L');
									}
								} else {
									$pdf->SetFont('', 'i', 7);
									$pdf->cell($colW2+$colW1*($level['tiesAllowed'] ? 2 : 3), $CellHeight*2, $item['scheduledDate'].' @ '.$item['scheduledTime'], '1', 0, 'L');
									$pdf->SetFont('', '', 8);
								}
								$pdf->ln($CellHeight);
								if($item['oppIrm']) {
									$pdf->SetFont('', 'i', 7);
								}
								$pdf->cell($colW1, $CellHeight, $item['oppTarget'] ?: '', '1', 0, 'C');
								$pdf->cell($colW3, $CellHeight, $item['oppAthlete'] ?: $rankData['meta']['bye'], '1', 0, 'L');
								$pdf->cell($colW2, $CellHeight, $item['oppCountryCode'] ?: '' , '1', 0, 'L');
								if($item['oppIrm']) {
									$pdf->cell($colW2+$colW1*($level['tiesAllowed'] ? 2 : 3), $CellHeight, $item['oppIrmText'], '1', 0, 'L');
									$pdf->SetFont('', '', 8);
								} elseif($print) {
									if($item['oppAthlete']) {
										$pdf->cell($colW1, $CellHeight, $item[$OppScoreField], '1', 0, 'C');
										if(!$level['tiesAllowed']) {
											$pdf->cell($colW1, $CellHeight, $item['oppTiebreakDecoded'], '1', 0, 'C');
										}
										$pdf->cell($colW1, $CellHeight, $item['oppPoints'], '1', 0, 'C');
										if($level['tb-1']) {
											$pdf->cell($colW2, $CellHeight, $item['oppTieBreaker'], '1', 0, 'C');
										}
										if($level['tb-2']) {
											$pdf->cell($colW2, $CellHeight, $item['oppTieBreaker2'], '1', 0, 'C');
										}
									} else {
										$pdf->cell($colW2+$colW1*($level['tiesAllowed'] ? 2 : 3), $CellHeight, '', '1', 0, 'L');
									}
								}
							} else {
								// double bye, so print 2 empty lines
								$pdf->cell($colW1, $CellHeight, '', '1', 0, 'C');
								$pdf->cell($colW3, $CellHeight, '', '1', 0, 'L');
								$pdf->cell($colW2, $CellHeight, '', '1', 0, 'L');
								$pdf->cell($colW2+$colW1*($level['tiesAllowed'] ? 2 : 3), $CellHeight, '', '1', 0, 'L');
								$pdf->ln($CellHeight);
								$pdf->cell($colW1, $CellHeight, '', '1', 0, 'C');
								$pdf->cell($colW3, $CellHeight, '', '1', 0, 'L');
								$pdf->cell($colW2, $CellHeight, '', '1', 0, 'L');
								$pdf->cell($colW2+$colW1*($level['tiesAllowed'] ? 2 : 3), $CellHeight, '', '1', 0, 'L');
							}
							$pdf->ln();
							$pdf->dy(1);
						}
						if($Offset) {
							$Offset=0;
                            $pdf->SetLeftMargin(10);
							$Y=$pdf->GetY();
						} else {
							$Offset=2+$MatchWidth;
						}
					}
				}
			}
		}
	}
}

