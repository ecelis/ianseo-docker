<?php

$pdf->setDocUpdate($PdfData->LastUpdate ?? $PdfData->Timestamp ?? '');
$pdf->setPhase($PdfData->Description);

$First=true;
foreach($PdfData->rankData['sections'] as $Event => $section) {
	if(empty($section['items'])) {
        continue;
    }

	$NumComponenti = 1;

	//Report Title
	$arrTitles = array("NOC", "Previous Line-up", "Revised Line-up", "Date and Time of Change#");
	$arrSizes = array(array(10, 40), 50, 50, $pdf->getPageWidth()-170);

	$pdf->SetDataHeader($arrTitles, $arrSizes);
	$pdf->setEvent($section['meta']['descr']);
	if($section['meta']['version']) {
		$pdf->setComment(trim("Vers. {$section['meta']['version']} ({$section['meta']['versionDate']}) {$section['meta']['versionNotes']}"));
	} else {
		$pdf->setComment(trim($section['meta']['printHeader']));
	}
	$pdf->setOrisCode($section['meta']['OrisCode'], $PdfData->Description);
	$pdf->AddPage();

	if($First and (empty($pdf->CompleteBookTitle) or $pdf->CompleteBookTitle!=$PdfData->IndexName)) {
		$pdf->Bookmark($PdfData->IndexName, 0);
		$pdf->CompleteBookTitle=$PdfData->IndexName;
	}
	$First=false;
	$pdf->Bookmark($section['meta']['descr'], 1);

    $oldTeam=-1;
    foreach($section['items'] as $item) {
		$NumComponenti = max(1, count($item['previousAthletes']), count($item['nextAthletes']));
		$changedPage = !$pdf->SamePage($NumComponenti+1, 3.5, $pdf->lastY);

		$dataRow = array(
			$item['countryCode'],
            $item['countryNameLong'] . ($item['subteam']<=1 ? '' : ' (' . $item['subteam'] .')'));
        if($oldTeam == $item['id'].'_'.$item['subteam']) {
            $dataRow = array('','');
        }

		if(count($item['previousAthletes'])) {
			$dataRow[] = $item['previousAthletes'][0]['athlete'] . ($section['meta']['mixedTeam'] ? ' ('.($item['previousAthletes'][0]['gender'] ? $PdfData->GenderShortF : $PdfData->GenderShortM).')' : '');
		} else {
			$dataRow[]='';
		}
        if(count($item['nextAthletes'])) {
            $dataRow[] = $item['nextAthletes'][0]['athlete'] . ($section['meta']['mixedTeam'] ? ' ('.($item['nextAthletes'][0]['gender'] ? $PdfData->GenderShortF : $PdfData->GenderShortM).')' : '');
        } else {
            $dataRow[]='';
        }
        $dataRow[]= dateRenderer($item['timestamp'],'d M Y H:i')."#";

		$pdf->printDataRow($dataRow);

		//Metto i nomi degli altri Componenti se li ho
		if($NumComponenti > 1) {
			for($k=1; $k < $NumComponenti; $k++) {
                $dataRow=array('','');
                if($k<count($item['previousAthletes'])) {
                    $dataRow[] = $item['previousAthletes'][$k]['athlete'] . ($section['meta']['mixedTeam'] ? ' ('.($item['previousAthletes'][$k]['gender'] ? $PdfData->GenderShortF : $PdfData->GenderShortM).')' : '');
                } else {
                    $dataRow[]='';
                }
                if($k<count($item['nextAthletes'])) {
                    $dataRow[] = $item['nextAthletes'][$k]['athlete'] . ($section['meta']['mixedTeam'] ? ' ('.($item['nextAthletes'][$k]['gender'] ? $PdfData->GenderShortF : $PdfData->GenderShortM).')' : '');
                } else {
                    $dataRow[]='';
                }
                $dataRow[]='';
                $pdf->printDataRow($dataRow);
			}
		}
        $oldTeam = $item['id'].'_'.$item['subteam'];
		$pdf->lastY += 2.5;
	}
}

