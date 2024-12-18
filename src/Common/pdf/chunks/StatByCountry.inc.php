<?php

$FirstTime=true;
if (isset($PdfData->Data['Items']) && count($PdfData->Data['Items'])>0) {
    $pdf->setDocUpdate($PdfData->Timestamp ?? $PdfData->LastUpdate ?? '');

    $mainHeaderList = array();
    $cnt = 1;
    foreach($PdfData->Data['Fields'] as $field) {
        if(strstr($field,'|') !== false) {
            list($div,$cl) = explode('|',$field);
            if(!array_key_exists($div,$mainHeaderList)) {
                $mainHeaderList[$div]=array();
                $cnt++;
            }
            $mainHeaderList[$div][$cl]=0;
            $cnt++;
        }
    }
    $pages=array();
    $maxColNo = ($pdf->getPageWidth()< $pdf->getPageHeight() ? 25 : 35);
    if($cnt<=$maxColNo) {
        $pages[0] = array_keys($mainHeaderList);
    } else {
        $curPage = 0;
        $curCnt = 0;
        foreach ($mainHeaderList as $hKey=>$hValue) {
            if($curCnt+count($hValue)>$maxColNo) {
                $curPage++;
                $curCnt=0;
            }
            $pages[$curPage][] = $hKey;
            $curCnt += count($hValue);
        }
    }

    $CountryCount = 0;
    $CountryTotal = array();
    foreach($pages as $kPage=>$vPage) {
        if($kPage != 0) {
            $pdf->AddPage();
            $FirstTime= true;
        }
        $HeaderList = array();
        $cnt = 1;
        foreach ($vPage as $pageDiv) {
            $HeaderList[$pageDiv] = $mainHeaderList[$pageDiv];
            $cnt += count($mainHeaderList[$pageDiv]) + 1;
        }

        $ClSize = ($pdf->getPageWidth() - 65) / $cnt;
        foreach ($PdfData->Data['Items'] as $Country => $Rows) {
            if(!array_key_exists($Country,$CountryTotal)) {
                $CountryTotal[$Country] = 0;
                $CountryCount++;
            }
            if ($FirstTime || !$pdf->SamePage(5)) {
                $TmpSegue = (!$pdf->SamePage(5) OR $kPage!=0);
                $pdf->SetFont($pdf->FontStd, 'B', 10);
                $pdf->SetX(55);
                $pdf->Cell(($pdf->getPageWidth() - 65), 6, $PdfData->StatCountries, 1, 1, 'C', 1);
                if ($TmpSegue) {
                    $pdf->SetXY(($pdf->getPageWidth() - 40), $pdf->GetY() - 6);
                    $pdf->SetFont($pdf->FontStd, 'I', 6);
                    $pdf->Cell(30, 6, $PdfData->Continue, 0, 1, 'R', 0);
                }
                $pdf->SetX(55);
                $pdf->SetFont($pdf->FontStd, 'B', 10);
                foreach ($HeaderList as $Key => $Value)
                    $pdf->Cell($ClSize * (count($Value) + 1), 6, ($Key == ' ' ? '--' : $Key), 1, 0, 'C', 1);
                $pdf->Cell(0.5, 10, '', 1, 0, 'C', 1);
                $pdf->Cell($ClSize - 0.5, 10, $PdfData->TotalShort, 1, 0, 'C', 1);
                $pdf->Cell(0.1, 6, '', 0, 1, 'C', 0);


                $pdf->SetX(55);
                $pdf->SetFont($pdf->FontStd, 'B', 8);
                foreach ($HeaderList as $Key => $Value) {
                    foreach ($Value as $Cl => $Total)
                        $pdf->Cell($ClSize, 4, $Cl, 1, 0, 'C', 1);
                    $pdf->Cell($ClSize, 4, $PdfData->TotalShort, 1, 0, 'C', 1);
                }
                $pdf->Cell(0.1, 4, '', 0, 1, 'C', 0);
                $FirstTime = false;
            }

            $pdf->SetFont($pdf->FontStd, '', 7);
            $pdf->Cell(45, 5, $Rows->NationCode . " - " . $Rows->NationName, 1, 0, 'L', 1);
            foreach ($HeaderList as $Key => $Value) {
                $DivTotal = 0;
                foreach ($Value as $Cl => $Total) {
                    $pdf->Cell($ClSize, 5, $Rows->{$Key . "|" . $Cl} ? $Rows->{$Key . "|" . $Cl} : '', 1, 0, 'R', 0);
                    $DivTotal += $Rows->{$Key . '|' . $Cl};
                    $HeaderList[$Key][$Cl] += $Rows->{$Key . '|' . $Cl};
                }
                $pdf->SetFont($pdf->FontStd, 'B', 7);
                $pdf->Cell($ClSize, 5, $DivTotal, 1, 0, 'R', 1);
                $CountryTotal[$Country] += $DivTotal;
            }
            $pdf->Cell(0.5, 5, '', 1, 0, 'C', 0);
            $pdf->SetFont($pdf->FontStd, 'B', 8);
            $pdf->Cell($ClSize - 0.5, 5, $CountryTotal[$Country], 1, 1, 'R', 1);
        }
        $pdf->SetFont($pdf->FontStd, 'B', 1);
        $pdf->Cell(($pdf->getPageWidth() - 20), 0.5, '', 1, 1, 'C', 0);

        $pdf->SetFont($pdf->FontStd, '', 8);
        $pdf->Cell(45, 5, $PdfData->Total . ": " . $CountryCount, 1, 0, 'L', 1);
        $GrandTotal=0;
        foreach ($HeaderList as $Key => $Value) {
            $DivTotal = 0;
            foreach ($Value as $Cl => $Total) {
                $pdf->Cell($ClSize, 5, $Total ? $Total : '', 1, 0, 'R', 0);
                $DivTotal += $Total;
            }
            $pdf->SetFont($pdf->FontStd, 'B', 8);
            $pdf->Cell($ClSize, 5, $DivTotal, 1, 0, 'R', 1);
            $GrandTotal += $DivTotal;
        }
        $pdf->Cell(0.5, 5, '', 1, 0, 'C', 0);
        $pdf->SetFont($pdf->FontStd, 'B', 8);
        $pdf->Cell($ClSize - 0.5, 5, $GrandTotal, 1, 1, 'R', 1);
    }
}

